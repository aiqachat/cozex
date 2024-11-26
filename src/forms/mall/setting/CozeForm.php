<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\setting;

use app\bootstrap\response\ApiCode;
use app\forms\common\coze\api\OauthToken;
use app\forms\common\coze\api\Workspaces;
use app\forms\common\coze\ApiForm;
use app\jobs\CommonJob;
use app\models\CozeAccount;
use app\models\Model;

class CozeForm extends Model
{
    public $coze_secret;
    public $name;
    public $client_id;
    public $client_secret;
    public $type;
    public $remark;
    public $id;
    public $page_size;

    public $code;
    public $state;

    public function rules()
    {
        return [
            [['id', 'type'], 'integer'],
            [['coze_secret', 'name', 'remark', 'client_id', 'client_secret', 'state', 'code'], 'string'],
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
            $return = array_map(function ($var){
                $var['role_type'] = $var['role_type'] == 'owner' ? '所有者' :
                    ($var['role_type'] == 'admin' ? '管理员' :
                        ($var['role_type'] == 'member' ? '成员' : ''));
                $var['workspace_type'] = $var['workspace_type'] == 'personal' ? '个人空间' :
                    ($var['workspace_type'] == 'team' ? '团队空间' : '');
                $var['name'] = "{$var['name']}（{$var['id']} {$var['role_type']} {$var['workspace_type']}）";
                return $var;
            }, $res['data']['workspaces']);

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
            if($this->type == 1) {
                $where = [
                    'and',
                    ['coze_secret' => $this->coze_secret],
                    ['not', ['id' => $this->id]]
                ];
            }else{
                $where = [
                    'and',
                    ['or', ['client_id' => $this->client_id], ['client_secret' => $this->client_secret]],
                    ['not', ['id' => $this->id]]
                ];
            }
        }else{
            $model = new CozeAccount();
            if($this->type == 1) {
                $where = ['coze_secret' => $this->coze_secret];
            }else{
                $where = ['or', ['client_id' => $this->client_id], ['client_secret' => $this->client_secret]];
            }
        }
        $exist = CozeAccount::find()->where(['is_delete' => 0])->andWhere($where)->exists();
        if($exist){
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => 'coze账号授权令牌已存在'
            ];
        }
        $model->attributes = $this->attributes;
        if($this->type == 1) {
            if (!$model->save ()) {
                return $this->getErrorResponse ($model);
            }
        }else{
            if($this->code){
                return $model;
            }
            if($model->getDirtyAttributes (['client_id', 'client_secret'])){
                $key = md5($this->client_id . $this->client_secret);
                \Yii::$app->cache->set($key, $this->attributes);
                $redirect_uri = \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/notify/coze.php';
                $url = "https://www.coze.cn/api/permission/oauth2/authorize?response_type=code&client_id={$this->client_id}&redirect_uri={$redirect_uri}&state={$key}";
            }elseif(!$model->save()){
                return $this->getErrorResponse($model);
            }
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功',
            'data' => [
                'url' => $url ?? '',
            ]
        ];
    }

    public function handleNotify()
    {
        try {
            $res = \Yii::$app->cache->get($this->state);
            if(!$res){
                throw new \Exception('数据异常');
            }
            $res['code'] = $this->code;
            $this->attributes = $res;
            $obj = new OauthToken();
            $obj->attribute = $this->attributes;
            $obj->redirect_uri = \Yii::$app->request->hostInfo . \Yii::$app->request->scriptUrl;
            $res = ApiForm::common(['secret' => $this->client_secret, 'object' => $obj])->request();
            $model = $this->save();
            if(isset($model['code']) && $model['code'] == ApiCode::CODE_ERROR){
                throw new \Exception($model['msg']);
            }
            $isNewRecord = $model->isNewRecord;
            if(!$model->saveOauth($res)){
                return $this->getErrorResponse($model);
            }
            if($isNewRecord){
                // refresh_token 有效期为 30 天。有效期内可以凭 refresh_token 调用 API
                \Yii::$app->queue->delay(30 * 24 * 3600 - 3600)->push(new CommonJob([
                    'type' => 'handle_coze_token',
                    'data' => ['id' => $model->id]
                ]));
            }
            return \Yii::$app->response->redirect(\Yii::$app->request->hostInfo  . dirname(\Yii::$app->request->baseUrl) . '/index.php?r=mall/setting/coze');
        } catch (\Exception $exception) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $exception->getMessage(),
                'url' => \Yii::$app->request->hostInfo  . dirname(\Yii::$app->request->baseUrl) . '/index.php?r=mall/setting/coze'
            ];
        }
    }

    public function handleJob()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        try {
            $model = CozeAccount::findOne($this->id);
            if(!$model){
                throw new \Exception('数据不存在');
            }
            if($model->is_delete = 1){
                throw new \Exception('数据已删除');
            }
            ApiForm::common(['account' => $model]);
            // refresh_token 有效期为 30 天。有效期内可以凭 refresh_token 调用 API
            \Yii::$app->queue->delay(30 * 24 * 3600 - 3600)->push(new CommonJob([
                'type' => 'handle_coze_token',
                'data' => ['id' => $model->id]
            ]));
        } catch (\Exception $exception) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $exception->getMessage(),
            ];
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '',
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
