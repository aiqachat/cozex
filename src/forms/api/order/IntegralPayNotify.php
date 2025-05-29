<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\api\order;

use app\bootstrap\payment\PaymentNotify;
use app\models\IntegralOrders;
use yii\helpers\Json;

class IntegralPayNotify extends PaymentNotify
{
    public function notify($paymentOrder)
    {
        $t = \Yii::$app->db->beginTransaction();
        try {
            /* @var IntegralOrders $order */
            $order = IntegralOrders::find()->where(['order_no' => $paymentOrder->orderNo])->one();
            if (!$order) {
                throw new \Exception('订单不存在:' . $paymentOrder->orderNo);
            }

            $order->is_pay = 1;
            $order->pay_time = mysql_timestamp();
            if (!$order->save()) {
                throw new \Exception('充值订单支付状态更新失败');
            }
            $t->commit();
            $orderData = Json::decode($order->order_data);
            \Yii::$app->currency->setUser($order->user)->integral
                ->add((float)$orderData['send_integral'], "用户发起积分兑换", $order->order_data, $order->order_no);
        } catch (\Exception $e) {
            $t->rollBack();
            \Yii::error($e);
            throw $e;
        }
    }
}
