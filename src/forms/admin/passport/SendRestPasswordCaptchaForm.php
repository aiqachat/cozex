<?php
/**
 * Created by IntelliJ IDEA.
 * author: wstianxia
 */

namespace app\forms\admin\passport;


use app\bootstrap\response\ApiCode;
use app\forms\admin\SmsCaptchaForm;
use app\models\AdminInfo;
use app\models\Model;
use app\models\User;

class SendRestPasswordCaptchaForm extends Model
{
    public $mobile;

    public function rules()
    {
        return [
            [['mobile'], 'trim'],
            [['mobile'], 'required'],
        ];
    }

    public function send()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $users = User::find()->alias ("u")->innerJoin([
                'i' => AdminInfo::tableName()
            ], 'u.id = i.user_id')
            ->select('u.id,i.mobile,u.username,u.nickname')->where([
                'i.mobile' => $this->mobile,
                'u.mall_id' => 0,
                'i.is_delete' => 0,
            ])->all();
        if (!$users) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '该手机号尚未注册',
            ];
        }
        $captchaForm = new SmsCaptchaForm();
        $captchaForm->mobile = $this->mobile;
        $res = $captchaForm->send();
        if ($res['code'] == 1) {
            return $res;
        }
        $res['data']['user_list'] = $users;
        return $res;
    }
}
