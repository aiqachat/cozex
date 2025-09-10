<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use yii\base\BaseObject;
use yii\helpers\Json;

class ArkRequestForm extends BaseObject
{
    private $apiUrl = 'https://ark.cn-beijing.volces.com/api/v3';

    public $apiKey;

    /** @var \app\forms\common\volcengine\ark\Base */
    public $object;

    /** @var integer 1：国内站；2：国际站 */
    public $type;

    public static function common($config = [])
    {
        return new self($config);
    }

    public function init()
    {
        parent::init();
        if($this->type == 2){
            $this->apiUrl = 'https://ark.ap-southeast.bytepluses.com/api/v3';
        }
    }

    public function request(){
        try {
            $url = $this->apiUrl . $this->object->getMethodName();
            $options = ['headers' => ['Authorization' => "Bearer {$this->apiKey}"]];
            $params = $this->object->getAttribute();
            if($this->object->getMethod() == Base::METHOD_POST) {
                $options['json'] = $params;
                $res = $this->getClient()->post($url, $options);
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
        return new Client(['verify' => \Yii::$app->request->isSecureConnection]);
    }
}
