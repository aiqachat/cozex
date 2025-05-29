<?php

namespace app\models;

/**
 * This is the model class for table "{{%recharge_orders}}".
 *
 * @property int $id
 * @property int $mall_id
 * @property string $order_no
 * @property int $user_id
 * @property string $pay_price
 * @property string $send_price
 * @property int $pay_type 支付方式 1.微信支付
 * @property int $is_pay
 * @property string $pay_time
 * @property int $is_delete
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property PaymentOrder $paymentOrder
 */
class RechargeOrders extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%recharge_orders}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mall_id', 'user_id', 'pay_price', 'send_price', 'pay_type', 'created_at', 'updated_at', 'deleted_at'], 'required'],
            [['mall_id', 'user_id', 'pay_type', 'is_pay', 'is_delete'], 'integer'],
            [['pay_price', 'send_price'], 'number'],
            [['pay_time', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['order_no'], 'string', 'max' => 32],
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
            'pay_price' => 'StripePay Price',
            'send_price' => 'Send Price',
            'pay_type' => 'StripePay Type', // 支付方式 1.线上支付
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
}
