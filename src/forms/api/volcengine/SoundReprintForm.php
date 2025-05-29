<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\api\volcengine;

use app\bootstrap\response\ApiCode;
use app\models\UserSpeaker;
use app\models\VolcengineAccount;

class SoundReprintForm extends \app\forms\mall\volcengine\SoundReprintForm
{
    public $name;

    public function rules()
    {
        return array_merge (parent::rules (), [
            [['name'], 'string'],
        ]);
    }

    public function save()
    {
        if(!$this->account_id) {
            $account = VolcengineAccount::findOne (['mall_id' => \Yii::$app->mall->id, 'is_default' => 1, 'is_delete' => 0]);
            $this->account_id = $account->id ?? 0;
        }
        $res = parent::save();
        if($res['code'] === ApiCode::CODE_SUCCESS){
            $speaker = UserSpeaker::findOne([
                'speaker_id' => $this->speaker_id,
                'mall_id' => \Yii::$app->mall->id,
                'user_id' => \Yii::$app->user->id,
                'is_delete' => 0,
            ]);
            if($speaker){
                $speaker->name = $this->name;
                $speaker->save ();
            }
        }
        return $res;
    }
}
