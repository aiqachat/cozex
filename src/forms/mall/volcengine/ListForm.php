<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\volcengine;

use app\bootstrap\response\ApiCode;
use app\models\AvData;
use app\models\Model;

class ListForm extends Model
{
    public $id;
    public $page_size;
    public $keyword;

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['keyword'], 'string'],
            [['id'], 'required', 'on' => ['del']],
            [['page_size'], 'default', 'value' => 10],
        ];
    }

    public function getList()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = AvData::find()->where(['type' => 2, 'is_delete' => 0]);
        if ($this->keyword) {
            $query->andWhere(['like', 'text', $this->keyword]);
        }
        $data = $query->page ($pagination, $this->page_size)->all ();

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $data,
                'pagination' => $pagination,
            ]
        ];
    }

    public function getGenerateList()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = AvData::find()->where(['type' => 1, 'is_delete' => 0]);
        if ($this->keyword) {
            $query->andWhere(['like', 'text', $this->keyword]);
        }
        $data = $query->page ($pagination, $this->page_size)->all ();

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $data,
                'pagination' => $pagination,
            ]
        ];
    }

    public function del()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $model = AvData::findOne (['id' => $this->id]);
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