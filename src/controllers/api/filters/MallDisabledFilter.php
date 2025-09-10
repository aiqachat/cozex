<?php
/**
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\controllers\api\filters;

use app\bootstrap\response\ApiCode;
use yii\base\ActionFilter;

class MallDisabledFilter extends ActionFilter
{
    public function beforeAction($action)
    {
        if (\Yii::$app->mall->is_disable) {
            \Yii::$app->response->data = [
                'code' => ApiCode::CODE_STORE_DISABLED,
                'msg' => \Yii::t("common", '系统已禁用'),
            ];
            return false;
        }

        $adminInfo = \Yii::$app->mall->user->adminInfo;
        if (!$adminInfo || (\Yii::$app->mall->expired_at != '0000-00-00 00:00:00' && strtotime(\Yii::$app->mall->expired_at) < time())
        || ($adminInfo->expired_at != '0000-00-00 00:00:00' && strtotime($adminInfo->expired_at) < time())) {
            \Yii::$app->response->data = [
                'code' => ApiCode::CODE_STORE_DISABLED,
                'msg' => \Yii::t("common", '系统已过期'),
            ];
            return false;
        }

        return true;
    }
}
