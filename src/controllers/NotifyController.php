<?php

namespace app\controllers;

use app\forms\mall\setting\CozeForm;

class NotifyController extends Controller
{
    public function init()
    {
        parent::init();
        $this->enableCsrfValidation = false;
    }

    public function actionCoze()
    {
        \Yii::warning('coze授权回调');
        try {
            $form = new CozeForm();
            $form->attributes = \Yii::$app->request->get();
            $res = $form->handleNotify();
            if(is_array ($res)) {
                return $this->render ('index', [
                    'message' => $res['msg'],
                    'jumpUrl' => $res['url'],
                ]);
            }else{
                return $res;
            }
        } catch (\Exception $exception) {
            \Yii::error($exception);
            exit;
        }
    }
}