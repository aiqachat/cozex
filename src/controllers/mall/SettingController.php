<?php
/**
 * @copyright ©2024 深圳网商天下科技
 * author: chenzs
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/11/8 18:11
 */

namespace app\controllers\mall;

use app\forms\mall\setting\CozeForm;
use app\forms\mall\setting\VolcengineForm;

class SettingController extends AdminController
{
    public function actionVolcengine()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new VolcengineForm();
            if (\Yii::$app->request->isPost) {
                $form->attributes = \Yii::$app->request->post();
                return $this->asJson($form->save());
            }else{
                return $this->asJson($form->get());
            }
        } else {
            return $this->render('volcengine');
        }
    }

    public function actionCoze()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new CozeForm();
            if (\Yii::$app->request->isPost) {
                $form->attributes = \Yii::$app->request->post();
                return $this->asJson($form->save());
            }else{
                return $this->asJson($form->get());
            }
        } else {
            return $this->render('coze');
        }
    }

    public function actionCozeDestroy()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new CozeForm();
            $form->attributes = \Yii::$app->request->post();
            return $this->asJson($form->del());
        }
    }

    public function actionVolcengineDestroy()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new VolcengineForm();
            $form->attributes = \Yii::$app->request->post();
            return $this->asJson($form->del());
        }
    }
}
