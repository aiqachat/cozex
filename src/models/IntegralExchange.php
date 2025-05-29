<?php

namespace app\models;

/**
 * This is the model class for table "{{%integral_exchange}}".
 *
 * @property int $id
 * @property int $mall_id
 * @property string $name
 * @property string $pay_price
 * @property int $send_integral
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
            [['mall_id', 'is_delete', 'send_integral'], 'integer'],
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
            'is_delete' => 'Is Delete',
        ];
    }
}
