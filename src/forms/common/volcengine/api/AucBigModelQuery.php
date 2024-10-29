<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\api;

// https://www.volcengine.com/docs/6561/1354868
use app\forms\common\volcengine\Base;

class AucBigModelQuery extends Base
{
    public $id;

    public function getMethodName()
    {
        return "/api/v3/auc/bigmodel/query";
    }

    public function getParams(){
        return '{}';
    }

    public function getHeaders(){
        return [
            'X-Api-Request-Id' => $this->id,
            'X-Api-Resource-Id' => 'volc.bigasr.auc',
            'X-Api-App-Key' => $this->api->appid,
            'X-Api-Access-Key' => $this->api->token,
        ];
    }

    public function response($response){
        if(isset($response['header'])){
            return parent::response($response['header']);
        }
        if(isset($response['code'])){
            return parent::response($response);
        }
        return $response;
    }
}
