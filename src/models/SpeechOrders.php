<?php

namespace app\models;

/**
 * This is the model class for table "{{%speech_orders}}".
 *
 * @property int $id
 * @property int $mall_id
 * @property string $order_data
 * @property string $order_no
 * @property int $account_id
 * @property int $user_id
 * @property string $total_pay_price
 * @property string $unit_price
 * @property int $is_pay
 * @property int $is_refund
 * @property string $pay_time
 * @property int $is_delete
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property VolcengineAccount $account
 * @property PaymentOrder $paymentOrder
 */
class SpeechOrders extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%speech_orders}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mall_id', 'user_id', 'total_pay_price', 'unit_price', 'created_at', 'updated_at', 'deleted_at'], 'required'],
            [['mall_id', 'user_id', 'is_pay', 'is_delete', 'is_refund', 'account_id'], 'integer'],
            [['total_pay_price', 'unit_price'], 'number'],
            [['pay_time', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['order_no', 'order_data'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mall_id' => 'Mall ID',
            'order_no' => 'Order No',
            'user_id' => 'User ID',
            'is_pay' => 'Is StripePay',
            'pay_time' => 'StripePay Time',
            'is_delete' => 'Is Delete',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
        ];
    }

    public function getPaymentOrder()
    {
        return $this->hasOne(PaymentOrder::className(), ['order_no' => 'order_no'])->andWhere(['is_pay' => 1]);
    }

    public function getAccount()
    {
        return $this->hasOne(VolcengineAccount::className(), ['id' => 'account_id']);
    }
}
