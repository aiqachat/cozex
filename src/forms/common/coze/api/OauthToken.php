<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\coze\api;

use app\forms\common\coze\Base;

class OauthToken extends Base
{
    const TYPE_TOKEN = 'authorization_code';
    const TYPE_REFRESH_TOKEN = 'refresh_token';

    public $grant_type = self::TYPE_TOKEN;

    /** @var string 授权码 */
    public $code;

    /** @var string 创建 OAuth 应用时获取的客户端 ID。  */
    public $client_id;

    /** @var string  */
    public $refresh_token;

    /** @var string 创建 OAuth 应用时指定的重定向 URL。   */
    public $redirect_uri;

    public function getMethodName()
    {
        return "/api/permission/oauth2/token";
    }

    function getMethod()
    {
        return self::METHOD_POST;
    }

    public function response($response){
        if(!empty($response['access_token'])){
            return $response;
        }
        throw new \Exception($response['error_message']);
    }
}
