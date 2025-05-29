<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/11/7 18:19
 */

namespace app\controllers\api;

use app\controllers\api\filters\MallDisabledFilter;
use app\controllers\Controller;
use app\models\Mall;
use app\models\User;
use yii\base\Action;
use yii\filters\Cors;
use yii\web\NotFoundHttpException;

class ApiController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'corsFilter' => [
                'class' => Cors::class,
            ], // @czs 跨域请求
            'disabled' => [
                'class' => MallDisabledFilter::class,
            ],
        ]);
    }

    public function beforeAction($action)
    {
        if (\Yii::$app->request->isOptions) {
            // 设置响应状态码和头信息
            \Yii::$app->response->statusCode = 200;
            \Yii::$app->response->headers->set('Allow', 'GET, POST, PUT, DELETE');
            \Yii::$app->response->format = \Yii::$app->response::FORMAT_RAW;
            return false; // 阻止后续操作执行
        }
        return parent::beforeAction($action);
    }

    public function init()
    {
        // 获取已附加的 Cors 行为
        $corsBehavior = $this->getBehavior('corsFilter');
        $corsBehavior->beforeAction(new Action($this->id, $this));
        $this->detachBehavior('corsFilter');

        parent::init();
        $this->enableCsrfValidation = false;
        if (!\Yii::$app->request->isOptions) {
            $this->setMall()->login()->lang();
        }
    }

    private function setMall()
    {
        $headers = \Yii::$app->request->headers;
        $mallId = $headers['x-mall-id'] ?? \Yii::$app->request->get('_mall_id');
        if (empty($mallId)) {
            throw new NotFoundHttpException('缺失商城id');
        }
        $mall = Mall::findOne([
            'id' => $mallId,
            'is_delete' => 0,
            'is_recycle' => 0,
        ]);
        if (!$mall) {
            throw new NotFoundHttpException('商城不存在，id = ' . $mallId);
        }
        \Yii::$app->setMall($mall);
        return $this;
    }

    private function login()
    {
        $headers = \Yii::$app->request->headers;
        $accessToken = empty($headers['x-access-token']) ? \Yii::$app->request->get('_access_token') : $headers['x-access-token'];
        $accountId = $headers['x-account-id'] ?? null;

        if (!$accessToken) {
            return $this;
        }
        /** @var User $user */
        $user = User::find()->where([
            'access_token' => $accessToken,
            'is_delete' => 0,
        ])->with(['platform' => function ($query) use ($accountId) {
            if($accountId){
                $query->andWhere(['id' => $accountId]);
            }
        }])->one();

        if ($user) {
            if ($user->userInfo->is_blacklist) {
                throw new NotFoundHttpException('账号已禁用，请联系管理员');
            }
            \Yii::$app->user->login($user);
        }
        return $this;
    }

    private function lang()
    {
        $headers = \Yii::$app->request->headers;
        $lang = empty($headers['x-lang']) ? 'zh' : $headers['x-lang'];
        \Yii::$app->language = $lang;
        return $this;
    }
}