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
        $baseName = pathinfo(\Yii::$app->request->scriptUrl, PATHINFO_BASENAME);
        if($baseName == "index.php"){
            return '';
        }
        return $this->redirect(\Yii::$app->urlManager->createUrl(['admin/index/index']));
    }

    public function actionQrCode()
    {
        $url = urldecode(\Yii::$app->request->get("url"));
        \QRcode::png($url);
    }
}
