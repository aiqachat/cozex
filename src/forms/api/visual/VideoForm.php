<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\api\visual;

use app\bootstrap\response\ApiCode;
use app\forms\common\volcengine\data\VisualVideoForm;
use app\helpers\ArrayHelper;
use app\models\VisualVideo;
use app\models\VolcengineKeys;

class VideoForm extends VisualVideoForm
{
    public $prompt;
    public $aspect_ratio;
    public $seed;
    public $id;
    public $model;
    public $resolution;
    public $mode;
    public $image_urls;
    public $is_saved;
    public $duration;

    public function rules()
    {
        return [
            [['prompt', 'aspect_ratio', 'mode', 'model', 'resolution'], 'string'],
            [['seed', 'id', 'is_saved', 'duration'], 'integer'],
            [['image_urls'], 'safe'],
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

        $res = $this->setUserId(\Yii::$app->user->id)->setData($this->attributes);
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功',
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
            $config = $this->getSetting();
            $key = VolcengineKeys::findOne(['mall_id' => \Yii::$app->mall->id, 'id' => $config['key_id'], 'is_delete' => 0]);
            if(!$key){
                throw new \Exception('请先设置即梦账号');
            }
            $res = $this->setKey($key)
                ->setUserId(\Yii::$app->user->id)
                ->saveData($this->id);
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

    public function handleData()
    {
        $this->modelObj->attributes = $this->attributes;
        $this->modelObj->type = 1;
        $data = [
            'duration' => $this->duration,
            'model' => $this->model,
            'resolution' => $this->resolution,
        ];

        $form = new ConfigForm();
        $form->function_type = VisualVideo::DREAM_NAME;
        $arr = $form->config()['data']['model_data'];
        $arr = ArrayHelper::index($arr, "value");

        if($this->mode == 'image'){ // 图片处理
            $this->modelObj->image_urls = [$this->image_urls[0] ?? ''];
            $ratio = $this->getClosestRatio($this->modelObj->image_urls[0], array_values($arr[$this->model]['resolution_details'])[0]);
            if($ratio){
                $this->aspect_ratio = $ratio;
            }
        }

        if($this->modelObj->user_id){
            $config = $this->getSetting();
            $ratio = explode(":", $this->aspect_ratio);
            rsort($ratio);

            $label = "{$arr[$this->model]['label']}_{$this->resolution}_" . implode("_", $ratio);
            if(!isset($config['video']) || !isset($config['video'][$label])){
                throw new \Exception(sprintf(\Yii::t('common', '未设置价格'), $this->aspect_ratio));
            }
            $price = $config['video'][$label] * $this->duration;
            if (\Yii::$app->currency->setUser($this->modelObj->user)->integral->select() < $price) {
                throw new \Exception('积分不足');
            }
            $data['cost'] = $price;
        }
        $this->modelObj->data = $data;
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
