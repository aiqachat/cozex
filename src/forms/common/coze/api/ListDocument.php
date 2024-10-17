<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\coze\api;

use app\forms\common\coze\Base;

class ListDocument extends Base
{
    /** @var string 查看文件的知识库 ID。 */
    public $dataset_id;

    /** @var integer 分页查询时的页码。默认为 1 */
    public $page;

    /** @var integer 分页大小。默认为 10 */
    public $size;

    public function getMethodName()
    {
        return "/open_api/knowledge/document/list";
    }
}
