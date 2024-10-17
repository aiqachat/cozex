<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/10/30 12:00
 */

namespace app\bootstrap;

/***
 * Class Application
 * @package app\bootstrap
 */
class ConsoleApplication extends \yii\console\Application
{
    use Application;

    /**
     * Application constructor.
     * @param null $config
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     */
    public function __construct($config = null)
    {
        $this->checkEnv()
            ->loadDotEnv()
            ->defineConstants();

        require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

        if (!$config) {
            $config = require __DIR__ . '/../config/console.php';
        }

        parent::__construct($config);

        $this->enableObjectResponse()
            ->enableErrorReporting();

        $this->loadAppLogger()
            ->loadAppHandler();
    }

    /**
     * 检查服务器php环境
     * @return $this
     * @throws \Exception
     */
    protected function checkEnv()
    {
        $checkFunctions = [
            'proc_open',
            'proc_get_status',
        ];
        if (version_compare(PHP_VERSION, '7.2.0') < 0) {
            throw new \Exception('PHP版本不能小于7.2，当前PHP版本为' . PHP_VERSION);
        }
        foreach ($checkFunctions as $function) {
            if (!function_exists($function)) {
                throw new \Exception('PHP函数' . $function . '已被禁用，请先取消禁用' . $function . '函数');
            }
        }
        return $this;
    }

    public function getSession()
    {
        return \Yii::$app->session;
    }
}
