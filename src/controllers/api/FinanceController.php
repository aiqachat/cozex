<?php
/**
 * @copyright ©2020 深圳网商天下科技
 * @link: https://www.netbcloud.com/
 * Created by PhpStorm.
 * author: wstianxia
 */

namespace app\controllers\api;

use app\controllers\api\filters\LoginFilter;
use app\forms\api\finance\IntegralForm;
use app\forms\api\finance\RechargeForm;
use app\forms\api\finance\RechargeOrderForm;

class FinanceController extends ApiController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'login' => [
                'class' => LoginFilter::class,
            ],
        ]);
    }

    public function actionRechargeSetting()
    {
        $form = new RechargeForm();
        return $form->getSetting();
    }

    public function actionRecharge()
    {
        $form = new RechargeOrderForm();
        $form->attributes = \Yii::$app->request->post();
        return $form->balanceRecharge();
    }

    public function actionRechargeResult()
    {
        $form = new RechargeForm();
        $form->attributes = \Yii::$app->request->post();
        return $this->asJson($form->result());
    }

    public function actionIntegralOption()
    {
        $form = new IntegralForm();
        $form->attributes = \Yii::$app->request->get();
        return $this->asJson($form->allData());
    }

    public function actionIntegralRecord()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new IntegralForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->getList());
        }
    }

    public function actionBalanceRecord()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new RechargeForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->getList());
        }
    }
}
