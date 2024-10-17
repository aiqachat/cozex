<?php
/**
 * @link:https://www.netbcloud.com/
 * @copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 *
 * Created by PhpStorm.
 * User: wstianxia
 * Date: 2018/12/4
 * Time: 13:42
 */

namespace app\controllers;


use app\models\DistrictArr;

class DistrictController extends Controller
{
    public function actionIndex()
    {
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->isPost) {
                $level = \Yii::$app->request->post('level');
            } elseif (\Yii::$app->request->isGet) {
                $level = \Yii::$app->request->get('level');
            } else {
                $level = 3;
            }
            switch ($level) {
                case 3:
                    $level = null;
                    break;
                case 2:
                    $level = 'district';
                    break;
                case 1:
                    $level = 'city';
                    break;
                default:
                    $level = null;
            }
            $list = DistrictArr::getArr();
            $district = DistrictArr::getList($list, $level);
            return $this->asJson([
                'code' => 0,
                'msg' => '',
                'data' => [
                    'district' => $district
                ]
            ]);
        }
        return $this->asJson([
            'code' => 1,
            'msg' => '返回错误'
        ]);
    }
}
