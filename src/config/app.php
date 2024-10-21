<?php

$local = file_exists(__DIR__ . '/local.php') ? require(__DIR__ . '/local.php') : [];
$params = file_exists(__DIR__ . '/params.php') ? require(__DIR__ . '/params.php') : [];
$db = file_exists(__DIR__ . '/db.php') ? require(__DIR__ . '/db.php') : [
    'host' => null,
    'port' => null,
    'dbname' => null,
    'username' => null,
    'password' => null,
    'tablePrefix' => null,
];

$config = [
    'id' => 'wstx_mall',
    'basePath' => dirname(__DIR__),
    'language' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
    'bootstrap' => ['log', 'queue'],
    'components' => [
        'cache' => $local['cache'] ?? [
                'class' => 'yii\caching\FileCache',
            ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=' . $db['host'] . ';port=' . $db['port'] . ';dbname=' . $db['dbname'],
            'username' => $db['username'],
            'password' => $db['password'],
            'tablePrefix' => $db['tablePrefix'],
            'charset' => 'utf8mb4',
            'attributes' => [
                // Windows 环境下貌似无效?
                // PDO::ATTR_EMULATE_PREPARES => false,
                // PDO::ATTR_STRINGIFY_FETCHES => false,
            ],
            'enableSchemaCache' => $local['enableSchemaCache'] ?? false,
            // Duration of schema cache.
            'schemaCacheDuration' => $local['schemaCacheDuration'] ?? 3600,
            // Name of the cache component used to store schema information
            'schemaCache' => $local['schemaCache'] ?? 'cache',
            'on afterOpen' => function ($event) {
                Yii::$app->db->createCommand(
                    "SET SESSION sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'"
                )->execute();
            },
        ],
        'log' => $local['log'] ?? [
                'traceLevel' => YII_DEBUG ? 3 : 0,
                'targets' => [
                    [
                        'class' => 'yii\log\FileTarget',
                        'levels' => ['error', 'warning',],
                        'logVars' => ['_GET', '_POST', '_FILES',],
                        'logFile' => "@runtime/logs/" . date('Ym') . '/' . date("d") . "/app.log",
                    ],
                ],
            ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@app/bootstrap/mail',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.qq.com',
                'port' => '465',
                'encryption' => 'ssl',
            ],
            'messageConfig' => [
                'charset' => 'UTF-8',
            ],
        ],
        'mutex' => [
            'class' => \yii\mutex\MysqlMutex::class,
        ],
        'queue' => $local['queue'] ?? [
                'class' => \yii\queue\db\Queue::class,
                'tableName' => '{{%core_queue}}',
            ],
        'serializer' => [
            'class' => '\app\bootstrap\Serializer',
        ],
        'session' => $local['session'] ?? [
                'name' => 'WS_SESSION_ID_110',
                'class' => 'yii\web\DbSession',
                'sessionTable' => '{{%core_session}}',
            ],
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
    ],
    'params' => $params,
    'modules' => [],
];
if (!$db['username']) {
    unset($config['components']['session']);
}
if (!empty($local['redis'])) {
    $config['components']['redis'] = $local['redis'];
}
return $config;
