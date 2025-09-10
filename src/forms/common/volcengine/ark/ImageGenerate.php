<?php

namespace app\forms\common\volcengine\ark;

// 火山方舟
class ImageGenerate extends Base
{
    /**
     * 国内文生图模型 https://www.volcengine.com/docs/82379/1541523
     */
    const CN_MODEL = 'doubao-seedream-3-0-t2i-250415';

    /**
     * https://www.volcengine.com/docs/82379/1666946  国内图生图模型
     */
    const IMAGE_G_SERVICE = 'doubao-seededit-3-0-i2i-250628';

    /**
     * 国际文生图模型  https://docs.byteplus.com/en/docs/ModelArk/1541523
     */
    const GLOBAL_MODEL = 'seedream-3-0-t2i-250415';

    /**
     * https://docs.byteplus.com/en/docs/ModelArk/1666946    国际图生图模型
     */
    const GLOBAL_IMAGE_G_SERVICE = 'seededit-3-0-i2i-250628';

    /** @var string 您需要调用的模型的 ID （Model ID） */
    public $model = self::CN_MODEL;

    /** @var string 生成图像的提示词 */
    public $prompt;

    /** @var string 需要编辑的图像 */
    public $image;

    /** @var string 生成图像的返回格式。支持以下两种取值：
    "url"：以可下载的 JPEG 图片链接形式返回；
    "b64_json"：以 Base64 编码字符串的 JSON 格式返回图像数据。 */
    public $response_format = 'b64_json';

    /** @var string 生成图像的宽高像素 */
    public $size;

    /** @var float 模型输出结果与prompt的一致程度，即生成图像的自由度；值越大，模型自由度越小，与用户输入的提示词相关性越强。取值范围：[1, 10] 之间的浮点数。 */
    public $guidance_scale;

    /** @var boolean 是否在生成的图片中添加水印 */
    public $watermark;

    /** @var int 种子整数，用于控制生成内容的随机性 */
    public $seed;

    public function getMethodName()
    {
        return "/images/generations";
    }
}
