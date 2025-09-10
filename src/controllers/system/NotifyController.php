<?php

namespace app\controllers\system;

use app\controllers\Controller;
use app\forms\api\user\login\OauthForm;
use app\forms\mall\setting\CozeForm;
use app\models\UserPlatform;

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

    public function actionGoogle()
    {
        // https://developers.google.com/identity/protocols/oauth2/web-server?hl=zh-cn
        \Yii::warning('谷歌授权回调');

        $form = new OauthForm();
        $form->attributes = \Yii::$app->request->get();
        $form->type = UserPlatform::PLATFORM_GOOGLE;
        return $form->handleNotify();
    }
}