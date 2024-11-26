<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\volcengine;

use app\bootstrap\Pagination;
use app\bootstrap\response\ApiCode;
use app\forms\common\volcengine\api\MegaTtsUpload;
use app\forms\common\volcengine\RequestForm;
use app\forms\common\volcengine\sdk\BatchListMegaTTSTrainStatus;
use app\models\Model;
use app\models\VolcengineAccount;

class SoundForm extends Model
{
    public $account_id;

    public function rules()
    {
        return [
            [['account_id'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'account_id' => '应用',
        ];
    }

    public function get()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $account = VolcengineAccount::findOne($this->account_id);
        if(!$account){
            throw new \Exception('账号不存在');
        }
        if($account->key){
            $obj = new BatchListMegaTTSTrainStatus([
                'AppID' => $account->app_id,
                'PageNumber' => intval(\Yii::$app->request->get("page", 1))
            ]);
            try {
                $form = new RequestForm(['account' => $account->key, 'object' => $obj]);
                $res = $form->request();
                $pagination = new Pagination([
                    'totalCount' => $res['TotalCount'],
                    'pageSize' => $res['PageSize'],
                    'page' => $res['PageNumber'] - 1
                ]);
            }catch (\Exception $e){}
        }

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功',
            'data' => [
                'list' => array_map (function ($var){
                    $var['CreateTime'] = substr($var['CreateTime'], 0, 10);
                    $var['CreateTime'] = $var['CreateTime'] ? mysql_timestamp($var['CreateTime']) : '';
                    $var['ExpireTime'] = mysql_timestamp(substr($var['ExpireTime'], 0, 10));
                    return $var;
                }, $res['Statuses'] ?? []),
                'pagination' => $pagination ?? new Pagination(),
                'language' => MegaTtsUpload::languages,
            ]
        ];
    }
}
