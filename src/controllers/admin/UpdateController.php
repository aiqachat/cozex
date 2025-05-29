<?php
/**
 * Created by IntelliJ IDEA.
 * author: wstianxia
 * Date: 2019/3/1
 * Time: 15:58
 */

namespace app\controllers\admin;

use app\controllers\behaviors\SuperAdminFilter;
use app\bootstrap\response\ApiCode;
use app\forms\admin\UpdateForm;

class UpdateController extends AdminController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'superAdminFilter' => [
                'class' => SuperAdminFilter::class,
            ],
        ]);
    }

    public function actionIndex()
    {
        if (\Yii::$app->request->isAjax) {
            try {
                $versions = (new UpdateForm())->getVersionData();
            } catch (\Exception $exception) {
                return [
                    'code' => ApiCode::CODE_ERROR,
                    'msg' => $exception->getMessage(),
                ];
            }
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'data' => $versions,
            ];
        } else {
            return $this->render('index');
        }
    }

    public function actionUpdate()
    {
        if (\Yii::$app->request->isPost) {
            return (new UpdateForm())->doUpdate();
        }
    }
}