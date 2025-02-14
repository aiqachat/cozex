<?php
/**
 * Created by PhpStorm.
 * User: wstianxia
 * @copyright: ©2024 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\jobs;

use app\forms\mall\setting\CozeForm;
use app\forms\mall\volcengine\SpeechForm;
use app\forms\mall\volcengine\SubtitleForm;
use yii\queue\RetryableJobInterface;

/**
 * @package app\jobs
 */
class CommonJob extends BaseJob implements RetryableJobInterface
{
    public $type;
    public $data;

    public function execute($queue)
    {
        \Yii::warning('CommonJob:'.$this->type);
        try {
            $this->setRequest();
            error_reporting(E_ALL);
            if($this->type == 'handle_subtitle'){
                $this->handleSubtitle();
            }
            if($this->type == 'handle_speech'){
                $this->handleSpeech();
            }
            if($this->type == 'handle_coze_token'){
                $this->handleCozeToken();
            }
        } catch (\Exception $exception) {
            \Yii::error($exception);
        }
    }

    private function handleSubtitle()
    {
        $model = new SubtitleForm();
        $model->id = $this->data['id'] ?? 0;
        $model->is_del = $this->data['is_del'] ?? 0;
        $res = $model->handle();
        if($res['code'] != 0){
            \Yii::error($res['msg']);
        }
    }

    private function handleSpeech()
    {
        $model = new SpeechForm();
        $model->id = $this->data['id'] ?? 0;
        $res = $model->handle();
        if($res['code'] != 0){
            \Yii::error($res['msg']);
        }
    }

    private function handleCozeToken()
    {
        $model = new CozeForm();
        $model->id = $this->data['id'] ?? 0;
        $res = $model->handleJob();
        if($res['code'] != 0){
            \Yii::error($res['msg']);
        }
    }

    public function getTtr()
    {
        // TODO: Implement getTtr() method.
        return 60 * 60;
    }

    public function canRetry($attempt, $error)
    {
        // TODO: Implement canRetry() method.
        \Yii::error($attempt);
        \Yii::error($error);
    }
}
