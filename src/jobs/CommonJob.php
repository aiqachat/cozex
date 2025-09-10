<?php
/**
 * Created by PhpStorm.
 * User: wstianxia
 * @copyright: ©2024 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\jobs;

use app\forms\api\order\SpeechPayNotify;
use app\forms\common\volcengine\data\SpeechBaseForm;
use app\forms\common\volcengine\data\SubtitleBaseForm;
use app\forms\common\volcengine\data\VisualImgForm;
use app\forms\common\volcengine\data\VisualVideoForm;
use app\forms\mall\setting\CozeForm;
use app\models\AvData;
use app\models\Mall;
use app\models\ModelActiveRecord;
use yii\queue\RetryableJobInterface;

/**
 * @package app\jobs
 */
class CommonJob extends BaseJob implements RetryableJobInterface
{
    /** @var Mall */
    public $mall;
    public $type;
    public $data;

    public function execute($queue)
    {
        \Yii::warning('CommonJob:'.$this->type);
        try {
            $this->setRequest();
            if($this->mall) {
                \Yii::$app->setMall($this->mall);
            }
            error_reporting(E_ALL);
            if($this->type == 'handle_subtitle'){
                $this->handleSubtitle();
            }
            if($this->type == 'handle_speech'){
                $this->handleSpeech();
            }
            if($this->type == 'listen_visual_video'){
                $this->handleVisualVideo();
            }
            if($this->type == 'listen_visual_image'){
                $this->handleVisualImage();
            }
            if($this->type == 'delete_avData'){
                $this->deleteAv();
            }
            if($this->type == 'handle_coze_token'){
                $this->handleCozeToken();
            }
            if($this->type == 'handle_speech_pay'){
                $this->handleSpeechPay();
            }
            if($this->type == 'del_data_log'){
                $this->delDataLog();
            }
            if($this->type == 'delete_visual_video'){
                $this->delVisualVideo();
            }
            if($this->type == 'delete_visual_img'){
                $this->delVisualImg();
            }
        } catch (\Exception $exception) {
            \Yii::error($exception);
        }
    }

    public function delDataLog(){

    }

    private function handleSubtitle()
    {
        ModelActiveRecord::$log = false;
        $model = new SubtitleBaseForm();
        $model->id = $this->data['id'] ?? 0;
        $model->duration = $this->data['duration'] ?? null;
        $res = $model->handle();
        if($res['code'] != 0){
            \Yii::error($res['msg']);
        }
    }

    private function handleSpeech()
    {
        ModelActiveRecord::$log = false;
        $model = new SpeechBaseForm();
        $model->id = $this->data['id'] ?? 0;
        $res = $model->handle();
        if($res['code'] != 0){
            \Yii::error($res['msg']);
        }
    }

    private function handleVisualVideo()
    {
        $model = new VisualVideoForm();
        $model->job($this->data);
    }

    private function handleVisualImage()
    {
        $model = new VisualImgForm();
        $model->job($this->data);
    }

    private function delVisualVideo()
    {
        // 删除会记录日志
        $model = new VisualVideoForm();
        $model->del($this->data['id'] ?? 0);
    }

    private function delVisualImg()
    {
        // 删除会记录日志
        $model = new VisualImgForm();
        $model->del($this->data['id'] ?? 0);
    }

    private function deleteAv()
    {
        try {
            $model = AvData::findOne(['id' => $this->data['id'] ?? 0, 'mall_id' => \Yii::$app->mall->id]);
            if (!$model) {
                throw new \Exception('数据不存在');
            }
            $model->deleteData();
        } catch (\Exception $e) {
            \Yii::error($e->getMessage() . $e->getLine() . $e->getFile());
        }
    }

    private function handleSpeechPay()
    {
        $model = new SpeechPayNotify();
        $model->handle($this->data['id'] ?? 0);
    }

    private function handleCozeToken()
    {
        ModelActiveRecord::$log = false;
        $model = new CozeForm();
        $model->id = $this->data['id'] ?? 0;
        $res = $model->handleJob();
        if($res['code'] != 0){
            \Yii::error($res['msg']);
        }
        \Yii::$app->queue->delay(30 * 86400 - 3600)->push(new CommonJob([
            'type' => 'handle_coze_token',
            'mall' => \Yii::$app->mall,
            'data' => ['id' => $model->id]
        ]));
    }

    public function getTtr()
    {
        // TODO: Implement getTtr() method.
        return 4 * 60 * 60;
    }

    public $attempts = 2;

    public function canRetry($attempt, $error)
    {
        // TODO: Implement canRetry() method.
        \Yii::error($attempt);
        \Yii::error($error);
        return $attempt < $this->attempts;
    }
}
