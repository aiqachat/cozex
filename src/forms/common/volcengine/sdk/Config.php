<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\sdk;

trait Config
{
    /** @var string 地域 */
    private $region = "";

    /** @var string 产品服务名 */
    private $service;

    /** @var string api版本号 */
    private $version;

    /** @var string 请求地址 */
    private $host = 'open.volcengineapi.com';

    /** @var string 请求方法 */
    private $method = 'POST';

    /** @var string 方法名 */
    private $action;

    public function speech(): void
    {
        $this->service = 'speech_saas_prod';
        $this->version = '2023-11-07';
        $this->region = 'cn-north-1';
    }
}