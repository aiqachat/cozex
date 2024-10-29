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

    /** @var int 在线网页是否自动更新 */
    public $update_type = 0;

    /** @var int 在线网页自动更新的频率 */
    public $update_interval;

    public function getAttribute(): array
    {
        $params = get_object_vars($this);
        if($this->source_info instanceof UploadedFile){
            $params['source_info'] = [
                'file_base64' => base64_encode(file_get_contents($this->source_info->tempName)),
                'file_type' => $this->source_info->extension
            ];
        }else{
            $params['source_info'] = [
                'web_url' => $this->source_info,
                'document_source' => 1
            ];
            $update_rule = [
                'update_type' => intval($this->update_type),
                'update_interval' => $this->update_interval ? intval($this->update_interval) : null,
            ];
        }
        unset($params['update_interval'], $params['update_type']);
        if(!empty($update_rule)){
            $params['update_rule'] = $update_rule;
        }
        return $params;
    }
}
