<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%integral_log}}".
 *
 * @property int $id
 * @property int $mall_id
 * @property int $user_id
 * @property int $type 类型：1=收入，2=支出
 * @property string $integral 变动积分
 * @property string $desc 变动说明
 * @property string $custom_desc 自定义详细说明|记录
 * @property string $order_no 订单号
 * @property string $created_at
 */
class IntegralLog extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%integral_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mall_id', 'user_id', 'type', 'integral', 'custom_desc', 'created_at'], 'required'],
            [['mall_id', 'user_id', 'type'], 'integer'],
            [['integral'], 'number'],
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
            'integral' => '变动积分',
            'desc' => '变动说明',
            'custom_desc' => '自定义详细说明|记录',
            'order_no' => '订单号',
            'created_at' => 'Created At',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::ClassName(), ['id' => 'user_id']);
    }
}
