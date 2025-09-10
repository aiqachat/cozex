<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * Created by IntelliJ IDEA
 */

namespace app\forms\api\index;

use app\bootstrap\response\ApiCode;
use app\forms\mall\setting\ContentForm;
use app\forms\mall\setting\PriceForm;
use app\forms\mall\setting\UserConfigForm;
use app\forms\mall\visual\SettingForm;
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

    /**
     * 根据路由获取相应配置信息
     * 
     * @return array 返回配置数据和状态码
     */
    public function search()
    {
        $userConfigForm = new UserConfigForm();
        if($this->route == 'default'){
            // 默认配置获取
            $userConfigForm->tab = UserConfigForm::TAB_SETTING;
            $data = $userConfigForm->config();
        }else {
            // 获取管理端和商城端基础配置
            $adminSetting = (new \app\forms\admin\ConfigForm())->config();
            $data = (new \app\forms\mall\setting\ConfigForm())->config();
            $data = array_merge ($data, [
                'copyright' => $adminSetting['copyright'],
                'copyright_url' => $adminSetting['copyright_url'],
            ]);
            
            // 登录注册页面配置
            if (in_array($this->route, ['login', 'register', 'forgotPassword'])) {
                $userConfigForm->tab = UserConfigForm::TAB_REGISTER;
                $data = array_merge($data, $userConfigForm->config());
            }
            // 语音相关配置
            else if (strpos($this->route, "voice.") !== false) {
                $userConfigForm->tab = UserConfigForm::TAB_INTEGRAL;
                if (strpos($this->route, "Abroad") !== false) {
                    $config = ['tab' => PriceForm::TAB_ABROAD];
                } else {
                    $config = [];
                }
                $data = array_merge($data, (new PriceForm($config))->config(), $userConfigForm->config());
            }
            // 可视化相关配置
            else if (strpos($this->route, "visual") !== false) {
                // 火山方舟Ark相关配置
                if (strpos($this->route, "visualArk.") !== false) {
                    if (strpos($this->route, "Abroad") !== false) {
                        $config = ['tab' => SettingForm::TAB_ARK_GLOBAL];
                    } else {
                        $config = ['tab' => SettingForm::TAB_ARK];
                    }
                    $data = array_merge($data, (new SettingForm($config))->config());
                    unset($data['api_key']);
                }
                // 即梦ai
                elseif (strpos($this->route, "visual.") !== false) {
                    $data = array_merge($data, (new SettingForm())->config());
                }
                $data = array_merge($data, (new ContentForm())->config());
            }
            // 广场页面配置
            else if ($this->route == 'square') {
                $data = array_merge($data, (new ContentForm(['tab' => ContentForm::TAB_SQUARE]))->config());
            }
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => $data,
        ];
    }
}
