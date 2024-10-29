<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\api;

// https://www.volcengine.com/docs/6561/149749
class VcQuery extends AtaQuery
{
    public function getMethodName()
    {
        return "/api/v1/vc/query?appid=" . $this->api->appid;
    }
}
