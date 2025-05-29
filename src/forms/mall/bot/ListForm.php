<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\bot;

use app\bootstrap\Pagination;
use app\bootstrap\response\ApiCode;
use app\forms\common\CommonUser;
use app\forms\common\coze\api\BotsList;
use app\forms\common\coze\api\Workspaces;
use app\forms\common\coze\ApiForm;
use app\models\BotConf;
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
        $model = CozeAccount::findOne(['id' => $this->account_id, 'is_delete' => 0, 'mall_id' => \Yii::$app->mall->id]);
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
            // workspace_type 空间类型，如果多个可能其它空间不可获取，要防止报错
            $error = count(array_column($res['data']['workspaces'], "workspace_type")) == 1;
            $data = [];
            foreach ($res['data']['workspaces'] as $spaces) {
                try {
                    $req = new BotsList([
                        'page_size' => 1000,
                        'space_id' => $spaces['id'],
                    ]);
                    $sdf = ApiForm::common (['object' => $req, "account" => $model])->request ();
                    $data = array_merge($data, array_map (function ($var) use ($spaces) {
                        $var['space_name'] = $spaces['name'];
                        $var['space_id'] = $spaces['id'];
                        $var['publish_time'] = date ("Y-m-d", $var['publish_time']);
                        return $var;
                    }, $sdf['data']['space_bots'] ?? []));
                } catch (\Exception $e) {
                    if($error){
                        throw $e;
                    }
                }
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
        $confList = BotConf::find()->where([
            'bot_id' => array_column($data, "bot_id"),
            'mall_id' => \Yii::$app->mall->id,
            'is_delete' => 0
        ])->all();
        $confList = array_column($confList, null, 'bot_id');
        $form = new IndexForm();
        $set = $form->getSetting();
        foreach ($data as &$item){
            if(isset($confList[$item['bot_id']])) {
                $item['voice_url'] = CommonUser::userWebUrl('coze/aigc', ['id' => $confList[$item['bot_id']]['id']]);
            }elseif($item['bot_id'] == $set['bot_id']){
                $item['voice_url'] = CommonUser::userWebUrl('coze/aigc');
            }
            $form->bot_id = $item['bot_id'];
            $item['preview_js'] = $form->html($confList[$item['bot_id']] ?? new BotConf(), $model, true);
        }
        unset($item);
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $data,
                'pagination' => $pagination,
                'set_bot' => $set,
                'refresh_token' => $model->refresh_token,
            ]
        ];
    }
}
