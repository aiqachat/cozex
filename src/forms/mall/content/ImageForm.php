<?php

namespace app\forms\mall\content;

use app\bootstrap\response\ApiCode;
use app\models\Model;
use app\models\VisualImage;

class ImageForm extends Model
{
    public $user_id;
    public $type;
    public $public;
    public $limit;
    public $id;
    public $action;
    public $permanent;
    public $ids; // 用于批量操作

    public function rules()
    {
        return [
            [['user_id', 'type', 'public', 'limit', 'id', 'permanent'], 'integer'],
            [['action'], 'string'],
            [['ids'], 'each', 'rule' => ['integer']],
        ];
    }

    public function get(){
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $query = VisualImage::find()
            ->where([
                'mall_id' => \Yii::$app->mall->id
            ])->andWhere(['>', "user_id", 0]);

        $query->keyword($this->user_id, ['user_id' => $this->user_id])
            ->keyword($this->type && $this->type <= 2, ['type' => $this->type])
            ->keyword($this->type == 3, ['is_home' => 2, 'type' => 2]);

        if($this->public == 1){
            $query->andWhere(['is_admin_public' => 1]);
        }elseif($this->public === '0'){
            $query->andWhere([
                'and',
                ['is_admin_public' => 0],
                ['<', 'is_permanent_public', 3],
            ]);
        }elseif($this->public == 2){
            $query->andWhere(['is_permanent_public' => 3]);
        }

        $dataList = $query->orderBy('created_at DESC')
            ->with(['user.userInfo'])
            ->page($pagination, $this->limit)
            ->asArray()
            ->all();

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功',
            'data' => [
                'list' => $dataList,
                'pagination' => $pagination
            ]
        ];
    }

    public function public()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $model = VisualImage::findOne(['id' => $this->id, 'mall_id' => \Yii::$app->mall->id]);
        if (!$model) {
            throw new \Exception('数据不存在');
        }
        $model->is_admin_public = $this->action == 'approve' ? 1 : 0;
        if($this->action == 'approve'){
            $model->is_permanent_public = $this->permanent ? 1 : 0;
        }else{
            $model->is_permanent_public = 2;
        }
        if ($model->save()) {
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '成功'
            ];
        } else {
            return $this->getErrorResponse($model);
        }
    }

    public function disable()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $model = VisualImage::findOne(['id' => $this->id, 'mall_id' => \Yii::$app->mall->id]);
        if (!$model) {
            throw new \Exception('数据不存在');
        }
        $model->is_delete = $model->is_delete == 0 ? 1 : 0;
        if ($model->save()) {
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '成功'
            ];
        } else {
            return $this->getErrorResponse($model);
        }
    }

    public function show()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $model = VisualImage::findOne(['id' => $this->id, 'mall_id' => \Yii::$app->mall->id]);
        if (!$model) {
            throw new \Exception('数据不存在');
        }
        $model->is_admin_public = $model->is_admin_public == 0 ? 1 : 0;
        $model->is_permanent_public = $model->is_admin_public == 1 ? 0 : 3;
        if ($model->save()) {
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '成功'
            ];
        } else {
            return $this->getErrorResponse($model);
        }
    }

    public function del()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $model = VisualImage::findOne(['id' => $this->id, 'mall_id' => \Yii::$app->mall->id]);
        if (!$model) {
            throw new \Exception('数据不存在');
        }
        $model->deleteData(true);
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功'
        ];
    }

    // 批量禁用/恢复
    public function batchDisable()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        if (empty($this->ids) || !is_array($this->ids)) {
            throw new \Exception('请选择要操作的数据');
        }

        $models = VisualImage::find()->where(['id' => $this->ids, 'mall_id' => \Yii::$app->mall->id])->all();
        foreach ($models as $model) {
            $model->is_delete = $model->is_delete == 0 ? 1 : 0;
            $model->save();
        }

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '操作成功'
        ];
    }

    // 批量审核展示
    public function batchPublic()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        if (empty($this->ids) || !is_array($this->ids)) {
            throw new \Exception('请选择要操作的数据');
        }

        $models = VisualImage::find()->where(['id' => $this->ids, 'mall_id' => \Yii::$app->mall->id])->all();
        foreach ($models as $model) {
            $model->is_admin_public = $this->action == 'approve' ? 1 : 0;
            if($this->action == 'approve'){
                $model->is_permanent_public = $this->permanent ? 1 : 0;
            }else{
                $model->is_permanent_public = 2;
            }
            $model->save();
        }

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '操作成功'
        ];
    }

    // 批量删除
    public function batchDel()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        if (empty($this->ids) || !is_array($this->ids)) {
            throw new \Exception('请选择要操作的数据');
        }

        $models = VisualImage::find()->where(['id' => $this->ids, 'mall_id' => \Yii::$app->mall->id])->all();
        foreach ($models as $model) {
            $model->deleteData(true);
        }

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '删除成功'
        ];
    }
}
