<?php

namespace app\models;

/**
 * This is the model class for table "{{%stripe_product}}".
 *
 * @property int $id
 * @property int $mall_id
 * @property string $prod_id
 * @property int $type 1：充值
 * @property int $is_delete
 * @property string $created_at
 * @property string $updated_at
 */
class StripeProduct extends ModelActiveRecord
{
    public $isLog = false; // 不记录日志了

    const TYPE_RECHARGE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%stripe_product}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mall_id', 'created_at', 'updated_at'], 'required'],
            [['mall_id', 'type', 'is_delete'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['prod_id'], 'string'],
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
}
