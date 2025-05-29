<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\bot;

use app\bootstrap\response\ApiCode;
use app\forms\common\CommonOption;
use app\forms\common\coze\api\BotsInfo;
use app\forms\common\coze\api\BotsList;
use app\forms\common\coze\api\ListVoice;
use app\forms\common\coze\ApiForm;
use app\models\BotConf;
use app\models\CozeAccount;
use app\models\Model;
use yii\helpers\Json;

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

    public function getSet($type)
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $model = CozeAccount::findOne(['id' => $this->account_id, 'is_delete' => 0]);
        $setting = BotConf::findOne(['bot_id' => $this->bot_id, 'is_delete' => 0, 'mall_id' => \Yii::$app->mall->id]);
        if ($setting) {
            $setting->footer_link = $setting->footer_link ? Json::decode($setting->footer_link) : [];
            $setting->width = $setting->is_width == 2 ? $setting->width : $this->default_width;
            $setting->audio_conf = $setting->audio_conf ? Json::decode($setting->audio_conf) : [];
        } else {
            $setting = new BotConf();
            $setting->attributes = $this->getDefault($model);
        }
        $setting->audio_conf = CommonOption::checkDefault($setting->audio_conf, $this->getDefault()['audio_conf']);
        $voiceList = [];
        if($type == 'voice') {
            $req = new ListVoice();
            $req->page_num = 1;
            do {
                $list = ApiForm::common (['object' => $req, "account" => $model])->request ();
                $voiceList = array_merge ($voiceList, $list['data']['voice_list']);
                $req->page_num++;
            } while (!empty($list['data']['has_more']));
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'data' => $setting->toArray(),
                'code' => $type == 'chat' ? $this->html($setting, $model) : '',
                'voice_list' => $voiceList,
            ]
        ];
    }

    public function html(BotConf $setting, CozeAccount $model, $return = false)
    {
        $userInfo = [
            'id' => \Yii::$app->user->id ?? 0,
        ];
        if (!$setting->id) {
            $ui = [];
            $setting = $this->getDefault();
        } else {
            $userInfo = array_merge($userInfo, [
                'url' => $setting->user_avatar,
                'nickname' => $setting->nickname
            ]);
            $footerLink = [];
            $footerName = [];
            if($setting->footer_link){
                if(!is_array($setting->footer_link)){
                    $setting->footer_link = Json::decode($setting->footer_link) ?? [];
                }
                foreach ((array)$setting->footer_link as $k => $item){
                    $footerLink['name' . $k] = $item;
                    $footerName[] = '{{name' . $k . '}}';
                }
            }
            $ui = [
                'base' => [
                    'icon' => $setting->icon,
                    'lang' => $setting->lang,
                    'layout' => $setting->layout,
                ],
                'footer' => [
                    'isShow' => (bool)$setting->show_footer,
                    'expressionText' => "{$setting->footer_text} " . implode("&", $footerName),
                    'linkvars' => $footerLink
                ],
                'chatBot' => [
                    'title' => $setting->title,
                    'uploadable' => (bool)$setting->is_upload,
                    'width' => $setting->width
                ]
            ];
        }
        $config = Json::encode([
            'config' => [
                'bot_id' => $this->bot_id,
            ],
            'auth' => [
                'type' => 'token',
                'token' => $model->coze_secret,
                'onRefreshToken' => ''
            ],
            'userInfo' => $userInfo,
            'ui' => $ui,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        if($return){
            return $config;
        }else {
            $config = str_replace('"onRefreshToken": ""', '"onRefreshToken": async () => "' . $model->refresh_token . '"', $config);
            return <<<EOF
<script src="https://lf-cdn.coze.cn/obj/unpkg/flow-platform/chat-app-sdk/{$setting['version']}/libs/cn/index.js"></script>
<script>
    new CozeWebSDK.WebChatClient({$config});
</script>
EOF;
        }
    }

    public function saveSet($type)
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $this->bot_id = $this->data['bot_id'];
        $setting = BotConf::findOne(['bot_id' => $this->bot_id, 'is_delete' => 0, 'mall_id' => \Yii::$app->mall->id]);
        if (!$setting) {
            $setting = new BotConf();
        }
        $setting->attributes = $this->data;
        $setting->account_id = $this->account_id;
        $setting->mall_id = \Yii::$app->mall->id;
        if(!isset($this->data['footer_link'])){
            $setting->footer_link = '';
        }
        if($setting->audio_conf && is_array($setting->audio_conf)){
            $setting->audio_conf = Json::encode($setting->audio_conf);
        }
        if(is_array($setting->footer_link)) {
            $setting->footer_link = Json::encode($setting->footer_link);
        }
        if (!$setting->save()) {
            return $this->getErrorResponse($setting);
        }
        if($type != 'voice') {
            $this->saveUse (0);
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '配置成功'
        ];
    }

    public function getDefault(CozeAccount $model = null)
    {
        if ($model) {
            try {
                $req = new BotsInfo(['bot_id' => $this->bot_id]);
                $list = ApiForm::common(['object' => $req, "account" => $model])->request();
                $title = $list['data']['name'];
            } catch (\Exception $e) {}
        }
        return [
            'version' => '1.2.0-beta.8',
            'title' => $title ?? '',
            'icon' => '',
            'lang' => 'en',
            'layout' => '',
            'width' => '',
            'bot_id' => $this->bot_id ?: '',
            'account_id' => $model && $model->id ? $model->id : $this->account_id,
            'is_width' => 1,
            'show_footer' => 1,
            'is_upload' => 1,
            'audio_conf' => [
                'voice_id' => '',
                'voice_name' => ''
            ], // 语音聊天配置
        ];
    }

    public function getSetting()
    {
        $setting = CommonOption::get(CommonOption::NAME_COZE_WEB_SDK, \Yii::$app->mall->id);
        $setting = $setting ? (array) $setting : [];
        return CommonOption::checkDefault($setting, ['bot_id' => ''], false);
    }

    public function saveUse($s = 1)
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $setting = $this->getSetting();
        if ($s && $setting['bot_id'] == $this->bot_id) {
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
        $this->attributes = $setting;
        $model = CozeAccount::findOne(['id' => $this->account_id, 'is_delete' => 0]);
        if ($this->bot_id && $model) {
            $conf = BotConf::findOne(['bot_id' => $this->bot_id, 'is_delete' => 0, 'mall_id' => \Yii::$app->mall->id]);
            if ($conf) {
                $conf->footer_link = $conf->footer_link ? Json::decode($conf->footer_link) : [];
                $conf->width = $conf->is_width == 2 ? $conf->width : $this->default_width;
            } else {
                $conf = new BotConf();
                $conf->attributes = $this->getDefault($model);
                if ($conf['is_width'] = 1) {
                    $conf->width = $this->default_width;
                }
                $conf->id = 999999;
            }
            return $this->html($conf, $model);
        } else {
            return '';
        }
    }
}
