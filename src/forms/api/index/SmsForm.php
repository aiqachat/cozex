<?php
/**
 * Created by PhpStorm
 * User: chenzs
 * @copyright: ©2020 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\forms\api\index;

use app\bootstrap\response\ApiCode;
use app\forms\common\CommonUser;
use app\models\CoreValidateCode;
use app\models\Model;
use app\validators\PhoneNumberValidator;
use Overtrue\EasySms\Message;

class SmsForm extends Model
{
    public $mobile;
    public $send_type;

    public function rules()
    {
        return [
            [['mobile', 'send_type'], 'string'],
            [['mobile'], 'trim'],
            [['mobile'], PhoneNumberValidator::className()],
        ];
    }

    public function send()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        try {
            if($this->send_type === 'forgotPassword'){ // 忘记密码
                $exists = CommonUser::userAccount('mobile', $this->mobile);
                if(!$exists || !$exists->user){
                    return [
                        'code' => ApiCode::CODE_ERROR,
                        'msg' => '手机号未注册',
                    ];
                }
            }

            $code = rand(100000, 999999);
            $coreValidateCode = new CoreValidateCode();
            $coreValidateCode->target = $this->mobile;
            $coreValidateCode->code = strval($code);

            $form = new \app\forms\mall\setting\ConfigForm();
            $form->tab = \app\forms\mall\setting\ConfigForm::TAB_SMS;
            $setting = $form->config();

            \Yii::$app->sms->module(\Yii::$app->sms::MODULE_MALL)->send($this->mobile, new Message([
                'template' => $setting['code_template_id'],
                'data' => [$code],
            ]));

            if (!$coreValidateCode->save()) {
                throw new \Exception($this->getErrorMsg($coreValidateCode));
            }
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '验证码已发送。',
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
