<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/12/5 16:13
 */


namespace app\controllers\api;

use app\controllers\api\filters\LoginFilter;
use app\forms\api\visual\ArkImgForm;
use app\forms\api\visual\ArkImgToImgForm;
use app\forms\api\visual\ArkVideoForm;
use app\forms\api\visual\ConfigForm;
use app\forms\api\visual\GeneratedForm;
use app\forms\api\visual\ImgForm;
use app\forms\api\visual\ImgToImgForm;
use app\forms\api\visual\VideoForm;

class VisualController extends ApiController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'login' => [
                'class' => LoginFilter::class,
            ],
        ]);
    }

    public function actionConfig()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ConfigForm();
            $form->attributes = \Yii::$app->request->get();
            return $form->config(true);
        }
    }

    public function actionGenerate()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new GeneratedForm();
            if (\Yii::$app->request->isGet) {
                $form->attributes = \Yii::$app->request->get();
                return $form->getList();
            }else{
                $form->attributes = \Yii::$app->request->post();
                return $form->generate();
            }
        }
    }

    public function actionGeneratedImages()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ImgForm();
            if (\Yii::$app->request->isGet) {
                $form->attributes = \Yii::$app->request->get();
                return $form->getList();
            }else{
                $form->attributes = \Yii::$app->request->post();
                return $form->generate();
            }
        }
    }

    public function actionImgToImgGenerate()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ImgToImgForm();
            if (\Yii::$app->request->isGet) {
                $form->attributes = \Yii::$app->request->get();
                return $form->getList();
            }else{
                $form->attributes = \Yii::$app->request->post();
                return $form->generate();
            }
        }
    }

    public function actionArkGeneratedImage()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ArkImgForm();
            if (\Yii::$app->request->isGet) {
                $form->attributes = \Yii::$app->request->get();
                return $form->getList();
            }else{
                $form->attributes = \Yii::$app->request->post();
                return $form->generate();
            }
        }
    }

    public function actionArkImgToImgGenerate()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ArkImgToImgForm();
            if (\Yii::$app->request->isGet) {
                $form->attributes = \Yii::$app->request->get();
                return $form->getList();
            }else{
                $form->attributes = \Yii::$app->request->post();
                return $form->generate();
            }
        }
    }

    public function actionGeneratedVideo()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new VideoForm();
            if (\Yii::$app->request->isGet) {
                $form->attributes = \Yii::$app->request->get();
                return $form->getList();
            }else{
                $form->attributes = \Yii::$app->request->post();
                return $form->generate();
            }
        }
    }

    public function actionArkGeneratedVideo()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ArkVideoForm();
            if (\Yii::$app->request->isGet) {
                $form->attributes = \Yii::$app->request->get();
                return $form->getList();
            }else{
                $form->attributes = \Yii::$app->request->post();
                return $form->generate();
            }
        }
    }

    public function actionDownload()
    {
        if(\Yii::$app->request->get("type") == 'video'){
            $form = new VideoForm();
        }else{
            $form = new ImgForm();
        }
        $form->attributes = \Yii::$app->request->get();
        return $this->asJson($form->download());
    }

    public function actionDelete()
    {
        if (\Yii::$app->request->isAjax) {
            if(\Yii::$app->request->get("type") == 'video'){
                $form = new VideoForm();
            }else{
                $form = new ImgForm();
            }
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson ($form->delete());
        }
    }

    public function actionPublic()
    {
        if (\Yii::$app->request->isAjax) {
            if(\Yii::$app->request->get("type") == 'video'){
                $form = new VideoForm();
            }else{
                $form = new ImgForm();
            }
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson ($form->public());
        }
    }
}
