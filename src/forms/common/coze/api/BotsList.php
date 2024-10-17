<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\coze\api;

use app\forms\common\coze\Base;

class BotsList extends Base
{
    /** @var string Bot 所在的空间的 Space ID。Space ID 是空间的唯一标识。 */
    public $space_id;

    /** @var int 分页查询时的页码。默认为 1，即从第一页数据开始返回。 */
    public $page_index;

    /** @var int 分页大小。默认为 20，即每页返回 20 条数据。 */
    public $page_size;

    public function getMethodName()
    {
        return "/v1/space/published_bots_list";
    }

    function getMethod()
    {
        return self::METHOD_GET;
    }
}
