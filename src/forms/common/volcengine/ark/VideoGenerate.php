<?php

namespace app\forms\common\volcengine\ark;

class VideoGenerate extends Base
{
    /** @var string 您需要调用的模型的 ID （Model ID） */
    public $model;

    /** @var string 输入给模型的文本内容，描述期望生成的视频 */
    public $text;

    /** @var string | array 图片信息，可以是图片URL或图片Base64编码。
        图片URL：请确保图片URL可被访问。
        Base64编码：请遵循此格式data:image/{图片格式};base64,{图片Base64编码}。 */
    public $image_url;

    /** @var string 填写本次生成任务结果的回调通知地址。当视频生成任务有状态变化时，方舟将向此地址发送包含任务最新状态的回调请求。 */
    public $callback_url;

    /** @var string 视频分辨率，枚举值：480p  720p  1080p */
    public $resolution;

    /** @var string 生成视频的宽高比例 */
    public $ratio;

    /** @var int 生成视频时长，单位：秒 */
    public $duration;

    /** @var int 帧率，即一秒时间内视频画面数量 */
    public $framepersecond;

    /** @var boolean 生成视频是否包含水印 */
    public $watermark;

    /** @var int 种子整数，用于控制生成内容的随机性 */
    public $seed;

    /** @var boolean 是否固定摄像头。枚举值： */
    public $camerafixed;

    public function getAttribute(): array
    {
        if ($this->image_url) {
            if(strpos($this->model, "wan2-1-14b") !== false){
                $this->ratio = 'keep_ratio';
            }else{
                $this->ratio = 'adaptive';
            }
        }
        $params = parent::getAttribute();
        unset($params['image_url'], $params['text'], $params['callback_url'], $params['model']);
        $data = [];
        foreach ($params as $key => $item) {
            if ($item === null || $item === '') {
                continue;
            }
            if (is_bool($item)) {
                $item = $item ? 'true' : 'false';
            }
            $data[] = "--{$key} {$item}";
        }
        $content = [
            [
                'type' => 'text',
                'text' => "{$this->text} " . implode(' ', $data),
            ]
        ];
        if ($this->image_url) {
            $content[] = [
                'type' => 'image_url',
                'image_url' => ['url' => $this->image_url[0] ?? $this->image_url],
            ];
            if(count($this->image_url) > 1) {
                $content[1]['role'] = 'first_frame';
                $content[] = [
                    'type' => 'image_url',
                    'image_url' => ['url' => $this->image_url[1]],
                    'role' => 'last_frame'
                ];
            }
        }
        return [
            'model' => $this->model,
            'content' => $content,
            'callback_url' => $this->callback_url ?: null,
        ];
    }

    public function getMethodName()
    {
        return "/contents/generations/tasks";
    }
}
