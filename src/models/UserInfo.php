<?php

namespace app\models;

/**
 * This is the model class for table "{{%user_info}}".
 *
 * @property string $id
 * @property int $user_id
 * @property string $avatar 头像
 * @property string $integral 积分
 * @property string $total_integral 最高积分
 * @property string $balance 余额
 * @property string $total_balance 总余额
 * @property int $is_blacklist 是否黑名单
 * @property string $mobile 手机号，不可随便改
 * @property string $email 邮箱，不可随便改
 * @property string $remark 备注
 * @property int $is_delete
 */
class UserInfo extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_info}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'is_blacklist', 'is_delete'], 'integer'],
            [['balance', 'total_balance', 'integral', 'total_integral'], 'number'],
            [['avatar', 'remark'], 'string', 'max' => 255],
            [['mobile', 'email'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'integral' => '积分',
            'total_integral' => '最高积分',
            'avatar' => '头像',
            'balance' => '余额',
            'total_balance' => '总余额',
            'is_blacklist' => '是否黑名单',
            'mobile' => '联系方式',
            'remark' => '备注',
            'is_delete' => 'Is Delete',
        ];
    }

    public function getIdentity()
    {
        return $this->hasOne(UserIdentity::className(), ['user_id' => 'user_id']);
    }
}