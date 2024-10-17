<?php
/**
 * @copyright ©2024 深圳网商天下科技
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 */

namespace app\controllers\mall;

use app\forms\mall\statistics\DataForm;

class StatisticController extends AdminController
{
    public function actionIndex()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new DataForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->search());
        } else {
            return $this->render('index');
        }
    }
}
