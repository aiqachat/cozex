<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\admin\user;

use app\bootstrap\response\ApiCode;
use app\forms\common\CommonAuth;
use app\models\AdminInfo;
use app\models\Mall;
use app\models\Model;
use app\models\User;
use app\models\UserIdentity;

class UserForm extends Model
{
    public $page;
    public $id;
    public $password;
    public $keyword;
    public $type;

    public function rules()
    {
        return [
            [['page'], 'default', 'value' => 1],
            [['password'], 'string', 'min' => 6, 'max' => 16],
            [['id'], 'integer'],
            [['keyword', 'type'], 'trim'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '用户ID',
            'password' => '密码',
        ];
    }

    public function getList()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $query = User::find()->alias('u')->where([
            'u.is_delete' => 0,
            'u.mall_id' => \Yii::$app->user->identity->mall_id
        ])->with(['mall' => function ($query) {
            $query->andWhere(['is_delete' => 0,]);
        }]);

        /** @var UserIdentity $identity */
        $identity = \Yii::$app->user->identity->identity;
        if ($identity->is_super_admin) {
            $query->innerJoin(['i' => UserIdentity::tableName()], 'i.user_id = u.id');
            $query->andWhere([
                'or',
                ['i.is_admin' => 1],
                ['i.is_super_admin' => 1]
            ]);
        }else{
            $query->andWhere(['u.id' => \Yii::$app->user->id]);
        }

        $query->joinWith(['adminInfo ad' => function ($query) {
            if ($this->type == '未到期') {
                $query->andWhere([
                    'or',
                    ['=', 'ad.expired_at', '0000-00-00 00:00:00'],
                    ['>', 'ad.expired_at', date('Y-m-d H:i:s')],
                ]);
            } else if ($this->type == '已到期') {
                $query->andWhere([
                    'and',
                    ['!=', 'ad.expired_at', '0000-00-00 00:00:00'],
                    ['<=', 'ad.expired_at', date('Y-m-d H:i:s')],
                ]);
            }
        }]);

        if ($this->keyword) {
            $query->andWhere([
                'or',
                ['like', 'u.username', $this->keyword],
                ['like', 'ad.mobile', $this->keyword],
                ['like', 'ad.remark', $this->keyword],
            ]);
        }

        $list = $query->page($pagination)->orderBy('created_at DESC')->asArray()->all();
        foreach ($list as &$item) {
            $item['create_app_count'] = count($item['mall']);

            if (($item['adminInfo']['expired_at'] > date('Y-m-d H:i:s')) || $item['adminInfo']['expired_at'] == '0000-00-00 00:00:00') {
                $item['expired_type'] = '未到期';
            } else {
                $item['expired_type'] = '已到期';
            }
            $item['mobile'] = $item['adminInfo']['mobile'];
        }
        unset($item);

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '请求成功',
            'data' => [
                'list' => $list,
                'pagination' => $pagination
            ]
        ];
    }

    public function getDetail()
    {
        $detail = User::find()->where(['id' => $this->id])->with(['identity', 'adminInfo'])->asArray()->one();

        if ($detail) {
            $detail['adminInfo']['permissions'] = json_decode($detail['adminInfo']['permissions'], true);
            $detail['adminInfo']['secondary_permissions'] = CommonAuth::getSecondaryPermissionList($detail['adminInfo']['secondary_permissions']);
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '请求成功',
                'data' => [
                    'detail' => $detail,
                ]
            ];
        }

        return [
            'code' => ApiCode::CODE_ERROR,
            'msg' => '请求失败',
        ];
    }

    public function destroy()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            /** @var User $user */
            $user = User::find()->where(['id' => $this->id])
                ->with('identity')->one();

            if (!$user) {
                throw new \Exception('数据异常,该条数据不存在');
            }

            if ($user->identity->is_super_admin) {
                throw new \Exception('超级管理员账号不可删除');
            }

            $user->is_delete = 1;
            $res = $user->save();
            if (!$res) {
                throw new \Exception($this->getErrorMsg($user));
            }
            /** @var UserIdentity $userIdentity */
            $userIdentity = UserIdentity::find()->where(['user_id' => $user->id])->one();
            $userIdentity->is_delete = 1;
            $res = $userIdentity->save();
            if (!$res) {
                throw new \Exception($this->getErrorMsg($userIdentity));
            }

            /** @var AdminInfo $adminInfo */
            $adminInfo = AdminInfo::find()->where(['user_id' => $user->id])->one();
            $adminInfo->is_delete = 1;
            $res = $adminInfo->save();

            if (!$res) {
                throw new \Exception($this->getErrorMsg($userIdentity));
            }
            
            $res = Mall::updateAll([
                'is_disable' => 1,
            ], [
                'user_id' => $user->id,
                'is_delete' => 0,
            ]);


            $transaction->commit();
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '删除成功',
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage(),
                'error' => [
                    'line' => $e->getLine()
                ]
            ];
        }
    }

    public function destroy_bind()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
//        $app_admin = false;
//        $permission_arr = \Yii::$app->role->getPermission();
//        if (!is_array($permission_arr) && $permission_arr) {
//            $app_admin = true;
//        } else {
//            foreach ($permission_arr as $value) {
//                if ($value == 'app_admin') {
//                    $app_admin = true;
//                }
//            }
//        }
//        if (!$app_admin) {
//            return [
//                'code' => ApiCode::CODE_ERROR,
//                'msg' => '无权限操作',
//            ];
//        }
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $userIdentity = UserIdentity::find()->where(['user_id' => $this->id])->one();
            $userIdentity->is_admin = 0;
            $res = $userIdentity->save();
            if (!$res) {
                throw new \Exception($this->getErrorMsg($userIdentity));
            }

            $adminInfo = AdminInfo::find()->where(['user_id' => $this->id])->one();
            $adminInfo->is_delete = 1;
            $res = $adminInfo->save();

            if (!$res) {
                throw new \Exception($this->getErrorMsg($adminInfo));
            }

            $transaction->commit();
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '解绑成功',
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage(),
                'error' => [
                    'line' => $e->getLine()
                ]
            ];
        }
    }

    public function editPassword()
    {
        $user = User::find()->alias('u')->where(['u.id' => $this->id, 'u.is_delete' => 0])->one();

        if (!$user) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '用户不存在',
            ];
        }

        $user->password = \Yii::$app->getSecurity()->generatePasswordHash($this->password);
        $res = $user->save();

        if ($res) {
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '密码修改成功',
            ];
        }

        return [
            'code' => ApiCode::CODE_ERROR,
            'msg' => '密码修改失败',
        ];
    }
}
