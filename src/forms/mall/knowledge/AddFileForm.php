<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\knowledge;

use app\bootstrap\response\ApiCode;
use app\forms\common\coze\api\BasesDocument;
use app\forms\common\coze\api\ChunkStrategy;
use app\forms\common\coze\api\CreateDocument;
use app\forms\common\coze\ApiForm;
use app\models\Attachment;
use app\models\Knowledge;
use app\models\Model;
use yii\web\UploadedFile;

class AddFileForm extends Model
{
    public $id;
    public $files;
    public $handle_rule;
    public $max_length;
    public $type;
    public $separator;
    public $separator_custom;

    public function rules()
    {
        return [
            [['id', 'max_length'], 'integer'],
            [['type', 'separator', 'separator_custom'], 'string'],
            [['files', 'handle_rule'], 'safe'],
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        try {
            $model = Knowledge::findOne(['id' => $this->id, 'is_delete' => 0]);
            if (!$model || !$model->account) {
                return [
                    'code' => ApiCode::CODE_ERROR,
                    'msg' => '数据不存在'
                ];
            }
            $req = new CreateDocument();
            $req->dataset_id = $model->dataset_id;
            $req->document_bases = [];
            foreach ($this->files as $item) {
                if (empty($item['id'])) {
                    continue;
                }
                $attachment = Attachment::findOne (['id' => $item['id']]);
                if (!$attachment) {
                    continue;
                }
                $localFilePath = str_replace (
                    \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl,
                    \Yii::$app->basePath . "/web",
                    $attachment->url
                );
                $req->document_bases[] = new BasesDocument([
                    'name' => $attachment->name,
                    'source_info' => new UploadedFile([
                        'tempName' => $localFilePath,
                        'name' => $attachment->name
                    ]),
                ]);
            }
            $strategy = new ChunkStrategy();
            if($this->type == 'custom'){
                $strategy->chunk_type = 1;
                $strategy->separator = $this->separator ?: $this->separator_custom;
                $strategy->max_tokens = intval($this->max_length);
                foreach ((array)$this->handle_rule as $item){
                    if(!property_exists($strategy, $item)){
                        continue;
                    }
                    $strategy->{$item} = true;
                }
            }
            $req->chunk_strategy = $strategy;
            $res = ApiForm::common(['object' => $req, 'account' => $model->account])->request();
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'data' => $res['document_infos']
            ];
        }catch (\Exception $e){
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage()
            ];
        }
    }
}
