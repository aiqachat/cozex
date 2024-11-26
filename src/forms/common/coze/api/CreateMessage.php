<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\coze\api;

use app\forms\common\coze\Base;

class CreateMessage extends Base
{
    /** @var string 发送这条消息的实体。取值：
        user：代表该条消息内容是用户发送的。
        assistant：代表该条消息内容是 智能体发送的。
     */
    public $role;

    /**
     * @var string 消息类型。默认为 question。
    question：用户输入内容。
    answer：智能体返回给用户的消息内容，支持增量返回。如果工作流绑定了消息节点，可能会存在多 answer 场景，此时可以用流式返回的结束标志来判断所有 answer 完成。
    function_call：智能体对话过程中调用函数（function call）的中间结果。
    tool_output：调用工具 （function call）后返回的结果。
    tool_response：调用工具 （function call）后返回的结果。
    follow_up：如果在 智能体上配置打开了用户问题建议开关，则会返回推荐问题相关的回复内容。不支持在请求中作为入参。
    verbose：多 answer 场景下，服务端会返回一个 verbose 包，对应的 content 为 JSON 格式，content.msg_type =generate_answer_finish 代表全部 answer 回复完成。不支持在请求中作为入参。
     */
    public $type;

    /**
     * @var string Conversation ID，即会话的唯一标识。*/
    public $conversation_id;

    /**
     * @var string 消息的内容，支持纯文本、多模态（文本、图片、文件混合输入）、卡片等多种类型的内容。
         content_type 为 object_string 时，content 为 object_string object 数组序列化之后的 JSON String，详细说明可参考 object_string object。
         当 content_type = text 时，content 为普通文本，例如 "content" :"Hello!"。
    */
    public $content;

    /**
     * @var string 消息内容类型。取值：
        text：文本。
        object_string：多模态内容，即文本和文件的组合、文本和图片的组合。
        card：卡片。此枚举值仅在接口响应中出现，不支持作为入参。
     */
    public $content_type = ContentType::TYPE_TEXT;

    /**
     * @var string 创建消息时的附加消息，获取消息时也会返回此附加消息。
     */
    public $meta_data;

    public function getMethodName()
    {
        return "/v1/conversation/message/create?conversation_id={$this->conversation_id}";
    }

    function getAttribute(): array
    {
        $params = parent::getAttribute();
        unset($params['conversation_id']);
        if(is_array($this->content) && $this->content_type == ContentType::TYPE_OBJECT){
            $params['content'] = [];
            foreach ($this->content as $item){
                if($item instanceof ContentType){
                    $params['content'][] = $item->getAttribute();
                }
            }
        }
        if(!$params['type']){
            unset($params['type']);
        }
        return $params;
    }
}
