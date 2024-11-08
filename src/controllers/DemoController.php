<?php
/**
 * @copyright ©2024 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 */

namespace app\controllers;

class DemoController extends Controller
{
    public function actionIndex($testCode = 200)
    {

        echo "<pre>";print_r($sql ?? '');die();
    }
}
