<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%payment_refund}}".
 *
 * @property int $id
 * @property int $mall_id
 * @property int $user_id
 * @property string $order_no 退款单号
 * @property string $amount 退款金额
 * @property int $is_pay 支付状态 0--未支付|1--已支付
 * @property int $pay_type 支付方式：1=微信支付，2=余额支付 3=积分支付 4=stripe支付
 * @property string $title
 * @property string $created_at
 * @property string $updated_at
 * @property string $out_trade_no 支付单号
 */
class PaymentRefund extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%payment_refund}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mall_id', 'user_id'], 'required'],
            [['mall_id', 'user_id', 'is_pay', 'pay_type'], 'integer'],
            [['amount'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['order_no', 'out_trade_no'], 'string', 'max' => 255],
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
            'order_no' => '退款单号',
            'amount' => '退款金额',
            'is_pay' => '支付状态 0--未支付|1--已支付',
            'pay_type' => '支付方式：1=微信支付，2=余额支付',
            'title' => 'Title',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'out_trade_no' => '支付单号',
        ];
    }
}
