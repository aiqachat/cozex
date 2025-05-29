<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms;

use Overtrue\EasySms\Message;

class MessageForm extends Message
{
    public function adminRegisterApply($data)
    {
        // setData 按占位顺序给值
        $this->setData($data);
        return $this;
    }
}