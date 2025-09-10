<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\oauth\google;

use app\forms\common\oauth\BasicForm;
use app\models\GoogleOauth;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Query;
use yii\helpers\Json;

/**
 * Google OAuth2.0 客户端表单类
 * 用于处理 Google 第三方登录认证相关功能
 * @see https://developers.google.com/identity/protocols/oauth2?hl=zh-cn
 */
class ClientForm extends BasicForm
{
    /**
     * @var string Google OAuth 客户端ID
     */
    public $client_id;

    /**
     * @var string Google OAuth 客户端密钥
     */
    public $client_secret;

    /**
     * @var array 存储访问令牌信息
     */
    public $token;

    // Google OAuth2.0 相关接口地址
    const OAUTH2_TOKEN_URI = 'https://oauth2.googleapis.com/token';
    const OAUTH2_AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    const API_BASE_PATH = 'https://www.googleapis.com';

    /**
     * 初始化方法
     * 从数据库获取商户的 Google OAuth 配置信息
     * 设置回调地址
     */
    public function init()
    {
        parent::init();

        if (!$this->client_id) {
            $model = GoogleOauth::findOne(['mall_id' => \Yii::$app->mall->id, 'is_delete' => 0]);
            $this->client_id = $model->client_id ?? '';
            $this->client_secret = $model->client_secret ?? '';
        }
        $this->redirect_uri = \Yii::$app->request->hostInfo .
            str_replace('/notify', '', \Yii::$app->request->baseUrl) .
            "/notify/google.php";
    }

    /**
     * 创建 Google OAuth 授权链接
     * 用户点击此链接将跳转到 Google 登录页面
     * @return string 返回完整的授权URL
     */
    public function createAuthUrl()
    {
        $params = [
            'client_id' => $this->client_id,
//            'access_type' => 'offline',    // 离线访问，可获取刷新令牌
            'login_hint' => '',            // 可预填登录邮箱
            'prompt' => '',                // 登录提示类型
            'redirect_uri' => $this->redirect_uri,
            'response_type' => 'code',     // 返回授权码
            'scope' => implode(' ', [      // 请求的权限范围
                'https://www.googleapis.com/auth/userinfo.profile',
                'https://www.googleapis.com/auth/userinfo.email'
            ]),
            'state' => Json::encode([
                'mall_id' => \Yii::$app->mall->id,
                'invite' => $this->invite
            ]),  // 状态参数，防止CSRF攻击
        ];

        return self::OAUTH2_AUTH_URL . "?" . Query::build($params);
    }

    /**
     * 使用授权码获取访问令牌
     * @param string $code 授权码
     * @return array 返回访问令牌信息
     * @throws \Exception 当获取令牌失败时抛出异常
     */
    public function fetchAccessTokenWithAuthCode($code)
    {
        $params = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->redirect_uri,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
        ];

        try {
            $res = $this->getClient()->request('post', self::OAUTH2_TOKEN_URI, [
                'body' => Query::build($params),
                'headers' => [
                    'Cache-Control' => 'no-store',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ]
            ])->getBody()->getContents();
        } catch (\Exception $e) {
            // 进行错误处理
            if ($e instanceof RequestException && $e->hasResponse()) {
                $res = $e->getResponse()->getBody()->getContents();
            } else {
                throw $e;
            }
        }
        $cred = Json::decode($res);

        if (empty($cred['access_token'])) {
            throw new \Exception($cred['error'] ?? '授权失败。');
        }
        return $cred;
    }

    /**
     * 设置访问令牌
     * @param string|array $token 访问令牌字符串或令牌信息数组
     */
    public function setAccessToken($token)
    {
        if (is_string($token)) {
            $token = [
                'access_token' => $token,
            ];
        }
        $this->token = $token;
    }

    /**
     * 获取用户信息
     * 通过访问令牌请求用户的个人资料
     * @return array 返回用户信息
     * @throws \Exception 当请求失败时抛出异常
     */
    public function getUserInfo()
    {
        $url = self::API_BASE_PATH . '/oauth2/v2/userinfo';
        $query = [
            'access_token' => $this->token['access_token'] ?? '',
        ];

        try {
            $res = $this->getClient()->request('GET', $url, [
                'query' => $query,
            ])->getBody()->getContents();
        } catch (\Exception $e) {
            // 进行错误处理
            if ($e instanceof RequestException && $e->hasResponse()) {
                $res = $e->getResponse()->getBody()->getContents();
            } else {
                throw $e;
            }
        }
        return Json::decode($res);
    }
}
