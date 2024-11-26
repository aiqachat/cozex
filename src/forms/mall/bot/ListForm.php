<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\bot;

use app\bootstrap\Pagination;
use app\bootstrap\response\ApiCode;
use app\forms\common\coze\api\BotsList;
use app\forms\common\coze\api\Workspaces;
use app\forms\common\coze\ApiForm;
use app\models\CozeAccount;
use app\models\Knowledge;
use app\models\Model;

class ListForm extends Model
{
    public $id;
    public $page;
    public $page_size;
    public $account_id;
    public $space_id;

    public function rules()
    {
        return [
            [['account_id', 'space_id'], 'required'],
            [['id', 'account_id', 'page'], 'integer'],
            [['space_id'], 'string', 'max' => 32],
            [['page_size'], 'default', 'value' => 10],
            [['page'], 'default', 'value' => 1],
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
        $model = CozeAccount::findOne(['id' => $this->account_id, 'is_delete' => 0]);
        if(!$model){
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '账号不存在'
            ];
        }
        if($this->space_id == 'all'){
            $res = ApiForm::common([
                'object' => new Workspaces(),
                'account' => $model
            ])->request();
            $data = [];
            foreach ($res['data']['workspaces'] as $spaces) {
                $req = new BotsList([
                    'page_size' => 1000,
                    'space_id' => $spaces['id'],
                ]);
                $sdf = ApiForm::common (['object' => $req, "account" => $model])->request();
                $data = array_merge($data, array_map(function ($var) use($spaces){
                    $var['space_name'] = $spaces['name'];
                    $var['space_id'] = $spaces['id'];
                    $var['publish_time'] = date("Y-m-d", $var['publish_time']);
                    return $var;
                }, $sdf['data']['space_bots'] ?? []));
            }
            $pagination = new Pagination(['totalCount' => 1, 'pageSize' => 10, 'page' => 0]);
        }else{
            $req = new BotsList([
                'space_id' => $this->space_id,
                'page_index' => $this->page,
                'page_size' => $this->page_size
            ]);
            $list = ApiForm::common(['object' => $req, "account" => $model])->request();
            $data = array_map(function ($var){
                $var['space_id'] = $this->space_id;
                $var['publish_time'] = date("Y-m-d", $var['publish_time']);
                return $var;
            }, $list['data']['space_bots']);
            $pagination = new Pagination(['totalCount' => $list['data']['total'], 'pageSize' => $this->page_size, 'page' => $this->page -1]);
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $data,
                'pagination' => $pagination,
                'set_bot' => (new IndexForm())->getSetting()
            ]
        ];
    }
}
