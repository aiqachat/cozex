<?php
/**
 * Created by PhpStorm.
 * User: wstianxia
 * @copyright: ©2024 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\jobs;

use app\forms\api\order\SpeechPayNotify;
use app\forms\common\CommonOption;
use app\forms\common\volcengine\data\SpeechBaseForm;
use app\forms\common\volcengine\data\SubtitleBaseForm;
use app\forms\mall\setting\CozeForm;
use app\models\AvData;
use app\models\CoreActionLog;
use app\models\CoreExceptionLog;
use app\models\Mall;
use app\models\ModelActiveRecord;
use yii\helpers\FileHelper;
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
        } catch (\Exception $exception) {
            \Yii::error($exception);
        }
    }

    public function delDataLog(){
        ModelActiveRecord::$log = false;

        $delay = strtotime(date("Y-m-d 23:59:59")) + 60 - time();
        $sql = sprintf(
            'select * from %s where `created_at` <= "%s" ORDER BY id desc limit 1',
            CoreActionLog::tableName(),
            mysql_timestamp(strtotime("-6 months"))
        );
        $log = \Yii::$app->db->createCommand($sql)->queryOne();
        if($log){
            $time = strtotime($log['created_at']);
            $end = date("Y-m-d 23:59:59", $time);
            CoreActionLog::deleteAll(['<=', "created_at", $end]);
            CoreExceptionLog::deleteAll(['<=', "created_at", $end]);

            foreach (['/console_log', "/logs"] as $dir){
                $res = FileHelper::findDirectories(\Yii::$app->runtimePath . $dir, ['recursive' => false]);
                sort($res);
                foreach ($res as $item) {
                    if(basename($item) == date("Ym", $time)){
                        FileHelper::removeDirectory($item . "/" . date("d", $time));
                    }elseif(basename($item) < date("Ym", $time)){
                        FileHelper::removeDirectory($item);
                    }else{
                        break;
                    }
                }
            }
        }else{
            $delay = $delay + 86400 * 2;
        }

        $modelList = AvData::find()->where([
            'and',
            ['<=', "updated_at", date("Y-m-d 00:00:00",  strtotime("-3 day"))],
            ['is_data_deleted' => 0]
        ])->all();
        foreach ($modelList as $model){
            $model->deleteData();
        }
        if($modelList) {
            // 把空目录删除了
            $fileRes = file_uri ('/web/uploads/av_file/');
            $dirList = FileHelper::findDirectories ($fileRes['local_uri'], ['recursive' => false]);
            foreach ($dirList as $dir) {
                if (empty(FileHelper::findFiles ($dir))) {
                    FileHelper::removeDirectory ($dir);
                }
            }
        }

        $option = CommonOption::get("delAction");
        if(!empty($option['time']) && $option['time'] > mysql_timestamp()){
            return;
        }
        \Yii::$app->queue1->delay($delay)->push(new CommonJob(['type' => "del_data_log"]));
        CommonOption::set("delAction", ['time' => mysql_timestamp(time() + $delay)]);
    }

    private function handleSubtitle()
    {
        ModelActiveRecord::$log = false;
        $model = new SubtitleBaseForm();
        $model->id = $this->data['id'] ?? 0;
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

    private function deleteAv()
    {
        ModelActiveRecord::$log = false;
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
