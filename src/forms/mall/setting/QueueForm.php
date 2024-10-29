<?php
/**
 * Created by PhpStorm
 * User: chenzs
 * @copyright: ©2024 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\forms\mall\setting;

use app\bootstrap\response\ApiCode;
use app\jobs\TestQueueServiceJob;
use app\models\Model;

class QueueForm extends Model
{
    public $action;
    public $id;
    public $time;

    public function rules()
    {
        return [
            [['action'], 'string'],
            [['id', 'time'], 'integer'],
        ];
    }

    public function get()
    {
        if (!$this->validate ()) {
            return $this->getErrorResponse ();
        }

        try {
            $data = [];
            if ($this->action == 'env') {
                $fs = [
                    'proc_open', 'proc_get_status', 'proc_close', 'proc_terminate',
                    'pcntl_signal_dispatch', 'pcntl_signal',
                    'pcntl_signal_get_handler',
                ];
                $notExistsFs = [];
                foreach ($fs as $f) {
                    if (!function_exists ($f)) $notExistsFs[] = $f;
                }
                $data = [
                    'not_exists_fs' => $notExistsFs,
                ];
            }
            if ($this->action == 'create') {
                $time = time();
                $job = new TestQueueServiceJob();
                $job->time = $time;
                $id = \Yii::$app->queue->delay(0)->push($job);
                $data = [
                    'id' => $id,
                    'time' => $time,
                ];
            }
            if ($this->action == 'test') {
                $done = \Yii::$app->queue->isDone($this->id);
                if ($done) {
                    $job = new TestQueueServiceJob();
                    $job->time = $this->time;
                    if (!$job->valid()) {
                        throw new \Exception('任务似乎已经运行，但没有得到预期结果，请检查redis是否连接正常并且数据正常。');
                    } else {
                        $data = ['done' => true];
                    }
                } else {
                    $data = ['done' => false];
                }
            }
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'data' => $data,
            ];
        } catch (\Exception $exception) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '队列服务测试失败：' . $exception->getMessage (),
            ];
        }
    }
}
