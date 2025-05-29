<?php

namespace app\models;

use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $id
 * @property int $mall_id
 * @property string $uid
 * @property string $username
 * @property string $password
 * @property string $nickname
 * @property string $auth_key
 * @property string $access_token
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $is_delete
 * @property UserIdentity $identity
 * @property UserInfo $userInfo
 * @property AdminInfo $adminInfo
 * @property UserPlatform $platform
 * @property Mall[] $mall
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
            [['mall_id', 'is_delete'], 'integer'],
            [['username', 'password', 'auth_key', 'access_token'], 'required'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['username'], 'string', 'max' => 64],
            [['password', 'auth_key', 'access_token'], 'string', 'max' => 128],
            [['nickname', 'uid'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'uid',
            'username' => 'Username',
            'password' => 'Password',
            'nickname' => 'Nickname',
            'auth_key' => 'Auth Key',
            'access_token' => 'Access Token',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
            'is_delete' => 'Is Delete',
        ];
    }

    public function generateUid(){
        if(!$this->id || $this->uid){
            return;
        }
        $this->isLog = false;
        $number = strval($this->id);
        if(strlen($number) < 10) {
            $number = str_pad("1", 10 - strlen($number), 0, STR_PAD_RIGHT).$number;
        }
        $this->uid = $number;
        $this->save();
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
        return $this->hasOne(UserIdentity::className(), ['user_id' => 'id'])
            ->alias("userIdentity")
            ->andWhere(['userIdentity.is_delete' => 0]);
    }

    public function getAdminInfo()
    {
        return $this->hasOne(AdminInfo::className(), ['user_id' => 'id']);
    }

    public function getUserInfo()
    {
        return $this->hasOne(UserInfo::className(), ['user_id' => 'id'])
            ->alias("userInfo")
            ->andWhere(['userInfo.is_delete' => 0]);
    }

    public function getMall()
    {
        return $this->hasMany(Mall::className(), ['user_id' => 'id']);
    }

    public function getPlatform()
    {
        return $this->hasOne(UserPlatform::className(), ['user_id' => 'id']);
    }

    const LOGIN_ADMIN = 'admin'; // 管理员和超管

    public function setLoginData($loginType, $routeUrl = ''){
        if($loginType != self::LOGIN_ADMIN){
            \Yii::$app->setSessionMallId($this->mall_id);
        }
        setcookie('__login_role', $loginType);
        setcookie('__login_token', $this->access_token);
        if(!$routeUrl){
            $routeUrl = \Yii::$app->requestedRoute;
        }
        setcookie('__login_route', $routeUrl);
    }

    public function clearLogin()
    {
        #设置cookie失效
        setcookie('__login_role', '', time() - 3600);
        setcookie('__login_token', '', time() - 3600);
        setcookie('__login_route', '', time() - 3600);
        \Yii::$app->removeSessionMallId();
    }
}
