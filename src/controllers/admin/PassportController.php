<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\controllers\admin;

use app\bootstrap\response\ApiCode;
use app\forms\admin\ConfigForm;
use app\forms\admin\passport\PassportForm;
use app\forms\admin\passport\RegisterForm;
use app\forms\admin\passport\ResetPasswordForm;
use app\forms\admin\passport\SendRestPasswordCaptchaForm;
use app\forms\admin\SmsCaptchaForm;
use app\forms\common\CommonOption;
use app\models\AdminRegister;
use app\models\Option;
use app\models\User;

class PassportController extends AdminController
{
    public $layout = 'main';

    /**
     * 登录
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        $baseName = pathinfo(\Yii::$app->request->scriptUrl, PATHINFO_BASENAME);
        if($baseName == "index.php"){
            return '';
        }
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->isPost) {
                $form = new PassportForm();
                $form->attributes = \Yii::$app->request->post('form');
                $form->user_type = \Yii::$app->request->post('user_type');
                $form->mall_id = \Yii::$app->request->post('mall_id');
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
        \Yii::$app->user->identity->clearLogin();
        \Yii::$app->user->logout();
        return $this->asJson([
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '请求成功',
            'data' => [
                'url' => 'admin/passport/login'
            ]
        ]);
    }

    /**
     * 注册申请
     */
    public function actionRegister()
    {
        $baseName = pathinfo(\Yii::$app->request->scriptUrl, PATHINFO_BASENAME);
        if($baseName == "index.php"){
            return '';
        }
        $status = \Yii::$app->request->get('status');
        $indSetting = (new ConfigForm())->config();
        if (!isset($indSetting['open_register']) || $indSetting['open_register'] != 1
            && $status != 'forget') {
            return $this->goBack();
        }
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->isPost) {
                $form = new RegisterForm();
                $form->attributes = \Yii::$app->request->post('form');
                $res = $form->register();
                return $this->asJson($res);
            }
        } else {
            return $this->render('register');
        }
    }

    public function actionSmsCaptcha()
    {
        $form = new SmsCaptchaForm();
        $form->attributes = \Yii::$app->request->post();
        return $this->asJson($form->send());
    }

    public function actionCheckUserExists()
    {
        $username = \Yii::$app->request->post('username');
        $exists = User::find()->where([
            'username' => $username,
            'is_delete' => 0,
            'mall_id' => 0,
        ])->exists();
        if ($exists) {
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'data' => [
                    'is_exists' => true,
                ],
            ];
        }
        $exists = AdminRegister::find()->where([
            'username' => $username,
            'status' => 0,
            'is_delete' => 0,
        ])->exists();
        if ($exists) {
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'data' => [
                    'is_exists' => true,
                ],
            ];
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'is_exists' => false,
            ],
        ];
    }

    public function actionSendResetPasswordCaptcha()
    {
        $form = new SendRestPasswordCaptchaForm();
        $form->attributes = \Yii::$app->request->post();
        return $form->send();
    }

    public function actionResetPassword()
    {
        $form = new ResetPasswordForm();
        $form->attributes = \Yii::$app->request->post();
        return $form->save();
    }
}
