<?php
/**
 * Created by PhpStorm
 * User: chenzs
 * Date: 2020/9/29
 * Time: 4:15 下午
 * @copyright: ©2020 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\forms\mall\setting;

use app\forms\common\CommonOption;
use app\forms\common\payment\stripe\StripePay;
use app\forms\common\payment\wechat\WechatException;
use app\forms\common\payment\wechat\WechatPay;
use app\forms\common\payment\wechat\WechatServicePay;

class PayConfigForm extends BasicConfigForm
{
    const TAB_BASIC = 'basic';
    const TAB_WX = 'wechat';
    const TAB_STRIPE = 'stripe';

    public function getList()
    {
        return [
            self::TAB_WX => CommonOption::NAME_WX_PAY,
            self::TAB_STRIPE => CommonOption::NAME_STRIPE_PAY,
        ];
    }

    public function save()
    {
        $form = new ConfigForm();
        if($this->tab === self::TAB_BASIC){
            $form->formData = $this->formData;
            return $form->save();
        }
        $config = $form->config();
        if($this->tab === self::TAB_WX){
            $config['is_wechat_pay'] = $this->formData['is_wechat_pay'];
        }
        if($this->tab === self::TAB_STRIPE){
            $config['is_stripe_pay'] = $this->formData['is_stripe_pay'];
        }
        $form->formData = $config;
        $form->save();
        return parent::save();
    }

    public function config()
    {
        if($this->tab === self::TAB_BASIC){
            $form = new ConfigForm();
            $data = $form->config();
            $data['currency_list'] = ConfigForm::CURRENCY;
        }else {
            $data = parent::config ();
            $config = (new ConfigForm())->config ();
            if ($this->tab === self::TAB_WX) {
                $data['is_wechat_pay'] = $config['is_wechat_pay'];
            }
            if ($this->tab === self::TAB_STRIPE) {
                $data['is_stripe_pay'] = $config['is_stripe_pay'];
            }
        }
        return $data;
    }

    public function getDefault()
    {
        if($this->tab === self::TAB_BASIC){
            return (new ConfigForm(['tab' => ConfigForm::TAB_BASIC]))->getDefault();
        }
        return parent::getDefault();
    }

    public function wechat()
    {
        return [
            'is_service' => 0,
            'appid' => '',
            'mch_id' => '',
            'key' => '',
            'cert_pem' => '',
            'key_pem' => '',
            'service_appid' => '',
            'service_mch_id' => '',
            'service_key' => '',
            'service_cert_pem' => '',
            'service_key_pem' => '',
            'is_v3' => 1,
            'v3key' => CommonOption::getWeChatV3Key(),
            'pub_key_id' => '',
            'pub_key' => '',
        ];
    }

    public function stripe()
    {
        return [
            'api_key' => '',
            'api_public_key' => '',
        ];
    }

    public function afterHandle($data)
    {
        if($this->tab === self::TAB_WX && $this->formData['is_wechat_pay'] == 1){
            $this->checkWxPay($data);
            $data['v3key'] = CommonOption::getWeChatV3Key();
        }
        if($this->tab === self::TAB_STRIPE && $this->formData['is_stripe_pay'] == 1){
            $this->checkStripe($data);
        }
        return $data;
    }

    public function append($data)
    {
        if($this->tab === self::TAB_STRIPE){
            $data['webhook_url'] = \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/pay-notify/stripe.php';
        }
        return $data;
    }

    private function checkStripe($data)
    {
        try {
            $pay = new StripePay($data);
            $pay->allPrice();
        }catch (\Exception $e){
            throw new \Exception('私钥错误');
        }
    }

    private function checkWxPay($data)
    {
        $data = (object)$data;
        // 检测参数是否有效
        if ($data->is_service) {
            $wechatPay = new WechatServicePay([
                'appId' => $data->service_appid,
                'mchId' => $data->service_mch_id,
                'sub_appid' => $data->appid,
                'sub_mch_id' => $data->mch_id,
                'secret' => $data->service_key,
            ]);
        } else {
            $wechatPay = new WechatPay([
                'appId' => $data->appid,
                'mchId' => $data->mch_id,
                'secret' => $data->key
            ]);
        }

        try {
            $wechatPay->orderQuery(['out_trade_no' => '88888888']);
        } catch (\Exception $e) {
            if ($e instanceof WechatException) {
                if($e->getRaw()['return_code'] != 'SUCCESS') {
                    $message = '微信支付商户号 或 微信支付Api密钥有误(' . $e->getRaw ()['return_msg'] . ')';
                    throw new \Exception($message);
                }
            }else{
                throw $e;
            }
        }
    }
}
