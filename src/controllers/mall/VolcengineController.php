<?php
/**
 * @copyright ©2024 深圳网商天下科技
 * author: chenzs
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/11/8 18:11
 */

namespace app\controllers\mall;

use app\forms\mall\index\IndexForm;
use app\forms\mall\volcengine\ListForm;
use app\forms\mall\volcengine\SpeechForm;
use app\forms\mall\volcengine\SubtitleForm;

class VolcengineController extends AdminController
{
    public function actionTitling()
    {
        if (\Yii::$app->request->isAjax) {
            if(\Yii::$app->request->isGet) {
                $form = new ListForm();
                $form->attributes = \Yii::$app->request->get ();
                return $this->asJson ($form->getList (SubtitleForm::TYPE_ATA));
            }else{
                $form = new SubtitleForm();
                $form->attributes = \Yii::$app->request->post();
                $form->type = SubtitleForm::TYPE_ATA;
                return $this->asJson ($form->save());
            }
        } else {
            return $this->render('titling');
        }
    }

    public function actionVc()
    {
        if (\Yii::$app->request->isAjax) {
            if(\Yii::$app->request->isGet) {
                $form = new ListForm();
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson ($form->getNew(SubtitleForm::TYPE_VC));
            }else{
                $form = new SubtitleForm();
                $form->attributes = \Yii::$app->request->post();
                $form->type = SubtitleForm::TYPE_VC;
                return $this->asJson ($form->save());
            }
        } else {
            return $this->render('vc');
        }
    }

    public function actionGenerate()
    {
        if (\Yii::$app->request->isAjax) {
            if(\Yii::$app->request->isGet) {
                $form = new ListForm();
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson ($form->getList(SubtitleForm::TYPE_VC));
            }else{
                $form = new SubtitleForm();
                $form->attributes = \Yii::$app->request->post();
                $form->type = SubtitleForm::TYPE_VC;
                return $this->asJson ($form->save());
            }
        } else {
            return $this->render('generate');
        }
    }

    public function actionAuc()
    {
        if (\Yii::$app->request->isAjax) {
            if(\Yii::$app->request->isGet) {
                $form = new ListForm();
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson ($form->getList(SubtitleForm::TYPE_AUC));
            }else{
                $form = new SubtitleForm();
                $form->attributes = \Yii::$app->request->post();
                $form->type = SubtitleForm::TYPE_AUC;
                return $this->asJson ($form->save());
            }
        } else {
            return $this->render('auc');
        }
    }

    public function actionTtsModel()
    {
        if (\Yii::$app->request->isAjax) {
            if(\Yii::$app->request->isGet) {
                $form = new ListForm();
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson ($form->getNew(SpeechForm::TYPE_TTS_1, 5));
            }else{
                $form = new SpeechForm();
                $form->attributes = \Yii::$app->request->post();
                $form->type = SpeechForm::TYPE_TTS_1;
                return $this->asJson ($form->save());
            }
        } else {
            return $this->render('tts-model');
        }
    }

    public function actionTts()
    {
        if (\Yii::$app->request->isAjax) {
            if(\Yii::$app->request->isGet) {
                $form = new ListForm();
                $form->attributes = \Yii::$app->request->get();
                $type = \Yii::$app->request->get('type');
                return $this->asJson ($form->getList($type ?: [SpeechForm::TYPE_TTS_1, SpeechForm::TYPE_TTS_2]));
            }else{
                $form = new SpeechForm();
                $form->attributes = \Yii::$app->request->post();
                return $this->asJson ($form->save());
            }
        } else {
            return $this->render('tts');
        }
    }

    public function actionDestroy()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ListForm();
            $form->scenario = "del";
            $form->attributes = \Yii::$app->request->post();
            return $this->asJson($form->del());
        }
    }

    public function actionRefresh()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ListForm();
            $form->attributes = \Yii::$app->request->post();
            return $this->asJson($form->refresh());
        }
    }
}
