<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 */

namespace app\controllers\api;

use app\controllers\api\filters\LoginFilter;
use app\forms\attachment\AttachmentForm;
use app\forms\AttachmentUploadForm;
use yii\web\UploadedFile;

class AttachmentController extends ApiController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'login' => [
                'class' => LoginFilter::class,
            ],
        ]);
    }

    public function actionList()
    {
        $form = new AttachmentForm();
        $form->attributes = \Yii::$app->request->get();
        $form->mall = \Yii::$app->mall;
        return $this->asJson($form->setForeground()->getList());
    }

    public function actionDelete()
    {
        $form = new AttachmentForm();
        $form->attributes = \Yii::$app->request->post();
        $form->mall = \Yii::$app->mall;
        return $this->asJson($form->delete());
    }

    public function actionUpload($name = 'file')
    {
        $form = new AttachmentUploadForm();
        if ($name === 'base64') {
            if ($filePath = $this->base64(\Yii::$app->request->post('database'))) {
                $form->file = AttachmentUploadForm::getInstanceFromFile($filePath);
            } else {
                return $this->asJson([
                    'code' => 1,
                    'msg' => '上传的图片有问题'
                ]);
            }
        } else {
            $form->file = UploadedFile::getInstanceByName($name);
            if (\Yii::$app->request->post('file_name') && \Yii::$app->request->post('file_name') !== 'null') {
                $form->file->name = \Yii::$app->request->post('file_name');
            }
        }
        $form->type = \Yii::$app->request->post('type', '');

        return $this->asJson($form->save());
    }

    public function base64($base64)
    {
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result)) {
            //后缀
            $type = $result[2];
            //创建文件夹，以年月日
            $res = file_uri('/web/temp/' . date('Ymd', time()) . "/");
            $newFile = $res['local_uri'];
            $newFile = $newFile . time() . ".{$type}";    //图片名以时间命名
            //保存为文件
            if (file_put_contents($newFile, base64_decode(str_replace($result[1], '', $base64)))) {
                //返回这个图片的路径
                return $newFile;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
