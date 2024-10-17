<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2019 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\controllers\mall\behaviors;

use app\models\UserIdentity;
use yii\base\ActionFilter;
use Yii;

class PermissionsBehavior extends ActionFilter
{
    /**
     * 安全路由，权限验证时会排除这些路由
     * @var array
     */
    private $safeRoute = ['mall/passport/login'];

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

            // 超级管理员无需验证
            /** @var UserIdentity $userIdentity */
            $userIdentity = \Yii::$app->user->identity->identity;
            if ($userIdentity->is_super_admin == 1) {
                return true;
            }

            // 子账号管理员
            if ($userIdentity->is_admin == 1) {
                return true;
            }

            return false;
        }

        return true;
    }
}
