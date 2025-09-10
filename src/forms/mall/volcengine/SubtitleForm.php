<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\volcengine;

use app\bootstrap\response\ApiCode;
use app\forms\common\volcengine\data\SubtitleBaseForm;

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

    public function save()
    {
        $this->scenario = 'save';
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
        parent::newSave();
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功'
        ];
    }
}
