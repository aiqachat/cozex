<?php

$local = file_exists(__DIR__ . '/local.php') ? require(__DIR__ . '/local.php') : [];
$config = require __DIR__ . '/app.php';
$config['controllerNamespace'] = 'app\commands';
$config['components']['request'] = [
    'class' => \app\bootstrap\ConsoleRequest::class,
];

$config['components']['log'] = [
    'traceLevel' => YII_DEBUG ? 3 : 0,
    'targets' => [
        [
            'class' => 'yii\log\FileTarget',
            'levels' => ['error', 'warning',],
            'logVars' => ['_GET', '_POST', '_FILES',],
            'logFile' => "@runtime/console_log/" . date('Ym') . '/' . date("d") . "/app.log",
        ],
    ],
];

return $config;
