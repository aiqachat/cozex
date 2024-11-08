<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\api;

use app\forms\common\volcengine\Base;

// https://www.volcengine.com/docs/6561/80909
class VcSubmit extends Base
{
    /** @var string 音视频地址 */
    public $url;

    /** @var int 每行最多展示字数 */
    public $words_per_line;

    /** @var int 每屏最多展示行数 */
    public $max_lines;

    /** @var bool 是否使用数字转换功能；
            默认关闭（False）。
            如果设置为开启（True），会将识别结果中的中文数字自动转成阿拉伯数字。
     */
    public $use_itn;

    /** @var string 字幕语言类型 */
    public $language;

    /** @var string 字幕识别类型；
            默认值为auto(同时识别说话和唱歌部分) 。
            可以选择speech(只识别说话部分)，
            可以选择singing(只识别唱歌部分)。
     */
    public $caption_type;

    /** @var bool 增加标点，默认False, 如果设置为True，则会将识别结果中增加标点符号。当且仅当(caption_type=speech的时候生效) */
    public $use_punc;

    /** @var bool 使用顺滑标注水词；
    默认 False，如果设置为 True，则会在返回的 utterances 里增加 text 为空的静音句子，其 attribute 的 event 是 silent。且 words 中可能需要被顺滑的词会被标注出来，如"extra": { "smoothed": "repeat" }，smoothed 的值可能为 repeat（重复词）或 filler（口水词）。
     */
    public $use_ddc;

    /** @var bool 返回说话人信息；默认 False，如果设置为 True，则会在 utterance 和 workd 的 attribute 中增加 speaker 信息如"attribute": {"speaker": "1"} */
    public $with_speaker_info;

    public function getMethodName()
    {
        $get = parent::getParams();
        $get['appid'] = $this->api->appid;
        $get['max_lines'] = intval($this->max_lines);
        $get['words_per_line'] = intval($this->words_per_line);
        unset($get['url'], $get['use_ddc'], $get['use_punc'], $get['use_itn']);
        $url = "/api/v1/vc/submit?" . http_build_query($get);
        if($this->use_itn){
            $url .= "&use_itn=True";
        }
        if($this->use_punc){
            $url .= "&use_punc=True";
        }
        if($this->use_ddc){
            $url .= "&use_ddc=True";
        }
        return $url;
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
