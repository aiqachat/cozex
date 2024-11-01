<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine;

use app\forms\mall\setting\VolcengineForm;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use yii\base\BaseObject;
use yii\helpers\Json;

class ApiForm extends BaseObject
{
    private $apiUrl = 'https://openspeech.bytedance.com';

    public $appid;
    public $token;

    /** @var Base */
    public $object;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        if(!$this->appid) {
            $setting = (new VolcengineForm())->getSetting ();
            $this->token = $setting['access_token'];
            $this->appid = strval ($setting['app_id']);
        }
        $this->object->setApi($this);
    }

    public static function common($config = [])
    {
        return new self($config);
    }

    public function request(){
        try {
            if(!$this->object->getApi()){
                $this->object->setApi($this);
            }
            $url = $this->apiUrl . $this->object->getMethodName();
            $headers = array_merge($this->object->getHeaders(), ['Authorization' => "Bearer; {$this->token}"]);
            $options = ['headers' => $headers];
            $params = $this->object->getParams();
            if($this->object->getMethod() == Base::METHOD_POST) {
                if(is_array($params)){
                    $options['json'] = $params;
                }else{
                    $options['body'] = $params;
                }
                $res = $this->getClient()
                    ->post($url, $options);
            }else{
                $char = (strpos ($url, "?") === false ? '?' : '&');
                $res = $this->getClient()
                    ->get($url . $char . http_build_query($params), $options);
            }
            $body = $res->getBody()->getContents();
        }catch (\Exception $e){
            if ($e instanceof RequestException && $e->hasResponse()) {
                $body = $e->getResponse()->getBody()->getContents();
            }else{
                \Yii::error($e);
                throw $e;
            }
        }
        return $this->object->response($body ? @Json::decode($body) : ['message' => '请求异常']);
    }

    private function getClient(): Client
    {
        return new Client(['verify' => false]);
    }
}
