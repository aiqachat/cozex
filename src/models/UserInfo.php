<?php

namespace app\models;

/**
 * This is the model class for table "{{%user_info}}".
 *
 * @property string $id
 * @property int $user_id
 * @property string $avatar 头像
 * @property string $platform_user_id 用户所属平台的用户id
 * @property string $balance 余额
 * @property string $total_balance 总余额
 * @property int $is_blacklist 是否黑名单
 * @property string $contact_way 联系方式
 * @property string $remark 备注
 * @property int $is_delete
 * @property string $remark_name 备注名
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
            [['balance', 'total_balance'], 'number'],
            [['avatar', 'platform_user_id', 'contact_way', 'remark', 'remark_name'], 'string', 'max' => 255],
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
            'avatar' => '头像',
            'platform_user_id' => '用户所属平台的用户id',
            'balance' => '余额',
            'total_balance' => '总余额',
            'is_blacklist' => '是否黑名单',
            'contact_way' => '联系方式',
            'remark' => '备注',
            'is_delete' => 'Is Delete',
            'remark_name' => '备注名',
        ];
    }

    public function getIdentity()
    {
        return $this->hasOne(UserIdentity::className(), ['user_id' => 'user_id']);
    }
}