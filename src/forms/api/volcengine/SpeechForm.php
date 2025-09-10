<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * Created by IntelliJ IDEA
 */

namespace app\forms\api\volcengine;

use app\bootstrap\response\ApiCode;
use app\forms\common\volcengine\api\MegaTtsUpload;
use app\forms\common\volcengine\data\BaseForm;
use app\forms\common\volcengine\data\SpeechBaseForm;
use app\forms\common\volcengine\data\VoiceForm;
use app\forms\mall\setting\ConfigForm;
use app\models\AvData;
use app\models\VolcengineAccount;

class SpeechForm extends SpeechBaseForm
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['text', 'account_id'], 'required', 'on' => ['save']],
        ]);
    }

    public function attributeLabels()
    {
        return [
            'account_id' => '应用',
            'text' => '文本'
        ];
    }

    public function getAccount()
    {
        $this->user_id = \Yii::$app->user->id;
        if (!$this->account_id) {
            $where = ['mall_id' => \Yii::$app->mall->id, 'is_default' => 1, 'is_delete' => 0, 'type' => 1];
            if($this->is_home){
                $where['type'] = $this->is_home;
            }
            $account = VolcengineAccount::findOne($where);
            $this->account_id = $account->id ?? 0;
        }
    }

    public function save()
    {
        $this->getAccount();
        return parent::save();
    }

    public function handleModel(AvData $model)
    {
        $this->data = $model->cost();
        if (!empty($this->data['payment_type'])) {
            $currency = \Yii::$app->currency->setUser(\Yii::$app->user->identity);
            switch ($this->data['payment_type']) {
                case \Yii::$app->payment::PAY_TYPE_INTEGRAL:
                    $amount = $currency->integral->select();
                    $text = '积分';
                    break;
                case \Yii::$app->payment::PAY_TYPE_BALANCE:
                    $amount = $currency->balance->select();
                    $text = '余额';
                    break;
                default:
                    throw new \Exception('请选择正确的支付方式');
            }
            if ($amount < $this->data['cost']) {
                throw new \Exception($text . '不足');
            }
        }
    }

    public function config()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $data = (new VoiceForm(['is_home' => $this->is_home]))->voiceType($this->type, false);
        $voice_data = (new ConfigForm(['tab' => ConfigForm::TAB_CONTENT]))->config();
        $voice_text = $voice_data['voice_text_' . \Yii::$app->language] ?? $voice_data['voice_text'];
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功',
            'data' => [
                'data' => $data,
                'title' => $this->textName($this->type),
                'desc' => $this->text($this->type),
                'type' => intval($this->type),
                'voice_text' => $voice_text,
                'language' => (new BaseForm())->ttsMega == $this->type ? MegaTtsUpload::languageList() : [],
            ]
        ];
    }
}
