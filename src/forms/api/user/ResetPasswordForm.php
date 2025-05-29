<?php

namespace app\forms\api\user;

use app\bootstrap\response\ApiCode;
use app\forms\common\CommonUser;
use app\models\Model;
use app\models\ModelActiveRecord;
use app\validators\PhoneNumberValidator;
use app\validators\ValidateCodeValidator;

class ResetPasswordForm extends Model
{
    public $mobile;
    public $email;
    public $type;
    public $validate_code_id;
    public $code;
    public $password;

    public function rules()
    {
        return [
            [['mobile', 'type', 'email'], 'string'],
            [['validate_code_id', 'code', 'password'], 'required'],
            [['email'], 'email'],
            [['code'], 'validateCode'],
            [['mobile'], PhoneNumberValidator::className()],
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
            'email' => '邮箱',
            'code' => '验证码',
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        if($this->type == 'email'){
            $platform = CommonUser::userAccount($this->type, $this->email);
        }else{
            $platform = CommonUser::userAccount($this->type, $this->mobile);
        }

        $user = $platform->user;
        if (!$user) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '账户不存在',
            ];
        }
        ModelActiveRecord::$log = false; // 不记录日志了
        $user->password = \Yii::$app->security->generatePasswordHash($this->password);
        if (!$user->save()) {
            return $this->getErrorResponse($user);
        }
        $platform->password = $user->password;
        if (!$platform->save()) {
            return $this->getErrorResponse($platform);
        }

        // 清除登录错误记录
        $attemptsKey = 'login_attempts_' . \Yii::$app->mall->id . '_' . $this->type . '_' . $platform->platform_account;
        $lockKey = 'login_lock_' . \Yii::$app->mall->id . '_' . $this->type . '_' . $platform->platform_account;
        $cache = \Yii::$app->cache;
        $cache->delete($attemptsKey);
        $cache->delete($lockKey);

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '密码已修改成功',
        ];
    }
}
