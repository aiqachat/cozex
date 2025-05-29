<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\controllers\netb;

use app\bootstrap\response\ApiCode;
use app\forms\mall\user\BalanceForm;
use app\forms\mall\user\IntegralForm;
use app\forms\mall\user\MenuForm;
use app\forms\mall\user\UserEditForm;
use app\forms\mall\user\UserForm;

class UserController extends AdminController
{
    public function actionIndex()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new UserForm();
            $form->attributes = \Yii::$app->request->get();
            $form->attributes = \Yii::$app->request->post();
            return $this->asJson($form->getList());
        } else {
            return $this->render('index');
        }
    }

    public function actionEdit()
    {
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->isPost) {
                $form = new UserEditForm();
                $form->attributes = \Yii::$app->request->post();
                return $form->save();
            } else {
                $form = new UserForm();
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson($form->getDetail());
            }
        } else {
            return $this->render('edit');
        }
    }

    public function actionLogout()
    {
        \Yii::$app->user->identity->clearLogin();
        $logout = \Yii::$app->user->logout();
        return $this->asJson([
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '注销成功',
        ]);
    }

    public function actionUpdatePassword()
    {
        $form = new UserForm();
        $form->attributes = \Yii::$app->request->post();
        $res = $form->updatePassword();

        return $this->asJson($res);
    }

    public function actionBalance()
    {
        if (\Yii::$app->request->isPost) {
            $form = new BalanceForm();
            $form->attributes = \Yii::$app->request->post();
            return $form->save();
        }
    }

    public function actionBalanceLog()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new UserForm();
            $form->attributes = \Yii::$app->request->get();
            $form->attributes = \Yii::$app->request->post();
            return $this->asJson($form->balanceLog());
        } else {
            return $this->render('balance-log');
        }
    }

    public function actionIntegral()
    {
        if (\Yii::$app->request->isPost) {
            $form = new IntegralForm();
            $form->attributes = \Yii::$app->request->post();
            return $this->asJson($form->save());
        }
    }

    public function actionMenu()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new MenuForm();
            if(\Yii::$app->request->isGet){
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson($form->get());
            }else{
                $form->attributes = \Yii::$app->request->post();
                return $this->asJson($form->save());
            }
        } else {
            return $this->render('menu');
        }
    }
}
