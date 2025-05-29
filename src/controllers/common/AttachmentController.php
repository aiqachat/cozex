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
use app\forms\attachment\AttachmentForm;
use app\forms\attachment\GroupUpdateForm;
use app\forms\AttachmentUploadForm;
use app\models\Attachment;
use app\models\AttachmentGroup;
use app\models\Mall;
use app\models\Model;
use yii\web\UploadedFile;

class AttachmentController extends Controller
{
    private $xMall;

    /**
     * @return null|Mall
     */
    protected function getMall()
    {
        if ($this->xMall) {
            return $this->xMall;
        }
        $id = \Yii::$app->getSessionMallId();
        if (!$id) {
            $this->xMall = new Mall();
            $this->xMall->id = -1;
            return $this->xMall;
        }
        $mall = Mall::findOne(['id' => $id]);
        if (!$mall) {
            return null;
        }
        $this->xMall = $mall;
        return $this->xMall;
    }

    public function actionList()
    {
        $form = new AttachmentForm();
        $form->attributes = \Yii::$app->request->get();
        $form->mall = $this->getMall();
        return $this->asJson($form->getList());
    }

    public function actionRename()
    {
        $form = new AttachmentForm();
        $form->attributes = \Yii::$app->request->post();
        $form->mall = $this->getMall();
        return $this->asJson($form->rename());
    }

    public function actionDelete()
    {
        $form = new AttachmentForm();
        $form->attributes = \Yii::$app->request->post();
        $form->mall = $this->getMall();
        return $this->asJson($form->delete());
    }

    public function actionUpload($name = 'file')
    {
        $mall = $this->getMall();
        if ($mall) {
            \Yii::$app->setMall($mall);
        }
        $form = new AttachmentUploadForm();
        $form->attributes = \Yii::$app->request->get();
        $form->file = UploadedFile::getInstanceByName($name);
        return $this->asJson($form->save());
    }

    public function actionMove()
    {
        $mall = $this->getMall();
        if (!$mall) {
            return $this->asJson([
                'code' => ApiCode::CODE_ERROR,
                'data' => 'Mall为空，请刷新页面后重试。'
            ]);
        }
        $ids = \Yii::$app->request->post('ids');
        $attachmentGroupId = \Yii::$app->request->post('attachment_group_id');

        $attachmentGroup = AttachmentGroup::findOne([
            'id' => $attachmentGroupId,
            'mall_id' => $mall->id,
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
            'mall_id' => $mall->id,
        ]);
        return $this->asJson([
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '操作成功。',
        ]);
    }

    public function actionGroupList($type = null, $is_recycle = null)
    {
        $mall = $this->getMall();
        if (!$mall) {
            return $this->asJson([
                'code' => 0,
                'data' => [
                    'no_mall' => true,
                    'list' => [],
                ],
            ]);
        }
        $typeMap = [
            'image' => 0,
            'video' => 1,
            'file' => 2,
        ];

        $query = AttachmentGroup::find()->where([
            'mall_id' => $mall->id,
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
        $mall = $this->getMall();
        if (!$mall) {
            return $this->asJson([
                'code' => ApiCode::CODE_ERROR,
                'data' => 'Mall为空，请刷新页面后重试。'
            ]);
        }
        $typeMap = [
            'image' => 0,
            'video' => 1,
            'file' => 2,
        ];
        $form = new GroupUpdateForm();
        $form->attributes = \Yii::$app->request->post();
        $form->mall_id = $mall->id;
        $form->type = $typeMap[\Yii::$app->request->post('type', 'image')];
        return $this->asJson($form->save());
    }

    public function actionGroupDelete()
    {
        $mall = $this->getMall();
        if (!$mall) {
            return $this->asJson([
                'code' => ApiCode::CODE_ERROR,
                'data' => 'Mall为空，请刷新页面后重试。'
            ]);
        }
        $model = AttachmentGroup::findOne([
            'id' => \Yii::$app->request->post('id'),
            'mall_id' => $mall->id,
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
            'mall_id' => $mall->id,
        ]);
        return $this->asJson([
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '操作成功。',
        ]);
    }
}
