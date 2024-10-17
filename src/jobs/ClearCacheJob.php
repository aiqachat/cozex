<?php


namespace app\jobs;


use yii\queue\JobInterface;
use yii\queue\Queue;

class ClearCacheJob implements JobInterface
{
    public $data;
    public $file;
    public $update;

    /**
     * @param Queue $queue which pushed and is handling the job
     * @return void|mixed result of the job execution
     */
    public function execute($queue)
    {
        if ($this->data) $this->clearData();
        if ($this->file) $this->clearFile();
        if ($this->update) $this->clearUpdate();
    }

    public function clearData()
    {
        @\Yii::$app->cache->flush();
        $this->clearDirs([
            \Yii::$app->runtimePath . '/wechat-cache',
        ]);
    }

    public function clearFile()
    {
        $this->clearDirs([
            \Yii::$app->basePath . '/web/temp',
            \Yii::$app->runtimePath . '/image',
        ]);
    }

    public function clearUpdate()
    {
        $this->clearDirs([
            \Yii::$app->runtimePath . '/plugin-package',
            \Yii::$app->runtimePath . '/update-package',
        ]);
    }

    protected function clearDirs($dirs)
    {
        foreach ($dirs as $dir) {
            if (file_exists($dir) && is_readable($dir) && is_writable($dir)) {
                @remove_dir($dir);
            }
        }
    }
}
