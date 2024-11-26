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
    public $account;
    public $name;
    public $id;
    public $page_size;

    public function rules()
    {
        return [
            [['secret_key', 'access_id', 'name'], 'string'],
            [['secret_key'], 'string', 'max' => 60],
            [['id'], 'integer'],
            [['account'], 'safe'],
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
        $data = VolcengineKeys::find()->where(['is_delete' => 0])
            ->with("keysRelation")
            ->page($pagination, $this->page_size)
            ->asArray()
            ->all();
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => array_map (function ($var){
                    $var['account'] = array_map(function ($account){
                        return intval($account['account_id']);
                    }, $var['keysRelation']);
                    unset($var['keysRelation']);
                    return $var;
                }, $data),
                'accounts' => (new VolcengineForm())->data()['data']['account'] ?? [],
                'pagination' => $pagination
            ]
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        if($this->id){
            $model = VolcengineKeys::findOne($this->id);
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
        $exist = VolcengineKeys::find()->where(['is_delete' => 0])->andWhere($where)->one();
        if($exist){
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '山火密钥已添加，名称：'.$exist->name
            ];
        }
        $model->attributes = $this->attributes;
        if(!$model->save()){
            return $this->getErrorResponse($model);
        }
        $exist = VolcengineKeysRelation::find()
            ->where(['account_id' => $this->account, "is_delete" => 0])
            ->andWhere(['!=', "key_id", $model->id])
            ->one();
        if($exist){
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => "应用（{$exist->account->name}）已被关联，密钥（{$exist->key->name}）"
            ];
        }
        VolcengineKeysRelation::updateAll(["is_delete" => 1], ["is_delete" => 0, "key_id" => $model->id]);
        foreach ($this->account as $account){
            $relation = VolcengineKeysRelation::findOne(['account_id' => $account, 'key_id' => $model->id]);
            if(!$relation) {
                $relation = new VolcengineKeysRelation();
                $relation->account_id = $account;
                $relation->key_id = $model->id;
            }
            $relation->is_delete = 0;
            if(!$relation->save()){
                return $this->getErrorResponse($relation);
            }
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
        $model = VolcengineKeys::findOne($this->id);
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
