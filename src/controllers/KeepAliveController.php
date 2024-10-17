<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2019/1/29 16:46
 */


namespace app\controllers;

use app\controllers\mall\behaviors\LoginFilter;

class KeepAliveController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'loginFilter' => [
                'class' => LoginFilter::class,
            ],
        ]);
    }

    public function actionIndex()
    {
    }
}
