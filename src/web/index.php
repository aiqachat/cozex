<?php
/**
 * @copyright 深圳网商天下科技有限公司
 */

error_reporting(E_ALL);

// 注册 Composer 自动加载器
require(__DIR__ . '/../vendor/autoload.php');

// 创建、运行一个应用
$application = new \app\bootstrap\WebApplication();
$application->run();