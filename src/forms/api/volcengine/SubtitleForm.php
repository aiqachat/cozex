<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * Created by IntelliJ IDEA
 */

namespace app\forms\api\volcengine;

use app\bootstrap\response\ApiCode;
use app\forms\common\volcengine\data\SubtitleBaseForm;
use app\models\VolcengineAccount;

class SubtitleForm extends SubtitleBaseForm
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['file', 'account_id'], 'required', 'on' => ['save']],
        ]);
    }

    public function attributeLabels()
    {
        return [
            'account_id' => '应用',
            'file' => '文件'
        ];
    }

    public function getAccount()
    {
        $this->user_id = \Yii::$app->user->id;
        $account = VolcengineAccount::findOne(['mall_id' => \Yii::$app->mall->id, 'is_default' => 1, 'is_delete' => 0]);
        $this->account_id = $account->id ?? 0;
    }

    public function save()
    {
        $this->getAccount ();
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        parent::save();
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功'
        ];
    }

    public function newSave()
    {
        $this->getAccount ();
        parent::newSave ();
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功'
        ];
    }
}
