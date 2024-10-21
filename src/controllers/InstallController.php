<?php
/**
 * Created by IntelliJ IDEA.
 * author: wstianxia
 * Date: 2019/4/15
 * Time: 15:54
 */

namespace app\controllers;

use app\forms\install\InstallForm;
use yii\web\ForbiddenHttpException;

class InstallController extends Controller
{
    public $layout = 'install';

    public function actionIndex()
    {
        if (file_exists(\Yii::$app->basePath . '/install.lock')) {
            throw new ForbiddenHttpException('禁止访问。');
        }
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->isPost) {
                $form = new InstallForm();
                $form->scenario = 'install';
                $form->attributes = \Yii::$app->request->post();
                return $form->install();
            }
        } else {
            return $this->render('index');
        }
    }

    public function actionCheck()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new InstallForm();
            $form->attributes = \Yii::$app->request->post();
            return $form->check();
        }
    }
}
