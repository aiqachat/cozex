<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\api\visual;

use app\bootstrap\response\ApiCode;
use app\forms\common\volcengine\data\VisualImgForm;
use app\forms\mall\visual\SettingForm;

class ArkImgForm extends VisualImgForm
{
    public $prompt;
    public $guidance_scale;
    public $watermark;
    public $id;
    public $size;
    public $seed;
    public $sort;
    public $text;
    public $is_home;
    public $is_saved;

    public $type = 2;

    public function rules()
    {
        return [
            [['prompt', 'size', 'sort', 'text'], 'string'],
            [['guidance_scale'], 'number'],
            [['watermark', 'id', 'seed', 'is_home', 'is_saved'], 'integer'],
            [['is_home'], 'default', 'value' => 1],
        ];
    }

    public function attributeLabels()
    {
        return [
            'prompt' => '提示词',
        ];
    }

    public function getList()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        };

        $res = $this->setUserId(\Yii::$app->user->id)
            ->setData($this->attributes, $this->type);
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '生成成功',
            'data' => $res
        ];
    }

    public function generate()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        };

        try {
            $data = $this->attributes;
            $init = ['tab' => $this->is_home == 2 ? SettingForm::TAB_ARK_GLOBAL : SettingForm::TAB_ARK];
            $config = (new SettingForm($init))->config();
            $res = $this->setAppKey($config['api_key'])
                ->setUserId(\Yii::$app->user->id)
                ->saveData($data)
                ->generateImg();
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '生成成功',
                'data' => $res
            ];
        } catch (\Exception $e) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage()
            ];
        }
    }

    public function download()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        return parent::down($this->id);
    }

    public function delete()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        parent::del($this->id);
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '删除成功',
        ];
    }
}
