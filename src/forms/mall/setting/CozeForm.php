<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\setting;

use app\bootstrap\response\ApiCode;
use app\forms\common\coze\api\Workspaces;
use app\forms\common\coze\ApiForm;
use app\models\CozeAccount;
use app\models\Model;

class CozeForm extends Model
{
    public $coze_secret;
    public $name;
    public $remark;
    public $id;
    public $page_size;

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['coze_secret', 'name', 'remark'], 'string'],
            [['page_size'], 'default', 'value' => 10],
        ];
    }

    public function attributeLabels()
    {
        return [
            'coze_secret' => '访问令牌',
        ];
    }

    public function get()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $data = CozeAccount::find()->where(['is_delete' => 0])
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
        /** @var CozeAccount[] $data */
        $data = CozeAccount::find()->where(['is_delete' => 0])->all();
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

    public function space()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        /** @var CozeAccount $data */
        $data = CozeAccount::find()->where(['id' => $this->id])->one();
        $return = [];
        if($data){
            $res = ApiForm::common([
                'object' => new Workspaces(),
                'account' => $data
            ])->request();
            $return = $res['data']['workspaces'];
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'space' => $return,
            ]
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        if($this->id){
            $model = CozeAccount::findOne($this->id);
            if(!$model){
                return [
                    'code' => ApiCode::CODE_ERROR,
                    'msg' => '数据不存在'
                ];
            }
            $where = [
                'and',
                ['coze_secret' => $this->coze_secret],
                ['not', ['id' => $this->id]]
            ];
        }else{
            $model = new CozeAccount();
            $where = ['coze_secret' => $this->coze_secret];
        }
        $exist = CozeAccount::find()->where(['is_delete' => 0])->andWhere($where)->exists();
        if($exist){
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => 'coze账号授权令牌已存在'
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
        $model = CozeAccount::findOne($this->id);
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
