<?php
/**
 * @copyright ©2024 深圳网商天下科技
 * author: chenzs
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/11/8 18:11
 */

namespace app\controllers\netb;

use app\forms\mall\setting\ConfigForm;
use app\forms\mall\setting\CozeForm;
use app\forms\mall\setting\OauthForm;
use app\forms\mall\setting\PayConfigForm;
use app\forms\mall\setting\PriceForm;
use app\forms\mall\setting\UserConfigForm;
use app\forms\mall\setting\VolcengineForm;

class SettingController extends AdminController
{
    public function actionIndex()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ConfigForm();
            if (\Yii::$app->request->isPost) {
                $form->attributes = \Yii::$app->request->post();
                return $this->asJson($form->save());
            }else{
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson($form->get());
            }
        } else {
            return $this->render('index');
        }
    }
    public function actionVoice()
    {
        if (\Yii::$app->request->isAjax) {
        } else {
            return $this->render('voice');
        }
    }

    public function actionUser()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new UserConfigForm();
            if (\Yii::$app->request->isPost) {
                $form->attributes = \Yii::$app->request->post();
                return $this->asJson($form->save());
            }else{
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson($form->get());
            }
        } else {
            return $this->render('user');
        }
    }

    public function actionPay()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new PayConfigForm();
            if (\Yii::$app->request->isPost) {
                $form->attributes = \Yii::$app->request->post();
                return $this->asJson($form->save());
            }else{
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson($form->get());
            }
        } else {
            return $this->render('pay');
        }
    }

    public function actionOauth()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new OauthForm();
            if (\Yii::$app->request->isPost) {
                $form->attributes = \Yii::$app->request->post();
                return $this->asJson($form->save());
            }else{
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson($form->get());
            }
        } else {
            return $this->render('oauth');
        }
    }

    public function actionCoze()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new CozeForm();
            if (\Yii::$app->request->isPost) {
                $form->attributes = \Yii::$app->request->post();
                return $this->asJson($form->save());
            }else{
                return $this->asJson($form->get());
            }
        } else {
            return $this->render('coze');
        }
    }

    public function actionCozeDestroy()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new CozeForm();
            $form->attributes = \Yii::$app->request->post();
            return $this->asJson($form->del());
        }
    }

    public function actionVolcengine()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new VolcengineForm();
            if (\Yii::$app->request->isPost) {
                $form->attributes = \Yii::$app->request->post();
                return $this->asJson($form->save());
            }else{
                return $this->asJson($form->get());
            }
        } else {
            return $this->render('volcengine');
        }
    }

    public function actionVolcengineDefault()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new VolcengineForm();
            $form->attributes = \Yii::$app->request->post();
            return $this->asJson($form->setDefault());
        }
    }

    public function actionVolcengineDestroy()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new VolcengineForm();
            $form->attributes = \Yii::$app->request->post();
            return $this->asJson($form->del());
        }
    }

    public function actionPrice()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new PriceForm();
            if (\Yii::$app->request->isPost) {
                $form->attributes = \Yii::$app->request->post();
                return $this->asJson($form->save());
            }else{
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson($form->get());
            }
        } else {
            $data = (new ConfigForm())->config();
            return $this->render('price', ['data' => $data]);
        }
    }

    public function actionSubtitle()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new ConfigForm();
            if (\Yii::$app->request->isPost) {
                $form->attributes = \Yii::$app->request->post();
                return $this->asJson($form->save());
            }else{
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson($form->get());
            }
        } else {
            $data = (new ConfigForm())->config();
            return $this->render('subtitle', ['data' => $data]);
        }
    }
}
