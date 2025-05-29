<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%balance_log}}".
 *
 * @property int $id
 * @property int $user_id
 * @property int $type 类型：0=未知,1=收入，2=支出
 * @property string $money 变动金额
 * @property string $desc 变动说明
 * @property string $custom_desc 自定义详细说明
 * @property string $order_no 订单号
 * @property string $created_at
 * @property string $mall_id
 */
class BalanceLog extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%balance_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'mall_id', 'type', 'money', 'desc', 'custom_desc', 'created_at'], 'required'],
            [['user_id', 'mall_id', 'type'], 'integer'],
            [['money'], 'number'],
            [['custom_desc'], 'string'],
            [['created_at'], 'safe'],
            [['desc', 'order_no'], 'string', 'max' => 255],
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
            'type' => '类型：1=收入，2=支出',
            'money' => '变动金额',
            'desc' => '变动说明',
            'custom_desc' => '自定义详细说明',
            'order_no' => '订单号',
            'created_at' => 'Created At',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
