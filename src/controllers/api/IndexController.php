<?php
/**
 * @copyright ©2024 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 */

namespace app\controllers\api;

use app\controllers\api\filters\LimiterFilter;
use app\controllers\api\filters\LoginFilter;
use app\forms\api\index\ConfigForm;
use app\forms\api\index\MailForm;
use app\forms\api\index\SmsForm;
use app\forms\api\index\SquareForm;
use app\forms\permission\menu\MenusForm;

class IndexController extends ApiController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'login' => [
                'class' => LoginFilter::class,
                'ignore' => ['config', 'send-email-code', 'send-sms-code', 'square', 'download']
            ],
            'limiter' => [
                'class' => LimiterFilter::class,
                'only' => ['send-email-code', 'send-sms-code']
            ],
        ]);
    }

    public function actionConfig()
    {
        $form = new ConfigForm();
        $form->attributes = \Yii::$app->request->get();
        return $form->search();
    }

    public function actionMenu()
    {
        $form = new MenusForm();
        $form->isExist = true;
        $res = $form->getMenus('user');
        return [
            'code' => 0,
            'data' => $form->handleUserMenu($res['menus'] ?? [], true),
        ];
    }

    public function actionSendEmailCode()
    {
        $form = new MailForm();
        $form->attributes = \Yii::$app->request->get ();
        return $form->send();
    }

    public function actionSendSmsCode()
    {
        $form = new SmsForm();
        $form->attributes = \Yii::$app->request->get ();
        return $form->send();
    }

    public function actionSquare()
    {
        $form = new SquareForm();
        $form->attributes = \Yii::$app->request->get();
        return $form->get();
    }

    public function actionDownload()
    {
        $form = new SquareForm();
        $form->attributes = \Yii::$app->request->get();
        return $form->down();
    }
}
