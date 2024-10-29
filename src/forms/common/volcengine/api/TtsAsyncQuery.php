<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\api;

use app\forms\common\volcengine\Base;

// https://www.volcengine.com/docs/6561/1096680
class TtsAsyncQuery extends Base
{
    /** @var string Appid从控制台获取 */
    public $appid;

    /** @var string 创建合成任务时返回的task_id */
    public $task_id;

    protected $version = TtsAsyncSubmit::TYPE_COMMON;

    public function getMethodName()
    {
        if($this->version == TtsAsyncSubmit::TYPE_COMMON) {
            return "/api/v1/tts_async/query";
        }else{
            return "/api/v1/tts_async_with_emotion/query";
        }
    }

    public function setVersion($type){
        $this->version = $type;
    }

    public function getHeaders(){
        $header = parent::getHeaders();
        if($this->version == TtsAsyncSubmit::TYPE_COMMON) {
            $header['Resource-Id'] = 'volc.tts_async.default';
        }else{
            $header['Resource-Id'] = 'volc.tts_async.emotion';
        }
        return $header;
    }

    public function getParams(){
        $this->appid = $this->api->appid;
        return parent::getParams();
    }

    function getMethod()
    {
        return self::METHOD_GET;
    }

    public function response($response){
        if(!empty($response['task_id'])){
            return $response;
        }
        return parent::response($response);
    }
}
