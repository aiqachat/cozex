<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\controllers\admin;

use app\bootstrap\response\ApiCode;
use app\controllers\behaviors\SuperAdminFilter;
use app\forms\admin\ConfigForm;
use app\forms\admin\mall\MallOverrunForm;
use app\forms\admin\MessageRemindSettingEditForm;
use app\forms\admin\MessageRemindSettingForm;
use app\forms\admin\UploadForm;
use app\forms\common\attachment\CommonAttachment;
use app\forms\QueueForm;
use yii\web\UploadedFile;

class SettingController extends AdminController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'superAdminFilter' => [
                'class' => SuperAdminFilter::class,
                'safeRoutes' => [
                    'admin/setting/small-routine',
                    'admin/setting/upload-file',
                    'admin/setting/attachment',
                    'admin/setting/attachment-create-storage',
                    'admin/setting/attachment-enable-storage',
                ]
            ],
        ]);
    }

    public function actionIndex()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ConfigForm();
            if (\Yii::$app->request->isPost) {
                $form->attributes = json_decode(\Yii::$app->request->post('setting'), true);
                return $this->asJson($form->save());
            }else{
                return $this->asJson($form->get());
            }
        } else {
            return $this->render('index');
        }
    }

    public function actionAttachment()
    {
        if (\Yii::$app->request->isAjax) {
            $user = \Yii::$app->user->identity;
            $common = CommonAttachment::getCommon($user);
            $list = $common->getAttachmentList();
            return $this->asJson([
                'code' => ApiCode::CODE_SUCCESS,
                'data' => [
                    'list' => $list,
                    'storageTypes' => $common->getStorageType()
                ]
            ]);
        } else {
            return $this->render('attachment');
        }
    }

    public function actionAttachmentCreateStorage()
    {
        try {
            $user = \Yii::$app->user->identity;
            $common = CommonAttachment::getCommon($user);
            $data = \Yii::$app->request->post();
            $common->attachmentCreateStorage($data);
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '保存成功'
            ];
        } catch (\Exception $exception) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $exception->getMessage()
            ];
        }
    }

    public function actionAttachmentEnableStorage($id)
    {
        $common = CommonAttachment::getCommon(\Yii::$app->user->identity);
        $common->attachmentEnableStorage($id);
        return $this->asJson([
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '操作成功。',
        ]);
    }

    public function actionOverrun()
    {
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->post()) {
                $form = new MallOverrunForm();
                $form->form = \Yii::$app->request->post('form');

                return $this->asJson($form->save());
            } else {
                $form = new MallOverrunForm();
                return $this->asJson($form->setting());
            }
        } else {
            return $this->render('overrun');
        }
    }

    public function actionQueueService()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new QueueForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->get());
        } else {
            return $this->render('queue-service');
        }
    }

    public function actionUploadLogo($name = 'file')
    {
        $form = new UploadForm();
        $form->file = UploadedFile::getInstanceByName($name);
        return $this->asJson($form->save());
    }

    public function actionMessageRemind()
    {
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->isPost) {
                $form = new MessageRemindSettingEditForm();
                $form->attributes = \Yii::$app->request->post('form');
                return $form->save();
            } else {
                $form = new MessageRemindSettingForm();
                return $form->search();
            }
        } else {
            return $this->render('message-remind');
        }
    }

    public function actionMessageRemindReset()
    {
        $form = new MessageRemindSettingForm();
        return $form->reset();
    }
}
