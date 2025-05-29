<?php
/**
 * Created by IntelliJ IDEA.
 * author: wstianxia
 * Date: 2019/7/10
 * Time: 16:28
 */

namespace app\bootstrap;

use app\bootstrap\currency\Currency;
use app\bootstrap\payment\Payment;
use app\forms\common\payment\stripe\StripePay;
use app\forms\common\payment\wechat\WechatPay;
use app\forms\common\payment\Factory;
use app\forms\permission\branch\BaseBranch;
use app\forms\permission\branch\IndBranch;
use app\forms\permission\role\AdminRole;
use app\forms\permission\role\BaseRole;
use app\forms\permission\role\SuperAdminRole;
use app\forms\permission\role\UserRole;
use app\handlers\HandlerBase;
use app\handlers\HandlerRegister;
use app\models\Mall;
use app\models\UserIdentity;
use yii\queue\Queue;
use yii\redis\Connection;
use yii\web\Response;

/**
 * Trait Application
 * @package app\bootstrap
 * @property Serializer $serializer
 * @property Connection $redis
 * @property Queue $queue
 * @property Queue $queue1
 * @property string $hostInfo
 * @property string $baseUrl
 * @property BaseRole $role
 * @property BaseBranch $branch
 * @property Currency $currency
 * @property Payment $payment
 * @property WechatPay $wechatPay
 * @property StripePay $stripePay
 * @property Mall $mall
 * @property Sms $sms
 * @property int $precision 精度位数
 */
trait Application
{
    private $hostInfo;
    private $baseUrl;
    private $role;
    private $branch;
    private $currency;
    private $payment;
    private $wechatPay;
    private $stripePay;
    private $mallId;
    protected $mall;
    private $sms;

    protected function setInitParams()
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');
        return $this;
    }

    public function getSms()
    {
        if (!$this->sms) {
            $this->sms = new Sms();
        }
        return $this->sms;
    }

    public function getPrecision()
    {
        return 4;
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
                /** @var Response $response */
                $response = $event->sender;
                if (is_array($response->data) || is_object($response->data)) {
                    $response->format = Response::FORMAT_JSON;
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

    // 获取登录商城的分支版本
    public function getBranch()
    {
        if (!$this->branch) {
            $this->branch = new IndBranch();
        }
        return $this->branch;
    }

    public function getCurrency()
    {
        if ($this->currency) {
            return $this->currency;
        }
        $this->currency = new Currency();
        return $this->currency;
    }

    public function getPayment()
    {
        if ($this->payment) {
            return $this->payment;
        }
        $this->payment = new Payment();
        return $this->payment;
    }

    public function getWechatPay()
    {
        if ($this->wechatPay) {
            return $this->wechatPay;
        }
        $this->wechatPay = (new Factory())->wechatPay();
        return $this->wechatPay;
    }

    public function getStripePay()
    {
        if ($this->stripePay) {
            return $this->stripePay;
        }
        $this->stripePay = (new Factory())->stripePay();
        return $this->stripePay;
    }

    public function getMallId()
    {
        return $this->mallId;
    }

    public function setMallId($mallId)
    {
        $this->mallId = $mallId;
    }

    /**
     * @return Mall
     * @throws \Exception
     */
    public function getMall()
    {
        if (!$this->mall || !$this->mall->id) {
            throw new \Exception('mall is Null');
        }
        return $this->mall;
    }


    /**
     * @param Mall $mall
     */
    public function setMall(Mall $mall)
    {
        $this->mall = $mall;
    }

    /**
     * @return BaseRole
     * @throws \Exception
     * 获取登录用户的角色
     */
    public function getRole()
    {
        if (!$this->role) {
            if (\Yii::$app->user->isGuest) {
                throw new \Exception('用户未登录');
            }
            /* @var UserIdentity $userIdentity */
            $userIdentity = \Yii::$app->user->identity->identity;
            $config = [
                'user' => \Yii::$app->user->identity,
                'mall' => \Yii::$app->mall
            ];
            if ($userIdentity->is_super_admin == 1) {
                // 总管理员
                $this->role = new SuperAdminRole($config);
            } elseif ($userIdentity->is_admin == 1) {
                // 子管理员
                $this->role = new AdminRole($config);
            } elseif (\Yii::$app->user->identity->mall_id == \Yii::$app->mall->id) {
                // 普通用户
                $this->role = new UserRole($config);
            } else {
                throw new \Exception('未知用户权限');
            }
        }
        return $this->role;
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
