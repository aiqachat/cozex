<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/12/5 16:13
 */


namespace app\controllers\api;

use app\controllers\api\filters\LoginFilter;
use app\forms\api\user\PromoteForm;
use app\forms\api\user\UserEditForm;
use app\forms\api\user\UserInfoForm;
use app\forms\api\volcengine\IndexForm;

class UserController extends ApiController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'login' => [
                'class' => LoginFilter::class,
            ],
        ]);
    }

    public function actionUserInfo()
    {
        $form = new UserInfoForm();
        return $form->getInfo();
    }

    public function actionUpdateUser()
    {
        $form = new UserEditForm();
        $form->attributes = \Yii::$app->request->post();
        return $form->update();
    }

    public function actionVolcengineAccount()
    {
        $form = new IndexForm();
        return $this->asJson($form->getVolcengineAccount());
    }

    public function actionPromoteInfo()
    {
        $form = new PromoteForm();
        return $this->asJson($form->getInfo());
    }

    public function actionPromoteUser()
    {
        $form = new PromoteForm();
        $form->attributes = \Yii::$app->request->get();
        return $this->asJson($form->getUser());
    }
}
