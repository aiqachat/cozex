<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\setting;

use app\bootstrap\response\ApiCode;
use app\models\Model;
use app\models\VolcengineKeys;
use app\models\VolcengineKeysRelation;

class VolcanoKeyForm extends Model
{
    public $secret_key;
    public $access_id;
    public $name;
    public $id;
    public $type;
    public $account_id;
    public $page_size;

    public function rules()
    {
        return [
            [['secret_key', 'access_id', 'name'], 'string'],
            [['secret_key'], 'string', 'max' => 60],
            [['id', 'type', 'account_id', 'page_size'], 'integer'],
            [['page_size'], 'default', 'value' => 10],
        ];
    }

    public function attributeLabels()
    {
        return [
            'secret_key' => '密钥',
        ];
    }

    public function get()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $data = VolcengineKeys::find()->where(['is_delete' => 0, 'mall_id' => \Yii::$app->mall->id])
            ->page($pagination, $this->page_size)
            ->asArray()
            ->all();
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $data,
                'pagination' => $pagination
            ]
        ];
    }

    public function getList()
    {
        $data = VolcengineKeys::find()->where(['is_delete' => 0, 'mall_id' => \Yii::$app->mall->id])
            ->asArray()
            ->all();
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => $data
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        if($this->id){
            $model = VolcengineKeys::find()->where(['id' => $this->id, 'mall_id' => \Yii::$app->mall->id])->one();
            if(!$model){
                return [
                    'code' => ApiCode::CODE_ERROR,
                    'msg' => '数据不存在'
                ];
            }
            $where = [
                'and',
                ['or', ['secret_key' => $this->secret_key], ['access_id' => $this->access_id]],
                ['not', ['id' => $this->id]]
            ];
        }else{
            $model = new VolcengineKeys();
            $where = ['or', ['secret_key' => $this->secret_key], ['access_id' => $this->access_id]];
        }
        $exist = VolcengineKeys::find()
            ->where(['is_delete' => 0, 'mall_id' => \Yii::$app->mall->id])
            ->andWhere($where)
            ->one();
        if($exist){
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '山火密钥已添加，名称：'.$exist->name
            ];
        }
        $model->attributes = $this->attributes;
        $model->mall_id = \Yii::$app->mall->id;
        if(!$model->save()){
            return $this->getErrorResponse($model);
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功'
        ];
    }

    public function del()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $model = VolcengineKeys::find()->where(['id' => $this->id, 'mall_id' => \Yii::$app->mall->id])->one();
        if(!$model){
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '数据不存在'
            ];
        }
        $model->is_delete = 1;
        if(!$model->save()){
            return $this->getErrorResponse($model);
        }
        VolcengineKeysRelation::updateAll(["is_delete" => 1], ["is_delete" => 0, "key_id" => $model->id]);
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功'
        ];
    }
}
