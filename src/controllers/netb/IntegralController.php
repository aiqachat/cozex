<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\controllers\netb;

use app\forms\mall\integral\IndexForm;
use app\forms\mall\integral\ListForm;
use app\forms\mall\user\UserForm;

class IntegralController extends AdminController
{
    public function actionExchange()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ListForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->getList());
        } else {
            return $this->render('exchange');
        }
    }

    public function actionEdit()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new IndexForm();
            if(\Yii::$app->request->isPost){
                $form->attributes = \Yii::$app->request->post();
                return $this->asJson($form->save());
            }else{
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson($form->detail());
            }
        } else {
            return $this->render('edit');
        }
    }

    public function actionDestroy()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new IndexForm();
            $form->attributes = \Yii::$app->request->post();
            return $this->asJson($form->del());
        }
    }

    public function actionLog()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new UserForm();
            $form->attributes = \Yii::$app->request->get();
            $form->attributes = \Yii::$app->request->post();
            return $this->asJson($form->integralLog());
        } else {
            return $this->render('log');
        }
    }

    public function actionSetting()
    {
        return $this->render('setting');
    }
}
