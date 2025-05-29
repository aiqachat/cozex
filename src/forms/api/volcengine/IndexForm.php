<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * Created by IntelliJ IDEA
 */

namespace app\forms\api\volcengine;

use app\bootstrap\response\ApiCode;
use app\forms\common\volcengine\data\BaseForm;
use app\jobs\CommonJob;
use app\models\AvData;
use app\models\Model;
use app\models\VolcengineAccount;
use yii\helpers\Json;

class IndexForm extends Model
{
    public $id;
    public $page_size;
    public $keyword;
    public $type;

    public function rules()
    {
        return [
            [['id', 'type'], 'integer'],
            [['keyword'], 'string'],
            [['page_size'], 'default', 'value' => 10],
        ];
    }

    public function getVolcengineAccount()
    {
        /** @var VolcengineAccount[] $data */
        $data = VolcengineAccount::find()->where(['is_delete' => 0, 'mall_id' => \Yii::$app->mall->id])->all();
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

    public function getList()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = AvData::find()->where([
            'type' => $this->type,
            'is_delete' => 0,
            'mall_id' => \Yii::$app->mall->id,
            'user_id' => \Yii::$app->user->id,
        ]);
        $data = $query->page($pagination, 10)
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
                $items['data'] = Json::decode($item->data) ?: [];
                unset($items['data']['app_id'], $items['data']['access_token']);
                $items['data']['voice_name'] = $item->voice($items['data']['voice_type'] ?? '', $this->type);
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

    public function del()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $model = AvData::findOne (['id' => $this->id, 'mall_id' => \Yii::$app->mall->id]);
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
            'is_delete' => 0,
            'mall_id' => \Yii::$app->mall->id,
            'user_id' => \Yii::$app->user->id,
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
