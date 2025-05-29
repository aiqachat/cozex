<?php

namespace app\forms\api\index;

use app\bootstrap\response\ApiCode;
use app\forms\common\coze\api\BotsInfo;
use app\forms\common\coze\api\ListVoice;
use app\forms\common\coze\ApiForm;
use app\forms\mall\bot\IndexForm;
use app\models\BotConf;
use app\models\CozeAccount;
use app\models\Model;
use yii\helpers\Json;

class CozeForm extends Model
{
    public $id;

    public function rules()
    {
        return [
            [['id'], 'integer'],
        ];
    }

    public function getInfo()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        try {
            if($this->id){
                $conf = BotConf::findOne(['id' => $this->id, 'is_delete' => 0, 'mall_id' => \Yii::$app->mall->id]);
            }
            if(empty($conf)) {
                $form = new IndexForm();
                $form->attributes = $form->getSetting();
                $conf = BotConf::findOne(['bot_id' => $form->bot_id, 'is_delete' => 0, 'mall_id' => \Yii::$app->mall->id]);
                if(!$conf){
                    $conf = new BotConf();
                    $conf->attributes = $form->getSetting();
                }
            }
            $account = CozeAccount::findOne(['id' => $conf->account_id, 'is_delete' => 0]);
            if (!$conf->bot_id || !$account) {
                throw new \Exception('请联系管理员设置智能体');
            }
//            if(!$conf->id && isset($form)){
//                $conf->attributes = $form->getDefault($account);
//            }
            $req = new BotsInfo(['bot_id' => $conf->bot_id]);
            $botsInfo = ApiForm::common(['object' => $req, "account" => $account])->request();
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'data' => [
                    'bot_id' => $conf->bot_id,
                    'token' => $account->coze_secret,
                    'audio_conf' => $conf->audio_conf ? Json::decode($conf->audio_conf) : [],
                    'type' => $account->type,
                    'title' => $conf->title ?: $botsInfo['data']['name'],
                    'icon_url' => $botsInfo['data']['icon_url'],
                ],
            ];
        }catch (\Exception $e){
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage(),
                'err' => $e->getTraceAsString(),
            ];
        }
    }
}