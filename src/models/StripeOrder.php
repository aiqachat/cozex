<?php

namespace app\models;

/**
 * This is the model class for table "{{%stripe_order}}".
 *
 * @property int $id
 * @property int $mall_id
 * @property string $checkout_id 订单号
 * @property int $payment_order_union_id
 * @property int $amount 金额
 * @property string $currency 货币类型
 * @property string $payment_intent 支付订单号
 * @property string $payment_status 订单状态
 * @property int $is_delete
 * @property string $created_at
 * @property string $updated_at
 * @property PaymentOrderUnion $paymentOrderUnion
 */
class StripeOrder extends ModelActiveRecord
{
    public $isLog = false; // 不记录日志了

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%stripe_order}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'required'],
            [['mall_id', 'payment_order_union_id', 'is_delete', 'amount'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['checkout_id', 'currency', 'payment_intent', 'payment_status'], 'string'],
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
            'is_delete' => 'Is Delete',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getPaymentOrderUnion()
    {
        return $this->hasOne(PaymentOrderUnion::className(), ['id' => 'payment_order_union_id']);
    }
}
