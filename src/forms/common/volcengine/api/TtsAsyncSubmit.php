<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\api;

use app\forms\common\volcengine\Base;

// https://www.volcengine.com/docs/6561/1096680
class TtsAsyncSubmit extends Base
{
    /** @var string Appid从控制台获取 */
    public $appid;

    /** @var string Request ID，不可重复，长度20～64，建议使用uuid */
    public $reqid;

    /** @var string 合成文本 */
    public $text;

    /** @var string 输出音频格式，支持pcm/wav/mp3/ogg_opus */
    public $format = 'mp3';

    /** @var string 音色，见音色列表 */
    public $voice_type;

    /** @var string 语种，与音色有关，具体值参考音色列表，默认为中文 */
    public $language;

    /** @var int 采样率，默认为24000 */
    public $sample_rate;

    /** @var float 音量，范围0.1～3，默认为1 */
    public $volume;

    /** @var float 语速，范围0.2～3，默认为1 */
    public $speed;

    /** @var float 语调，范围0.1～3，默认为1 */
    public $pitch;

    /** @var string 指定情感，“情感预测版”默认为预测值，“普通版”默认为音色默认值 */
    public $style;

    protected $version = self::TYPE_COMMON;
    const TYPE_COMMON = 1;
    const TYPE_EMOTION = 2;

    public function getMethodName()
    {
        if($this->version == self::TYPE_COMMON) {
            return "/api/v1/tts_async/submit";
        }else{
            return "/api/v1/tts_async_with_emotion/submit";
        }
    }

    public function setVersion($type){
        $this->version = $type;
    }

    public function getHeaders(){
        $header = parent::getHeaders();
        if($this->version == self::TYPE_COMMON) {
            $header['Resource-Id'] = 'volc.tts_async.default';
        }else{
            $header['Resource-Id'] = 'volc.tts_async.emotion';
        }
        return $header;
    }

    public function getParams(){
        $this->appid = $this->api->appid;
        if(!$this->reqid){
            $this->reqid = \Yii::$app->security->generateRandomString();
        }
        return parent::getParams();
    }

    public function response($response){
        if(!empty($response['task_id'])){
            return $response;
        }
        return parent::response($response);
    }
}
