<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/11/7 18:19
 */

namespace app\controllers\api;

use app\controllers\Controller;
use app\models\User;

class ApiController extends Controller
{

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
//            'corsFilter' => [
//                'class' => Cors::class,
//            ], // @czs 跨域请求
        ]);
    }

    public function init()
    {
        parent::init();
        $this->enableCsrfValidation = false;
        $this->login();
    }

    private function login()
    {
        $headers = \Yii::$app->request->headers;
        $accessToken = empty($headers['x-access-token']) ? null : $headers['x-access-token'];

        if (!$accessToken) {
            return $this;
        }
        $user = User::findOne([
            'access_token' => $accessToken,
            'is_delete' => 0,
        ]);

        if ($user) {
            \Yii::$app->user->login($user);
        }
        return $this;
    }
}