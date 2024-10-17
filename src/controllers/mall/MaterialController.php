<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\controllers\mall;

class MaterialController extends AdminController
{
    public function actionIndex()
    {
        if (\Yii::$app->request->isAjax) {

        } else {
            return $this->render('index');
        }
    }
}