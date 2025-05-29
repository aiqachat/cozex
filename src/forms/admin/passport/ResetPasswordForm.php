<?php
/**
 * Created by IntelliJ IDEA.
 * author: wstianxia
 * Date: 2019/4/1
 * Time: 17:26
 */

namespace app\forms\admin\passport;


use app\bootstrap\response\ApiCode;
use app\models\AdminInfo;
use app\models\Model;
use app\models\User;
use app\validators\ValidateCodeValidator;

class ResetPasswordForm extends Model
{
    public $mobile;
    public $user_id;
    public $validate_code_id;
    public $captcha;
    public $pass;

    public function rules()
    {
        return [
            [['mobile',], 'trim'],
            [['mobile', 'user_id', 'validate_code_id', 'captcha', 'pass'], 'required'],
            [['captcha'],
                ValidateCodeValidator::class,
                'validateCodeIdAttribute' => 'validate_code_id',
                'mobileAttribute' => 'mobile',
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'mobile' => '手机号',
            'user_id' => '账户',
            'captcha' => '短信验证码',
            'pass' => '密码',
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $admin = AdminInfo::findOne([
            'user_id' => $this->user_id,
            'mobile' => $this->mobile,
            'is_delete' => 0,
        ]);
        if (!$admin || !$admin->user) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '账户不存在',
            ];
        }
        $admin->user->password = \Yii::$app->security->generatePasswordHash($this->pass);
        if (!$admin->user->save()) {
            return $this->getErrorResponse($admin->user);
        }

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '密码已修改成功',
        ];
    }
}
