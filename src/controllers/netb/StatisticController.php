<?php
/**
 * @copyright ©2024 深圳网商天下科技
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 */

namespace app\controllers\netb;

use app\forms\mall\statistics\IntegralForm;

class StatisticController extends AdminController
{
    public function actionIndex()
    {
        if (\Yii::$app->request->isAjax) {
        } else {
            return $this->render('index');
        }
    }

    public function actionIntegral()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new IntegralForm();
            $form->attributes = \Yii::$app->request->get();
            $form->attributes = \Yii::$app->request->post();
            return $this->asJson($form->search());
        } else {
            return $this->render('integral');
        }
    }
}
