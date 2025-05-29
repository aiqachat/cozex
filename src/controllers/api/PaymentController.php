<?php

namespace app\controllers\api;

use app\bootstrap\response\ApiCode;
use app\controllers\api\filters\LoginFilter;

class PaymentController extends ApiController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'login' => [
                'class' => LoginFilter::class,
            ],
        ]);
    }

    public function actionPayBuyBalance($id)
    {
        try {
            \Yii::$app->payment->payBuyBalance($id);
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '支付成功。',
            ];
        } catch (\Exception $e) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage(),
            ];
        }
    }

    public function actionPayBuyIntegral($id)
    {
        try {
            \Yii::$app->payment->payBuyIntegral($id);
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '支付成功。',
            ];
        } catch (\Exception $e) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage(),
            ];
        }
    }
}
