<?php

namespace app\forms\common\payment\stripe;

use app\bootstrap\payment\PaymentNotify;
use app\bootstrap\payment\PaymentOrder;
use app\bootstrap\response\ApiCode;
use app\models\Mall;
use app\models\Model;
use app\models\StripeOrder;

class StripeNotify extends Model
{
    public $id;
    public $object;
    public $amount_total;
    public $payment_intent;
    public $payment_status;
    public $status;

    public function rules()
    {
        return [
            [['amount_total'], 'integer'],
            [['id', 'object', 'payment_intent', 'payment_status', 'status'], 'string'],
        ];
    }

    public function handle()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        try {
            switch ($this->object) {
                case 'checkout.session':
                    $this->checkoutSession();
                    break;
                default:
                    throw new \Exception('不处理');
            }
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '处理成功'
            ];
        }catch (\Exception $e){
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage()
            ];
        }
    }

    public function checkoutSession()
    {
        \Yii::warning("stripe支付通知结果");

        $order = StripeOrder::findOne([
            'checkout_id' => $this->id,
            'is_delete' => 0
        ]);
        if (!$order) {
            throw new \Exception('stripe订单不存在');
        }
        $paymentOrderUnion = $order->paymentOrderUnion;
        if (!$paymentOrderUnion) {
            throw new \Exception('系统订单不存在');
        }

        if($this->payment_status !== 'paid' || $this->status !== 'complete'){
            throw new \Exception('stripe订单未支付');
        }
        $order->payment_status = $this->payment_status;
        $order->payment_intent = $this->payment_intent;
        if (!$order->save()) {
            throw new \Exception($this->getErrorMsg($order));
        }
        if ($paymentOrderUnion->is_pay === 1) {
            throw new \Exception('订单已付款');
        }
        $mall = Mall::findOne($paymentOrderUnion->mall_id);
        if (!$mall) {
            throw new \Exception('未查询到id=' . $paymentOrderUnion->id . '的商城。 ');
        }
        \Yii::$app->setMall($mall);

        if ($order->amount !== $this->amount_total) {
            throw new \Exception('支付金额与订单金额不一致。');
        }
        $paymentOrders = $paymentOrderUnion->paymentOrder;
        $paymentOrderUnion->is_pay = 1;
        $paymentOrderUnion->pay_type = 4;
        if (!$paymentOrderUnion->save()) {
            throw new \Exception($this->getErrorMsg($paymentOrderUnion));
        }
        foreach ($paymentOrders as $paymentOrder) {
            $Class = $paymentOrder->notify_class;
            if (!class_exists($Class)) {
                continue;
            }
            $paymentOrder->is_pay = 1;
            $paymentOrder->pay_type = 4;
            if (!$paymentOrder->save()) {
                throw new \Exception($this->getErrorMsg($paymentOrder));
            }
            /** @var PaymentNotify $notify */
            $notify = new $Class();
            $po = new PaymentOrder([
                'orderNo' => $paymentOrder->order_no,
                'amount' => (float)$paymentOrder->amount,
                'title' => $paymentOrder->title,
                'notifyClass' => $paymentOrder->notify_class,
                'pay_type' => $paymentOrder->pay_type,
            ]);
            $notify->notify($po);
        }
    }
}
