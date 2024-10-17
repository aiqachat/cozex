<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\coze\api;

use app\forms\common\coze\Base;
use yii\helpers\Json;

class CreateDocument extends Base
{
    /** @var string 知识库 ID。 */
    public $dataset_id;

    /** @var BasesDocument[] 上传文件的元数据信息 */
    public $document_bases;

    /** @var ChunkStrategy 分段规则，仅向某个知识库首次上传文件时必须设置，后续向此知识库上传文件时可以不传，默认沿用首次设置，且不支持修改。 */
    public $chunk_strategy;

    public function getMethodName()
    {
        return "/open_api/knowledge/document/create";
    }

    public function getAttribute(): array
    {
        $params = get_object_vars($this);
        if($this->document_bases) {
            foreach ($this->document_bases as $k => $item) {
                $params['document_bases'][$k] = $item->getAttribute();
            }
        }
        if($this->chunk_strategy){
            $params['chunk_strategy'] = $this->chunk_strategy->getAttribute();
        }
        return $params;
    }
}
