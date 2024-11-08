<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\knowledge;

use app\bootstrap\Pagination;
use app\bootstrap\response\ApiCode;
use app\forms\common\coze\api\DeleteDocument;
use app\forms\common\coze\api\ListDocument;
use app\forms\common\coze\ApiForm;
use app\models\Knowledge;
use app\models\KnowledgeFile;
use app\models\Model;

class FileListForm extends Model
{
    public $id;
    public $page;
    public $keyword;
    public $document_id;

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['keyword'], 'trim'],
            [['keyword', 'document_id'], 'string', 'max' => 155],
            [['page'], 'default', 'value' => 1],
        ];
    }

    public function getList()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $model = Knowledge::findOne(['id' => $this->id, 'is_delete' => 0]);
        if(!$model){
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '知识库不存在'
            ];
        }
        $req = new ListDocument();
        $req->dataset_id = $model->dataset_id;
        $req->page = (int)$this->page;
        $req->size = 10;
        $res = ApiForm::common(['object' => $req, 'account' => $model->account])->request();
        $document = [];
        $ids = [];
        foreach ($res['document_infos'] as $item){
            $item['update_time'] = mysql_timestamp ($item['update_time']);
            $item['create_time'] = mysql_timestamp ($item['create_time']);
            $item['size'] = space_unit($item['size']);
            $document[$item['document_id']] = $item;
            $ids[] = $item['document_id'];
            $model->format_type = $item['format_type'];
        }
        $model->num = $res['total'];
        $model->save();
        $pagination = new Pagination(['totalCount' => $res['total'], 'pageSize' => $req->size, 'page' => $req->page - 1]);

        $fileList = KnowledgeFile::find ()
            ->where(['knowledge_id' => $model->id])
            ->andWhere (['document_id' => $ids])
            ->all ();
        /** @var KnowledgeFile $item */
        foreach ($fileList as $item){
            if(!isset($document[$item->document_id])){
                continue;
            }
            $document[$item->document_id]['file_id'] = $item->id;
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => array_values ($document),
                'name' => $model->name,
                'format_type' => $model->format_type,
                'pagination' => $pagination
            ]
        ];
    }

    public function get()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $model = Knowledge::findOne(['id' => $this->id, 'is_delete' => 0]);
        if(!$model){
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '数据不存在'
            ];
        }
        $req = new ListDocument();
        $req->dataset_id = $model->dataset_id;
        $req->page = 1;
        $req->size = 1;
        $res = ApiForm::common(['object' => $req, 'account' => $model->account])->request();
        if(!empty($res['document_infos'][0])) {
            $model->format_type = $res['document_infos'][0]['format_type'];
            $model->save();
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功',
            'data' => [
                'data' => $model,
                'is_set' => empty($res['document_infos'])
            ]
        ];
    }

    public function del()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $model = Knowledge::findOne(['id' => $this->id, 'is_delete' => 0]);
        if(!$model){
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '数据不存在'
            ];
        }
        $req = new DeleteDocument();
        $req->document_ids = [$this->document_id];
        $res = ApiForm::common(['object' => $req, 'account' => $model->account])->request();
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功',
            'data' => $res
        ];
    }
}
