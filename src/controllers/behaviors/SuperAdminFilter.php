<?php
/**
 * Created by IntelliJ IDEA.
 * author: wstianxia
 * Date: 2019/3/21
 * Time: 13:57
 */

namespace app\controllers\behaviors;


use app\bootstrap\response\ApiCode;
use app\models\User;
use yii\base\ActionFilter;

class SuperAdminFilter extends ActionFilter
{
    public $loginUrl;
    public $safeActions;
    public $onlyActions;
    public $safeRoutes;

    public function beforeAction($action)
    {
        if (\Yii::$app->user->isGuest) {
            return false;
        }
        if (is_array($this->safeActions) && in_array($action->id, $this->safeActions)) {
            return parent::beforeAction($action);
        }
        if (is_array($this->safeRoutes) && in_array(\Yii::$app->requestedRoute, $this->safeRoutes)) {
            return parent::beforeAction($action);
        }
        if (is_array($this->onlyActions) && !in_array($action->id, $this->onlyActions)) {
            return parent::beforeAction($action);
        }
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        if ($user->identity->is_super_admin == 1) {
            return parent::beforeAction($action);
        }
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->data = [
                'code' => ApiCode::CODE_NOT_LOGIN,
                'msg' => '您不是超级管理员，没有访问权限。',
            ];
        } else {
            if (!$this->loginUrl) {
                $this->loginUrl = \Yii::$app->urlManager->createAbsoluteUrl(['admin/passport/login']);
            }
            \Yii::$app->response->redirect($this->loginUrl);
        }
        return false;
    }
}
