<?php

namespace app\forms\common\payment\refund;

use app\bootstrap\payment\PaymentException;
use app\models\PaymentRefund;
use app\models\StripeOrder;

class StripeRefund extends BaseRefund
{
    /**
     * @param PaymentRefund $paymentRefund
     * @param \app\models\PaymentOrderUnion $paymentOrderUnion
     * @return bool|mixed
     * @throws PaymentException
     */
    public function refund($paymentRefund, $paymentOrderUnion)
    {
        $t = \Yii::$app->db->beginTransaction();
        try {
            $order = StripeOrder::findOne([
                'payment_order_union_id' => $paymentOrderUnion->id,
                'mall_id' => $paymentOrderUnion->mall_id,
                'is_delete' => 0
            ]);
            if (!$order) {
                throw new PaymentException('订单不存在');
            }
            \Yii::$app->stripePay->refund($order->payment_intent, $paymentRefund->amount * 100);
            $this->save($paymentRefund);
            $t->commit();
            return true;
        } catch (\Exception $e) {
            $t->rollBack();
            \Yii::error($e->getMessage() . ' = Line : ' . $e->getLine() . " = File: " . $e->getFile());
            throw new PaymentException($e->getMessage());
        }
    }

    /**
     * @param PaymentRefund $paymentRefund
     * @throws \Exception
     */
    private function save($paymentRefund)
    {
        $paymentRefund->is_pay = 1;
        $paymentRefund->pay_type = 4;
        if (!$paymentRefund->save()) {
            throw new \Exception($this->getErrorMsg($paymentRefund));
        }
    }
}
