<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\api;

// https://www.volcengine.com/docs/6561/1354868
use app\forms\common\volcengine\Base;

class AucBigModelSubmit extends Base
{
    /** @var string 用户标识 */
    public $uid;

    /** @var string 音视频地址 */
    public $url;

    /** @var string 音视频格式，例如 raw / wav / mp3 / ogg */
    public $format;

    /** @var string 音视频编码格式 */
    public $codec;

    /** @var int 采样率 */
    public $rate;

    /** @var string 模型名称，目前只有bigmodel */
    public $model_name = 'bigmodel';

    /** @var bool 启用itn，文本规范化 (ITN) 是自动语音识别 (ASR) 后处理管道的一部分。 ITN 的任务是将 ASR 模型的原始语音输出转换为书面形式，以提高文本的可读性。
    例如，“一九七零年”->“1970年”和“一百二十三美元”->“$123”。 */
    public $enable_itn = true;

    public $requestId;

    public function getMethodName()
    {
        return "/api/v3/auc/bigmodel/submit";
    }

    public function getParams(){
        $param = $this->getAttribute();
        $data = [
            'user' => ['uid' => $this->uid],
            'request' => ['model_name' => $this->model_name, 'enable_itn' => $this->enable_itn],
        ];
        unset($param['uid'], $param['model_name'], $param['enable_itn'], $param['requestId']);
        $data['audio'] = $param;
        return $data;
    }

    public function getHeaders(){
        $this->requestId = $this->requestId ?: \Yii::$app->security->generateRandomString();
        return [
            'X-Api-Request-Id' => $this->requestId,
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
        return ['id' => $this->requestId];
    }
}
