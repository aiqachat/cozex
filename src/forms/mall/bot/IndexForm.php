<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\bot;

use app\bootstrap\response\ApiCode;
use app\forms\common\CommonOption;
use app\forms\common\coze\api\BotsList;
use app\forms\common\coze\ApiForm;
use app\models\BotConf;
use app\models\CozeAccount;
use app\models\Model;

class IndexForm extends Model
{
    public $bot_id;
    public $data;
    public $account_id;
    public $space_id;

    private $default_width = 460;

    public function rules()
    {
        return [
            [['bot_id', 'account_id', 'space_id'], 'integer'],
            [['data'], 'safe'],
        ];
    }

    public function getSet()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $setting = BotConf::findOne(['bot_id' => $this->bot_id, 'is_delete' => 0]);
        $conf = "{}";
        if($setting) {
            $width = $setting->is_width == 2 ? $setting->width : $this->default_width;
            $conf = <<<EOF
{
          title: '{$setting['title']}',
          icon: '{$setting['icon']}',
          lang: '{$setting['lang']}',
          layout: '{$setting['layout']}',
          width: {$width},
        }
EOF;
        }else {
            $setting = $this->getDefault(1);
        }

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'data' => $setting,
                'code' => <<<EOF
    <script src="https://lf-cdn.coze.cn/obj/unpkg/flow-platform/chat-app-sdk/{$setting['version']}/libs/cn/index.js"></script>
    <script>
      new CozeWebSDK.WebChatClient({
        config: {
          bot_id: '{$this->bot_id}',
        },
        componentProps: $conf,
      });
    </script>
EOF,
            ]
        ];
    }

    public function saveSet()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $this->bot_id = $this->data['bot_id'];
        $setting = BotConf::findOne(['bot_id' => $this->bot_id, 'is_delete' => 0]);
        if(!$setting){
            $setting = new BotConf();
        }
        $setting->attributes = $this->data;
        if(!$setting->save()){
            return $this->getErrorResponse($setting);
        }
        $this->saveUse(0);
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '配置成功'
        ];
    }

    private function getDefault($get = 0)
    {
        if($get){
            try {
                $model = CozeAccount::findOne (['id' => $this->account_id, 'is_delete' => 0]);
                if ($model) {
                    $req = new BotsList([
                        'space_id' => $this->space_id,
                        'page_index' => 1,
                        'page_size' => 1000
                    ]);
                    $list = ApiForm::common (['object' => $req, "account" => $model])->request ();
                    foreach ($list['data']['space_bots'] as $item) {
                        if ($this->bot_id == $item['bot_id']) {
                            $title = $item['bot_name'];
                        }
                    }
                }
            }catch (\Exception $e){}
        }
        return [
            'version' => '0.1.0-beta.6',
            'title' => $title ?? '',
            'icon' => '',
            'lang' => 'en',
            'layout' => '',
            'width' => '',
            'bot_id' => $this->bot_id ?: '',
            'is_width' => 1,
        ];
    }

    public function getSetting()
    {
        $setting = CommonOption::get(CommonOption::NAME_COZE_WEB_SDK);
        $setting = $setting ? (array)$setting : [];
        return CommonOption::checkDefault($setting, ['bot_id' => ''], false);
    }

    public function saveUse($s = 1)
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $setting = $this->getSetting();
        if($s && $setting['bot_id'] == $this->bot_id){
            $this->bot_id = '';
        }
        CommonOption::set(CommonOption::NAME_COZE_WEB_SDK, [
            'bot_id' => $this->bot_id,
            'account_id' => $this->account_id,
            'space_id' => $this->space_id,
        ]);
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '设置成功'
        ];
    }

    public function page()
    {
        $setting = $this->getSetting();
        $conf = BotConf::findOne(['bot_id' => $setting['bot_id'], 'is_delete' => 0]);
        if($conf){
            $conf->width = $conf->is_width == 2 ? $conf->width : $this->default_width;
            $conf = $conf->toArray();
        }else {
            $get = 0;
            if ($setting['bot_id'] && !empty($setting['account_id'])) {
                $this->account_id = $setting['account_id'];
                $this->space_id = $setting['space_id'];
                $get = 1;
                $this->bot_id = $setting['bot_id'];
            }
            $conf = $this->getDefault($get);
            if($conf['is_width'] = 1){
                $conf['width'] = $this->default_width;
            }
        }
        return array_merge($conf, $setting);
    }
}
