<?php
/**
 * Created by IntelliJ IDEA.
 * author: wstianxia
 */

namespace app\controllers\mall;

use app\forms\mall\UpdateForm;

class UpdateController extends AdminController
{
    public function actionIndex()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new UpdateForm();
            if(\Yii::$app->request->isPost){
                return $form->doUpdate();
            }
        }
    }
}