<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\api\finance;

use app\bootstrap\payment\PaymentNotify;
use app\models\RechargeOrders;
use app\models\User;

class RechargePayNotify extends PaymentNotify
{
    private $desc = '';

    public function notify($paymentOrder)
    {
        try {
            /* @var RechargeOrders $order */
            $order = RechargeOrders::find()->where(['order_no' => $paymentOrder->orderNo])->one();

            if (!$order) {
                throw new \Exception('订单不存在:' . $paymentOrder->orderNo);
            }

            $order->is_pay = 1;
            $order->pay_time = date('Y-m-d H:i:s', time());
            $res = $order->save();

            if (!$res) {
                throw new \Exception('充值订单支付状态更新失败');
            }

            $user = User::findOne($order->user_id);
            if (!$user) {
                throw new \Exception('用户不存在');
            }

            $this->sendData($user, $order);

        } catch (\Exception $e) {
            \Yii::error($e);
            throw $e;
        }
    }

    protected function sendData(User $user, RechargeOrders $order)
    {
        $this->desc = '';
        $this->sendBalance($user, $order);
    }

    protected function sendBalance($user, RechargeOrders $order)
    {
        $desc = '充值余额：' . $order->pay_price . '元';
        $price = (float)($order->pay_price);
        \Yii::$app->currency->setUser($user)->balance->add(
            $price,
            $desc . $this->desc,
            \Yii::$app->serializer->encode($order->attributes),
            $order->order_no
        );
    }
}
