<?php
/**
 * Created by IntelliJ IDEA.
 * author: wstianxia
 * Date: 2019/7/12
 * Time: 10:27
 */

namespace app\forms\admin\mall;

use app\models\Mall;
use app\models\Model;
use app\models\User;
use yii\web\NotFoundHttpException;

class MallEntryForm extends Model
{
    public function entry($id)
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        $identity = $user->identity;
        if ($identity->is_super_admin == 1) {
            $model = Mall::findOne([
                'id' => $id,
                'is_recycle' => 0,
                'is_delete' => 0,
            ]);
        } else {
            $model = Mall::findOne([
                'user_id' => $user->id,
                'id' => $id,
                'is_recycle' => 0,
                'is_delete' => 0,
            ]);
        }
        if (!$model) {
            throw new NotFoundHttpException('商城不存在。');
        }
        \Yii::$app->setSessionMallId($model->id);
        return \Yii::$app->response->redirect(\Yii::$app->urlManager->createUrl(['netb/statistic/index']));
    }
}