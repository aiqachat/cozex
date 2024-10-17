<?php
/**
 * Created by PhpStorm
 * User: chenzs
 * Date: 2020/9/29
 * Time: 4:15 下午
 * @copyright: ©2020 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\forms\mall\setting;

use app\bootstrap\response\ApiCode;
use app\forms\common\CommonOption;
use app\models\Model;

class ConfigForm extends Model
{
    public $name;
    public $mall_logo_pic;
    public $passport_logo;
    public $passport_bg;
    public $copyright;
    public $copyright_url;

    public function rules()
    {
        return [
            [['copyright'], 'trim'],
            [['copyright_url', 'passport_logo', 'copyright', 'passport_bg', 'name', 'mall_logo_pic'], 'string', 'max' => 255],
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        CommonOption::set(CommonOption::NAME_IND_SETTING, $this->attributes, CommonOption::GROUP_ADMIN);
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
                'data' => $this->config(),
                'default' => $this->defaultValue()
            ]
        ];
    }

    public function config()
    {
        $setting = CommonOption::get(CommonOption::NAME_IND_SETTING, CommonOption::GROUP_ADMIN);
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
            'name' => 'cozex',
            'mall_logo_pic' => $host . 'statics/img/mall/poster-big-shop.png', //商城logo
            'passport_logo' => $host . 'statics/img/admin/login-logo.png',
            'passport_bg' => $host . 'statics/img/admin/BG.png',
            'copyright' => 'Powered by AIQA',
            'copyright_url' => 'https://aiqa.chat',
        ];
    }
}
