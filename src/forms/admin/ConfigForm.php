<?php
/**
 * Created by PhpStorm
 * User: chenzs
 * @copyright: ©2020 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\forms\admin;

use app\bootstrap\response\ApiCode;
use app\forms\common\CommonOption;
use app\models\Model;
use yii\helpers\HtmlPurifier;
use yii\validators\UrlValidator;

class ConfigForm extends Model
{
    public $name;
    public $description;
    public $keywords;
    public $logo;
    public $manage_bg;
    public $edition;
    public $copyright;
    public $copyright_url;
    public $passport_bg;
    public $passport_box_bg;
    public $open_register;
    public $open_verify;
    public $open_sms;
    public $registered_bg;
    public $is_required;
    public $register_protocol;
    public $ind_sms;
    public $permissions_num;
    public $mall_permissions;
    public $secondary_permissions;
    public $use_days;
    public $create_num;
    public $ai_code;

    public function rules()
    {
        return [
            [['description', 'name', 'description', 'keywords', 'logo', 'manage_bg', 'edition',
                'copyright', 'copyright_url', 'passport_bg', 'passport_box_bg', 'registered_bg',
                'register_protocol', 'ai_code'], 'string'],
            [['open_register', 'open_verify', 'open_sms', 'use_days', 'create_num', 'is_required',
                'permissions_num'], 'integer'],
            [['copyright_url'], UrlValidator::className()],
            [['secondary_permissions', 'mall_permissions', 'ind_sms'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'copyright_url' => '底部版权url',
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $this->copyright = trim (HtmlPurifier::process ($this->copyright));
        CommonOption::set(CommonOption::NAME_IND_SETTING, $this->attributes, 0,  CommonOption::GROUP_ADMIN);
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '保存成功'
        ];
    }

    public function get(){
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功',
            'data' => [
                'setting' => $this->config(),
                'default' => $this->defaultValue()
            ]
        ];
    }

    public function config()
    {
        $setting = CommonOption::get(CommonOption::NAME_IND_SETTING, 0, CommonOption::GROUP_ADMIN);
        $setting = $setting ? (array)$setting : [];
        return CommonOption::checkDefault($setting, $this->getDefault());
    }

    private function getDefault()
    {
        return array_merge(array_fill_keys(array_keys($this->attributes), ''), $this->defaultValue());
    }

    public function defaultValue()
    {
        $host = \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . "/";
        return [
            'name' => 'CozeX-扣子X',
            'logo' => $host . 'statics/img/admin/login-logo.png',
            'manage_bg' => $host . 'statics/img/admin/d.png',
            'passport_bg' => $host . 'statics/img/admin/BG.png',
            'registered_bg' => $host . 'statics/img/admin/B.png',
            'copyright' => 'Powered by Netbcloud',
            'copyright_url' => 'https://www.netbcloud.com',
            'mall_permissions' => [],
            'secondary_permissions' => [
                'attachment' => []
            ],
            'edition' => app_version (),
            'open_sms' => 0,
            'is_required' => 0,
            'open_register' => 0,
            'open_verify' => 1,
            'create_num' => 1,
            'use_days' => 7,
            'ind_sms' => [
                'aliyun' => [
                    'access_key_id' => '',
                    'access_key_secret' => '',
                    'sign' => '',
                    'tpl_id' => '',
                    'register_success_tpl_id' => '',
                    'register_fail_tpl_id' => '',
                    'register_apply_tpl_id' => '',
                ]
            ],
        ];
    }
}
