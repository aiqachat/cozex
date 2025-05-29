<?php

namespace app\forms\common\payment\wechat;

use app\jobs\WechatTransferJob;
use app\models\Model;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\RejectionException;
use WeChatPay\Builder;
use WeChatPay\Crypto\AesGcm;
use WeChatPay\Crypto\Hash;
use WeChatPay\Crypto\Rsa;
use WeChatPay\Formatter;
use WeChatPay\Transformer;
use WeChatPay\Util\PemUtil;
use yii\helpers\Json;

class WechatPay extends Model
{
    const NOTIFY_URL = 'wechat.php';

    const SIGN_TYPE_MD5 = 'MD5';
    const TRADE_TYPE_JSAPI = 'JSAPI';
    const TRADE_TYPE_NATIVE = 'NATIVE';
    const TRADE_TYPE_APP = 'APP';
    const TRADE_TYPE_MWEB = 'MWEB';

    public $appId;
    public $mchId;
    public $secret;
    public $certPemFile;
    public $keyPemFile;
    public $is_v3;

    // v3 接口需要的参数
    public $serial;
    public $privateKey;
    public $v3key; // APIv3密钥
    public $pubId;
    public $pubPemFile;

    /**
     * v2版统一下单, https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=9_1
     * @param array $args ['body', 'out_trade_no', 'total_fee', 'notify_url', 'trade_type', 'openid']
     * @return array
     */
    public function unifiedOrder($args)
    {
        return $this->v2Send('pay/unifiedorder', $args);
    }

    public function invokePayArg($res)
    {
        $data = [
            'appId' => $this->sub_appid ?? $this->appId,
            'timeStamp' => (string) Formatter::timestamp(),
            'nonceStr' => $res['nonce_str'] ?? Formatter::nonce(),
            'package' => 'prepay_id=' . $res['prepay_id'],
            'signType' => self::SIGN_TYPE_MD5,
        ];
        $data['paySign'] = $this->v2MakeSign($data);
        return $data;
    }

    /**
     * v2 关闭订单, https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=9_3
     * @param array $args
     * @return array
     */
    public function closeOrder($args)
    {
        return $this->v2Send('pay/closeorder', $args);
    }

    /**
     * v2查询订单, https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=9_2
     * @param array $args ['out_trade_no']
     * @return array
     */
    public function orderQuery($args)
    {
        return $this->v2Send('pay/orderquery', $args);
    }

    /**
     * v2申请退款, https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=9_4
     * @param array $args
     * @return array
     */
    public function refund($args)
    {
        if(empty($args['appid'])){
            $args['appid'] = $this->appId;
        }
        if(empty($args['mch_id'])){
            $args['mch_id'] = $this->mchId;
        }
        return $this->v2SendWithPem('secapi/pay/refund', $args);
    }

    /**
     * 新商户号基本不能用了
     * v2企业付款, https://pay.weixin.qq.com/wiki/doc/api/tools/mch_pay.php?chapter=14_2
     * @param array $args ['partner_trade_no', 'openid', 'amount', 'desc']
     * @return array
     */
    public function transfers($args)
    {
        if ($this->is_v3 == 2) {
            return $this->newTransfer($args);
        }
        if(empty($args['mch_appid'])){
            $args['mch_appid'] = $this->appId;
        }
        if(empty($args['mchid'])){
            $args['mchid'] = $this->mchId;
        }
        if(empty($args['check_name'])){
            $args['check_name'] = 'NO_CHECK';
        }
        return $this->v2SendWithPem('mmpaymkttransfers/promotion/transfers', $args);
    }

    protected function v2Send($api, $args)
    {
        if(empty($args['appid'])){
            $args['appid'] = $this->appId;
        }
        if(empty($args['mch_id'])){
            $args['mch_id'] = $this->mchId;
        }
        return $this->getV2ClientResult($this->v2Post($api, $args));
    }

    protected function v2SendWithPem($api, $args)
    {
        return $this->getV2ClientResult($this->v2Post($api, $args, true));
    }

    public function v2MakeSign($data)
    {
        return Hash::sign(self::SIGN_TYPE_MD5, Formatter::queryStringLike(Formatter::ksort($data)), $this->secret);
    }

    public function v3MakeSign($data)
    {
        return Rsa::sign(Formatter::joinedByLineFeed($data), $this->privateKey);
    }

