<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\api;

use app\forms\common\volcengine\Base;

// https://www.volcengine.com/docs/6561/1257584
class TtsGenerate extends Base
{
    const ONE = 'volcano_tts'; // 语音合成
    const TWO = 'volcano_mega'; // 语音复刻  https://www.volcengine.com/docs/6561/1305191

    /** @var string 业务集群 */
    public $cluster = self::ONE;

    /** @var string 用户标识 */
    public $uid;

    /** @var string 文本 */
    public $text;

    /** @var string 操作，query（非流式，http只能query） / submit（流式） */
    public $operation = 'query';

    /** @var string 文本类型  使用ssml时需要指定，值为"ssml" */
    public $text_type;

    /** @var string 音频编码格式，wav / pcm / ogg_opus / mp3，默认为 pcm  注意：wav 不支持流式 */
    public $encoding = 'mp3';

    /** @var string 音色，见音色列表 */
    public $voice_type;

    /** @var string 音色情感 */
    public $emotion;

    /** @var float 情绪值设置 */
    public $emotion_scale;

    /** @var boolean 开启音色情感 */
    public $enable_emotion;

    /** @var float 语速，[0.8,2]，默认为1，通常保留一位小数即可 */
    public $speed_ratio = 1;

    /** @var string 请求标识  需要保证每次调用传入值唯一，建议使用 UUID */
    public $reqid;

    /** @var int 音频采样率 */
    public $rate;

    /** @var int 比特率 */
    public $bitrate;

    /** @var float 音量调节 */
    public $loudness_ratio;

    /** @var string 语言类型 */
    public $language;

    public function getMethodName()
    {
        return "/api/v1/tts";
    }

    public function getParams(){
        $param = [
            'app' => [
                'appid' => $this->api->appid,
                'token' => $this->api->token,
                'cluster' => $this->cluster,
            ],
            'user' => [
                'uid' => $this->uid ?: "cozex-".date("Ymd"),
            ],
            'audio' => [
                'voice_type' => $this->voice_type,
                'encoding' => $this->encoding,
                'speed_ratio' => $this->speed_ratio,
            ],
            'request' => [
                'reqid' => $this->reqid ?: \Yii::$app->security->generateRandomString(),
                'text' => $this->text,
                'operation' => $this->operation
            ],
        ];
        if($this->emotion){
            $param['audio']['emotion'] = $this->emotion;
        }
        if($this->language){
            $param['audio']['language'] = $this->language;
        }
        if($this->enable_emotion){
            $param['audio'] = array_merge($param['audio'], [
                'emotion' => $this->emotion,
                'emotion_scale' => $this->emotion_scale,
                'enable_emotion' => true,
            ]);
        }
        if($this->rate){
            $param['audio']['rate'] = $this->rate;
        }
        if($this->bitrate){
            $param['audio']['bitrate'] = $this->bitrate;
        }
        if($this->loudness_ratio){
            $param['audio']['loudness_ratio'] = $this->loudness_ratio;
        }
        return $param;
    }

    public function response($response){
        if(isset($response['code']) && $response['code'] == 3000 && $response['message'] == 'Success'){
            return $response;
        }
        $this->errorMsg($response);
    }
}
