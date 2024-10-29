<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\coze\api;

use app\forms\common\coze\Base;

class ChatRetrieve extends Base
{
    /** @var string 即会话的唯一标识。
     */
    public $conversation_id;

    /**
     * @var string 即对话的唯一标识。
     */
    public $chat_id;

    public function getMethodName()
    {
        return "/v3/chat/retrieve";
    }

    function getMethod()
    {
        return self::METHOD_GET;
    }
}
