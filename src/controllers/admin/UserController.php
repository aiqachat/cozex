<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\controllers\admin;

use app\bootstrap\response\ApiCode;
use app\forms\admin\MessageRemindSettingForm;
use app\forms\admin\SmsCaptchaForm;
use app\forms\admin\user\BatchPermissionForm;
use app\forms\admin\user\RegisterAuditForm;
use app\forms\admin\user\UserEditForm;
use app\forms\admin\user\UserForm;
use app\forms\common\CommonAuth;
use app\forms\common\CommonUser;
use app\forms\common\attachment\CommonAttachment;
use app\models\User;

class UserController extends AdminController
{
    public function actionIndex()
    {
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->isPost) {
            } else {
                $form = new UserForm();
                $form->attributes = \Yii::$app->request->get();

                return $this->asJson($form->getList());
            }
        } else {
            return $this->render('index');
        }
    }

    /**
     * 管理员用户信息
     */
    public function actionUser()
    {
        /* @var User $user */
        $user = User::find()->where(['id' => \Yii::$app->user->id, 'is_delete' => 0])->with('identity')->one();

        if (!$user) {
            \Yii::$app->user->logout();
            return [
                'code' => ApiCode::CODE_NOT_LOGIN,
                'msg' => '请求成功'
            ];
        }
        $adminInfo = CommonUser::getAdminInfo();

        $newUser = [
            'nickname' => $user->nickname,
            'mobile' => $adminInfo->mobile,
            'app_max_count' => $adminInfo->app_max_count == -1 ? '无限制' : $adminInfo->app_max_count,
            'username' => $user->username,
            'identity' => $user->identity
        ];

        return $this->asJson([
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '请求成功',
            'data' => [
                'user' => $newUser,
                'admin_info' => $adminInfo,
                'expired_data' => $this->getExpiredData($adminInfo)
            ]
        ]);
    }

    private function getExpiredData($adminInfo)
    {
        $messageRemind = new MessageRemindSettingForm();
        $newDay = (strtotime($adminInfo->expired_at) - time()) / (24 * 60 * 60);
        if ($newDay <= 0) {
            $newDay = 0;
        } elseif ($newDay <= 1) {
            $newDay = 1;
        } else {
            $newDay = floor($newDay);
        }

        $setting = $messageRemind->getSetting();

        $data = [
            'is_show_message' => false,
            'expired_day' => $newDay,
            'expired_at' => $adminInfo->expired_at,
            'id' => $adminInfo->id
        ];

        if ($setting['status'] == 1 && $adminInfo->expired_at != '0000-00-00 00:00:00' && $adminInfo->expired_at > date('Y-m-d H:i:s') && $newDay < $setting['day']) {
            $data['is_show_message'] = true;
            $data = array_merge($data, $setting);
        }

        return $data;
    }

    /**
     * 账户编辑
     * @return array|string|\yii\web\Response
     */
    public function actionEdit()
    {
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->isPost) {
                $formData = \Yii::$app->request->post('form');
                $form = new UserEditForm();
                $form->user_id = $formData['id'] ?? '';
                $form->attributes = $formData;
                $form->attributes = $formData['adminInfo'];
                $form->page = \Yii::$app->request->post('page');
                $form->permissions = \Yii::$app->request->post('permissions');
                $form->isCheckExpired = \Yii::$app->request->post('isCheckExpired');
                $form->isAppMaxCount = \Yii::$app->request->post('isAppMaxCount');
                return $form->save();
            } else {
                $form = new UserForm();
                $form->attributes = \Yii::$app->request->get();
                $detail = $form->getDetail();

                return $this->asJson($detail);
            }
        } else {
            return $this->render('edit');
        }
    }

    /**
     * 管理员账号解绑
     * @return \yii\web\Response
     */
    public function actionDestroyBind()
    {
        $form = new UserForm();
        $form->id = \Yii::$app->request->post('id');
        $res = $form->destroy_bind();

        return $this->asJson($res);
    }

    /**
     * 管理员账号删除
     * @return \yii\web\Response
     */
    public function actionDestroy()
    {
        $form = new UserForm();
        $form->id = \Yii::$app->request->post('id');
        $res = $form->destroy();

        return $this->asJson($res);
    }

    /**
     * 修改密码
     * @return \yii\web\Response
     */
    public function actionEditPassword()
    {
        $form = new UserForm();
        $form->attributes = \Yii::$app->request->post();
        $res = $form->editPassword();

        return $this->asJson($res);
    }

    /**
     * 当前账号修改密码
     * @return \yii\web\Response
     */
    public function actionAdminEditPassword()
    {
        $form = new UserForm();
        $form->attributes = \Yii::$app->request->post();
        $form->id = \Yii::$app->user->id;
        $res = $form->editPassword();

        return $this->asJson($res);
    }

    /**
     * 注册账号审核列表
     */
    public function actionRegister()
    {
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->isPost) {
            } else {
                $form = new RegisterAuditForm();
                $form->attributes = \Yii::$app->request->get();
                $list = $form->getList();

                return $this->asJson($list);
            }
        } else {
            return $this->render('register');
        }
    }

    /**
     * 账号审核
     * @return \yii\web\Response
     */
    public function actionRegisterAudit()
    {
        $form = new RegisterAuditForm();
        $form->attributes = \Yii::$app->request->post();

        return $this->asJson($form->audit());
    }

    /**
     * 注册审核账号删除
     */
    public function actionRegisterDestroy()
    {
        $form = new RegisterAuditForm();
        $form->attributes = \Yii::$app->request->post();
        $res = $form->destroy();

        return $this->asJson($res);
    }


    public function actionPermissions()
    {
        return $this->asJson([
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '请求成功',
            'data' => [
                'permissions' => CommonAuth::getPermissionsList(),
                'storage' => CommonAttachment::getCommon()->getStorage()
            ]
        ]);
    }

    public function actionMe()
    {
        return $this->render('me');
    }

    public function actionUpdateMobile()
    {
        $form = new SmsCaptchaForm();
        $form->attributes = \Yii::$app->request->post();

        return $this->asJson($form->updateMobile());
    }

    /**
     * 批量设置账户权限
     * @return \yii\web\Response
     */
    public function actionBatchPermission()
    {
        $form = new BatchPermissionForm();
        $form->formData = \Yii::$app->request->post('form');

        return $this->asJson($form->save());
    }
}
