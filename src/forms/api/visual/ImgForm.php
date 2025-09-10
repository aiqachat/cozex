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
use app\models\VolcengineKeys;

class ImgForm extends VisualImgForm
{
    public $prompt;
    public $width;
    public $height;
    public $use_pre_llm;
    public $logo_info;
    public $id;
    public $sort;
    public $text;
    public $seed;
    public $is_saved;

    public $type = 1;

    public function rules()
    {
        return [
            [['prompt', 'sort', 'text'], 'string'],
            [['logo_info'], 'safe'],
            [['width', 'height', 'use_pre_llm', 'id', 'seed', 'is_saved'], 'integer'],
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
            ->setData($this->attributes);
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

        $t = \Yii::$app->db->beginTransaction();
        try {
            $config = (new SettingForm())->config();
            $key = VolcengineKeys::findOne(['mall_id' => \Yii::$app->mall->id, 'id' => $config['key_id'], 'is_delete' => 0]);
            if(!$key){
                throw new \Exception('请先设置即梦账号');
            }
            $res = $this->setKey($key)
                ->setUserId(\Yii::$app->user->id)
                ->saveData($this->attributes)
                ->generateImg();
            $t->commit();
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '生成成功',
                'data' => $res
            ];
        } catch (\Exception $e) {
            $t->rollBack();
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
        return $this->success(['msg' => '删除成功']);
    }

    public function public()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        parent::isPublic($this->id);
        return $this->success(['msg' => '成功']);
    }
}
