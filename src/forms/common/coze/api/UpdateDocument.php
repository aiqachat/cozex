<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\coze\api;

use app\forms\common\coze\Base;

// https://www.coze.cn/docs/developer_guides/modify_knowledge_files
class UpdateDocument extends Base
{
    /** @var string 待修改的知识库文件 ID。 */
    public $document_id;

    /** @var string 知识库文件的新名称。 */
    public $document_name;

    public function getMethodName()
    {
        return "/open_api/knowledge/document/update";
    }
}
