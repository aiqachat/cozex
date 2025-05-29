<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\bootstrap;

use app\forms\admin\ConfigForm;
use app\validators\PhoneNumberValidator;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
use Overtrue\EasySms\Strategies\OrderStrategy;
use yii\base\Component;

class Sms extends Component
{
    const MODULE_ADMIN = 'admin';
    const MODULE_MALL = 'mall';

    private $moduleSmsList = [];

    protected $config = [
        // HTTP 请求的超时时间（秒）
        'timeout' => 5.0,

        // 默认发送配置
        'default' => [
            // 网关调用策略，默认：顺序调用
            'strategy' => OrderStrategy::class,
            // 默认可用的发送网关
            'gateways' => ['aliyun', 'qcloud'],
        ],
        // 可用的网关配置
        'gateways' => [
            'errorlog' => [
                'file' => '/runtime/easy-sms.log',
            ]
        ],
        'options' => ['verify' => false]
    ];

    /** @var EasySms */
    protected $easySms;

    public function init()
    {
        $this->config['options']['verify'] = \Yii::$app->request->isSecureConnection;
    }

    /**
     * @param string $module Sms::MODULE_ADMIN 或 Sms::MODULE_MALL
     * @return $this
     * @throws \Exception
     */
    public function module($module)
    {
        if (isset($this->moduleSmsList[$module])) {
            $this->easySms = $this->moduleSmsList[$module];
            return $this;
        }
        switch ($module) {
            case static::MODULE_ADMIN:
                $indSetting = (new ConfigForm())->config();
                if (!empty($indSetting['ind_sms']['aliyun'])) {
                    $params = $indSetting['ind_sms']['aliyun'];
                    $this->config['gateways']['aliyun'] = [
                        'access_key_id' => $params['access_key_id'] ?? '',
                        'access_key_secret' => $params['access_key_secret'] ?? '',
                        'sign_name' => $params['sign'] ?? '',
                    ];
                } else {
                    throw new \Exception('短信信息尚未配置。');
                }
                $this->moduleSmsList[$module] = new EasySms($this->config);
                break;
            case static::MODULE_MALL:
                $form = new \app\forms\mall\setting\ConfigForm();
                $form->tab = \app\forms\mall\setting\ConfigForm::TAB_SMS;
                $setting = $form->config();
                if(!empty($setting['app_id'])) {
                    $this->config['gateways']['qcloud'] = [
                        'sdk_app_id' => $setting['app_id'], // 短信应用的 SDK APP ID
                        'secret_id' => $setting['access_key_id'], // SECRET ID
                        'secret_key' => $setting['access_key_secret'], // SECRET KEY
                        'sign_name' => $setting['template_name'], // 短信签名
                    ];
                    $this->moduleSmsList[$module] = new EasySms($this->config);
                } else {
                    throw new \Exception('短信信息尚未配置。');
                }
                break;
            default:
                throw new \Exception('尚未支持的module: ' . $module);
        }
        $this->easySms = $this->moduleSmsList[$module];
        return $this;
    }

    public function send($mobile, $message)
    {
        try {
            $pattern = (new PhoneNumberValidator())->pattern;
            if (!preg_match($pattern, $mobile)) {
                throw new \Exception('手机号错误');
            }
            $this->easySms->send($mobile, $message);
        } catch (NoGatewayAvailableException $e) {
            $raw = $e->getLastException()->raw;
            throw new \Exception($raw['Response']['Error']['Message'] ?? $e->getLastException()->getMessage());
        }
    }
}
