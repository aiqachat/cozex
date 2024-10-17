<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\coze\api;

use app\forms\common\coze\Base;

class DeleteDocument extends Base
{
    /** @var array 待删除的知识库文件列表。数组最大长度为 100，即一次性最多可删除 100 个文件。 */
    public $document_ids;

    public function getMethodName()
    {
        return "/open_api/knowledge/document/delete";
    }
}