    /**
     * https://pay.weixin.qq.com/docs/merchant/apis/batch-transfer-to-balance/transfer-batch/initiate-batch-transfer.html
     * v3版提现
     */
    protected function newTransfer($args)
    {
        try {
            return $this->transferBatches($args['partner_trade_no']);
        }catch (\Exception $e){
            if($e->getCode() != 100){
                throw $e;
            }
        }
        $notify_url = \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/transfer-notify/' . self::NOTIFY_URL;
        $request = [
            'appid' => $this->appId,
            'out_batch_no' => $args['partner_trade_no'],
            'total_amount' => intval($args['amount']),
            'batch_name' => $args['desc'],
            'batch_remark' => $args['desc'],
            'total_num' => 1,
            'transfer_detail_list' => [
                [
                    'out_detail_no' => $args['partner_trade_no'],
                    'transfer_amount' => intval($args['amount']),
                    'transfer_remark' => $args['desc'],
                    'openid' => $args['openid'],
                ]
            ],
            'notify_url' => str_replace('http://', 'https://', $notify_url),
        ];
        $header = [];
        if ($request['total_amount'] >= 200000) {
            $cer = $this->getCertificate();
            $request['transfer_detail_list'][0]['user_name'] = Rsa::encrypt($args['name'], $cer['certificates']);
            $header['Wechatpay-Serial'] = $cer['serial_no'];
        }
        $res = $this->post("v3/transfer/batches", $request, $header);
        if (!empty($res['out_batch_no'])) {
            \Yii::warning("提现发起成功了：" . var_export($res, true));
            if(\Yii::$app->request->post()) {
                \Yii::$app->queue4->delay(1)->push(new WechatTransferJob([
                    'mall' => \Yii::$app->mall,
                    'post' => \Yii::$app->request->post(),
                    'result' => $res
                ]));
            }
            return $this->transferStatus($res);
        } else {
            throw new \Exception(!empty($res['message']) ? $res['message'] : '转账失败！');
        }
    }

    protected function transferStatus($result)
    {
        if($result['batch_status'] == 'CLOSED'){
            throw new \Exception($result['close_reason'] ?? '提现已关闭');
        }elseif($result['batch_status'] == 'WAIT_PAY'){
            throw new \Exception('待商户员工确认付款');
        }elseif($result['batch_status'] == 'ACCEPTED'){
            throw new \Exception('提现已受理，处理中');
        }elseif($result['batch_status'] == 'PROCESSING'){
            throw new \Exception('提现转账中');
        }elseif($result['batch_status'] != 'FINISHED'){
            throw new \Exception('提现失败');
        }
        return $result;
    }

    /**
     * @param $out_batch_no
     * @param bool $need_query_detail
     * @return array
     * @throws \Exception
     * https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter4_3_5.shtml
     * v3 商家批次单号查询批次单API
     */
    public function transferBatches($out_batch_no, bool $need_query_detail = true){
        $params = ['need_query_detail' => $need_query_detail];
        if($need_query_detail){
            $params['detail_status'] = 'ALL';
        }
        $res = $this->get("v3/transfer/batches/out-batch-no/{out_batch_no}", $params, [], ['out_batch_no' => $out_batch_no]);
        if(!empty($res['transfer_batch'])){
            $this->transferStatus($res['transfer_batch']);
            if($res['transfer_batch']['total_num'] == $res['transfer_batch']['success_num']) {
                return $res['transfer_detail_list'];
            }else{
                throw new \Exception("提现失败，请前往商户平台查看原因");
            }
        }
        $code = $res['code'] == 'NOT_FOUND' ? 100 : 0;
        throw new \Exception($res['message'] ?? '提现查询失败', $code);
    }

    /**
     * @return array|false
     * @throws \Exception
     * v3 获取商户平台证书
     */
    public function getCertificate(){
        try {
            $key = "wechat_v3_cert_{$this->mchId}";
            if (!$certs = \Yii::$app->cache->get ($key)) {
                $instance = $this->getObject ();
                $response = $instance->chain ('v3/certificates')->get ();
                $res = Json::decode ($response->getBody ());
                if (!empty($res['data'][0]['encrypt_certificate'])) {
                    $encrypt_certificate = $res['data'][0]['encrypt_certificate'];
                    try {
                        $certs = [
                            'certificates' => AesGcm::decrypt (
                                $encrypt_certificate['ciphertext'],
                                $this->v3key,
                                $encrypt_certificate['nonce'],
                                $encrypt_certificate['associated_data']
                            ),
                            'serial_no' => $res['data'][0]['serial_no']
                        ];
                    } catch (\Exception $e) {
                    }
                    if (empty($certs['certificates'])) {
                        throw new \Exception('获取证书失败，支付V3加密密钥错误');
                    }
                    \Yii::$app->cache->set ($key, $certs, strtotime ($res['data'][0]['expire_time']) - time () - 86400);
                } else {
                    throw new \Exception('获取证书失败');
                }
            }
        }catch (\Exception $e){
            $platformPublicKeyFilePath = "file://{$this->pubPemFile}";
            $platformPublicKeyInstance = Rsa::from($platformPublicKeyFilePath, Rsa::KEY_TYPE_PUBLIC);
            $certs = [
                'certificates' => $platformPublicKeyInstance,
                'serial_no' => $this->pubId
            ];
        }
        return $certs;
    }

