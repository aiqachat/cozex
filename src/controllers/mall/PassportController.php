<?php

/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\controllers\mall;

use app\bootstrap\response\ApiCode;
use app\forms\mall\passport\PassportForm;

class PassportController extends AdminController
{
    public $layout = 'main';

    public $safeActions = ['login'];

    /**
     * 登录
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->isPost) {
                $form = new PassportForm();
                $form->attributes = \Yii::$app->request->post('form');
                $res = $form->login();
                return $this->asJson($res);
            }
        } else {
            return $this->render('login', ["key" => PassportForm::DES_KEY]); // @czs
        }
    }

    /**
     * 独立版总后台 账号注销
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        \Yii::$app->user->logout();

        return $this->asJson([
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '请求成功',
            'data' => [
                'url' => 'admin/passport/login'
            ]
        ]);
    }
}
