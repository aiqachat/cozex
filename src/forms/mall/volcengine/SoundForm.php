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
use app\helpers\ArrayHelper;
use app\models\Model;
use app\models\UserSpeaker;
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
        $account = VolcengineAccount::findOne(['id' => $this->account_id, 'mall_id' => \Yii::$app->mall->id]);
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
                if($res['Statuses']) {
                    $data = UserSpeaker::find ()->where ([
                        'mall_id' => \Yii::$app->mall->id,
                        'is_delete' => 0,
                        'speaker_id' => array_column ($res['Statuses'], 'SpeakerID')
                    ])->all ();
                    $data = ArrayHelper::index ($data, null, "speaker_id");
                    foreach ($res['Statuses'] as $k => $var) {
                        if (isset($data[$var['SpeakerID']])) { // 去掉用户购买的音色
                            unset($res['Statuses'][$k]);
                            continue;
                        }
                        $var['CreateTime'] = substr ($var['CreateTime'], 0, 10);
                        $res['Statuses'][$k]['CreateTime'] = $var['CreateTime'] ? mysql_timestamp ($var['CreateTime']) : '';
                        $res['Statuses'][$k]['ExpireTime'] = mysql_timestamp (substr ($var['ExpireTime'], 0, 10));
                        $res['Statuses'][$k]['OrderTime'] = mysql_timestamp (substr ($var['OrderTime'], 0, 10));
                    }
                    $res['Statuses'] = array_values ($res['Statuses']);
                }
            }catch (\Exception $e){
                return [
                    'code' => ApiCode::CODE_ERROR,
                    'msg' => $e->getMessage(),
                ];
            }
        }else{
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '未关联火山引擎账号',
            ];
        }

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功',
            'data' => [
                'list' => $res['Statuses'] ?? [],
                'pagination' => $pagination ?? new Pagination(),
                'language' => MegaTtsUpload::languageList(),
            ]
        ];
    }
}
