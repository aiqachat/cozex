<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\controllers\netb;

use app\forms\mall\member\MemberLevelForm;
use app\forms\mall\member\MemberLevelEditForm;
use app\forms\mall\member\MemberPermissionForm;
use app\forms\mall\member\MemberPermissionEditForm;

class MemberController extends AdminController
{
    public function actionLevelIndex()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new MemberLevelForm();
            $form->attributes = \Yii::$app->request->get();
            $form->attributes = \Yii::$app->request->post();
            return $this->asJson($form->getList());
        } else {
            return $this->render('level-index');
        }
    }

    public function actionLevelEdit()
    {
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->isPost) {
                $form = new MemberLevelEditForm();
                $form->attributes = \Yii::$app->request->post();
                return $form->save();
            } else {
                $form = new MemberLevelForm();
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson($form->getDetail());
            }
        } else {
            return $this->render('level-edit');
        }
    }

    public function actionLevelDelete()
    {
        $form = new MemberLevelForm();
        $form->attributes = \Yii::$app->request->post();
        $res = $form->delete();
        return $this->asJson($res);
    }

    public function actionLevelToggleStatus()
    {
        $form = new MemberLevelForm();
        $form->attributes = \Yii::$app->request->post();
        $res = $form->toggleStatus();
        return $this->asJson($res);
    }

    public function actionLevelSetDefault()
    {
        $form = new MemberLevelForm();
        $form->attributes = \Yii::$app->request->post();
        $res = $form->setDefault();
        return $this->asJson($res);
    }

    public function actionLevelSetPermissions()
    {
        $form = new MemberLevelForm();
        $form->attributes = \Yii::$app->request->post();
        $res = $form->setPermissions();
        return $this->asJson($res);
    }

    public function actionPermissionIndex()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new MemberPermissionForm();
            $form->attributes = \Yii::$app->request->post();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->getList());
        } else {
            return $this->render('permission-index');
        }
    }

    public function actionPermissionAll()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new MemberPermissionForm();
            $form->attributes = \Yii::$app->request->post();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->getSelectList());
        }
    }

    public function actionPermissionEdit()
    {
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->isPost) {
                $form = new MemberPermissionEditForm();
                $form->attributes = \Yii::$app->request->post();
                return $form->save();
            } else {
                $form = new MemberPermissionForm();
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson($form->getDetail());
            }
        } else {
            return $this->render('permission-edit');
        }
    }

    public function actionPermissionDelete()
    {
        $form = new MemberPermissionForm();
        $form->attributes = \Yii::$app->request->post();
        $res = $form->delete();
        return $this->asJson($res);
    }

    public function actionPermissionToggleStatus()
    {
        $form = new MemberPermissionForm();
        $form->attributes = \Yii::$app->request->post();
        $res = $form->toggleStatus();
        return $this->asJson($res);
    }

    public function actionUPermissionpdateSort()
    {
        $form = new MemberPermissionForm();
        $form->attributes = \Yii::$app->request->post();
        $res = $form->updateSort();
        return $this->asJson($res);
    }
    
} 