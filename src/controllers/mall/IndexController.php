<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: chenzs
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/11/8 18:11
 */

namespace app\controllers\mall;

use app\bootstrap\response\ApiCode;
use app\forms\mall\setting\ConfigForm;
use app\forms\mall\setting\CozeForm;
use app\forms\mall\setting\QueueForm;
use app\forms\mall\setting\VolcanoKeyForm;
use app\forms\mall\setting\VolcengineForm;

class IndexController extends AdminController
{
    public function actionIndex()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ConfigForm();
            if (\Yii::$app->request->isPost) {
                $form->attributes = \Yii::$app->request->post();
                return $this->asJson($form->save());
            }else{
                return $this->asJson($form->get());
            }
        } else {
            return $this->render('index');
        }
    }

    public function actionVolcengine()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new VolcanoKeyForm();
            if (\Yii::$app->request->isPost) {
                $form->attributes = \Yii::$app->request->post();
                return $this->asJson($form->save());
            }else{
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson($form->get());
            }
        } else {
            return $this->render('volcengine');
        }
    }

    public function actionVolcengineDestroy()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new VolcanoKeyForm();
            $form->attributes = \Yii::$app->request->post();
            return $this->asJson($form->del());
        }
    }

    public function actionQueue()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new QueueForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->get());
        } else {
            return $this->render('queue');
        }
    }

    public function actionHeaderBar()
    {
        $user = \Yii::$app->user->identity;
        $indSetting = (new ConfigForm())->config();
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'nickname' => $user->nickname,
                    'identity' => $user->identity,
                ],
                'mall' => [
                    'id' => 0,
                    'name' => $indSetting['name'] ?? 'cozex',
                    'mall_logo_pic' => $indSetting['mall_logo_pic'] ?? \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/statics/img/mall/poster-big-shop.png',
                ],
            ],
        ];
    }

    public function actionCozeAccount()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new CozeForm();
            return $this->asJson($form->data());
        }
    }

    public function actionVolcengineAccount()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new VolcengineForm();
            return $this->asJson($form->data());
        }
    }

    public function actionCozeSpace()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new CozeForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->space());
        }
    }
}
