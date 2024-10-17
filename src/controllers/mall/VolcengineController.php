<?php
/**
 * @copyright ©2024 深圳网商天下科技
 * author: chenzs
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/11/8 18:11
 */

namespace app\controllers\mall;

use app\forms\mall\volcengine\ListForm;
use app\forms\mall\volcengine\SubtitleForm;

class VolcengineController extends AdminController
{
    public function actionTitling()
    {
        if (\Yii::$app->request->isAjax) {
            if(\Yii::$app->request->isGet) {
                $form = new ListForm();
                $form->attributes = \Yii::$app->request->get ();
                return $this->asJson ($form->getList ());
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

    public function actionGenerate()
    {
        if (\Yii::$app->request->isAjax) {
            if(\Yii::$app->request->isGet) {
                $form = new ListForm();
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson ($form->getGenerateList());
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

    public function actionDestroy()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ListForm();
            $form->scenario = "del";
            $form->attributes = \Yii::$app->request->post();
            return $this->asJson($form->del());
        }
    }
}
