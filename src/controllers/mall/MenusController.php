<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\controllers\mall;

use app\bootstrap\response\ApiCode;
use app\forms\MenusForm;

class MenusController extends AdminController
{
    public function actionIndex()
    {
        $route = \Yii::$app->request->post('route');

        $form = new MenusForm();
        $form->currentRoute = $route;
        $res = $form->getMenus();

        return $this->asJson([
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '请求成功',
            'data' => [
                'menus' => $res['menus'],
                'currentRouteInfo' => $res['currentRouteInfo'],
            ]
        ]);
    }
}
