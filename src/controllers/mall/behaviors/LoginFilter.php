<?php
/**
 * Created by IntelliJ IDEA.
 * author: wstianxia
 * Date: 2019/3/21
 * Time: 13:56
 */

namespace app\controllers\mall\behaviors;

use app\bootstrap\response\ApiCode;
use app\models\User;
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
            /** @var User $user */
            $user = \Yii::$app->user->identity;
            if ($user->adminInfo) {
                return parent::beforeAction($action);
            }
        }
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->data = [
                'code' => ApiCode::CODE_NOT_LOGIN,
                'msg' => '请先登录。',
            ];
        } else {
            if (!$this->loginUrl) {
                // cookie存储最后一个登录角色相关信息
                $url = 'mall/passport/login';
                $data = [$url];
                $this->loginUrl = \Yii::$app->urlManager->createAbsoluteUrl($data);
            }
            \Yii::$app->response->redirect($this->loginUrl);
        }
        return false;
    }
}
