<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\controllers\admin;

use app\forms\admin\DataForm;
use app\forms\admin\mall\MallEntryForm;
use app\models\User;

class IndexController extends AdminController
{
    public function actionIndex()
    {
        if(!empty($_SERVER['HTTP_REFERER'])){
            $query = urldecode(parse_url($_SERVER['HTTP_REFERER'])['query'] ?? '');
            if($query && strpos($query, 'admin/passport/login') !== false){
                /** @var User $user */
                $user = \Yii::$app->user->identity;
                $list = [];
                if($user->identity->is_super_admin == 0) {
                    foreach ($user->mall as $mall) {
                        if ($mall->is_delete == 0 && $mall->is_recycle == 0 && ($mall->expired_at == '0000-00-00 00:00:00' ||
                                time () < strtotime ($mall->expired_at))) {
                            $list[] = $mall;
                        }
                    }
                }
                if(count($list) == 1){
                    return (new MallEntryForm())->entry($list[0]->id);
                }
            }
        }
        return \Yii::$app->response->redirect(\Yii::$app->urlManager->createUrl(['admin/user/me']));
    }

    public function actionBackIndex()
    {
        \Yii::$app->removeSessionMallId();
        return \Yii::$app->response->redirect(\Yii::$app->urlManager->createUrl(['admin/user/me']));
    }

    public function actionInfo()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new DataForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->search());
        } else {
            return $this->render('info');
        }
    }
}
