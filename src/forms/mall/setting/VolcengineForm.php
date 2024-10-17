<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\setting;

use app\bootstrap\response\ApiCode;
use app\forms\common\CommonOption;
use app\models\Model;

class VolcengineForm extends Model
{
    public $access_token;
    public $app_id;

    public function rules()
    {
        return [
            [['access_token', 'app_id'], 'string'],
            [['access_token'], 'string', 'max' => 32],
        ];
    }

    public function attributeLabels()
    {
        return [
            'access_token' => 'token',
        ];
    }

    public function get()
    {
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => $this->getSetting()
        ];
    }

    public function getSetting()
    {
        $setting = CommonOption::get(CommonOption::NAME_VOLCENGINE_SETTING, CommonOption::GROUP_APP);
        $setting = $setting ? (array)$setting : [];
        return CommonOption::checkDefault($setting, $this->getDefault());
    }

    private function getDefault()
    {
        return array_fill_keys(array_keys($this->attributes), '');
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        CommonOption::set(CommonOption::NAME_VOLCENGINE_SETTING, $this->attributes, CommonOption::GROUP_APP);
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功'
        ];
    }
}
