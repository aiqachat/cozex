<?php
/**
 * Created by IntelliJ IDEA.
 * author: wstianxia
 * Date: 2019/7/10
 * Time: 16:28
 */

namespace app\bootstrap;

use app\handlers\HandlerBase;
use app\handlers\HandlerRegister;
use yii\queue\Queue;
use yii\redis\Connection;
use yii\web\Response;

/**
 * Trait Application
 * @package app\bootstrap
 * @property Serializer $serializer
 * @property Connection $redis
 * @property Queue $queue
 * @property string $hostInfo
 * @property string $baseUrl
 */
trait Application
{
    private $hostInfo;
    private $baseUrl;

    protected function setInitParams()
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');
        return $this;
    }


    /**
     * Load .env file
     *
     * @return self
     */
    protected function loadDotEnv()
    {
        try {
            $dotenv = new \Dotenv\Dotenv(dirname(__DIR__));
            $dotenv->load();
        } catch (\Dotenv\Exception\InvalidPathException $ex) {
        }
        return $this;
    }

    /**
     * Define some constants
     *
     * @return self
     */
    protected function defineConstants()
    {
        $this->defineEnvConstants(['YII_DEBUG', 'YII_ENV']);
        return $this;
    }

    /**
     * Define some constants via `env()`
     *
     * @param array $names
     * @return self
     */
    protected function defineEnvConstants($names = [])
    {
        foreach ($names as $name) {
            if ((!defined($name)) && ($value = env($name))) {
                define($name, $value);
            }
        }
        return $this;
    }

    /**
     * Enable JSON response if controller returns Array or Object
     */
    protected function enableObjectResponse()
    {
        $this->response->on(
            Response::EVENT_BEFORE_SEND,
            function ($event) {
                /** @var \yii\web\Response $response */
                $response = $event->sender;
                if (is_array($response->data) || is_object($response->data)) {
                    $response->format = \yii\web\Response::FORMAT_JSON;
                }
            }
        );
        return $this;
    }

    /**
     * Enable full error reporting if using debug mode
     *
     * @return self
     */
    protected function enableErrorReporting()
    {
        if (YII_DEBUG) {
            error_reporting(E_ALL);
        } else {
            error_reporting(E_ALL ^ E_NOTICE);
        }
        return $this;
    }

    /**
     * @return $this
     */
    protected function loadAppHandler()
    {
        $register = new HandlerRegister();
        $HandlerClasses = $register->getHandlers();
        foreach ($HandlerClasses as $HandlerClass) {
            $handler = new $HandlerClass();
            if ($handler instanceof HandlerBase) {
                $handler->register();
            }
        }
        return $this;
    }

    /**
     * @return $this
     */
    protected function loadAppLogger()
    {
        return $this;
    }

    private $loadedViewComponents = [];

    /**
     * 加载vue组件
     * @param string $component 组件id
     * @param string $basePath 文件目录，默认/views/components
     * @param bool $singleLoad 只加载一次
     */
    public function loadViewComponent($component, $basePath = null, $singleLoad = true)
    {
        if (!$basePath) {
            $basePath = \Yii::$app->viewPath . '/components';
        }
        $file = "{$basePath}/{$component}.php";
        if (isset($this->loadedViewComponents[$file]) && $singleLoad) {
            return;
        }
        $this->loadedViewComponents[$file] = true;
        echo $this->getView()->renderFile($file) . "\n";
    }

    public function getHostInfo()
    {
        if ($this->hostInfo) {
            return $this->hostInfo;
        }
        return '';
    }

    public function setHostInfo($hostInfo)
    {
        $this->hostInfo = $hostInfo;
    }

    public function getBaseUrl()
    {
        if ($this->baseUrl) {
            return $this->baseUrl;
        }
        return '';
    }

    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }
}
