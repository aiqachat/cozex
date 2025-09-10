<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\sdk;

class CVSync2AsyncSubmitTask extends Basics
{
    /**
     * 即梦AI-视频生成3.0 Pro  https://www.volcengine.com/docs/85621/1777001
     */
    const V_GEN_SERVICE = 'jimeng_ti2v_v30_pro';

    // 即梦AI-视频生成3.0 720P  https://www.volcengine.com/docs/85621/1792710
    const V_GEN_1_SERVICE = 'jimeng_t2v_v30';

    // 即梦AI-视频生成3.0 1080P  https://www.volcengine.com/docs/85621/1792711
    const V_GEN_2_SERVICE = 'jimeng_t2v_v30_1080p';

    /**
     * 即梦AI-文生图3.1 https://www.volcengine.com/docs/85621/1756900
     */
    const TI_GEN_SERVICE = 'jimeng_t2i_v31';

    /**
     * 即梦AI-图生图 https://www.volcengine.com/docs/85128/1602254
     */
    const IMAGE_G_SERVICE = 'jimeng_i2i_v30';

    /** @var string 服务标识 */
    public $req_key;

    /** @var string 生成视频的的提示词，支持中英文，150字符以内 */
    public $prompt;

    /** @var int 随机种子，作为确定扩散初始状态的基础，默认-1（随机）。若随机种子为相同正整数且其他参数均一致，则生成图片极大概率效果一致 */
    public $seed;

    /** @var int 生成的总帧数（帧数 = 24 * n + 1，其中n为秒数，支持5s、10s） */
    public $frames;

    /** @var string 生成视频的尺寸 */
    public $aspect_ratio;

    /** @var array 图片Base64数组 */
    public $binary_data_base64;

    /** @var array 图片链接数组 */
    public $image_urls;

    /** @var float 文本描述影响的程度，该值越大代表文本描述影响程度越大，且输入图片影响程度越小，默认值：0.5，取值范围：[0, 1] */
    public $scale;

    /** @var int 生成图像的宽 */
    public $width;

    /** @var int 生成图像的高 */
    public $height;

    /** @var bool 开启文本扩写，会针对输入prompt进行扩写优化，如果输入prompt较短建议开启，如果输入prompt较长建议关闭 */
    public $use_pre_llm;

    function setIdent()
    {
        // TODO: Implement getSign() method.
        $this->visual();
        $this->action = 'CVSync2AsyncSubmitTask';
    }
}
