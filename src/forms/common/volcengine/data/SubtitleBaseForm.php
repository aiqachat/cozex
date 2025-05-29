<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\data;

use app\bootstrap\response\ApiCode;
use app\forms\common\volcengine\api\AtaQuery;
use app\forms\common\volcengine\api\AtaSubmit;
use app\forms\common\volcengine\api\AucBigModelQuery;
use app\forms\common\volcengine\api\AucBigModelSubmit;
use app\forms\common\volcengine\api\VcQuery;
use app\forms\common\volcengine\api\VcSubmit;
use app\forms\common\volcengine\ApiForm;
use app\jobs\CommonJob;
use app\models\Attachment;
use app\models\AvData;
use yii\helpers\Json;
use yii\web\UploadedFile;

class SubtitleBaseForm extends BaseForm
{
    public $file;
    public $text;
    public $id;
    public $type;
    public $data;
    public $account_id;
    public $user_id;
    public $format;

    public function rules()
    {
        return [
            [['file', 'text', 'format'], 'string'],
            [['data'], 'safe'],
            [['id', 'type', 'account_id', 'user_id'], 'integer'],
        ];
    }

    public function save()
    {
        $model = new AvData();
        $model->attributes = $this->attributes;
        $model->mall_id = \Yii::$app->mall->id;
        $model->user_id = $this->user_id ?: 0;
        $model->type = $this->type ?: 1;
        $model->data = Json::encode($this->data ?: []);
        if (!$model->save()) {
            throw new \Exception($this->getErrorMsg($model));
        }
        \Yii::$app->queue->delay(0)->push(new CommonJob([
            'type' => 'handle_subtitle',
            'mall' => \Yii::$app->mall,
            'data' => ['id' => $model->id]
        ]));
    }

    public function newSave()
    {
        $file = UploadedFile::getInstanceByName("file");
        $fileRes = file_uri("/web/temp/");
        $file->saveAs($fileRes['local_uri'] . $file->name);
        $this->file = $fileRes['web_uri'] . $file->name;
        if (is_array($this->data)) {
            $this->data['is_del'] = 1;
        }
        $this->save();
    }

    public function handle()
    {
        $model = AvData::findOne(['id' => $this->id, 'mall_id' => \Yii::$app->mall->id]);
        if(!$model){
            return null;
        }
        try {
            if (!$model->account) {
                throw new \Exception('数据不存在');
            }
            $data = $model->data ? Json::decode($model->data) : [];
            $api = ApiForm::common(['account' => $model->account]);
            if ($model->type == $this->auc) {
                $obj = new AucBigModelSubmit();
                $queryObj = new AucBigModelQuery();
            } else if ($model->type == $this->vc) {
                $obj = new VcSubmit();
                $obj->attribute = $data;
                $queryObj = new VcQuery();
            } else {
                $obj = new AtaSubmit();
                $obj->audio_text = $model->text;
                $queryObj = new AtaQuery();
            }
            if ($model->type == $this->auc) {
                $obj->url = $model->file;
                $extension = pathinfo($obj->url, PATHINFO_EXTENSION);
                $extension = strtolower($extension);
                $obj->format = $extension;
            } else {
                $obj->url = $model->localFile($model->file);
            }
            $res = $api->setObject($obj)->request();

            $model->job_id = $res['id'] ?? '';
            $queryObj->id = $model->job_id;

            if ($model->type == $this->auc) {
                do {
                    sleep(1);
                    $res = $api->setObject($queryObj)->request();
                    if (!isset($res['result'])) {
                        throw new \Exception('文件不存在或外网无法访问');
                    }
                } while (empty($res['result']['text']));
                $res = ['utterances' => $res['result']['utterances']];
            } else {
                $res = $api->setObject($queryObj)->request();
            }
            $text = '';
            foreach ($res['utterances'] as $k => $item) {
                $k++;
                $text .= "{$k}\r\n" . $this->times($item['start_time']) . " --> "
                    . $this->times($item['end_time']) . "\r\n" . $item['text'] . "\r\n\r\n";
            }
            $fileRes = file_uri('/web/uploads/av_file/' . date("Y-m-d") . "/");
            do {
                $name = uniqid() . ".srt";
                $file = $fileRes['local_uri'] . $name;
            } while (file_exists($file));
            file_put_contents($file, $text);
            $model->result = $fileRes['web_uri'] . $name;
            $model->status = 2;
            $model->is_data_deleted = 0;
            $return = [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '成功'
            ];
        } catch (\Exception $e) {
            $model->status = 3;
            $model->err_msg = $e->getMessage();
            $return = [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage() . $e->getLine() . $e->getFile()
            ];
        }
        if (!empty($data['is_del'])) {
            if (isset($obj) && file_exists($obj->url)) {
                @unlink($obj->url);
            }
            $attachment = Attachment::findOne([
                'url' => $model->file,
                'is_delete' => 0,
                'type' => 2,
                'mall_id' => \Yii::$app->mall->id
            ]);
            if ($attachment) {
                $attachment->delete();
            }
        }
        if (!$model->save()) {
            \Yii::error("model 保存失败");
            \Yii::error($model->attributes);
            return $this->getErrorResponse($model);
        }
        if($model->status == 2){
            \Yii::$app->queue1->delay($model::DELETE_FILE_DAY * 86400)->push(new CommonJob([
                'type' => 'delete_avData',
                'mall' => \Yii::$app->mall,
                'data' => ['id' => $model->id]
            ]));
        }
        return $return;
    }

