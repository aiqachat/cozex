<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\admin;


use app\bootstrap\response\ApiCode;
use app\models\Model;
use app\models\Option;
use yii\helpers\ArrayHelper;


class MessageRemindSettingForm extends Model
{
    public function search()
    {
        $setting = $this->getSetting();
        
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '请求成功',
            'data' => [
                'setting' => $setting
            ]
        ];
    }

    public function getSetting()
    {
        $setting = \app\forms\common\CommonOption::get('message_remind_setting', 0, Option::GROUP_ADMIN);
        $setting = $setting ? ArrayHelper::toArray($setting) : [];

        $diffSetting = array_diff_key($this->getDefault(), $setting);
        $setting = array_merge($setting, $diffSetting);

        $setting = array_map(function ($item) {
            return is_numeric($item) ? (int)$item : $item;
        }, $setting);

        return $setting;
    }

    private function getDefault()
    {
        return [
            'status' => 1,
            'day' => 30,
            'message_text' => '请注意，商城即将到期！'
        ];
    }

    public function reset()
    {
        $setting = \app\forms\common\CommonOption::set('message_remind_setting', $this->getDefault(), 0, Option::GROUP_ADMIN);

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '重置成功',
        ];
    }
}
