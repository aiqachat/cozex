<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\controllers\mall;

use app\forms\mall\bot\IndexForm;
use app\forms\mall\bot\ListForm;

class BotController extends AdminController
{
    public function actionIndex()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ListForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->getList());
        } else {
            return $this->render('index');
        }
    }
    public function actionSet()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new IndexForm();
            if (\Yii::$app->request->isGet) {
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson ($form->getSet());
            }else{
                $form->attributes = \Yii::$app->request->post();
                return $this->asJson ($form->saveSet());
            }
        } else {
            return $this->render('set');
        }
    }

    public function actionUse()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new IndexForm();
            $form->attributes = \Yii::$app->request->post();
            return $form->saveUse();
        }
    }
}
