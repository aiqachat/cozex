<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%payment_order_union}}".
 *
 * @property int $id
 * @property int $mall_id
 * @property int $user_id
 * @property string $order_no
 * @property string $amount
 * @property int $is_pay 支付状态：0=未支付，1=已支付
 * @property int $pay_type 支付方式：1=微信支付 2=余额支付 3=积分支付 4=stripe支付
 * @property string $title
 * @property string $created_at
 * @property string $updated_at
 * @property PaymentOrder[] $paymentOrder
 * @property PaymentOrder $payOrder
 * @property User $user
 */
class PaymentOrderUnion extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%payment_order_union}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mall_id', 'order_no', 'amount', 'title'], 'required'],
            [['mall_id', 'user_id', 'is_pay', 'pay_type'], 'integer'],
            [['amount'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['order_no'], 'string', 'max' => 32],
            [['title'], 'string', 'max' => 128],
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
            'user_id' => 'User ID',
            'order_no' => 'Order No',
            'amount' => 'Amount',
            'is_pay' => '支付状态：0=未支付，1=已支付',
            'pay_type' => '支付方式：1=微信支付',
            'title' => 'Title',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getPaymentOrder()
    {
        return $this->hasMany(PaymentOrder::className(), ['payment_order_union_id' => 'id']);
    }

    public function getPayOrder()
    {
        return $this->hasOne(PaymentOrder::className(), ['payment_order_union_id' => 'id', 'is_pay' => 1]);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
