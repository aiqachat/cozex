<?php
/**
 * link: https://www.wegouer.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\controllers\netb;

use app\bootstrap\response\ApiCode;
use app\forms\mall\level\LevelEditForm;
use app\forms\mall\level\LevelForm;

class LevelController extends AdminController
{
    public function actionIndex()
    {
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->isPost) {
            } else {
                $form = new LevelForm();
                $form->attributes = \Yii::$app->request->get();
                $list = $form->getList();

                return $this->asJson($list);
            }
        } else {
            return $this->render('index');
        }
    }

    /**
     * 添加、编辑
     * @return string|\yii\web\Response
     */
    public function actionEdit()
    {
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->isPost) {
                $form = new LevelEditForm();
                $form->attributes = \Yii::$app->request->post();
                $res = $form->save();

                return $this->asJson($res);
            } else {
                $form = new LevelForm();
                $form->attributes = \Yii::$app->request->get();
                $detail = $form->getDetail();

                return $this->asJson($detail);
            }
        } else {
            return $this->render('edit');
        }
    }

    /**
     * 删除
     * @return \yii\web\Response
     */
    public function actionDestroy()
    {
        $form = new LevelForm();
        $form->attributes = \Yii::$app->request->post();
        $res = $form->destroy();

        return $this->asJson($res);
    }

    public function actionSwitchStatus()
    {
        $form = new LevelForm();
        $form->attributes = \Yii::$app->request->post();
        return $this->asJson($form->switchStatus());
    }

    public function actionSetDefault()
    {
        $form = new LevelForm();
        $form->attributes = \Yii::$app->request->post();
        return $this->asJson($form->setDefault());
    }
}
