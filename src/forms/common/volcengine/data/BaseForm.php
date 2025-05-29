<?php
/**
 * link: https://www.netbcloud.com//
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\data;

use app\models\Model;
use Yii;

/**
 * @property $vc
 * @property $ata
 * @property $auc
 * @property $ttsBig
 * @property $ttsLong
 * @property $ttsMega
 * @property $tts
 */
class BaseForm extends Model
{
    public $repeat = '_repeat';

    private $TYPE_VC = 1; // 音视频转字幕
    private $TYPE_ATA = 2; // 音频打轴
    private $TYPE_AUC = 3; // 大模型录音文件转字幕
    private $TYPE_TTS_1 = 4; // 语音合成 - 一次性合成  依赖大模型  https://www.volcengine.com/docs/6561/1257584
    private $TYPE_TTS_2 = 5; // 语音合成 - 异步合成服务 支持10万字的长文本  https://www.volcengine.com/docs/6561/1096680
    private $TYPE_TTS_3 = 6; // 声音复刻来语音合成 - 一次性合成  https://www.volcengine.com/docs/6561/1305191
    private $TYPE_TTS_4 = 7; // 语音合成 - 普通一次性合成  支持300字  https://www.volcengine.com/docs/6561/79820

    public function getVc()
    {
        return $this->TYPE_VC;
    }

    public function getAta()
    {
        return $this->TYPE_ATA;
    }

    public function getAuc()
    {
        return $this->TYPE_AUC;
    }

    public function getTtsBig()
    {
        return $this->TYPE_TTS_1;
    }

    public function getTtsLong()
    {
        return $this->TYPE_TTS_2;
    }

    public function getTtsMega()
    {
        return $this->TYPE_TTS_3;
    }

    public function getTts()
    {
        return $this->TYPE_TTS_4;
    }

    public function text($type)
    {
        $data = [
            $this->ttsLong => Yii::t('voice', '语音合成精品长文本'),
            $this->ttsBig => Yii::t('voice', '语音大模型'),
            $this->ttsMega => Yii::t('voice', '声音复刻'),
            $this->tts => Yii::t('voice', '语音合成'),
        ];
        return $data[$type] ?? '';
    }

    public function textName($type)
    {
        $data = [
            $this->vc => Yii::t('voice', '音视频转字幕'),
            $this->ata => Yii::t('voice', '音频打轴'),
            $this->auc => Yii::t('voice', '大模型录音文件转字幕'),
            $this->ttsBig => Yii::t('voice', '大模型语音合成'),
            $this->ttsLong => Yii::t('voice', '语音合成TTS长文本'),
            $this->ttsMega => Yii::t('voice', '大模型声音复刻-火山引擎'),
            $this->tts => Yii::t('voice', '语音合成TTS短文本'),
        ];
        return $data[$type] ?? '';
    }
}
