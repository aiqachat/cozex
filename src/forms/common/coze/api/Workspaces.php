<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\coze\api;

use app\forms\common\coze\Base;

class Workspaces extends Base
{
    /** @var int 分页查询时的页码。默认为 1 */
    public $page_num;
    /** @var int 分页大小。默认为 20，最大为 50。 */
    public $page_size = 50;

    public function getMethodName()
    {
        return "/v1/workspaces";
    }

    function getMethod()
    {
        return self::METHOD_GET;
    }
}
