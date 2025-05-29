<?php
/**
 * Created by PhpStorm
 * User: chenzs
 * @copyright: ©2020 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\forms\api\index;

use app\bootstrap\mail\SendMail;
use app\bootstrap\response\ApiCode;
use app\forms\common\CommonUser;
use app\forms\mall\setting\UserConfigForm;
use app\models\CoreValidateCode;
use app\models\Model;

class MailForm extends Model
{
    public $email;
    public $send_type;

    public function rules()
    {
        return [
            [['email', 'send_type'], 'string'],
            [['email'], 'trim'],
            [['email'], 'email'],
        ];
    }

    public function send()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        try {
            if($this->send_type === 'forgotPassword'){ // 忘记密码
                $exists = CommonUser::userAccount('email', $this->email);
                if(!$exists || !$exists->user){
                    return [
                        'code' => ApiCode::CODE_ERROR,
                        'msg' => '邮箱未注册',
                    ];
                }
            }

            $userConfigForm = new UserConfigForm();
            $userConfigForm->tab = UserConfigForm::TAB_SETTING;
            $config = $userConfigForm->config();
            $code = rand(100000, 999999);
            $coreValidateCode = new CoreValidateCode();
            $coreValidateCode->target = $this->email;
            $coreValidateCode->code = strval($code);
            $mailer = new SendMail();
            $mailer->mall = \Yii::$app->mall;
            $config['code'] = $code;
            $mailer->codedMsg($this->email, $config);
            if (!$coreValidateCode->save()) {
                throw new \Exception($this->getErrorMsg($coreValidateCode));
            }
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '邮箱验证码已发送。',
                'data' => [
                    'validate_code_id' => $coreValidateCode->id,
                ],
            ];
        } catch (\Exception $exception) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $exception->getMessage(),
            ];
        }
    }
}
