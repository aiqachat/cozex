<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\coze\api;

use app\forms\common\coze\Base;

// https://www.coze.cn/docs/developer_guides/chat_v3
class Chat extends Base
{
    /** @var string 标识对话发生在哪一次会话中。
     */
    public $conversation_id;

    /**
     * @var string 要进行会话聊天的智能体ID。
     */
    public $bot_id;

    /**
     * @var string 标识当前与智能体的用户，由使用方自行定义、生成与维护
    */
    public $user_id;

    /**
     * @var CreateMessage[] 对话的附加信息
     */
    public $additional_messages;

    /**
     * @var bool 是否启用流式返回。
     */
    public $stream;

    /**
     * @var bool 是否保存本次对话记录。
     */
    public $auto_save_history;

    /**
     * @var string 附加信息，通常用于封装一些业务相关的字段。查看对话消息详情时，系统会透传此附加信息。
     */
    public $meta_data;

    public function getMethodName()
    {
        $uri = "/v3/chat";
        if($this->conversation_id){
            $uri .= "?conversation_id={$this->conversation_id}";
        }
        return $uri;
    }

    function getAttribute(): array
    {
        $params = parent::getAttribute();
        unset($params['conversation_id']);
        if(is_array($this->additional_messages)){
            foreach ($this->additional_messages as $k => $item){
                if($item instanceof CreateMessage){
                    $params['additional_messages'][$k] = $item->getAttribute();
                }
            }
        }elseif($params['additional_messages'] instanceof CreateMessage){
            $params['additional_messages'] = [$params['additional_messages']->getAttribute()];
        }
        return $params;
    }
}
