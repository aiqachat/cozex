<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\coze\api;

use app\forms\common\coze\Base;
use yii\web\UploadedFile;

class BasesDocument extends Base
{
    /** @var string 文件名称 */
    public $name;

    /** @var object 文件的元数据信息 */
    public $source_info;

    /** @var object 在线网页的更新策略。默认不自动更新 */
    public $update_rule;

    public function getAttribute(): array
    {
        if($this->source_info instanceof UploadedFile){
            $this->source_info = [
                'file_base64' => base64_encode(file_get_contents($this->source_info->tempName)),
                'file_type' => $this->source_info->extension
            ];
        }else{
            throw new \Exception('文件错误');
        }
        return get_object_vars($this);
    }
}
