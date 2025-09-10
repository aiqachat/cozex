<?php
/**
 * @copyright ©2024 深圳网商天下科技
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 */

namespace app\controllers\netb;

use app\forms\mall\content\ImageForm;
use app\forms\mall\content\VideoForm;
use app\forms\mall\setting\ContentForm;

class ContentController extends AdminController
{
    public function actionSetting()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ContentForm();
            if (\Yii::$app->request->isPost) {
                $form->attributes = \Yii::$app->request->post();
                return $this->asJson($form->save());
            }else{
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson($form->get());
            }
        } else {
            return $this->render('setting');
        }
    }

    public function actionImage()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ImageForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->get());
        } else {
            return $this->render('image');
        }
    }

    public function actionImagePublic()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ImageForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->public());
        }
    }

    public function actionImageDisable()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ImageForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->disable());
        }
    }

    public function actionImageShow()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ImageForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->show());
        }
    }

    public function actionImageDel()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ImageForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->del());
        }
    }

    public function actionImageBatchDisable()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ImageForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->batchDisable());
        }
    }

    public function actionImageBatchPublic()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ImageForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->batchPublic());
        }
    }

    public function actionImageBatchDel()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ImageForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->batchDel());
        }
    }

    public function actionVideo()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new VideoForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->get());
        } else {
            return $this->render('video');
        }
    }

    public function actionVideoPublic()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new VideoForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->public());
        }
    }

    public function actionVideoDisable()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new VideoForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->disable());
        }
    }

    public function actionVideoShow()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new VideoForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->show());
        }
    }

    public function actionVideoDel()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new VideoForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->del());
        }
    }

    public function actionVideoBatchDisable()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new VideoForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->batchDisable());
        }
    }

    public function actionVideoBatchPublic()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new VideoForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->batchPublic());
        }
    }

    public function actionVideoBatchDel()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new VideoForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->batchDel());
        }
    }
}
