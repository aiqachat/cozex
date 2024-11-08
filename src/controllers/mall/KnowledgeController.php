<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\controllers\mall;

use app\forms\mall\knowledge\AddFileForm;
use app\forms\mall\knowledge\FileListForm;
use app\forms\mall\knowledge\ListEditForm;
use app\forms\mall\knowledge\ListForm;
use app\forms\mall\knowledge\LocalFileForm;

class KnowledgeController extends AdminController
{
    public function actionIndex()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ListForm();
            $form->scenario = 'list';
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->getList());
        } else {
            return $this->render('index');
        }
    }

    public function actionEdit()
    {
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->isPost) {
                $form = new ListEditForm();
                $form->attributes = \Yii::$app->request->post();
                return $form->save();
            }
        }
    }

    public function actionDestroy()
    {
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->isPost) {
                $form = new ListForm();
                $form->attributes = \Yii::$app->request->post();
                return $form->del();
            }
        }
    }

    public function actionFileList()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new FileListForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->getList());
        } else {
            return $this->render('file');
        }
    }

    public function actionAddFile()
    {
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->isPost) {
                $form = new AddFileForm();
                $form->attributes = \Yii::$app->request->post();
                return $this->asJson ($form->save());
            }else {
                $form = new FileListForm();
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson ($form->get());
            }
        } else {
            return $this->render('add-file');
        }
    }

    public function actionUpdateFile()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new AddFileForm();
            $form->attributes = \Yii::$app->request->post();
            return $this->asJson ($form->update());
        }
    }

    public function actionAddLocal()
    {
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->isPost) {
                $form = new LocalFileForm();
                $form->attributes = \Yii::$app->request->post();
                return $this->asJson ($form->saveLocal());
            }else {
                $form = new LocalFileForm();
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson ($form->get());
            }
        } else {
            return $this->render('add-local');
        }
    }

    public function actionFileDestroy()
    {
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->isPost) {
                $form = new FileListForm();
                $form->attributes = \Yii::$app->request->post();
                return $form->del();
            }
        }
    }
}
