<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\setting;

use app\bootstrap\response\ApiCode;
use app\models\ModelActiveRecord;

class OauthForm extends BasicConfigForm
{
    /** @var ModelActiveRecord $class */
    protected $class;

    public function model()
    {
        $this->class = $this->getList()[$this->tab] ?? '';
        if (!class_exists($this->class)) {
            throw new \Exception('未实现，请联系管理员');
        }
        return $this->class::find()->where([
            'is_delete' => 0,
            'mall_id' => \Yii::$app->mall->id,
        ])->one();
    }

    public function get()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $data = $this->model();
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => $data ?: (new $this->class())->attributes
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $data = $this->model();
        if(!$data){
            /** @var ModelActiveRecord $data */
            $data = new $this->class();
        }
        $data->attributes = $this->formData;
        $data->mall_id = \Yii::$app->mall->id;
        $data->is_delete = 0;
        if(!$data->save()){
            return $this->getErrorResponse($data);
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功'
        ];
    }

    const TAB_GOOGLE = 'basic';

    public function getList()
    {
        return [
            self::TAB_GOOGLE => 'app\\models\\GoogleOauth',
        ];
    }
}
