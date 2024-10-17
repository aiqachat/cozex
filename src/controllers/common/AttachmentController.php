<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/11/14 11:33
 */

namespace app\controllers\common;

use app\controllers\Controller;
use app\bootstrap\response\ApiCode;
use app\forms\attachment\GroupUpdateForm;
use app\forms\AttachmentUploadForm;
use app\models\Attachment;
use app\models\AttachmentGroup;
use app\models\Model;
use yii\web\UploadedFile;

class AttachmentController extends Controller
{
    public function actionList($attachment_group_id = null, $type = 'image', $is_recycle = null, $keyword = null)
    {
        $typeMap = [
            'image' => 1,
            'video' => 2,
            'file' => 3,
        ];
        $query = Attachment::find()->where([
            'is_delete' => 0,
            'type' => $typeMap[$type],
        ]);

        !is_null($is_recycle) && $query->andWhere(['is_recycle' => $is_recycle]);
        !is_null($keyword) && $query->keyword($keyword, ['like', 'name', $keyword]);
        $attachment_group_id && $query->andWhere(['attachment_group_id' => $attachment_group_id]);

        $list = $query
            ->orderBy('id DESC')
            ->page($pagination)
            ->asArray()
            ->all();

        foreach ($list as &$item) {
            $item['thumb_url'] = $item['thumb_url'] ?: $item['url'];
        }
        return $this->asJson([
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $list,
                'pagination' => $pagination,
            ],
        ]);
    }

    public function actionRename()
    {
        $post = \Yii::$app->request->post();

        $attachment = Attachment::findOne([
            'is_delete' => 0,
            'id' => $post['id'],
        ]);
        if (!$attachment) {
            throw new \Exception('数据为空');
        }
        $attachment->name = $post['name'];
        $attachment->save();
        return $this->asJson([
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '保存成功'
        ]);
    }

    public function actionDelete()
    {
        $ids = \Yii::$app->request->post('ids');
        if (!is_array($ids)) {
            return $this->asJson([
                'code' => ApiCode::CODE_ERROR,
                'msg' => '提交数据格式错误。',
            ]);
        }
        switch (\Yii::$app->request->post('type')) {
            case 1:
                $edit = ['is_recycle' => 1];
                break;
            case 2:
                $edit = ['is_recycle' => 0];
                break;
            case 3:
                $edit = ['is_delete' => 1];
                break;
            default:
                $edit = [];
                break;
        }
        Attachment::updateAll($edit, [
            'id' => $ids,
        ]);
        return $this->asJson([
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '操作成功。',
        ]);
    }

    public function actionUpload($name = 'file')
    {
        $form = new AttachmentUploadForm();
        $form->attributes = \Yii::$app->request->get();
        $form->file = UploadedFile::getInstanceByName($name);
        return $this->asJson($form->save());
    }

    public function actionMove()
    {
        $ids = \Yii::$app->request->post('ids');
        $attachmentGroupId = \Yii::$app->request->post('attachment_group_id');

        $attachmentGroup = AttachmentGroup::findOne([
            'id' => $attachmentGroupId,
            'is_delete' => 0,
        ]);
        if (!$attachmentGroup) {
            return $this->asJson([
                'code' => ApiCode::CODE_ERROR,
                'data' => '分组不存在，请刷新页面后重试。',
            ]);
        }
        Attachment::updateAll(['attachment_group_id' => $attachmentGroup->id,], [
            'id' => $ids,
        ]);
        return $this->asJson([
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '操作成功。',
        ]);
    }

    public function actionGroupList($type = null, $is_recycle = null)
    {
        $typeMap = [
            'image' => 0,
            'video' => 1,
            'file' => 2,
        ];

        $query = AttachmentGroup::find()->where([
            'is_delete' => 0,
        ]);

        is_null($type) || $query->andWhere(['type' => $typeMap[$type] ?? 0]);
        is_null($is_recycle) || $query->andWhere(['is_recycle' => $is_recycle]);

        return $this->asJson([
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $query->all(),
            ],
        ]);
    }

    public function actionGroupUpdate()
    {
        $typeMap = [
            'image' => 0,
            'video' => 1,
            'file' => 2,
        ];
        $form = new GroupUpdateForm();
        $form->attributes = \Yii::$app->request->post();
        $form->type = $typeMap[\Yii::$app->request->post('type', 'image')];
        return $this->asJson($form->save());
    }

    public function actionGroupDelete()
    {
        $model = AttachmentGroup::findOne([
            'id' => \Yii::$app->request->post('id'),
            'is_delete' => 0,
        ]);
        if (!$model) {
            return $this->asJson([
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '分组已删除。',
            ]);
        }

        switch (\Yii::$app->request->post('type')) {
            case 1:
                $edit = ['is_recycle' => 1];
                break;
            case 2:
                $edit = ['is_recycle' => 0];
                break;
            case 3:
                $edit = ['is_delete' => 1];
                break;
            default:
                throw new \Exception('TYPE 错误');
        }
        $model->attributes = $edit;
        if (!$model->save()) {
            return $this->asJson((new Model())->getErrorResponse($model));
        }

        Attachment::updateAll($edit, [
            'attachment_group_id' => $model->id,
        ]);
        return $this->asJson([
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '操作成功。',
        ]);
    }
}
