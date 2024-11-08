<?php
/**
 * Queue服务配置测试，请勿删除
 */

namespace app\jobs;

use app\forms\common\CommonOption;
use yii\queue\JobInterface;
use yii\queue\Queue;

class TestQueueServiceJob extends BaseJob implements JobInterface
{
    public $time;

    /**
     * @param Queue $queue which pushed and is handling the job
     * @return void|mixed result of the job execution
     */
    public function execute($queue)
    {
        CommonOption::set($this->getKey(), ['time' => intval($this->time)]);
    }


    public function valid()
    {
        $result = CommonOption::get($this->getKey());
        $res = intval($result['time'] ?? 0) === intval($this->time);
        $result['done'] = $res;
        CommonOption::set($this->getKey(), $result);
        return $res;
    }

    public function getKey()
    {
        return 'test_queue_service_job_time';
    }
}
