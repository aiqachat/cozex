<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2022/10/08 12:00
 */

namespace app\bootstrap;

use yii\base\InvalidConfigException;

/***
 * Class Application
 * @package app\bootstrap
 */
class WebApplication extends \yii\web\Application
{
    use Application;

    private $appIsRunning = true;

    /**
     * Application constructor.
     * @param null $config
     * @throws InvalidConfigException
     * @throws \Exception
     */
    public function __construct($config = null)
    {
        $this->setInitParams()
            ->loadDotEnv()
            ->defineConstants();

        require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

        if (!$config) {
            $config = require __DIR__ . '/../config/web.php';
        }

        parent::__construct($config);

        $this->enableObjectResponse()
            ->enableErrorReporting()
            ->loadAppLogger()
            ->loadAppHandler();
    }

    public function setDb($db)
    {
        if (\Yii::$app->has('db')) {
            \Yii::$app->db->close();
        }
        \Yii::$app->set('db', $db);
    }
}
