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
use app\forms\api\order\IntegralForm;
use app\forms\api\order\SpeechForm;

class OrderController extends ApiController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'login' => [
                'class' => LoginFilter::class,
            ],
        ]);
    }

    public function actionBuyTtsMega()
    {
        $form = new SpeechForm();
        $form->attributes = \Yii::$app->request->post();
        $form->scenario = 'buy';
        return $form->submit();
    }

    public function actionRenewTtsMega()
    {
        $form = new SpeechForm();
        $form->attributes = \Yii::$app->request->post();
        $form->scenario = 'renew';
        return $form->submit();
    }

    public function actionBuyTtsMegaResult()
    {
        $form = new SpeechForm();
        $form->attributes = \Yii::$app->request->post();
        return $this->asJson($form->result());
    }

    public function actionExchangeIntegral()
    {
        $form = new IntegralForm();
        $form->attributes = \Yii::$app->request->post();
        $form->scenario = 'buy';
        return $form->submit();
    }

    public function actionExchangeIntegralResult()
    {
        $form = new IntegralForm();
        $form->attributes = \Yii::$app->request->post();
        return $form->result();
    }
}
