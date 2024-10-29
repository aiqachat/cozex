<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\api;

use app\forms\common\volcengine\Base;

// https://www.volcengine.com/docs/6561/149749
class AtaSubmit extends Base
{
    /** @var string 音视频地址 */
    public $url;

    /** @var string 字幕识别类型；speech（说话）或 singing（唱歌） */
    public $caption_type = 'speech';

    /** @var string 音频字幕文本；用于打轴的字幕文本 */
    public $audio_text;

    /** @var string 打轴服务标点模式；
                    默认值为'1'（省略打轴结果句级别末尾逗号句号）
                    可选'2'（省略打轴结果句级别某些标点，使用空格代替）
                    可选'3'（保留原文本完整标点）
     */
    public $sta_punc_mode;

    public function getMethodName()
    {
        $get = [
            'caption_type' => $this->caption_type,
            'audio_text' => $this->audio_text,
            'sta_punc_mode' => $this->sta_punc_mode ?: 1,
            'appid' => $this->api->appid,
        ];
        return "/api/v1/vc/ata/submit?" . http_build_query($get);
    }

    public function getHeaders(){
        if(file_exists($this->url)){
            return ['Content-Type' => 'audio/*'];
        }
        return parent::getHeaders();
    }

    public function getParams(){
        if(!file_exists($this->url)){
            return ['url' => $this->url];
        }
        return fopen($this->url, 'r');
    }
}
