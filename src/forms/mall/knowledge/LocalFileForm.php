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
use app\forms\common\coze\api\DeleteDocument;
use app\forms\common\coze\ApiForm;
use app\models\Knowledge;
use app\models\KnowledgeFile;
use app\models\Model;
use Dompdf\Dompdf;
use Dompdf\Options;
use yii\web\UploadedFile;

class LocalFileForm extends Model
{
    public $id;
    public $knowledge_id;
    public $name;
    public $content;

    public function rules()
    {
        return [
            [['id', 'knowledge_id'], 'integer'],
            [['name', 'content'], 'string'],
        ];
    }

    public function get()
    {
        if (!$this->validate ()) {
            return $this->getErrorResponse ();
        }
        try {
            $fileModel = KnowledgeFile::findOne(['id' => $this->id, "knowledge_id" => $this->knowledge_id, 'is_delete' => 0]);
            if(!$fileModel){
                throw new \Exception('本地在线文件不存在');
            }
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '成功',
                'data' => $fileModel
            ];
        }catch (\Exception $e){
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage()
            ];
        }
    }

    public function saveLocal()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        try {
            $model = Knowledge::findOne(['id' => $this->knowledge_id, 'is_delete' => 0]);
            if (!$model || !$model->account) {
                throw new \Exception('数据不存在');
            }
            if($this->id) {
                $fileModel = KnowledgeFile::findOne (['id' => $this->id, "knowledge_id" => $model->id]);
                if(!$fileModel){
                    throw new \Exception('本地在线文件不存在');
                }
            }else{
                $fileModel = new KnowledgeFile();
                $fileModel->knowledge_id = $model->id;
            }
            $fileModel->name = $this->name;
            $fileModel->content = $this->content;
            if($fileModel->document_id){
                $req = new DeleteDocument();
                $req->document_ids = [$fileModel->document_id];
                ApiForm::common(['object' => $req, 'account' => $model->account])->request();
            }

            $option = new Options(['isRemoteEnabled' => true, 'httpContext' => [
                'ssl' => ['verify_peer' => \Yii::$app->request->isSecureConnection]
            ]]);
            $pdf = new Dompdf($option);
            $pdf->loadHtml ($this->content);
            $pdf->setPaper('A4');
            $pdf->render();

            $res = file_uri('/web/temp/');
            $file = $res['local_uri'] . time () . ".pdf";
            file_put_contents($file, $pdf->output());

            $req = new CreateDocument();
            $req->dataset_id = $model->dataset_id;
            $req->document_bases[] = new BasesDocument([
                'name' => "{$this->name}.pdf",
                'source_info' => new UploadedFile([
                    'tempName' => $file,
                    'name' => "{$this->name}.pdf",
                ]),
            ]);
            $req->chunk_strategy = new ChunkStrategy();
            $res = ApiForm::common(['object' => $req, 'account' => $model->account])->request();
            unlink ($file);

            $fileModel->document_id = $res['document_infos'][0]['document_id'];
            if(!$fileModel->save()){
                throw new \Exception($this->getErrorMsg($fileModel));
            }
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '修改成功',
            ];
        }catch (\Exception $e){
            if(isset($file)){
                unlink ($file);
            }
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage()
            ];
        }
    }
}
