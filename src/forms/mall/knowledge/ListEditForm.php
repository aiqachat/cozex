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

class ListEditForm extends Model
{
    public $id;
    public $name;
    public $desc;
    public $format_type;
    public $dataset_id;
    public $account_id;
    public $space_id;

    public function rules()
    {
        return [
            [['account_id', 'space_id'], 'required'],
            [['id', 'format_type', 'account_id'], 'integer'],
            [['dataset_id'], 'trim'],
            [['name', 'desc', 'space_id'], 'string'],
            [['dataset_id'], 'string', 'max' => 100],
        ];
    }

    public function attributeLabels()
    {
        return [
            'account_id' => '授权账号',
            'space_id' => '所属空间',
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        if($this->id){
            $model = Knowledge::findOne($this->id);
            if(!$model){
                return [
                    'code' => ApiCode::CODE_ERROR,
                    'msg' => '数据不存在'
                ];
            }
            $model->attributes = $this->attributes;
            $where = [
                'and',
                ['dataset_id' => $this->dataset_id, 'space_id' => $this->space_id],
                ['not', ['id' => $this->id]]
            ];
        }else{
            $model = new Knowledge();
            $model->attributes = $this->attributes;
            $where = ['dataset_id' => $this->dataset_id, 'space_id' => $this->space_id];
        }
        $exist = Knowledge::find()->where(['is_delete' => 0])->andWhere($where)->exists();
        if($exist){
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '资源库ID已存在'
            ];
        }
        if(!$model->save()){
            return $this->getErrorResponse($model);
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功'
        ];
    }
}
