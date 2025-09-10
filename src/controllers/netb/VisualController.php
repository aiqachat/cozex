<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/12/5 16:13
 */


namespace app\controllers\netb;

use app\forms\mall\visual\SettingForm;
use app\forms\mall\setting\ConfigForm;

class VisualController extends AdminController
{
    public function actionSetting()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new SettingForm();
            if (\Yii::$app->request->isPost) {
                $form->attributes = \Yii::$app->request->post();
                return $this->asJson($form->save());
            }else{
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson($form->get());
            }
        } else {
            $data = (new ConfigForm())->config();
            return $this->render('setting', ['data' => $data]);
        }
    }
}
