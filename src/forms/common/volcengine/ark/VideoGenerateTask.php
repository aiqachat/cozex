<?php

namespace app\forms\common\volcengine\ark;

class VideoGenerateTask extends Base
{
    /** @var string 视频生成任务的 ID */
    public $id;

    public function getAttribute(): array
    {
        return [];
    }

    public function getMethodName()
    {
        return "/contents/generations/tasks/{$this->id}";
    }

    function getMethod()
    {
        return self::METHOD_GET;
    }
}
