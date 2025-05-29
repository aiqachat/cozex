<?php
/**
 * @copyright ©2024 深圳网商天下科技
 * author: chenzs
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/11/8 18:11
 */

namespace app\controllers\netb;

use app\forms\mall\volcengine\BatchDataForm;
use app\forms\mall\volcengine\ListForm;
use app\forms\mall\volcengine\SoundForm;
use app\forms\mall\volcengine\SoundReprintForm;
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
                return $this->asJson ($form->getList ((new SubtitleForm())->ata));
            }else{
                $form = new SubtitleForm();
                $form->attributes = \Yii::$app->request->post();
                $form->type = $form->ata;
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
                return $this->asJson ($form->getNew((new SubtitleForm())->vc));
            }else{
                $form = new SubtitleForm();
                $form->attributes = \Yii::$app->request->post();
                $form->type = $form->vc;
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
                return $this->asJson ($form->getList((new SubtitleForm())->vc));
            }else{
                $form = new SubtitleForm();
                $form->attributes = \Yii::$app->request->post();
                $form->type = $form->vc;
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
                return $this->asJson ($form->getList((new SubtitleForm())->auc));
            }else{
                $form = new SubtitleForm();
                $form->attributes = \Yii::$app->request->post();
                $form->type = $form->auc;
                return $this->asJson ($form->save());
            }
        } else {
            return $this->render('auc');
        }
    }

    public function actionAucModel()
    {
        if (\Yii::$app->request->isAjax) {
            if(\Yii::$app->request->isGet) {
                $form = new ListForm();
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson ($form->getNew((new SubtitleForm())->auc));
            }
        } else {
            if(\Yii::$app->request->isPost){
                $form = new SubtitleForm();
                $form->attributes = \Yii::$app->request->post();
                $form->type = $form->auc;
                return $this->asJson ($form->newSave());
            }
            return $this->render('auc-model');
        }
    }

    /**
     * 字幕下载，支持多种格式（txt, srt, vtt, lrc）
     */
    public function actionDownload()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new SubtitleForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->download());
        }
    }

    public function actionTtsModel()
    {
        if (\Yii::$app->request->isAjax) {
            if(\Yii::$app->request->isGet) {
                $form = new ListForm();
                $form->attributes = \Yii::$app->request->get();
                $form->page_size = 5;
                return $this->asJson ($form->getList((new SpeechForm())->ttsBig));
            }else{
                $form = new SpeechForm();
                $form->attributes = \Yii::$app->request->post();
                $form->type = $form->ttsBig;
                return $this->asJson ($form->save());
            }
        } else {
            return $this->render('tts-model');
        }
    }

    public function actionTtsMega()
    {
        if (\Yii::$app->request->isAjax) {
            if(\Yii::$app->request->isGet) {
                $form = new SoundForm();
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson($form->get());
            }else{
                $form = new SoundReprintForm();
                $form->attributes = \Yii::$app->request->post();
                return $this->asJson ($form->save());
            }
        } else {
            return $this->render('tts-mega');
        }
    }

    public function actionBatch()
    {
        $form = new BatchDataForm();
        $form->attributes = \Yii::$app->request->post();
        $form->attributes = \Yii::$app->request->get();
        return $form->save();
    }

    public function actionTtsMegaGenerate()
    {
        if (\Yii::$app->request->isAjax) {
            if(\Yii::$app->request->isGet) {
                $form = new ListForm();
                $form->attributes = \Yii::$app->request->get();
                $form->page_size = 5;
                return $this->asJson ($form->getList((new SpeechForm())->ttsMega));
            }else {
                $form = new SpeechForm();
                $form->attributes = \Yii::$app->request->post ();
                $form->type = $form->ttsMega;
                return $this->asJson ($form->save ());
            }
        }
    }

    public function actionTtsLongText()
    {
        if (\Yii::$app->request->isAjax) {
            if(\Yii::$app->request->isGet) {
                $form = new ListForm();
                $form->attributes = \Yii::$app->request->get();
                $form->page_size = 5;
                return $this->asJson ($form->getList((new SpeechForm())->ttsLong));
            }else{
                $form = new SpeechForm();
                $form->attributes = \Yii::$app->request->post();
                $form->type = $form->ttsLong;
                return $this->asJson ($form->save());
            }
        } else {
            return $this->render('tts-long-text');
        }
    }

    public function actionTts()
    {
        if (\Yii::$app->request->isAjax) {
            if(\Yii::$app->request->isGet) {
                $form = new ListForm();
                $form->attributes = \Yii::$app->request->get();
                $form->page_size = 5;
                return $this->asJson ($form->getList((new SpeechForm())->tts));
            }else{
                $form = new SpeechForm();
                $form->attributes = \Yii::$app->request->post();
                $form->type = $form->tts;
                return $this->asJson ($form->save());
            }
        } else {
            return $this->render('tts');
        }
    }

    public function actionRecord()
    {
        if (\Yii::$app->request->isAjax) {
            if(\Yii::$app->request->isGet) {
                $form = new ListForm();
                $form->attributes = \Yii::$app->request->get();
                $type = \Yii::$app->request->get('type');
                $obj = new SpeechForm();
                return $this->asJson ($form->getList($type ?: [
                    $obj->ttsBig,
                    $obj->ttsLong,
                    $obj->ttsMega
                ]));
            }else{
                $form = new SpeechForm();
                $form->attributes = \Yii::$app->request->post();
                return $this->asJson ($form->save());
            }
        } else {
            return $this->render('record');
        }
    }

    public function actionOne()
    {
        return $this->render('record');
    }

    public function actionTwo()
    {
        return $this->render('record');
    }

    public function actionThree()
    {
        return $this->render('record');
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
