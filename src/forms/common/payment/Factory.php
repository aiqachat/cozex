<?php
/**
 * Created by PhpStorm
 * Date: 2021/2/24
 * Time: 10:20 上午
 * @copyright: ©2020 深圳网商天下科技有限公司
 * @link: https://www.netbcloud.com/
 */

namespace app\forms\common\payment;

use app\forms\common\payment\stripe\StripePay;
use app\forms\common\payment\wechat\WechatPay;
use app\forms\common\payment\wechat\WechatServicePay;
use app\forms\mall\setting\PayConfigForm;
use app\models\Model;

class Factory extends Model
{
    public function wechatPay()
    {
        $form = new PayConfigForm(['tab' => PayConfigForm::TAB_WX]);
        $data = $form->config();

        $data = (object)$data;
        if(!$data->appid){
            throw new \Exception('未配置支付');
        }
        $config = [
            'v3key' => $data->v3key,
            'is_v3' => $data->is_v3,
            'pubId' => $data->pub_key_id
        ];
        if ($data->is_service) {
            if ($data->service_cert_pem && $data->service_key_pem) {
                $this->generatePem($config, $data->service_cert_pem, $data->service_key_pem, $data->pub_key);
            }
            $object = new WechatServicePay(array_merge([
                'appId' => $data->service_appid,
                'mchId' => $data->service_mch_id,
                'secret' => $data->service_key,
                'sub_appid' => $data->appid,
                'sub_mch_id' => $data->mch_id,
            ], $config));
        } else {
            if ($data->cert_pem && $data->key_pem) {
                $this->generatePem($config, $data->cert_pem, $data->key_pem, $data->pub_key);
            }
            $object = new WechatPay(array_merge([
                'appId' => $data->appid,
                'mchId' => $data->mch_id,
                'secret' => $data->key,
            ], $config));
        }
        return $object;
    }

    public function stripePay()
    {
        $form = new PayConfigForm(['tab' => PayConfigForm::TAB_STRIPE]);
        $data = $form->config();

        if(!$data['api_key']){
            throw new \Exception('未配置支付');
        }
        unset($data['webhook_url']);

        return new StripePay([
            'api_key' => $data['api_key'],
            'api_public_key' => $data['api_public_key'],
        ]);
    }

    private function generatePem(&$config, $cert_pem, $key_pem, $pub_key = '')
    {
        $pemDir = \Yii::$app->runtimePath . '/pem';
        make_dir($pemDir);
        $certPemFile = $pemDir . '/' . md5($cert_pem);
        if (!file_exists($certPemFile)) {
            file_put_contents($certPemFile, $cert_pem);
        }
        $keyPemFile = $pemDir . '/' . md5($key_pem);
        if (!file_exists($keyPemFile)) {
            file_put_contents($keyPemFile, $key_pem);
        }
        $config['certPemFile'] = $certPemFile;
        $config['keyPemFile'] = $keyPemFile;
        if(!empty($pub_key)) {
            $pubPemFile = $pemDir . '/' . md5 ($pub_key);
            if (!file_exists ($pubPemFile)) {
                file_put_contents ($pubPemFile, $pub_key);
            }
        }
        $config['pubPemFile'] = $pubPemFile ?? '';
    }
}
