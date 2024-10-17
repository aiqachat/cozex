<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\bootstrap\response;


class ApiCode
{
    /**
     *  状态码：成功
     */
    const CODE_SUCCESS = 0;

    /**
     * 状态码：失败
     */
    const CODE_ERROR = 1;

    /**
     * 状态码：未登录
     */
    const CODE_NOT_LOGIN = -1;
}
