<?php
/**
 * Created by PhpStorm.
 * User: wstianxia
 * Date: 2019/7/22
 * Time: 16:54
 * @copyright: ©2021 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\controllers\mall;

use app\bootstrap\response\ApiCode;
use app\forms\common\attachment\CommonAttachment;
use app\forms\mall\attachment\AttachmentForm;

class AttachmentController extends AdminController
{
    public function actionAttachment()
    {
        if (\Yii::$app->request->isAjax) {
            $user = \Yii::$app->user->identity;
            $common = CommonAttachment::getCommon($user);
            try {
                $list = $common->getAttachmentList();
                $attachment = $common->getAttachment();
            } catch (\Exception $exception) {
                $list = [];
                $attachment = null;
            }
            $storage = $common->getStorage();
            return $this->asJson([
                'code' => ApiCode::CODE_SUCCESS,
                'data' => [
                    'list' => $list,
                    'storageTypes' => $common->getStorageType(),
                    'storage' => $attachment ? $storage[$attachment->type] : '暂无配置',
                    'nickname' => $common->user->nickname
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
            $res = $common->attachmentCreateStorage($data);
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

    public function actionCreateStorageFromAccount()
    {
        $form = new AttachmentForm();
        $form->attributes = \Yii::$app->request->post();
        return $this->asJson($form->save());
    }
}
