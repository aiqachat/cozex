<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\volcengine;

use app\bootstrap\response\ApiCode;
use app\jobs\CommonJob;
use app\models\AvData;
use app\models\Model;
use yii\helpers\Json;
use yii\web\UploadedFile;

class BatchDataForm extends Model
{
    public $type;
    public $account_id;
    public $data;
    public $op;
    public $user_id;

    public function rules()
    {
        return [
            [['account_id'], 'required'],
            [['type', 'account_id', 'user_id'], 'integer'],
            [['op'], 'string'],
            [['data'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'account_id' => '应用',
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        if ($this->op == 'handle') {
            return $this->handle();
        } elseif ($this->op == 'delete') {
            return $this->delete();
        } elseif ($this->op == 'down') {
            return $this->down();
        }
        $file = UploadedFile::getInstanceByName("file");
        if (!$file) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '请上传文件',
            ];
        }
        $fileRes = file_uri(AvData::FILE_DIR . date("Y-m-d") . "/");

        $pathInfo = pathinfo($file->name);
        $counter = 1;
        $name = $file->name;
        while (file_exists($fileRes['local_uri'] . $name)) {
            $name = $pathInfo['filename'] . '_' . $counter . '.' . $pathInfo['extension'];
            $counter++;
        }
        $file->saveAs($fileRes['local_uri'] . $name);

        $this->data = $this->data ?: [];
        $model = new AvData();
        $model->attributes = $this->attributes;
        $model->mall_id = \Yii::$app->mall->id;
        $model->type = $this->type ?: 1;
        $model->file = $fileRes['web_uri'] . $name;
        $model->status = 0;
        $model->user_id = $this->user_id ?: 0;
        $this->data = $model->cost();
        $model->data = is_array($this->data) ? Json::encode($this->data) : $this->data;
        if (!$model->save()) {
            return $this->getErrorResponse($model);
        }
        $model->data = Json::decode($model->data);
        $model->text = file_get_contents($fileRes['local_uri'] . $file->name);
        \Yii::$app->queue1->delay($model::DELETE_FILE_DAY * 86400)->push(new CommonJob([
            'type' => 'delete_avData',
            'mall' => \Yii::$app->mall,
            'data' => ['id' => $model->id]
        ]));
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功',
            'data' => $model
        ];
    }

    private function handle()
    {
        $list = AvData::find()->where([
            'id' => $this->data,
            'account_id' => $this->account_id,
            'mall_id' => \Yii::$app->mall->id,
        ])->keyword($this->user_id, ['user_id' => $this->user_id])->all();
        /** @var AvData $item */
        foreach ($list as $item) {
            if (in_array($item->status, [0, 3])) {
                $item->status = 1;
                $item->save();
                \Yii::$app->queue->delay(0)->push(new CommonJob([
                    'type' => 'handle_speech',
                    'mall' => \Yii::$app->mall,
                    'data' => ['id' => $item->id]
                ]));
            }
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '处理成功',
        ];
    }

    private function delete()
    {
        $list = AvData::find()->where([
            'id' => $this->data,
            'account_id' => $this->account_id,
            'mall_id' => \Yii::$app->mall->id,
        ])->keyword($this->user_id, ['user_id' => $this->user_id])->all();
        /** @var AvData $item */
        foreach ($list as $item) {
            $item->is_delete = 1;
            if (!$item->save()) {
                return $this->getErrorResponse($item);
            }
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '删除成功',
        ];
    }

    private function down()
    {
        if($this->data && is_string($this->data)) {
            try {
                $this->data = Json::decode ($this->data);
            }catch (\Exception $e){
                $this->data = explode(",", $this->data);
            }
        }
        $list = AvData::find()->where([
            'id' => $this->data,
            'account_id' => $this->account_id,
            'mall_id' => \Yii::$app->mall->id,
        ])->keyword($this->user_id, ['user_id' => $this->user_id])->all();
        $fileList = [];
        /** @var AvData $item */
        foreach ($list as $item) {
            if ($item->status != 2 || empty($item->result)) {
                continue;
            }
            $outputPath = $item->localFile($item->result);
            if (!file_exists($outputPath) || is_dir($outputPath)) {
                continue;
            }
            $fileList[] = $outputPath;
        }
        if (empty($fileList)) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '没有可下载的文件',
            ];
        }
        // 创建临时zip文件
        $fileRes = file_uri('/web/temp');
        $zipFile = $fileRes['local_uri'] . '/download_' . date('YmdHis') . '.zip';
        $zip = new \ZipArchive();
        if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '无法创建压缩文件',
            ];
        }
        // 添加文件到zip
        foreach ($fileList as $file) {
            $zip->addFile($file, basename($file));
        }
        $zip->close();

        // 发送文件到客户端
        return \Yii::$app->response->sendFile($zipFile);
    }
}
