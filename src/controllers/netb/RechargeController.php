<?php
/**
* link: https://www.wegouer.com/
* copyright: Copyright (c) 2018 深圳网商天下科技有限公司
* author: wstianxia
*/

namespace app\controllers\netb;

use app\forms\mall\setting\ConfigForm;

class RechargeController extends AdminController
{
    public function actionConfig()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ConfigForm();
            $form->tab = ConfigForm::TAB_RECHARGE;
            if (\Yii::$app->request->isPost) {
                $form->formData = \Yii::$app->request->post();
                return $this->asJson($form->save());
            }else{
                return $this->asJson($form->get());
            }
        } else {
            return $this->render('config');
        }
    }
}
