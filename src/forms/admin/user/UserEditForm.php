<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\admin\user;

use app\bootstrap\response\ApiCode;
use app\forms\common\CommonAdminUser;
use app\forms\common\CommonAuth;
use app\models\Mall;
use app\models\Model;
use app\validators\PhoneNumberValidator;

class UserEditForm extends Model
{
    public $user_id;
    public $username;
    public $password;
    public $mobile;
    public $app_max_count;
    public $remark;
    public $expired_at;
    public $permissions;
    public $isCheckExpired;
    public $isAppMaxCount;
    public $secondary_permissions;
    public $page; // 该page用于保存之后返回列表指定页数

    public function rules()
    {
        return [
            [['username', 'password', 'mobile', 'app_max_count',
                'expired_at', 'isCheckExpired', 'isAppMaxCount'], 'required'],
            [['user_id', 'page'], 'integer'],
            [['mobile', 'username', 'password'], 'trim'],
            [['mobile'], PhoneNumberValidator::className()],
            [['remark', 'secondary_permissions'], 'safe'],
            [['permissions'], 'default', 'value' => []],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'password' => '密码',
            'mobile' => '手机号',
            'app_max_count' => '数量'
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if (!$this->isAppMaxCount && $this->app_max_count < 0) {
                throw new \Exception('可创建数量不能小于0');
            }
            $count = Mall::find()->where([
                'user_id' => $this->user_id,
                'is_delete' => 0,
            ])->count();
            if ($this->app_max_count != -1 && $count > $this->app_max_count) {
                throw new \Exception('可创建数量不能小于' . $count);
            }
            
            $expiredAt = !$this->isCheckExpired ? $this->expired_at : '0000-00-00 00:00:00';
            if (!$this->secondary_permissions) {
                $this->secondary_permissions = CommonAuth::secondaryDefault();;
            }
            if (in_array('attachment', $this->permissions)
                && (!isset($this->secondary_permissions['attachment']) || empty($this->secondary_permissions['attachment']))) {
                throw new \Exception('请选择上传权限');
            }

            if ($this->user_id) {
                $adminUser = CommonAdminUser::updateAdminUser([
                    'user_id' => $this->user_id,
                    'mobile' => $this->mobile,
                    'app_max_count' => $this->app_max_count,
                    'remark' => $this->remark,
                    'expired_at' => $expiredAt,
                    'permissions' => $this->permissions,
                    'secondary_permissions' => $this->secondary_permissions,
                ]);
            } else {
                $adminUser = CommonAdminUser::createAdminUser([
                    'username' => $this->username,
                    'password' => $this->password,
                    'mobile' => $this->mobile,
                    'app_max_count' => $this->app_max_count,
                    'remark' => $this->remark,
                    'expired_at' => $expiredAt,
                    'permissions' => $this->permissions,
                    'secondary_permissions' => $this->secondary_permissions,
                ]);
            }

            if (!$this->isCheckExpired) {
                $expiredAt = strtotime($this->expired_at) - time();
                \Yii::$app->queue->delay(max($expiredAt, 0))->push(new UserUpdateJob([
                    'user_id' => $adminUser->user_id
                ]));
            }

            $transaction->commit();
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '保存成功',
                'data' => [
                    'url' => \Yii::$app->urlManager->createUrl('admin/user/index') . '&page=' . $this->page,
                    'user_id' => $adminUser->user_id
                ]
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
}
