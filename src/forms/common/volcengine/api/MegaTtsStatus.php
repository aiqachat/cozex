<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\api;

use app\forms\common\volcengine\Base;

// https://www.volcengine.com/docs/6561/1305191
class MegaTtsStatus extends Base
{
    /** @var string appid */
    public $appid;

    /** @var string 唯一音色代号 */
    public $speaker_id = 'S_PNLpwIY81';

    public function getMethodName()
    {
        return "/api/v1/mega_tts/status";
    }

    public function getHeaders(){
        return [
            'Resource-Id' => 'volc.megatts.voiceclone',
        ];
    }

    public function getParams(){
        $this->appid = $this->api->appid;
        return parent::getAttribute();
    }

    public function response($response){
        if(isset($response['BaseResp']) && $response['BaseResp']['StatusCode'] == 0){
            return $response;
        }
        if(!empty($response['BaseResp']['StatusMessage'])){
            $response = ['code' => $response['BaseResp']['StatusCode'], 'message' => $response['BaseResp']['StatusMessage']];
        }
        $this->errorMsg($response);
    }
}
