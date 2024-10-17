<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/10/30 16:10
 */

namespace app\controllers;

use app\forms\BdCaptchaAction;

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'pic-captcha' => [
                'class' => BdCaptchaAction::class,
                'minLength' => 4,
                'maxLength' => 5,
                'padding' => 5,
                'offset' => 4,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->redirect(\Yii::$app->urlManager->createUrl(['mall/statistic/index']));
    }
}
