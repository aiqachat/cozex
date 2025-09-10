<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/11/16 16:11
 */


namespace app\controllers\system;

use app\bootstrap\payment\PaymentOrder;
use app\controllers\Controller;
use app\bootstrap\payment\PaymentNotify;
use app\forms\common\payment\stripe\StripeNotify;
use app\models\Mall;
use app\models\PaymentOrderUnion;
use WeChatPay\Transformer;
use yii\web\Response;

class PayNotifyController extends Controller
{
    public function init()
    {
        parent::init();
        $this->enableCsrfValidation = false;
    }

    public function actionWechat()
    {
        \Yii::$app->response->format = Response::FORMAT_XML;
        $xml = \Yii::$app->request->rawBody;
        if (!$res = Transformer::toArray($xml)) {
            throw new \Exception('请求数据错误: ' . $xml);
        }
        if (empty($res['out_trade_no'])
            || empty($res['sign'])
            || empty($res['total_fee'])
            || empty($res['result_code'])
            || empty($res['return_code'])
        ) {
            throw new \Exception('请求数据错误: ' . $xml);
        }
        if ($res['result_code'] !== 'SUCCESS' || $res['return_code'] !== 'SUCCESS') {
            throw new \Exception('订单尚未支付: ' . $xml);
        }

        $paymentOrderUnion = PaymentOrderUnion::findOne([
            'order_no' => $res['out_trade_no'],
        ]);
        if (!$paymentOrderUnion) {
            throw new \Exception('订单不存在: ' . $res['out_trade_no']);
        }
        $responseData = [
            'return_code' => 'SUCCESS',
            'return_msg' => 'OK',
        ];
        if ($paymentOrderUnion->is_pay === 1) {
            echo Transformer::toXml($responseData);
            return;
        }
        $mall = Mall::findOne($paymentOrderUnion->mall_id);
        if (!$mall) {
            throw new \Exception('未查询到id=' . $paymentOrderUnion->id . '的商城。 ');
        }
        \Yii::$app->setMall($mall);

        $this->checkWechatSign($res);

        $paymentOrderUnionAmount = (doubleval($paymentOrderUnion->amount) * 100) . '';
        if (intval($res['total_fee']) !== intval($paymentOrderUnionAmount)) {
            throw new \Exception('支付金额与订单金额不一致。');
        }
        $paymentOrders = $paymentOrderUnion->paymentOrder;
        $paymentOrderUnion->is_pay = 1;
        $paymentOrderUnion->pay_type = 1;
        if (!$paymentOrderUnion->save()) {
            throw new \Exception($paymentOrderUnion->getFirstErrors());
        }
        foreach ($paymentOrders as $paymentOrder) {
            $Class = $paymentOrder->notify_class;
            if (!class_exists($Class)) {
                continue;
            }
            $paymentOrder->is_pay = 1;
            $paymentOrder->pay_type = 1;
            if (!$paymentOrder->save()) {
                throw new \Exception($paymentOrder->getFirstErrors());
            }
            /** @var PaymentNotify $notify */
            $notify = new $Class();
            try {
                $po = new PaymentOrder([
                    'orderNo' => $paymentOrder->order_no,
                    'amount' => (float)$paymentOrder->amount,
                    'title' => $paymentOrder->title,
                    'notifyClass' => $paymentOrder->notify_class,
                    'pay_type' => $paymentOrder->pay_type,
                ]);
                $notify->notify($po);
            } catch (\Exception $e) {
                \Yii::error("微信支付通知结果异常");
                \Yii::error($e);
            }
        }
        echo Transformer::toXml($responseData);
    }

    public function actionStripe()
    {
        $data = \Yii::$app->request->post();
        if(empty($data['data']['object'])){
            return;
        }
        \Yii::warning($data);
        $form = new StripeNotify();
        $form->attributes = $data['data']['object'];
        $form->handle();
    }

    private function checkWechatsign($res)
    {
        $wechatPay = \Yii::$app->wechatPay;
        $truthSign = $wechatPay->v2MakeSign($res);
        if ($truthSign !== $res['sign']) {
            throw new \Exception('签名验证失败。');
        }
    }
}
