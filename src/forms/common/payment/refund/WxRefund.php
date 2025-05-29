<?php

namespace app\forms\common\payment\refund;

use app\bootstrap\payment\PaymentException;
use app\models\PaymentRefund;

class WxRefund extends BaseRefund
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
            // 微信退款
            \Yii::$app->wechatPay->refund([
                'out_trade_no' => $paymentRefund->out_trade_no,
                'out_refund_no' => $paymentRefund->order_no,
                'total_fee' => $paymentOrderUnion->amount * 100,
                'refund_fee' => $paymentRefund->amount * 100,
            ]);
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
        $paymentRefund->pay_type = 1;
        if (!$paymentRefund->save()) {
            throw new \Exception($this->getErrorMsg($paymentRefund));
        }
    }
}
