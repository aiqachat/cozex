<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\sdk;

// 即梦AI-图像生成2.1  https://www.volcengine.com/docs/85621/1537648
class CVProcess extends Basics
{
    /** @var string 服务标识 */
    public $req_key = 'jimeng_high_aes_general_v21_L';

    /** @var string 用于生成图像的提示词 ，中英文均可输入 */
    public $prompt;

    /** @var int 随机种子，作为确定扩散初始状态的基础，默认-1（随机）。若随机种子为相同正整数且其他参数均一致，则生成图片极大概率效果一致 */
    public $seed;

    /** @var int 生成图像的宽 */
    public $width;

    /** @var int 生成图像的高 */
    public $height;

    /** @var bool 开启文本扩写，会针对输入prompt进行扩写优化，如果输入prompt较短建议开启，如果输入prompt较长建议关闭 */
    public $use_pre_llm;

    /** @var bool 默认值：true；True：文生图+AIGC超分，False：文生图 */
    public $use_sr;

    /** @var bool 输出是否返回图片链接 （链接有效期为24小时） */
    public $return_url;

    function setIdent()
    {
        // TODO: Implement getSign() method.
        $this->visual();
        $this->action = 'CVProcess';
    }
}
