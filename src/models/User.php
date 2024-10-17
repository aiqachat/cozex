<?php

namespace app\models;

use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $nickname
 * @property string $auth_key
 * @property string $access_token
 * @property string $mobile
 * @property string $email
 * @property string $unionid
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $is_delete
 * @property UserIdentity $identity
 * @property UserInfo $userInfo
 * @property AdminInfo $adminInfo
 */
class User extends ModelActiveRecord implements IdentityInterface
{
    const EVENT_REGISTER = 'userRegister';
    const EVENT_LOGIN = 'userLogin';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_delete'], 'integer'],
            [['username', 'password', 'auth_key', 'access_token'], 'required'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['username', 'unionid'], 'string', 'max' => 64],
            [['password', 'auth_key', 'access_token'], 'string', 'max' => 128],
            [['nickname'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'nickname' => 'Nickname',
            'auth_key' => 'Auth Key',
            'access_token' => 'Access Token',
            'unionid' => 'Unionid',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
            'is_delete' => 'Is Delete',
        ];
    }

    /**
     * 根据给到的ID查询身份。
     *
     * @param string|integer $id 被查询的ID
     * @return IdentityInterface|null 通过ID匹配到的身份对象
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * 根据 token 查询身份。
     *
     * @param string $token 被查询的 token
     * @return IdentityInterface|null 通过 token 得到的身份对象
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * @return int|string 当前用户ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string 当前用户的（cookie）认证密钥
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return boolean if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * 用户身份
     * @return \yii\db\ActiveQuery
     */
    public function getIdentity()
    {
        return $this->hasOne(UserIdentity::className(), ['user_id' => 'id']);
    }

    public function getAdminInfo()
    {
        return $this->hasOne(AdminInfo::className(), ['user_id' => 'id']);
    }

    public function getUserInfo()
    {
        return $this->hasOne(UserInfo::className(), ['user_id' => 'id']);
    }
}