    public function getInstance($is_cert = 1)
    {
        if($is_cert) {
            $certs = $this->getCertificate();
            $certs = [$certs['serial_no'] => $certs['certificates']];
        }
        return $this->getObject($certs ?? null);
    }

    public function getObject($certs = null)
    {
        if($this->serial === null) {
            // 从「商户API证书」中获取「证书序列号」
            $merchantCertificateFilePath = "file://{$this->certPemFile}";
            $this->serial = PemUtil::parseCertificateSerialNo($merchantCertificateFilePath);
        }

        if($this->privateKey === null) {
            // 加载「商户API私钥」，「商户API私钥」会用来生成请求的签名
            $merchantPrivateKeyFilePath = "file://{$this->keyPemFile}";
            $this->privateKey = Rsa::from($merchantPrivateKeyFilePath);
        }

        $factory = Builder::factory([
            'verify' => \Yii::$app->request->isSecureConnection,
            'mchid'      => $this->mchId,
            'serial'     => $this->serial,
            'privateKey' => $this->privateKey,
            'certs'      => $certs ?: ["aa" => 'aa'],

            // v2版接口需要
            'secret' => $this->secret,
            'merchant' => [
                'cert' => $this->certPemFile,
                'key' => $this->keyPemFile
            ],
        ]);
        if(!$certs) {
            $stack = $factory->getDriver()->select()->getConfig('handler');
            $stack->remove('verifier'); // 去除中间件校验数据和签名
        }
        return $factory;
    }

    protected function v2Post($url, $request, $security = false)
    {
        try {
            $this->serial = '';
            $this->privateKey = '';
            $params = ['xml' => $request];
            if($security){
                $params['security'] = true;
            }
            $response = $this->getInstance(0)->chain("v2/{$url}")->post($params);
            return $response->getBody();
        } catch (RejectionException $e) {
            return $e->getReason()->getBody();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    protected function post($url, $request, $header = [])
    {
        try {
            $response = $this->getInstance()->chain($url)
                ->post(['json' => $request, 'headers' => $header]);
            return Json::decode($response->getBody());
        } catch (\Exception $e) {
            // 进行错误处理
            if ($e instanceof RequestException && $e->hasResponse()) {
                $body = $e->getResponse()->getBody();
                return Json::decode($body) ?: $body;
            }else{
                throw $e;
            }
        }
    }

    protected function get($url, $request = [], $header = [], $arg = [])
    {
        try {
            $instance = $this->getInstance();
            $response = $instance->chain($url)
                ->get(array_merge(['headers' => $header, "query" => $request], $arg));
            return Json::decode($response->getBody());
        } catch (\Exception $e) {
            // 进行错误处理
            if ($e instanceof RequestException && $e->hasResponse()) {
                $body = $e->getResponse()->getBody();
                return Json::decode($body) ?: $body;
            }else{
                throw $e;
            }
        }
    }

    protected function getV2ClientResult($result)
    {
        $result = Transformer::toArray($result);
        if (!isset($result['return_code'])) {
            throw new WechatException(
                '返回数据格式不正确: ' . Json::encode($result, JSON_UNESCAPED_UNICODE)
            );
        }
        if ($result['return_code'] !== 'SUCCESS') {
            $msg = 'returnCode: ' . $result['return_code'] . ', returnMsg: ' . $result['return_msg'];
            throw new WechatException($msg, 0, null, $result);
        }
        if (!isset($result['result_code'])) {
            throw new WechatException(
                '返回数据格式不正确: ' . Json::encode($result, JSON_UNESCAPED_UNICODE)
            );
        }
        if ($result['result_code'] !== 'SUCCESS') {
            $msg = 'errCode: ' . $result['err_code'] . ', errCodeDes: ' . $result['err_code_des'];
            throw new WechatException($msg, 0, null, $result);
        }
        return $result;
    }
}
