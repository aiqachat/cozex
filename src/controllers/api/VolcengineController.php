<?php
/**
 * @copyright ©2024 深圳网商天下科技
 * author: chenzs
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/11/8 18:11
 */

namespace app\controllers\api;

use app\controllers\api\filters\LoginFilter;
use app\forms\api\volcengine\BatchForm;
use app\forms\api\volcengine\IndexForm;
use app\forms\api\volcengine\SoundForm;
use app\forms\api\volcengine\SoundReprintForm;
use app\forms\api\volcengine\SpeechForm;
use app\forms\api\volcengine\SubtitleForm;

class VolcengineController extends ApiController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'login' => [
                'class' => LoginFilter::class,
            ],
        ]);
    }

    public function actionIndex()
    {
        if (\Yii::$app->request->isAjax) {
            if(\Yii::$app->request->isGet) {
                $form = new IndexForm();
                $form->attributes = \Yii::$app->request->get ();
                $form->type = (new SubtitleForm())->vc;
                return $this->asJson ($form->getList ());
            }else{
                $form = new SubtitleForm();
                $form->scenario = 'save';
                $form->attributes = \Yii::$app->request->post();
                $form->type = $form->vc;
                return $this->asJson ($form->save());
            }
        }
    }

    public function actionTitling()
    {
        if (\Yii::$app->request->isAjax) {
            if(\Yii::$app->request->isGet){
                $form = new IndexForm();
                $form->attributes = \Yii::$app->request->get();
                $form->type = (new SubtitleForm())->ata;
                return $this->asJson ($form->getList());
            }else{
                $form = new SubtitleForm();
                $form->scenario = 'save';
                $form->attributes = \Yii::$app->request->post();
                $form->type = $form->ata;
                return $this->asJson ($form->save());
            }
        }
    }

    public function actionAuc()
    {
        if (\Yii::$app->request->isAjax) {
            if(\Yii::$app->request->isGet){
                $form = new IndexForm();
                $form->attributes = \Yii::$app->request->get();
                $form->type = (new SubtitleForm())->auc;
                return $this->asJson ($form->getList());
            }else{
                $form = new SubtitleForm();
                $form->attributes = \Yii::$app->request->post();
                $form->type = $form->auc;
                return $this->asJson ($form->newSave());
            }
        }
    }

    public function actionDestroy()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new IndexForm();
            $form->attributes = \Yii::$app->request->post();
            return $this->asJson ($form->del());
        }
    }

    /**
     * 字幕下载，支持多种格式（txt, srt, vtt, lrc）
     */
    public function actionDownload()
    {
        $form = new SubtitleForm();
        $form->attributes = \Yii::$app->request->get();
        return $this->asJson($form->download(false));
    }

    public function actionConfig()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new SpeechForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson ($form->config());
        }
    }

    public function actionTtsModel()
    {
        if (\Yii::$app->request->isAjax) {
            if(\Yii::$app->request->isGet){
                $form = new IndexForm();
                $form->attributes = \Yii::$app->request->get();
                $form->type = (new SubtitleForm())->ttsBig;
                return $this->asJson ($form->getList());
            }else{
                $form = new SpeechForm();
                $form->scenario = 'save';
                $form->attributes = \Yii::$app->request->post();
                $form->type = $form->ttsBig;
                return $this->asJson ($form->save());
            }
        }
    }

    public function actionTtsLong()
    {
        if (\Yii::$app->request->isAjax) {
            if(\Yii::$app->request->isGet){
                $form = new IndexForm();
                $form->attributes = \Yii::$app->request->get();
                $form->type = (new SubtitleForm())->ttsLong;
                return $this->asJson ($form->getList());
            }else{
                $form = new SpeechForm();
                $form->scenario = 'save';
                $form->attributes = \Yii::$app->request->post();
                $form->type = $form->ttsLong;
                return $this->asJson ($form->save());
            }
        }
    }

    public function actionTts()
    {
        if (\Yii::$app->request->isAjax) {
            if(\Yii::$app->request->isGet){
                $form = new IndexForm();
                $form->attributes = \Yii::$app->request->get();
                $form->type = (new SubtitleForm())->tts;
                return $this->asJson ($form->getList());
            }else{
                $form = new SpeechForm();
                $form->scenario = 'save';
                $form->attributes = \Yii::$app->request->post();
                $form->type = $form->tts;
                return $this->asJson ($form->save());
            }
        }
    }

    public function actionTtsMega()
    {
        if (\Yii::$app->request->isAjax) {
            if(\Yii::$app->request->isGet){
                $form = new SoundForm();
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson($form->get());
            }else{
                $form = new SoundReprintForm();
                $form->attributes = \Yii::$app->request->post();
                return $this->asJson ($form->save());
            }
        }
    }

    public function actionTtsMegaGenerate()
    {
        if (\Yii::$app->request->isAjax) {
            if(\Yii::$app->request->isGet) {
                $form = new IndexForm();
                $form->attributes = \Yii::$app->request->get();
                $form->type = (new SubtitleForm())->ttsMega;
                return $this->asJson ($form->getList());
            }else {
                $form = new SpeechForm();
                $form->scenario = 'save';
                $form->attributes = \Yii::$app->request->post();
                $form->type = $form->ttsMega;
                return $this->asJson ($form->save ());
            }
        }
    }

    public function actionDownloadVoice()
    {
        $form = new SpeechForm();
        $form->attributes = \Yii::$app->request->get();
        return $this->asJson($form->download());
    }

    public function actionBatch()
    {
        if(\Yii::$app->request->isPost){
            $form = new BatchForm();
            $form->attributes = \Yii::$app->request->post();
            return $form->save();
        }
    }

    public function actionRefresh()
    {
        $form = new IndexForm();
        $form->attributes = \Yii::$app->request->post();
        return $this->asJson($form->refresh());
    }
}
