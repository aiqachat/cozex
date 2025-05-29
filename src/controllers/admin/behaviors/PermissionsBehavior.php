<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\controllers\admin\behaviors;

use app\forms\common\CommonAuth;
use app\models\User;
use yii\base\ActionFilter;
use Yii;

class PermissionsBehavior extends ActionFilter
{
    /**
     * 安全路由，权限验证时会排除这些路由
     * @var array
     */
    public $safeRoute = [];

    public function beforeAction($action)
    {
        if (!\Yii::$app->user->isGuest) {

            //路由名称
            $route = Yii::$app->requestedRoute;
            //排除安全路由
            if (in_array($route, $this->safeRoute)) {
                return true;
            }

            // TODO 异步请求不验证
            if (Yii::$app->request->isAjax) {
                return true;
            }

            /** @var User $user */
            $user = Yii::$app->user->identity;

            if(\Yii::$app->getSessionMallId()){
                $this->redirect('netb/statistic/index');
            }

            // 超级管理员无需验证
            if ($user->identity->is_super_admin == 1) {
                return true;
            }

            // 子账号管理员
            if ($user->identity->is_admin == 1 && $user->mall_id == 0) {
                $notPermissionRoutes = CommonAuth::getPermissionsRouteList();
                if (in_array($route, $notPermissionRoutes)) {
                    $this->redirect('admin/user/me');
                }
                return true;
            }

            if($token = $_COOKIE['__login_token']){
                $user = User::findOne([
                    'access_token' => $token,
                    'is_delete' => 0,
                ]);
                if($user) {
                    Yii::$app->user->login($user);
                    return true;
                }
            }
            $this->permissionError();
        }

        return true;
    }

    protected function redirect($url)
    {
        \Yii::$app->response->redirect(\Yii::$app->urlManager->createUrl($url))->send();
    }

    public function permissionError()
    {
        $response = Yii::$app->getResponse();
        $response->data = Yii::$app->controller->renderFile('@app/views/error/permission.php');
        $response->send();
    }
}
