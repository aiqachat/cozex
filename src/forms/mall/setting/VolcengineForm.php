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
use app\models\VolcengineKeysRelation;

class VolcengineForm extends Model
{
    public $access_token;
    public $app_id;
    public $name;
    public $type;
    public $id;
    public $key_id;
    public $page_size;

    public function rules()
    {
        return [
            [['access_token', 'app_id', 'name'], 'string'],
            [['access_token'], 'string', 'max' => 32],
            [['id', 'type', 'key_id'], 'integer'],
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
            ->with("key")
            ->page($pagination, $this->page_size)
            ->asArray()
            ->all();
        if (!empty($data)) {
            // 为每个账号添加关联的密钥ID
            foreach ($data as &$item) {
                $item['key_id'] = $item['key']['id'] ?? null;
                unset($item['key']);
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

    public function data($type = 1)
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        /** @var VolcengineAccount[] $data */
        $data = VolcengineAccount::find()
            ->where(['is_delete' => 0, 'mall_id' => \Yii::$app->mall->id])
            ->keyword($type, ['type' => $type])
            ->all();
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'account' => array_map(function ($var) {
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
        if ($this->id) {
            $model = VolcengineAccount::find()->where(['id' => $this->id, 'mall_id' => \Yii::$app->mall->id])->one();
            if (!$model) {
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
        } else {
            $model = new VolcengineAccount();
            $where = ['or', ['access_token' => $this->access_token], ['app_id' => $this->app_id]];
        }
        $exist = VolcengineAccount::find()
            ->where(['is_delete' => 0, 'mall_id' => \Yii::$app->mall->id])
            ->andWhere($where)
            ->one();
        if ($exist) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '授权账号已添加，名称：' . $exist->name
            ];
        }
        $model->attributes = $this->attributes;
        $model->mall_id = \Yii::$app->mall->id;
        if (!$model->save()) {
            return $this->getErrorResponse($model);
        }
        
        // 处理密钥关联关系
        if ($this->key_id) {
            // 删除旧的关联关系
            VolcengineKeysRelation::updateAll(['is_delete' => 1], ['account_id' => $model->id, 'is_delete' => 0]);
            
            // 创建新的关联关系
            $relation = VolcengineKeysRelation::findOne(['account_id' => $model->id, 'key_id' => $this->key_id]);
            if (!$relation) {
                $relation = new VolcengineKeysRelation();
                $relation->account_id = $model->id;
                $relation->key_id = $this->key_id;
            }
            $relation->is_delete = 0;
            if (!$relation->save()) {
                return $this->getErrorResponse($relation);
            }
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
        if (!$model) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '数据不存在'
            ];
        }
        VolcengineAccount::updateAll(['is_default' => 0], ['mall_id' => \Yii::$app->mall->id, 'is_delete' => 0, 'type' => $model->type]);
        $model->is_default = 1;
        if (!$model->save()) {
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
        if (!$model) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '数据不存在'
            ];
        }
        $model->is_delete = 1;
        if (!$model->save()) {
            return $this->getErrorResponse($model);
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功'
        ];
    }
}
