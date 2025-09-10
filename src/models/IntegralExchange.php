<?php

namespace app\models;

/**
 * This is the model class for table "{{%integral_exchange}}".
 *
 * @property int $id
 * @property int $mall_id
 * @property string $name
 * @property string $pay_price 支付金额
 * @property int $send_integral 兑换的积分
 * @property int $give_integral 赠送的积分
 * @property int $buy_num 可购买次数
 * @property int $period 有效期
 * @property int $serial_num 序号
 * @property string $created_at
 * @property string $updated_at
 * @property string $language_data
 * @property int $is_delete 是否删除
 */
class IntegralExchange extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%integral_exchange}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mall_id', 'is_delete', 'send_integral', 'give_integral', 'buy_num', 'period', 'serial_num'], 'integer'],
            [['pay_price'], 'number'],
            [['name', 'updated_at', 'created_at', 'language_data'], 'string'],
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
            'name' => '兑换方案',
            'pay_price' => '支付金额',
            'send_integral' => '兑换积分',
            'give_integral' => '赠送积分',
            'buy_num' => '可购买次数',
            'period' => '有效期',
            'serial_num' => '序号',
            'language_data' => '多语言数据',
            'is_delete' => 'Is Delete',
        ];
    }

}
