<?php
/**
 * Created by PhpStorm.
 * User: wstianxia
 * Date: 2019/7/19
 * Time: 17:09
 * @copyright: ©2021 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\helpers;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use yii\base\Component;
use yii\helpers\Json;

class CurlHelper extends Component
{
    protected $postType = 'form_params';
    public const FORM_PARAMS = 'form_params';
    public const BODY = 'body';
    public const MULTIPART = 'multipart';

    public $timeout = 0;

    public static function getInstance($timeout = 0)
    {
        return new self(['timeout' => $timeout]);
    }

    /**
     * @param array $headers
     * @return Client
     */
    public function getClient($headers = array())
    {
        return new Client([
            'verify' => \Yii::$app->request->isSecureConnection,
            'headers' => $headers,
            'timeout' => $this->timeout,
        ]);
    }

    public function httpPost($url, $params = [], $data = [])
    {
        try {
            $url = $this->appendParams($url, $params);
            $postData = [];
            if ($this->postType == static::BODY) {
                $postData['body'] = Json::encode($data, JSON_UNESCAPED_UNICODE);
            } elseif ($this->postType == static::MULTIPART) {
                $postData['multipart'] = $data;
            } else {
                $postData['form_params'] = $data;
            }
            $response = $this->getClient()->post($url, $postData);
            $body = $response->getBody();
        } catch (ClientException $exception) {
            $body = $exception->getResponse()->getBody();
        }
        if (!$body) {
            throw new \Exception('请求没有有效的返回');
        }
        $res = Json::decode($body->getContents(), true);
        if (!$res) {
            throw new \Exception('请求返回值为空');
        }
        return $res;
    }
    public function httpGet($url, $param = array(), $headers = array())
    {
        try {
            $url = $this->appendParams($url, $param);
            $client = $this->getClient($headers);
            $response = $client->get($url);
            $body = $response->getBody();
        } catch (ClientException $e) {
            $body = $e->getResponse()->getBody();
        }
        if (!$body) {
            throw new \Exception('请求没有有效的返回');
        }
        $res = Json::decode($body->getContents(), true);
        if (!$res) {
            throw new \Exception('请求返回值为空');
        }
        return $res;
    }

    private function appendParams($url, $params = [])
    {
        if (!is_array($params)) {
            return $url;
        }
        if (!count($params)) {
            return $url;
        }
        $url = trim($url, '?');
        $url = trim($url, '&');
        $queryString = $this->paramsToQueryString($params);
        if (mb_stripos($url, '?')) {
            return $url . '&' . $queryString;
        } else {
            return $url . '?' . $queryString;
        }
    }

    private function paramsToQueryString($params = [])
    {
        if (!is_array($params)) {
            return '';
        }
        if (!count($params)) {
            return '';
        }
        $str = '';
        foreach ($params as $k => $v) {
            $v = urlencode($v);
            $str .= "{$k}={$v}&";
        }
        return trim($str, '&');
    }

    public function setPostType($value)
    {
        $this->postType = $value;
        return $this;
    }
}