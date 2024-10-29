<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\coze\api;

use app\forms\common\coze\Base;

class BotsInfo extends Base
{
    /** @var string 智能体ID */
    public $bot_id;

    public function getMethodName()
    {
        return "/v1/bot/get_online_info";
    }

    function getMethod()
    {
        return self::METHOD_GET;
    }
}
