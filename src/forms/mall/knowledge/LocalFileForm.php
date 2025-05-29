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
use FontLib\Font;
use yii\web\UploadedFile;

class LocalFileForm extends Model
{
    public $id;
    public $knowledge_id;
    public $name;
    public $content;

    private $fontFile = 'hanyicuyuanti.ttf';
    private $fontFamily = 'hanyicuyuanti';

    public function rules()
    {
        return [
            [['id', 'knowledge_id'], 'integer'],
            [['name', 'content'], 'string'],
        ];
    }

    public function get()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        try {
            $fileModel = KnowledgeFile::findOne([
                'id' => $this->id,
                "knowledge_id" => $this->knowledge_id,
                'is_delete' => 0,
                'mall_id' => \Yii::$app->mall->id
            ]);
            if (!$fileModel) {
                throw new \Exception('本地在线文件不存在');
            }
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '成功',
                'data' => $fileModel
            ];
        } catch (\Exception $e) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage()
            ];
        }
    }

    private function registerFont($fontDir)
    {
        $fontPath = $fontDir . '/' . $this->fontFile;
        // 复制文件到$fontDir目录下
        copy(\Yii::$app->basePath . '/web/statics/font/' . $this->fontFile, $fontPath);
        if (!file_exists($fontPath)) {
            throw new \Exception('字体文件不存在');
        }
    }

    public function saveLocal()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        try {
            $model = Knowledge::findOne(['id' => $this->knowledge_id, 'is_delete' => 0, 'mall_id' => \Yii::$app->mall->id]);
            if (!$model || !$model->account) {
                throw new \Exception('数据不存在');
            }
            if ($this->id) {
                $fileModel = KnowledgeFile::findOne(['id' => $this->id, "knowledge_id" => $model->id]);
                if (!$fileModel) {
                    throw new \Exception('本地在线文件不存在');
                }
            } else {
                $fileModel = new KnowledgeFile();
                $fileModel->knowledge_id = $model->id;
            }
            $fileModel->mall_id = \Yii::$app->mall->id;
            $fileModel->name = $this->name;
            $fileModel->content = $this->content;
            if ($fileModel->document_id) {
                $req = new DeleteDocument();
                $req->document_ids = [$fileModel->document_id];
                ApiForm::common(['object' => $req, 'account' => $model->account])->request();
            }

            // 准备字体目录
            $fontDir = \Yii::$app->basePath . '/web/temp/font';
            @mkdir($fontDir, 0777, true);
            $this->registerFont($fontDir);

            // 配置Dompdf
            $options = new Options();
            $options->set('defaultFont', $this->fontFamily);
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isFontSubsettingEnabled', true);
            $options->set('defaultMediaType', 'screen');
            $options->set('defaultPaperSize', 'A4');
            $options->set('fontDir', $fontDir);
            $options->set('fontCache', $fontDir);
            $options->set('chroot', $fontDir);

            $dompdf = new Dompdf($options);

            // 构建HTML内容
            $html = '<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @font-face {
            font-family: "' . $this->fontFamily . '";
            src: url("' . $fontDir . '/' . $this->fontFile . '") format("truetype");
            font-weight: normal;
            font-style: normal;
        }
        * {
            font-family: "' . $this->fontFamily . '", sans-serif !important;
        }
    </style>
</head>
<body>' . $this->content . '</body>
</html>';

            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4');
            $dompdf->render();

            $res = file_uri('/web/temp/');
            $file = $res['local_uri'] . time() . ".pdf";
            file_put_contents($file, $dompdf->output());

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
            if (!$fileModel->save()) {
                throw new \Exception($this->getErrorMsg($fileModel));
            }
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '修改成功',
            ];
        } catch (\Exception $e) {
            if (isset($file)) {
                unlink($file);
            }
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage()
            ];
        }
    }
}
