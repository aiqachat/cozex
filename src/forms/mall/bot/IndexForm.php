<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\bot;

use app\bootstrap\response\ApiCode;
use app\forms\common\CommonOption;
use app\models\Model;

class IndexForm extends Model
{
    public $bot_id;
    public $data;

    public function rules()
    {
        return [
            [['bot_id'], 'integer'],
            [['data'], 'safe'],
        ];
    }

    public function getSet()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $setting = $this->getSetting();
        $setting['bot_id'] = $this->bot_id;
        $width = $setting['width'] ?: $setting['custom_width'];
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'code' => <<<EOF
    <script src="https://lf-cdn.coze.cn/obj/unpkg/flow-platform/chat-app-sdk/{$setting['version']}/libs/cn/index.js"></script>
    <script>
      new CozeWebSDK.WebChatClient({
        config: {
          bot_id: '{$setting['bot_id']}',
        },
        componentProps: {
          title: '{$setting['title']}',
          icon: '{$setting['icon']}',
          lang: '{$setting['lang']}',
          layout: '{$setting['layout']}',
          width: '{$width}',
        },
      });
    </script>
EOF,
                'data' => $setting
            ]
        ];
    }

    public function getSetting()
    {
        $setting = CommonOption::get(CommonOption::NAME_COZE_WEB_SDK);
        $setting = $setting ? (array)$setting : [];
        return CommonOption::checkDefault($setting, $this->getDefault());
    }

    private function getDefault()
    {
        return [
            'version' => '0.1.0-beta.6',
            'title' => 'Coze',
            'icon' => \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/img/mall/poster-big-shop.png',
            'lang' => 'en',
            'layout' => '',
            'width' => 460,
            'bot_id' => $this->bot_id ?: '',
            'last_bot_id' => '',
            'custom_width' => '',
        ];
    }

    public function saveUse()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $setting = $this->getSetting();
        $last_bot_id = $setting['bot_id'];
        if(!empty($setting['bot_id']) && $setting['bot_id'] == $this->bot_id){
            $this->bot_id = '';
        }
        CommonOption::set(CommonOption::NAME_COZE_WEB_SDK, array_merge($setting, [
            'bot_id' => $this->bot_id,
            'last_bot_id' => $last_bot_id
        ]));
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '设置成功'
        ];
    }

    public function saveSet()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $setting = $this->getSetting();
        $this->data = CommonOption::checkDefault((array)$this->data, $setting);
        if($this->data['bot_id'] != $setting['bot_id']){
            $this->data['last_bot_id'] = $setting['bot_id'];
        }
        CommonOption::set(CommonOption::NAME_COZE_WEB_SDK, $this->data);
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '配置成功'
        ];
    }
}
