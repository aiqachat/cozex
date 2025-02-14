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
use app\models\Option;

class QueueForm extends Model
{
    public $action;
    public $id;
    public $time;
    public $maxC;
    public $c;

    public function rules()
    {
        return [
            [['action'], 'string'],
            [['id', 'time', 'maxC', 'c'], 'integer'],
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
                $model = Option::findOne([
                    'name' => (new TestQueueServiceJob())->getKey(),
                ]);
                $data = [
                    'not_exists_fs' => $notExistsFs,
                    'date' => $model->updated_at,
                    'done' => \Yii::$app->serializer->decode($model->value)['done'] ?? false
                ];
            }
            if ($this->action == 'create') {
                $time = time();
                $job = new TestQueueServiceJob(['time' => $time]);
                $id = \Yii::$app->queue->delay(0)->push($job);
                $data = [
                    'id' => $id,
                    'time' => $time,
                ];
            }
            if ($this->action == 'test') {
                $done = \Yii::$app->queue->isDone($this->id);
                if ($done) {
                    $job = new TestQueueServiceJob(['time' => $this->time]);
                    if (!$job->valid()) {
                        throw new \Exception('任务似乎已经运行，但没有得到预期结果，请检查redis是否连接正常并且数据正常');
                    } else {
                        $data = ['done' => true];
                    }
                } else {
                    $data = ['done' => false];
                    if($this->maxC === $this->c){
                        (new TestQueueServiceJob(['time' => -1]))->valid();
                    }
                }
            }
            if(!isset($data['date'])) {
                $data['date'] = mysql_timestamp();
            }
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '成功',
                'data' => $data,
            ];
        } catch (\Exception $exception) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '队列服务失败：' . $exception->getMessage(),
                'data' => [
                    'date' => mysql_timestamp(),
                ]
            ];
        }
    }

    public function clearQueue()
    {
        $content = "#!/bin/bash\n";
        $content .= "basepath=$(cd `dirname $0`; pwd)\n";
        $content .= "command=\"php " . '$basepath' . "/yii queue/listen 1\"\n";
        $content .= "pid=$(ps -ef | grep \"" . '$command' . "\" | grep -v \"grep\" | awk '{print $2}')\n";
        $content .= "if [ -z \"" . '$pid' . "\" ]\n";
        $content .= "then\n";
        $content .= "  echo \"Process not started\"\n";
        $content .= "  echo -e \"\\033[32mOk.\\033[0m\"\n";
        $content .= "else\n";
        $content .= "  echo \"The process has been started. Killing the process with PID " . '$pid' . ".\"\n";
        $content .= "  kill " . '$pid' . "\n";
        $content .= "  echo -e \"\\033[31mProcess killed.\\033[0m\"\n";
        $content .= "fi\n";
        $file = \Yii::$app->basePath . '/temp.sh';
        file_put_contents($file, $content);
        cmd_exe("chmod +x {$file} & sh {$file}");
        @unlink ($file);
    }

    public function queueFile()
    {
        $file = \Yii::$app->basePath . '/queue.sh';
        $content = "#!/bin/bash\n\n";
        $content .= "basepath=$(cd `dirname $0`; pwd)\n";
        $content .= "chmod a+x \"" . '$basepath' . "/yii\"\n";
        $content .= "command=\"php " . '$basepath' . "/yii queue/listen 1\"\n\n";
        $content .= "result=$(ps -ef | grep \"`echo " . '$command' . "`\" | grep -v \"grep\")\n\n";
        $content .= "if [ ! -n \"" . '$result' . "\" ]\n";
        $content .= "then\n";
        $content .= "  echo \"Starting the process.\"\n";
        $content .= "  str=$(nohup " . '$command' . " >/dev/null 2>&1 &)\n";
        $content .= "  echo -e \"\\033[32mOk.\\033[0m\"\n";
        $content .= "else\n";
        $content .= "  echo \"The process has been started.\"\n";
        $content .= "fi\n\n";
        $content .= "result=$(crontab -l|grep -i \"* * * * * " . '$basepath' . "/queue.sh\"|grep -v grep)\n";
        $content .= "if [ ! -n \"" . '$result' . "\" ]\n";
        $content .= "then\n";
        $content .= "  echo -e \"\\033[32mCreating queue crontab.\\033[0m\"\n";
        $content .= "  echo \"Export crontab data\"\n";
        $content .= "  crontab -l > createcrontemp\n";
        $content .= "  echo \"Add new crontab line\"\n";
        $content .= "  echo \"* * * * * " . '$basepath' . "/queue.sh\" >> createcrontemp\n";
        $content .= "  echo \"Import crontab data\"\n";
        $content .= "  crontab createcrontemp\n";
        $content .= "  echo \"Delete temp file\"\n";
        $content .= "  rm -f createcrontemp\n";
        $content .= "  echo -e \"\\033[32mCreating queue crontab success.\\033[0m\"\n";
        $content .= "else\n";
        $content .= "  echo \"The queue crontab has been add .\"\n";
        $content .= "fi\n";
        file_put_contents($file, $content);
    }
}
