<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\coze\api;

use app\forms\common\coze\Base;

// https://www.coze.cn/docs/developer_guides/list_message
class ConversationMsgList extends Base
{
    /** @var string 即会话的唯一标识。 */
    public $conversation_id;

    /** @var string 消息列表的排序方式。 desc：（默认）按创建时间倒序  asc：按创建时间正序 */
    public $order;

    /** @var string 待查看的 Chat ID。 */
    public $chat_id;

    /** @var string 查看指定位置之前的消息。默认为 0，表示不指定位置。如需向前翻页，则指定为返回结果中的 first_id。 */
    public $before_id;

    /** @var string 查看指定位置之后的消息。默认为 0，表示不指定位置。如需向后翻页，则指定为返回结果中的 last_id。 */
    public $after_id;

    /** @var int 每次查询返回的数据量。默认为 50，取值范围为 1~50。 */
    public $limit;

    public function getMethodName()
    {
        $uri = "/v1/conversation/message/list";
        if($this->conversation_id){
            $uri .= "?conversation_id={$this->conversation_id}";
        }
        return $uri;
    }
}
