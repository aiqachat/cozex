<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\api;

use app\forms\common\volcengine\Base;

// https://www.volcengine.com/docs/6561/1305191
class MegaTtsUpload extends Base
{
    /** @var string appid */
    public $appid;

    /** @var string 唯一音色代号 */
    public $speaker_id;

    /** @var string 可以让用户按照该文本念诵，服务会对比音频与该文本的差异。若差异过大会返回1109 WERError */
    public $text;

    /** @var string 二进制音频字节，需对二进制音频进行base64编码 */
    public $audio_bytes;

    /** @var string 音频格式，pcm、m4a必传，其余可选 */
    public $audio_format;

    /** @var int 固定值：2 */
    public $source = 2;

    /** @var int cn = 0 中文（默认）
        en = 1 英文
        ja = 2 日语
        es = 3 西班牙语
        id = 4 印尼语
        pt = 5 葡萄牙语
     */
    public $language;

    /** @var int 默认为0   1为2.0效果，0为1.0效果 */
    public $model_type;

    const languageList = [
        'cn' => 0,
        'en' => 1,
        'ja' => 2,
        'es' => 3,
        'id' => 4,
        'pt' => 5,
    ];

    const languages = [
        ['id' => 'cn', 'name' => '中文'],
        ['id' => 'en', 'name' => '英文'],
        ['id' => 'ja', 'name' => '日语'],
        ['id' => 'es', 'name' => '西班牙语'],
        ['id' => 'id', 'name' => '印尼语'],
        ['id' => 'pt', 'name' => '葡萄牙语'],
    ];

    public function getMethodName()
    {
        return "/api/v1/mega_tts/audio/upload";
    }

    public function getHeaders(){
        return [
            'Resource-Id' => 'volc.megatts.voiceclone',
        ];
    }

    public function getParams(){
        $this->appid = $this->api->appid;
        $params = parent::getAttribute();
        $audios = [
            'audio_bytes' => $this->audio_bytes,
            'audio_format' => $this->audio_format,
        ];
        if($this->text){
            $audios['text'] = $this->text;
        }
        $params['audios'][] = $audios;
        unset($params['audio_bytes'], $params['audio_format'], $params['text']);
        return $params;
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
