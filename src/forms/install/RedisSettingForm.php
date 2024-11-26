<?php
/**
 * Created by IntelliJ IDEA.
 * author: wstianxia
 * Date: 2019/6/14
 * Time: 10:45
 */

namespace app\forms\install;


use app\bootstrap\response\ApiCode;
use app\models\Model;

class RedisSettingForm extends Model
{
    public $host;
    public $port;
    public $password;

    public function rules()
    {
        return [
            [['host', 'port', 'password',], 'trim'],
            [['host', 'port'], 'required'],
            [['port',], 'integer', 'min' => 1, 'max' => 65535],
        ];
    }

    public function attributeLabels()
    {
        return [
            'host' => 'Redis服务器',
            'port' => 'Redis端口',
        ];
    }

    public function saveSetting()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $channel = 'wstx_' . rand(100, 999);
        $localConfigFile = \Yii::$app->basePath . '/config/local.php';
        if(file_exists($localConfigFile)){
            $localConfig = require $localConfigFile;
            if(!empty($localConfig['queue']['channel'])){
                $channel = $localConfig['queue']['channel'];
            }
        }
        if ($this->password) {
            $passwordVal = "'{$this->password}'";
        } else {
            $passwordVal = "null";
        }
        $configContent = <<<EOF
<?php

return [
    'redis' => [
        'class' => 'yii\\redis\\Connection',
        'hostname' => '{$this->host}',
        'port' => {$this->port},
        'password' => {$passwordVal},
    ],
    'queue' => [
        'class' => \\yii\\queue\\redis\\Queue::class,
        'channel' => '{$channel}',
    ],
];

EOF;
        file_put_contents($localConfigFile, $configContent);
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => 'success',
        ];
    }
}
