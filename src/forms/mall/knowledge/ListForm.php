<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\knowledge;

use app\bootstrap\response\ApiCode;
use app\models\Knowledge;
use app\models\Model;

class ListForm extends Model
{
    public $id;
    public $page_size;
    public $keyword;
    public $account_id;
    public $space_id;

    public function rules()
    {
        return [
            [['account_id', 'space_id'], 'required', "on" => 'list'],
            [['id', 'account_id'], 'integer'],
            [['keyword'], 'trim'],
            [['keyword', 'space_id'], 'string', 'max' => 155],
            [['page_size'], 'default', 'value' => 10],
        ];
    }

    public function attributeLabels()
    {
        return [
            'account_id' => '授权账号',
            'space_id' => '所属空间',
        ];
    }

    public function getList()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = Knowledge::find()->where([
            'is_delete' => 0,
            'account_id' => $this->account_id,
        ]);
        if ($this->keyword) {
            $query->andWhere(['or', ['like', 'name', $this->keyword]]);
        }
        if ($this->space_id != 'all') {
            $query->andWhere(['space_id' => $this->space_id]);
        }
        $list = $query->page($pagination, $this->page_size)->all();
        /** @var Knowledge $item */
        foreach ($list as $item){
            $item->format_type = (string)$item->format_type;
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $list,
                'pagination' => $pagination
            ]
        ];
    }

    public function del()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $model = Knowledge::findOne($this->id);
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
