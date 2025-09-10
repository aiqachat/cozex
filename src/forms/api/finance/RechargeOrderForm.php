<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\api\finance;


use app\bootstrap\payment\PaymentOrder;
use app\bootstrap\response\ApiCode;
use app\models\Model;
use app\models\RechargeOrders;

class RechargeOrderForm extends Model
{
    public $amount;
    public $pay_type;

    private $payment;

    public function init()
    {
        parent::init();
        $this->payment = [
            \Yii::$app->payment::PAY_TYPE_WECHAT => 1,
            \Yii::$app->payment::PAY_TYPE_STRIPE => 2,
        ];
    }

    public function rules()
    {
        return [
            [['amount'], 'required'],
            [['amount'], 'double'],
            [['pay_type'], function ($attribute, $params) {
                if (!$this->pay_type || !in_array ($this->pay_type, array_keys($this->payment))) {
                    $this->addError($attribute, '支付方式错误。');
                }
            }],
        ];
    }

    public function balanceRecharge()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $t = \Yii::$app->db->beginTransaction();
        try {
            $order = new RechargeOrders();
            $order->mall_id = \Yii::$app->mall->id;
            $order->order_no = generate_order_no('RE');
            $order->user_id = \Yii::$app->user->id;
            $order->pay_price = $this->amount;
            $order->send_price = 0;
            $order->pay_type = $this->payment[$this->pay_type];
            if (!$order->save()) {
                throw new \Exception($this->getErrorMsg($order));
            }

            $payOrder = new PaymentOrder([
                'title' => '余额充值',
                'amount' => floatval($order->pay_price),
                'orderNo' => $order->order_no,
                'notifyClass' => RechargePayNotify::class,
            ]);
            $id = \Yii::$app->payment->createOrder($payOrder);
            $t->commit();
            $res = \Yii::$app->payment->getPayData($id, $this->pay_type);
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '订单创建成功',
                'data' => array_merge($res, ["order_id" => $order->id, 'no' => $order->order_no]),
            ];
        } catch (\Exception $e) {
            $t->rollBack();
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage(),
                'error' => [
                    'line' => $e->getLine(),
                    'string' => $e->getTraceAsString(),
                ]
            ];
        }
    }
}
