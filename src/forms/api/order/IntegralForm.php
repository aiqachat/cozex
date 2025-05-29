<?php
/**
 * Created by PhpStorm
 * User: chenzs
 * @copyright: ©2020 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\forms\api\order;

use app\bootstrap\payment\PaymentOrder;
use app\bootstrap\response\ApiCode;
use app\models\IntegralExchange;
use app\models\IntegralOrders;
use app\models\Model;
use yii\helpers\Json;

class IntegralForm extends Model
{
    public $id;
    public $pay_type;

    public function rules()
    {
        return [
            [['pay_type'], 'required', 'on' => ['buy']],
            [['id',], 'integer'],
            [['pay_type'], 'string'],
        ];
    }

    public function submit()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $t = \Yii::$app->db->beginTransaction();
        try {
            $model = IntegralExchange::findOne(['id' => $this->id, 'is_delete' => 0, 'mall_id' => \Yii::$app->mall->id]);
            if (!$model) {
                throw new \Exception('兑换数据不存在');
            }
            $orderData = [
                'goods_name' => '积分兑换',
                'id' => $this->id,
                'send_integral' => $model->send_integral
            ];
            $order = new IntegralOrders();
            $order->mall_id = \Yii::$app->mall->id;
            $order->order_no = generate_order_no("INT");
            $order->user_id = \Yii::$app->user->id;
            $order->order_data = Json::encode($orderData);
            $order->total_pay_price = $model->pay_price;
            if (!$order->save()) {
                throw new \Exception($this->getErrorMsg($order));
            }
            $payOrder = new PaymentOrder([
                'title' => '积分兑换',
                'amount' => floatval($order->total_pay_price),
                'orderNo' => $order->order_no,
                'notifyClass' => IntegralPayNotify::class,
            ]);
            $id = \Yii::$app->payment->createOrder($payOrder);
            $t->commit();
            $res = \Yii::$app->payment->getPayData($id, $this->pay_type);
            $result = array_merge($res, ["order_id" => $order->id, 'no' => $order->order_no]);
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '订单创建成功',
                'data' => $result
            ];
        } catch (\Exception $e) {
            $t->rollBack ();
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage(),
                'error' => [
                    'line' => $e->getLine()
                ]
            ];
        }
    }

    public function result()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $order = IntegralOrders::findOne($this->id);
        if (!$order) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '订单不存在'
            ];
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'retry' => $order->is_pay ? 0 : 1,
            ],
        ];
    }
}
