<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\setting;

use app\bootstrap\response\ApiCode;
use app\models\Model;
use app\models\VolcengineAccount;

class VolcengineForm extends Model
{
    public $access_token;
    public $app_id;
    public $name;
    public $id;
    public $page_size;

    public function rules()
    {
        return [
            [['access_token', 'app_id', 'name'], 'string'],
            [['access_token'], 'string', 'max' => 32],
            [['id'], 'integer'],
            [['page_size'], 'default', 'value' => 10],
        ];
    }

    public function attributeLabels()
    {
        return [
            'access_token' => 'token',
        ];
    }

    public function get()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $data = VolcengineAccount::find()->where(['is_delete' => 0, 'mall_id' => \Yii::$app->mall->id])
            ->page($pagination, $this->page_size)
            ->all();
        if(!empty($data)) {
            $account = VolcengineAccount::findOne (['mall_id' => \Yii::$app->mall->id, 'is_default' => 1, 'is_delete' => 0]);
            if (!$account) {
                VolcengineAccount::updateAll(['is_default' => 1], ['id' => $data[0]->id]);
                $data[0]->is_default = 1;
            }
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $data,
                'pagination' => $pagination
            ]
        ];
    }

    public function data()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        /** @var VolcengineAccount[] $data */
        $data = VolcengineAccount::find()->where(['is_delete' => 0, 'mall_id' => \Yii::$app->mall->id])->all();
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'account' => array_map(function ($var){
                    return [
                        'id' => $var->id,
                        'name' => $var->name,
                        'is_default' => $var->is_default
                    ];
                }, $data),
            ]
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        if($this->id){
            $model = VolcengineAccount::find()->where(['id' => $this->id, 'mall_id' => \Yii::$app->mall->id])->one();
            if(!$model){
                return [
                    'code' => ApiCode::CODE_ERROR,
                    'msg' => '数据不存在'
                ];
            }
            $where = [
                'and',
                ['or', ['access_token' => $this->access_token], ['app_id' => $this->app_id]],
                ['not', ['id' => $this->id]]
            ];
        }else{
            $model = new VolcengineAccount();
            $where = ['or', ['access_token' => $this->access_token], ['app_id' => $this->app_id]];
        }
        $exist = VolcengineAccount::find()
            ->where(['is_delete' => 0, 'mall_id' => \Yii::$app->mall->id])
            ->andWhere($where)
            ->one();
        if($exist){
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '授权账号已添加，名称：'.$exist->name
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

    public function setDefault()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $model = VolcengineAccount::findOne(['id' => $this->id, 'mall_id' => \Yii::$app->mall->id]);
        if(!$model){
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '数据不存在'
            ];
        }
        VolcengineAccount::updateAll(['is_default' => 0], ['mall_id' => \Yii::$app->mall->id, 'is_delete' => 0]);
        $model->is_default = 1;
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
        $model = VolcengineAccount::findOne(['id' => $this->id, 'mall_id' => \Yii::$app->mall->id]);
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
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功'
        ];
    }
}
