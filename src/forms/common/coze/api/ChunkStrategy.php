<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\coze\api;

use app\forms\common\coze\Base;
use yii\web\UploadedFile;

class ChunkStrategy extends Base
{
    /** @var string 分段设置。取值包括 0：自动分段与清洗， 1：自定义 */
    public $chunk_type = 0;

    /** @var string 分段标识符。在 chunk_type=1 时必选。 */
    public $separator;

    /** @var int 最大分段长度，取值范围为 100~2000。在 chunk_type=1 时必选。 */
    public $max_tokens;

    /** @var bool 是否自动过滤连续的空格、换行符和制表符。取值包括：
        true：自动过滤
        false：（默认）不自动过滤
        在 chunk_type=1 时生效。
     */
    public $remove_extra_spaces;

    /** @var bool 是否自动过滤所有 URL 和电子邮箱地址。取值包括：
        true：自动过滤
        false：（默认）不自动过滤
        在 chunk_type=1 时生效。
     */
    public $remove_urls_emails;
}
