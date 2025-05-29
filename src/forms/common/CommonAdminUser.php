<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common;


use app\models\AdminInfo;
use app\models\Model;
use app\models\User;
use app\models\UserIdentity;

class CommonAdminUser
{

    /**
     * 添加管理员
     * @param $data
     * @return AdminInfo
     * @throws \Exception
     */
    public static function createAdminUser($data)
    {
        $model = new Model();
        // $data 需传参数
        $arr = [
            'username' => '用户名',
            'password' => '密码',
            'mobile' => '手机号',
            'app_max_count' => '最大限制：无限制则传整数：0',
            'expired_at' => '账户过期日期 默认请传：0000-00-00 00:00:00',
            'permissions' => '账户插件权限: 默认请传空数组'
        ];

        self::checkData($arr, $data);

        // 判断账号是否重复
        $user = User::find()->alias('u')
            ->innerJoin(['i' => UserIdentity::tableName()], 'i.user_id = u.id')
            ->where([
                'or',
                ['i.is_admin' => 1],
                ['i.is_super_admin' => 1]
            ])
            ->andWhere(['u.username' => [$data['username']], 'u.is_delete' => 0])
            ->one();
        if ($user) {
            throw new \Exception('账号已存在');
        }

        $user = new User();
        $user->mall_id = 0;
        $user->username = $data['username'];
        $user->password = \Yii::$app->getSecurity()->generatePasswordHash($data['password']);
        $user->auth_key = \Yii::$app->security->generateRandomString();
        $user->access_token = \Yii::$app->security->generateRandomString();
        $user->nickname = $data['username'];
        $res = $user->save();
        if (!$res) {
            throw new \Exception($model->getErrorMsg($user));
        }

        // 用户角色信息
        $userIdentity = new UserIdentity();
        $userIdentity->user_id = $user->id;
        $userIdentity->is_admin = 1;
        $res = $userIdentity->save();
        if (!$res) {
            throw new \Exception($model->getErrorMsg($userIdentity));
        }

        // 管理员信息
        $adminInfo = new AdminInfo();
        $adminInfo->user_id = $user->id;
        $adminInfo->app_max_count = $data['app_max_count'];
        $adminInfo->remark = $data['remark'] ?: '';
        $adminInfo->mobile = $data['mobile'];
        if (!is_array($data['permissions'])) {
            throw new \Exception('权限列表必须为数组');
        }
        $adminInfo->permissions = \Yii::$app->serializer->encode($data['permissions']);
        $adminInfo->secondary_permissions = \Yii::$app->serializer->encode($data['secondary_permissions']);
        $adminInfo->expired_at = $data['expired_at'];
        $res = $adminInfo->save();
        if (!$res) {
            throw new \Exception($model->getErrorMsg($adminInfo));
        }

        return $adminInfo;
    }

    /**
     * 更新管理员
     * @param $data
     * @return AdminInfo
     * @throws \Exception
     */
    public static function updateAdminUser($data)
    {
        $model = new Model();
        // $data 需传参数
        $arr = [
            'user_id' => '用户 ID',
            'mobile' => '手机号',
            'app_max_count' => '最大限制：无限制则传整数：0',
            'expired_at' => '账户过期日期 默认请传：0000-00-00 00:00:00',
            'permissions' => '账户插件权限: 默认请传空数组'
        ];
        self::checkData($arr, $data);
        $user = User::findOne($data['user_id']);
        if (!$user) {
            throw new \Exception('用户不存在');
        }
        $userIdentity = UserIdentity::find()->where(['user_id' => $user->id])->one();
        if (!$userIdentity) {
            throw new \Exception('用户角色信息不存在');
        }
        /* @var AdminInfo $adminInfo */
        $adminInfo = AdminInfo::find()->where(['user_id' => $user->id, 'is_delete' => 0])->one();
        if (!$adminInfo) {
            throw new \Exception('用户信息不存在');
        }

        // 管理员信息
        $adminInfo->mobile = $data['mobile'];
        $adminInfo->app_max_count = $data['app_max_count'];
        $adminInfo->remark = $data['remark'] ?: '';
        if (!is_array($data['permissions'])) {
            throw new \Exception('权限列表必须为数组');
        }
        $adminInfo->permissions = \Yii::$app->serializer->encode($data['permissions']);
        $adminInfo->secondary_permissions = \Yii::$app->serializer->encode($data['secondary_permissions']);
        $adminInfo->expired_at = $data['expired_at'];
        $res = $adminInfo->save();
        if (!$res) {
            throw new \Exception($model->getErrorMsg($adminInfo));
        }

        return $adminInfo;
    }

    /**
     * 绑定管理员
     * @param $data
     * @return bool
     * @throws \Exception
     */
    public static function bindAdminUser($data)
    {
        $model = new Model();
        // $data 需传参数
        $arr = [
            'user_id' => '用户 ID',
        ];
        self::checkData($arr, $data);
        $user = User::findOne($data['user_id']);
        if (!$user) {
            throw new \Exception('用户不存在');
        }
        $userIdentity = UserIdentity::find()->where(['user_id' => $user->id])->one();
        if (!$userIdentity) {
            $userIdentity = new UserIdentity();
        }
        $adminInfo = AdminInfo::find()->where(['user_id' => $user->id])->one();
        if (!$adminInfo) {
            $adminInfo = new AdminInfo();
        }
        // 用户角色信息
        $userIdentity->user_id = $user->id;
        $userIdentity->is_admin = 1;
        $res = $userIdentity->save();
        if (!$res) {
            throw new \Exception($model->getErrorMsg($userIdentity));
        }
        // 管理员信息
        $adminInfo->user_id = $user->id;
        $adminInfo->app_max_count = 0;
        $adminInfo->remark = '管理员';
        $adminInfo->permissions = '[]';//\Yii::$app->serializer->encode($permissions_arr);
        $adminInfo->expired_at = '0000-00-00 00:00:00';
        $adminInfo->is_delete = 0;
        $res = $adminInfo->save();
        if (!$res) {
            throw new \Exception($model->getErrorMsg($adminInfo));
        }

        return true;
    }

    /**
     * 检测参数数据
     * @param $arr
     * @param $data
     * @throws \Exception
     */
    private static function checkData($arr, $data)
    {
        foreach ($arr as $key => $item) {
            if (!isset($data[$key])) {
                throw new \Exception('请传参数' . $key . '->' . $item);
            }
        }
    }
}
