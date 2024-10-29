<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\coze\api;

use app\forms\common\coze\Base;

class ContentType extends Base
{
    /**
     * @var string 多模态消息内容类型，支持设置为：
         text：文本类型。
         file：文件类型。
         image：图片类型。
     */
    public $type;

    /**
     * @var string 文本内容。
    */
    public $text;

    /**
     * @var string 文件或图片内容的 ID。
     */
    public $file_id;

    /**
     * @var string 文件或图片内容的在线地址。必须是可公共访问的有效地址。
     */
    public $file_url;

    const TYPE_TEXT = 'text';
    const TYPE_OBJECT = 'object_string';
}
