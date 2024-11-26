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
        $data = VolcengineAccount::find()->where(['is_delete' => 0])
            ->page($pagination, $this->page_size)
            ->all();
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
        $data = VolcengineAccount::find()->where(['is_delete' => 0])->all();
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'account' => array_map(function ($var){
                    return [
                        'id' => $var->id,
                        'name' => $var->name
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
            $model = VolcengineAccount::findOne($this->id);
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
        $exist = VolcengineAccount::find()->where(['is_delete' => 0])->andWhere($where)->one();
        if($exist){
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '授权账号已添加，名称：'.$exist->name
            ];
        }
        $model->attributes = $this->attributes;
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
        $model = VolcengineAccount::findOne($this->id);
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
