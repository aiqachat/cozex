<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * Created by IntelliJ IDEA
 */

namespace app\controllers\api;

use app\forms\api\user\login\OauthForm;
use app\forms\api\user\login\PassportForm;
use app\forms\api\user\RegisterForm;
use app\forms\api\user\ResetPasswordForm;

class PassportController extends ApiController
{
    public function actionRegister()
    {
        $form = new RegisterForm();
        $form->scenario = 'register';
        $form->attributes = \Yii::$app->request->post();
        return $this->asJson($form->register());
    }

    public function actionLogin()
    {
        $form = new PassportForm();
        $form->attributes = \Yii::$app->request->post();
        return $this->asJson($form->login());
    }

    public function actionOauthLogin()
    {
        $form = new OauthForm();
        $form->attributes = \Yii::$app->request->post();
        return $this->asJson($form->getUrl());
    }

    public function actionResetPassword()
    {
        $form = new ResetPasswordForm();
        $form->attributes = \Yii::$app->request->post();
        return $form->save();
    }

    public function actionLogout()
    {
        \Yii::$app->user->logout();
        return $this->asJson([
            'code' => 0,
            'msg' => '退出成功',
        ]);
    }
}
