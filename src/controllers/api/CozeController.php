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
use app\forms\api\index\CozeForm;

class CozeController extends ApiController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'login' => [
                'class' => LoginFilter::class,
                'ignore' => ['info']
            ],
        ]);
    }

    public function actionInfo()
    {
        $form = new CozeForm();
        $form->attributes = \Yii::$app->request->get();
        return $form->getInfo();
    }
}
