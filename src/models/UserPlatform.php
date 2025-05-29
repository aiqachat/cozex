<?php

namespace app\models;

/**
 * This is the model class for table "{{%user_platform}}".
 *
 * @property int $id
 * @property int $mall_id
 * @property int $user_id
 * @property string $platform_id 用户所属平台标识
 * @property string $platform_account 用户所属平台的用户账号
 * @property string $password 平台使用的密码
 * @property User $user
 */
class UserPlatform extends ModelActiveRecord
{
    const PLATFORM_EMAIL = 'email';
    const PLATFORM_MOBILE = 'mobile';
    const PLATFORM_GOOGLE = 'google';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_platform}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mall_id', 'user_id'], 'required'],
            [['mall_id', 'user_id'], 'integer'],
            [['platform_account', 'platform_id', 'password'], 'string', 'max' => 255],
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
            'platform_account' => '用户所属平台的用户账号',
            'platform_id' => '用户所属平台标识',
            'password' => '平台使用的密码',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
