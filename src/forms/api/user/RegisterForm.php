<?php
/**
 * Created by PhpStorm
 * User: wstianxia
 */

namespace app\forms\api\user;

use app\bootstrap\response\ApiCode;
use app\events\UserEvent;
use app\models\Model;
use app\models\User;
use app\models\UserIdentity;
use app\models\UserInfo;
use app\models\UserPlatform;
use app\validators\PhoneNumberValidator;
use app\validators\ValidateCodeValidator;

class RegisterForm extends Model
{
    public $mobile;
    public $email;
    public $code;
    public $validate_code_id;
    public $password;
    public $type;

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['register'] = ['code', 'validate_code_id', 'mobile', 'password', 'email', 'type'];
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['code', 'type', 'password'], 'required', 'on' => ['register']],
            [['validate_code_id'], 'required', 'on' => ['register'], 'message' => '请先发送验证码'],
            [['code'], 'validateCode', 'on' => ['register']],
            [['email'], 'email'],
            [['mobile'], PhoneNumberValidator::className()],
//            [['pic_captcha'], 'captcha', 'captchaAction' => 'site/pic-captcha', 'on' => ['send_code']],
        ];
    }

    public function validateCode()
    {
        $valid = new ValidateCodeValidator();
        $valid->validateCodeIdAttribute = "validate_code_id";
        if($this->type == 'email'){
            $valid->mobileAttribute = 'email';
        }else{
            $valid->mobileAttribute = 'mobile';
        }
        $valid->validateAttribute($this, 'code');
    }

    public function attributeLabels()
    {
        return [
            'mobile' => '手机号',
            'code' => '验证码',
        ];
    }

    public function register()
    {
        $t = \Yii::$app->db->beginTransaction();
        try {
            if (!$this->validate()) {
                throw new \Exception($this->getErrorMsg());
            }
            $userPlatform = new UserPlatform();

            $query = UserPlatform::find()->where([
                'and',
                ['!=', 'user_id', 0],
                ['mall_id' => \Yii::$app->mall->id,]
            ]);
            if($this->type == UserPlatform::PLATFORM_EMAIL){
                $query->andWhere ([
                    'platform_account' => $this->email,
                    'platform_id' => UserPlatform::PLATFORM_EMAIL
                ]);
                $userPlatform->platform_account = $this->email;
                $userPlatform->platform_id = UserPlatform::PLATFORM_EMAIL;
            }else{
                $query->andWhere ([
                    'platform_account' => $this->mobile,
                    'platform_id' => UserPlatform::PLATFORM_MOBILE
                ]);
                $userPlatform->platform_account = $this->mobile;
                $userPlatform->platform_id = UserPlatform::PLATFORM_MOBILE;
            }
            if ($query->exists()) {
                throw new \Exception('账号已经注册，请直接登录');
            }

            $user = new User();
            $user->mall_id = \Yii::$app->mall->id;
            $user->access_token = \Yii::$app->security->generateRandomString();
            $user->auth_key = \Yii::$app->security->generateRandomString();
            $user->username = $userPlatform->platform_account;
            $user->nickname = $user->username;
            $user->password = \Yii::$app->getSecurity()->generatePasswordHash($this->password);
            if (!$user->save()) {
                throw new \Exception($this->getErrorMsg($user));
            }
            $user->generateUid();

            $uInfo = new UserInfo();
            $uInfo->email = $this->email ?: '';
            $uInfo->mobile = $this->mobile ?: '';
            $uInfo->user_id = $user->id;
            $uInfo->is_delete = 0;
            if (!$uInfo->save()) {
                throw new \Exception($this->getErrorMsg($uInfo));
            }

            $userIdentity = new UserIdentity();
            $userIdentity->user_id = $user->id;
            if (!$userIdentity->save()) {
                throw new \Exception($this->getErrorMsg($userIdentity));
            }

            $userPlatform->mall_id = \Yii::$app->mall->id;
            $userPlatform->user_id = $user->id;
            $userPlatform->password = $user->password;
            if (!$userPlatform->save()) {
                throw new \Exception($this->getErrorMsg($userPlatform));
            }

            $t->commit();
            $event = new UserEvent();
            $event->sender = $this;
            $event->user = $user;
            \Yii::$app->trigger(User::EVENT_REGISTER, $event);
            return $this->success(['msg' => '注册成功']);
        } catch (\Exception $exception) {
            $t->rollBack();
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $exception->getMessage(),
            ];
        }
    }
}
