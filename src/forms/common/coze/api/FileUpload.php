<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\coze\api;

use app\forms\common\coze\Base;
use app\forms\common\MediaUtil;

class FileUpload extends Base
{
    /** @var string 文件地址 */
    public $file;

    public function getMethodName()
    {
        return "/v1/files/upload";
    }

    function getMethod()
    {
        return self::METHOD_UPLOAD;
    }

    public function getAttribute(): array
    {
        if(!file_exists($this->file) && $content = @file_get_contents($this->file)){
            $file = file_uri("/web/temp/");
            $name = @basename($this->file);
            file_put_contents($file['local_uri']. $name, $content);
            $this->file = $file['local_uri']. $name;
        }
        $media = new MediaUtil($this->file);
        return [
            'body' => $media->getStream(),
            'contentType' => $media->getContentType(),
        ];
    }
}
