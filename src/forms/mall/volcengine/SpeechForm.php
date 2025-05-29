<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\volcengine;

use app\forms\common\volcengine\data\SpeechBaseForm;

class SpeechForm extends SpeechBaseForm
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['text', 'account_id'], 'required'],
        ]);
    }

    public function attributeLabels()
    {
        return [
            'account_id' => '应用',
            'text' => '文本'
        ];
    }
}
