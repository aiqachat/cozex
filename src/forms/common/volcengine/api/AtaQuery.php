<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\api;

use app\forms\common\volcengine\Base;

// https://www.volcengine.com/docs/6561/149749
class AtaQuery extends Base
{
    /** @var string 任务 ID  这里填写的是submit接口返回的id。 */
    public $id;

    /** @var string 0表示非阻塞，1表示阻塞（默认是阻塞模式） */
    public $blocking = 1;

    public function getMethodName()
    {
        return "/api/v1/vc/ata/query?appid=" . $this->api->appid;
    }

    function getMethod()
    {
        return self::METHOD_GET;
    }
}
