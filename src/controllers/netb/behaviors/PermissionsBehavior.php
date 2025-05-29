<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2019 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\controllers\netb\behaviors;

use app\forms\common\CommonAuth;
use app\models\UserIdentity;
use yii\base\ActionFilter;
use Yii;

class PermissionsBehavior extends ActionFilter
{
    /**
     * 安全路由，权限验证时会排除这些路由
     * @var array
     */
    private $safeRoute = [];

    public function beforeAction($action)
    {
        if (!\Yii::$app->user->isGuest) {
            if (Yii::$app->branch->checkMallUser(Yii::$app->user->identity)) {
                Yii::$app->response->redirect(Yii::$app->branch->logoutUrl())->send();
                return false;
            }

            // TODO 异步请求不验证
            if (Yii::$app->request->isAjax) {
                return true;
            }

            //路由名称
            $route = Yii::$app->requestedRoute;
            //排除安全路由
            if (in_array($route, $this->safeRoute)) {
                return true;
            }

            if(!\Yii::$app->getSessionMallId()){
                Yii::$app->response->redirect(Yii::$app->branch->logoutUrl())->send();
                return false;
            }

            // 超级管理员无需验证
            /** @var UserIdentity $userIdentity */
            $userIdentity = \Yii::$app->user->identity->identity;
            if ($userIdentity->is_super_admin == 1) {
                return true;
            }

            // 子账号管理员
            if ($userIdentity->is_admin == 1) {
                $notPermissionRoutes = CommonAuth::getPermissionsRouteList();

                if (in_array($route, $notPermissionRoutes)) {
                    $this->permissionError();
                }
                return true;
            }

            return false;
        }

        return true;
    }

    public function permissionError()
    {
        $response = Yii::$app->getResponse();
        $response->data = Yii::$app->controller->renderFile('@app/views/error/permission.php');
        $response->send();
    }
}
