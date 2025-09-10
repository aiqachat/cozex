<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\volcengine;

use app\bootstrap\response\ApiCode;
use app\forms\common\volcengine\data\BaseForm;
use app\jobs\CommonJob;
use app\models\AvData;
use app\models\Model;
use yii\helpers\Json;

class ListForm extends Model
{
    public $id;
    public $page_size;
    public $keyword;
    public $account_id;

    public function rules()
    {
        return [
            [['id', 'account_id'], 'integer'],
            [['keyword'], 'string'],
            [['id'], 'required', 'on' => ['del']],
            [['page_size'], 'default', 'value' => 10],
        ];
    }

    public function getList($type = 2)
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = AvData::find()->where([
            'type' => $type,
            'is_delete' => 0,
            'account_id' => $this->account_id,
            'mall_id' => \Yii::$app->mall->id
        ]);
        if ($this->keyword) {
            $query->andWhere(['like', 'text', $this->keyword]);
        }
        $data = $query->page($pagination, $this->page_size)
            ->orderBy(['updated_at' => SORT_DESC])
            ->all();
        $list = [];
        $form = new BaseForm();
        /** @var AvData $item */
        foreach ($data as $item){
            $items = $item->toArray();
            unset($items['data']);
            $items['file'] = basename($item->file);
            if($item->data){
                $items['data'] = @Json::decode($item->data) ?? [];
                unset($items['data']['app_id'], $items['data']['access_token']);
                if(empty($items['data']['voice_name'])){
                    $items['data']['voice_name'] = $item->voice($items['data']['voice_type'] ?? '');
                }
            }
            if(!in_array($item->type, [$form->vc, $form->ata, $form->auc])) {
                if (!$item->text) {
                    $items['text'] = @file_get_contents($item->localFile ()) ?: '';
                }
            }
            $list[] = $items;
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $list,
                'pagination' => $pagination,
            ]
        ];
    }

    public function getNew($type = 1, $limit = 10)
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = AvData::find()->where([
            'type' => $type,
            'is_delete' => 0,
            'account_id' => $this->account_id,
            'mall_id' => \Yii::$app->mall->id
        ]);
        $data = $query->limit($limit)
            ->orderBy(['updated_at' => SORT_DESC])
            ->all();
        $list = [];
        /** @var AvData $item */
        foreach ($data as $item){
            $items = $item->toArray();
            unset($items['data']);
            $items['file'] = basename($item->file);
            if($item->data){
                $items['data'] = Json::decode($item->data) ?: [];
                unset($items['data']['app_id'], $items['data']['access_token']);
                if(empty($items['data']['voice_name'])){
                    $items['data']['voice_name'] = $item->voice($items['data']['voice_type'] ?? '');
                }
            }
            $list[] = $items;
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $list,
            ]
        ];
    }

    public function del()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $model = AvData::findOne (['id' => $this->id, 'account_id' => $this->account_id, 'mall_id' => \Yii::$app->mall->id]);
        if(!$model){
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '数据不存在'
            ];
        }
        $model->isLog = false;
        $model->deleteData();
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功'
        ];
    }

    public function refresh()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $model = AvData::findOne ([
            'id' => $this->id,
            'account_id' => $this->account_id,
            'is_delete' => 0,
            'mall_id' => \Yii::$app->mall->id
        ]);
        if(!$model){
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '数据不存在'
            ];
        }
        $model->status = 1;
        $model->save ();
        $form = new BaseForm();
        if(in_array ($model->type, [$form->vc, $form->ata, $form->auc])) {
            \Yii::$app->queue->delay (0)->push (new CommonJob([
                'type' => 'handle_subtitle',
                'mall' => \Yii::$app->mall,
                'data' => ['id' => $model->id]
            ]));
        }else{
            \Yii::$app->queue->delay (0)->push (new CommonJob([
                'type' => 'handle_speech',
                'mall' => \Yii::$app->mall,
                'data' => ['id' => $model->id]
            ]));
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功'
        ];
    }
}
