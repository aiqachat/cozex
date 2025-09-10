<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\sdk;

// 视频生成-查询任务  https://www.volcengine.com/docs/85621/1538636
class CVSync2AsyncGetResult extends Basics
{
    /** @var string 服务标识 */
    public $req_key;

    /** @var string 任务ID，此字段的取值为提交任务接口的返回 */
    public $task_id;

    /** @var string json序列化后的字符串 */
    public $req_json;

    function setIdent()
    {
        // TODO: Implement getSign() method.
        $this->visual();
        $this->action = 'CVSync2AsyncGetResult';
    }
}
