<?php

namespace app\forms\common\payment\refund;

use app\bootstrap\payment\PaymentException;
use app\models\PaymentOrderUnion;
use app\models\PaymentRefund;
use app\models\User;
use yii\db\Exception;

class BalanceRefund extends BaseRefund
{
    /**
     * @param PaymentRefund $paymentRefund
     * @param PaymentOrderUnion $paymentOrderUnion
     * @return bool|mixed
     * @throws PaymentException
     */
    public function refund($paymentRefund, $paymentOrderUnion)
    {
        $t = \Yii::$app->db->beginTransaction();
        try {
            $user = User::find()->where(['id' => $paymentRefund->user_id, 'mall_id' => $paymentRefund->mall_id])
                ->with('userInfo')->one();
            $customDesc = \Yii::$app->serializer->encode($paymentRefund->attributes);
            $info = explode(':', $paymentRefund->title);
            $no = $info[1] ?? '';
            \Yii::$app->currency->setUser($user)->balance->refund(floatval($paymentRefund->amount), '订单退款', $customDesc, $no);
            $paymentRefund->is_pay = 1;
            $paymentRefund->pay_type = 2;
            if (!$paymentRefund->save()) {
                throw new Exception($this->getErrorMsg($paymentRefund));
            }
            $t->commit();
            return true;
        } catch (Exception $e) {
            $t->rollBack();
            throw new PaymentException($e->getMessage());
        }
    }
}
