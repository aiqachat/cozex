<?php
/**
 * Created by IntelliJ IDEA.
 * author: wstianxia
 */

namespace app\controllers\behaviors;

use app\bootstrap\response\ApiCode;
use yii\base\ActionFilter;

class LoginFilter extends ActionFilter
{
    public $loginUrl;
    public $safeActions;
    public $safeRoutes;

    public function beforeAction($action)
    {
        if (is_array($this->safeActions) && in_array($action->id, $this->safeActions)) {
            return parent::beforeAction($action);
        }
        if (is_array($this->safeRoutes) && in_array(\Yii::$app->requestedRoute, $this->safeRoutes)) {
            return parent::beforeAction($action);
        }
        if (!\Yii::$app->user->isGuest) {
            return parent::beforeAction($action);
        }
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->data = [
                'code' => ApiCode::CODE_NOT_LOGIN,
                'msg' => '请先登录。',
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
