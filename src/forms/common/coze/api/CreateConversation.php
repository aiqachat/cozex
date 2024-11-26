<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\coze\api;

use app\forms\common\coze\Base;

class CreateConversation extends Base
{
    /**
     * @var CreateMessage[] 会话中的消息内容
     */
    public $messages;

    /**
     * @var string 创建消息时的附加消息，获取消息时也会返回此附加消息。
     */
    public $meta_data;

    public function getMethodName()
    {
        return "/v1/conversation/create";
    }

    function getAttribute(): array
    {
        $params = parent::getAttribute();
        if(is_array($this->messages)){
            foreach ($this->messages as $k => $item){
                if($item instanceof CreateMessage){
                    $params['messages'][$k] = $item->getAttribute();
                }
            }
        }elseif($params['messages'] instanceof CreateMessage){
            $params['messages'] = [$params['messages']->getAttribute()];
        }
        return $params;
    }
}
