<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * Created by IntelliJ IDEA
 */

namespace app\forms\api\index;

use app\bootstrap\response\ApiCode;
use app\forms\mall\setting\PriceForm;
use app\forms\mall\setting\UserConfigForm;
use app\models\Model;

class ConfigForm extends Model
{
    public $route;

    public function rules()
    {
        return [
            [['route'], 'string'],
        ];
    }

    public function search()
    {
        $userConfigForm = new UserConfigForm();
        if($this->route == 'default'){
            $userConfigForm->tab = UserConfigForm::TAB_SETTING;
            $data = $userConfigForm->config();
        }else {
            $adminSetting = (new \app\forms\admin\ConfigForm())->config();
            $data = (new \app\forms\mall\setting\ConfigForm())->config();
            $data = array_merge ($data, [
                'copyright' => $adminSetting['copyright'],
                'copyright_url' => $adminSetting['copyright_url'],
            ]);
            if (in_array ($this->route, ['login', 'register', 'forgotPassword'])) { // 登录注册页面配置
                $userConfigForm->tab = UserConfigForm::TAB_REGISTER;
                $data = array_merge ($data, $userConfigForm->config ());
            }
            if ($this->route && strpos ($this->route, "voice.") !== false) {
                $userConfigForm->tab = UserConfigForm::TAB_INTEGRAL;
                $data = array_merge ($data, (new PriceForm())->config (), $userConfigForm->config ());
            }
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => $data,
        ];
    }
}
