<?php
/**
 * Queue服务配置测试，请勿删除
 */

namespace app\jobs;

use app\forms\common\CommonOption;
use yii\queue\JobInterface;
use yii\queue\Queue;

class TestQueueServiceJob implements JobInterface
{

    public $time;

    /**
     * @param Queue $queue which pushed and is handling the job
     * @return void|mixed result of the job execution
     */
    public function execute($queue)
    {
        CommonOption::set($this->getKey(), intval($this->time));
    }


    public function valid()
    {
        $result = CommonOption::get($this->getKey());
        return intval($result) === intval($this->time);
    }

    private function getKey()
    {
        return 'test_queue_service_job_time';
    }
}
