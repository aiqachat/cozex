<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/11/15 9:42
 */


namespace app\controllers\admin;

use app\bootstrap\response\ApiCode;
use app\forms\admin\mall\MallCreateForm;
use app\forms\admin\mall\MallEntryForm;
use app\forms\admin\mall\MallForm;
use app\forms\admin\mall\MallRemovalForm;
use app\forms\admin\mall\MallUpdateForm;
use app\forms\common\CommonUser;
use app\models\Mall;
use app\bootstrap\Pagination;
use app\models\User;
use yii\db\Query;

class MallController extends AdminController
{
    public function actionIndex($keyword = null, $is_recycle = 0)
    {
        if (\Yii::$app->request->isAjax) {
            $query = Mall::find()->where([
                'is_recycle' => $is_recycle,
                'is_delete' => 0,
            ]);

            $type = \Yii::$app->request->get('type');
            if ($type == '未到期') {
                $query->andWhere([
                    'or',
                    ['=', 'expired_at', '0000-00-00 00:00:00'],
                    ['>', 'expired_at', date('Y-m-d H:i:s')],
                ]);
            } else if ($type == '已到期') {
                $query->andWhere([
                    'and',
                    ['!=', 'expired_at', '0000-00-00 00:00:00'],
                    ['<=', 'expired_at', date('Y-m-d H:i:s')],
                ]);
            }

            /** @var User $user */
            $user = \Yii::$app->user->identity;
            if ($user->identity->is_super_admin != 1) {
                $query->andWhere(['user_id' => $user->id,]);
            }

            // TODO 不知有何作用
            $userId = \Yii::$app->request->get('user_id');
            if ($userId) {
                $query->andWhere(['user_id' => $userId]);
            }
            $keyword = trim($keyword);
            if ($keyword) {
                $userIds = User::find()->where(['like', 'username', $keyword])->select('id');
                $query->andWhere([
                    'OR',
                    ['LIKE', 'name', $keyword,],
                    ['user_id' => $userIds]
                ]);
            }

            $count = $query->count();
            $pagination = new Pagination(['totalCount' => $count,]);
            $list = $query
                ->with(['user' => function ($query) {
                    /** @var Query $query */
                    $query->select('id,username,nickname,is_delete');
                }])
                ->orderBy('id DESC')
                ->offset($pagination->offset)
                ->limit($pagination->limit)
                ->asArray()
                ->all();

            foreach ($list as &$item) {
                if ($item['expired_at'] == '0000-00-00 00:00:00') {
                    $item['expired_at_text'] = '永久';
                } elseif (strtotime($item['expired_at']) < time()) {
                    $item['expired_at_text'] = '已过期';
                } else {
                    $item['expired_at_text'] = $item['expired_at'];
                }

                if (($item['expired_at'] > date('Y-m-d H:i:s')) || $item['expired_at'] == '0000-00-00 00:00:00') {
                    $item['expired_type'] = '未到期';
                } else {
                    $item['expired_type'] = '已到期';
                }
            }
            unset($item);
            $adminInfo = CommonUser::getAdminInfo();
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'data' => [
                    'list' => $list,
                    'pagination' => $pagination,
                    'admin_info' => $adminInfo,
                ],
            ];
        } else {
            return $this->render('index');
        }
    }

    public function actionCreate()
    {
        $form = new MallCreateForm();
        $form->attributes = \Yii::$app->request->post();
        return $this->asJson($form->save());
    }


    // 加入回收站|移出回收站
    public function actionUpdate()
    {
        $form = new MallUpdateForm();
        $form->attributes = \Yii::$app->request->post();
        return $this->asJson($form->save());
    }

    // 进入商城
    public function actionEntry($id)
    {
        return (new MallEntryForm())->entry($id);
    }

    // 迁移
    public function actionRemoval()
    {
        $form = new MallRemovalForm();
        $form->attributes = \Yii::$app->request->get();
        return $this->asJson($form->save());
    }

    // 商城禁用
    public function actionDisable()
    {
        $form = new MallForm();
        $form->attributes = \Yii::$app->request->get();

        return $this->asJson($form->disable());
    }

    // 商城回收站删除
    public function actionDelete()
    {
        $form = new MallForm();
        $form->attributes = \Yii::$app->request->get();

        return $this->asJson($form->delete());
    }
}
