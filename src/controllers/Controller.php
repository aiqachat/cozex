<?php
/**
 * 本项目所有web端控制器的基类
 *
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/10/30 12:00
 */

namespace app\controllers;

use yii\helpers\HtmlPurifier;

class Controller extends \yii\web\Controller
{
    public function init()
    {
        // 判断是否为https @czs
        if(isset($_SERVER["HTTP_X_FORWARDED_PROTO"]) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0) {
            $_SERVER['HTTPS'] = 1;
        }
        parent::init();
        if (\Yii::$app->request->get('_layout')) {
            $this->layout = \Yii::$app->request->get('_layout');
            $this->layout = HtmlPurifier::process($this->layout); // 过滤html @czs
        }
    }
}