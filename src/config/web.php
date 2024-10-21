<?php

$local = file_exists(__DIR__ . '/local.php') ? require(__DIR__ . '/local.php') : [];
$config = require __DIR__ . '/app.php';

$config['components']['errorHandler'] = [
    'class' => 'app\bootstrap\ErrorHandler',
];
$config['components']['request'] = [
    'cookieValidationKey' => 'JZVyijtbbpYd-m02CXCXHsA9mRurIDaI',
    'parsers' => [
        'application/json' => 'yii\web\JsonParser',
    ], // 解析json数据请求 @czs
];

if (YII_ENV_DEV) {
    // Yii2 Debug模块
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'panels' => [
            'queue' => \yii\queue\debug\Panel::class,
        ],
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => $local['debugAllowedIPs'] ?? ['127.0.0.1', '::1',],
    ];

    // Yii2 gii模块（脚手架）
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => $local['giiAllowedIPs'] ?? ['127.0.0.1', '::1'],
    ];
}

return $config;
