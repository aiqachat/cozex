<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\api\volcengine;

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
    /** @var integer 1：国内站；2：国际站 */
    public $is_home;

    public function rules()
    {
        return [
            [['is_home'], 'integer'],
            [['is_home'], 'default', 'value' => 1],
        ];
    }
    public function get()
    {
        $dataList = UserSpeaker::find ()->where([
            'mall_id' => \Yii::$app->mall->id,
            'user_id' => \Yii::$app->user->id,
            'is_delete' => 0,
            'account_id' => VolcengineAccount::find()
                ->select('id')
                ->where(['is_delete' => 0, 'type' => $this->is_home, 'mall_id' => \Yii::$app->mall->id])
        ])->orderBy('id DESC')->page($pagination)->all();
        $list = [];
        $dataList = ArrayHelper::index($dataList, null, 'account_id');
        /** @var UserSpeaker[] $item */
        foreach ($dataList as $item){
            $obj = new BatchListMegaTTSTrainStatus(['type' => $item[0]->account->type]);
            $obj->AppID = $item[0]->account->app_id;
            $obj->PageSize = 100;
            $obj->SpeakerIDs = array_column($item, 'speaker_id');
            $form = new RequestForm(['account' => $item[0]->account->key, 'object' => $obj]);
            $result = ArrayHelper::index($item, 'speaker_id');
            $res = $form->request();
            foreach ($res['Statuses'] as $var){
                $var['CreateTime'] = substr($var['CreateTime'], 0, 10);
                $var['CreateTime'] = $var['CreateTime'] ? mysql_timestamp($var['CreateTime']) : '';
                $var['ExpireTime'] = mysql_timestamp(substr($var['ExpireTime'], 0, 10));
                $var['OrderTime'] = mysql_timestamp(substr($var['OrderTime'], 0, 10));
                $var['account_id'] = $item[0]->account->id;
                $var['Alias'] = $result[$var['SpeakerID']]->name ?? $var['Alias'];
                $list[] = $var;
            }
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功',
            'data' => [
                'list' => $list,
                'pagination' => $pagination,
            ]
        ];
    }
}