    function times($milliseconds): string
    {
        // 将毫秒转换为秒
        $seconds = intval($milliseconds / 1000);

        // 计算小时、分钟和秒
        $hours = intval($seconds / 3600);
        $minutes = intval(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        // 返回格式化的字符串
        return sprintf('%02d:%02d:%02d,%03d', $hours, $minutes, $seconds, $milliseconds % 1000);
    }

    /**
     * 下载不同格式的字幕文件
     * @return array
     */
    public function download($return = true)
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $model = AvData::findOne(['id' => $this->id, 'mall_id' => \Yii::$app->mall->id]);
        if (!$model) {
            return [
                'code' => 1,
                'msg' => '数据不存在'
            ];
        }

        if ($model->status != 2 || empty($model->result)) {
            return [
                'code' => 1,
                'msg' => '该记录尚未生成字幕或字幕生成失败'
            ];
        }

        // 获取原SRT文件内容
        $srtContent = @file_get_contents($model->localFile($model->result));
        if (empty($srtContent)) {
            return [
                'code' => 1,
                'msg' => '字幕文件不存在'
            ];
        }

        // 转换为请求的格式
        $fileRes = file_uri('/web/temp/');
        $fileName = "{$model->id}.{$this->format}";
        $outputPath = $fileRes['local_uri'] . $fileName;
        $outputUrl = $fileRes['web_uri'] . $fileName;

        $num = 1;
        switch ($this->format) {
            case 'txt':
                // 提取纯文本内容
                $lines = explode("\n", $srtContent);
                $textContent = '';
                for ($i = 0; $i < count($lines); $i++) {
                    if (trim($lines[$i]) != $num && !preg_match('/^\d{2}:\d{2}:\d{2},\d{3} --> \d{2}:\d{2}:\d{2},\d{3}$/', trim($lines[$i])) && !empty(trim($lines[$i]))) {
                        $textContent .= trim($lines[$i]) . "\n";
                        $num++;
                    }
                }
                file_put_contents($outputPath, $textContent);
                break;

            case 'lrc':
                // 转换为LRC格式
                $lines = explode("\n", $srtContent);
                $lrcContent = "[ti:字幕]\n[ar:Volcengine]\n[al:字幕生成]\n";
                $currentTime = '';

                for ($i = 0; $i < count($lines); $i++) {
                    $line = trim($lines[$i]);
                    if (preg_match('/^(\d{2}):(\d{2}):(\d{2}),(\d{3}) --> /', $line, $matches)) {
                        $hours = intval($matches[1]);
                        $minutes = intval($matches[2]) + ($hours * 60);
                        $seconds = intval($matches[3]);
                        $milliseconds = intval($matches[4]);

                        // 格式化为 [mm:ss.xx] 格式
                        $currentTime = sprintf("[%02d:%02d.%02d]", $minutes, $seconds, floor($milliseconds / 10));
                    } else if (!empty($line) && $line != $num) {
                        // 如果不是序号或时间标记，就是字幕文本
                        if ($currentTime) {
                            $lrcContent .= $currentTime . $line . "\n";
                            $num++;
                        }
                    }
                }
                file_put_contents($outputPath, $lrcContent);
                break;

            case 'srt':
            default:
                // 原始SRT格式，直接返回原文件
                $outputUrl = $model->result;
                $outputPath = $model->localFile($model->result);
                break;
        }

        if($return){
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '成功',
                'data' => [
                    'url' => $outputUrl
                ]
            ];
        }

        if (!file_exists($outputPath)) {
            return [
                'code' => 1,
                'msg' => '文件生成失败'
            ];
        }

        // 设置响应头，强制下载
        $downloadFileName = basename($outputPath);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $downloadFileName . '"');
        header('Content-Length: ' . filesize($outputPath));
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        // 读取文件并输出
        readfile($outputPath);

        // 如果不是原始srt文件，则删除临时文件
        if ($this->format !== 'srt') {
            @unlink($outputPath);
        }

        exit();
    }
}
