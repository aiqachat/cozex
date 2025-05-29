<?php
/**
 * Created by IntelliJ IDEA.
 * author: wstianxia
 * Date: 2019/3/28
 * Time: 18:08
 */

namespace app\forms\admin;

use app\bootstrap\response\ApiCode;
use app\models\CoreValidateCode;
use app\models\Model;
use app\models\User;
use Overtrue\EasySms\Message;

class SmsCaptchaForm extends Model
{
    public $mobile;
    public $captcha;

    public function rules()
    {
        return [
            [['mobile', 'captcha'], 'trim'],
            [['mobile'], 'required'],
        ];
    }

    public function send()
    {
        try {
            if (!$this->validate()) {
                throw new \Exception($this->getErrorMsg());
            }
            $code = '' . rand(100000, 999999);
            $indSetting = (new ConfigForm())->config();
            if (empty($indSetting['ind_sms'])
                || empty($indSetting['ind_sms']['aliyun'])
                || empty($indSetting['ind_sms']['aliyun']['tpl_id'])) {
                throw new \Exception('短信信息尚未配置');
            }
            $coreValidateCode = new CoreValidateCode();
            $coreValidateCode->target = $this->mobile;
            $coreValidateCode->code = $code;
            if (!$coreValidateCode->save()) {
                throw new \Exception($this->getErrorMsg($coreValidateCode));
            }
            \Yii::$app->sms->module(\Yii::$app->sms::MODULE_ADMIN)->send($this->mobile, new Message([
                'content' => null,
                'template' => $indSetting['ind_sms']['aliyun']['tpl_id'],
                'data' => [
                    'code' => $code,
                ],
            ]));
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '短信验证码已发送。',
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

    public function updateMobile()
    {
        try {
            if (!$this->validate()) {
                throw new \Exception($this->getErrorMsg());
            }

            if (!$this->captcha) {
                throw new \Exception('请输入验证码');
            }

            $coreValidateCode = CoreValidateCode::find()->where([
                'target' => $this->mobile,
                'code' => $this->captcha,
            ])->one();

            if (!$coreValidateCode) {
                throw new \Exception('验证码不正确');
            }

            if ($coreValidateCode->is_validated == 1) {
                throw new \Exception('验证码已失效');
            }

            $coreValidateCode->is_validated = 1;
            $coreValidateCode->save();

            /** @var User $user */
            $user = User::find()->where(['id' => \Yii::$app->user->id])->one();
            if (!$user) {
                throw new \Exception('用户不存在');
            }
            $user->adminInfo->mobile = $this->mobile;
            $res = $user->adminInfo->save();
            if (!$res) {
                throw new \Exception($this->getErrorMsg($user->adminInfo));
            }

            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => "更新成功"
            ];
        } catch (\Exception $e) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage()
            ];
        }
    }
}
