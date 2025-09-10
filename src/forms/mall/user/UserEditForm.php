<?php
/**
 * link: https://www.netbcloud.com//
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\user;

use app\bootstrap\response\ApiCode;
use app\forms\common\CommonUser;
use app\models\Model;
use app\models\User;
use app\models\UserIdentity;
use app\models\UserInfo;
use app\models\UserPlatform;
use app\validators\PhoneNumberValidator;

class UserEditForm extends Model
{
    public $id;
    public $is_blacklist;
    public $remark;
    public $mobile;
    public $nickname;
    public $avatar;
    public $email;
    public $account;
    public $password;
    public $user_level;
    public $member_level;
    public $parent_id;

    public function rules()
    {
        return [
            [['is_blacklist', 'id', 'user_level', 'parent_id', 'member_level'], 'integer'],
            [['mobile', 'remark', 'email', 'nickname', 'avatar', 'account', 'password'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['mobile'], PhoneNumberValidator::className()],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'is_blacklist' => '是否黑名单',
            'mobile' => '联系方式',
            'remark' => '备注',
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        };

        if ($this->id) {
            /* @var User $user */
            $user = User::find()->alias('u')
                ->where(['u.id' => $this->id])
                ->one();
            if (!$user) {
                return [
                    'code' => ApiCode::CODE_ERROR,
                    'msg' => '数据为空'
                ];
            }
            $userInfo = $user->userInfo;
            $userPlatform = $user->platform;
            if ($this->email) {
                $platform = CommonUser::userAccount($userPlatform->platform_id, $this->email, $user->id);
                $userPlatform->platform_account = $this->email;
            } else {
                $platform = CommonUser::userAccount($userPlatform->platform_id, $this->mobile, $user->id);
                $userPlatform->platform_account = $this->mobile;
            }
            if ($platform) {
                return [
                    'code' => ApiCode::CODE_ERROR,
                    'msg' => $platform->platform_account . '已存在',
                ];
            }
        } else {
            $userPlatform = new UserPlatform();
            $userPlatform->mall_id = \Yii::$app->mall->id;
            $user = new User();
            $user->mall_id = \Yii::$app->mall->id;
            $userInfo = new UserInfo();
            $userInfo->register_time = mysql_timestamp();
            if ($this->account == 'email') {
                $platform = CommonUser::userAccount($this->account, $this->email);
                $user->username = $this->email;
                $userPlatform->platform_account = $this->email;
                $userPlatform->platform_id = UserPlatform::PLATFORM_EMAIL;
            } else {
                $platform = CommonUser::userAccount($this->account, $this->mobile);
                $user->username = $this->mobile;
                $userPlatform->platform_account = $this->mobile;
                $userPlatform->platform_id = UserPlatform::PLATFORM_MOBILE;
            }
            if ($platform) {
                return [
                    'code' => ApiCode::CODE_ERROR,
                    'msg' => $platform->platform_account . '，账号已存在',
                ];
            }
            $user->access_token = \Yii::$app->security->generateRandomString();
            $user->auth_key = \Yii::$app->security->generateRandomString();
            $user->password = \Yii::$app->getSecurity()->generatePasswordHash($this->password);
            $userPlatform->password = $user->password;
        }
        $userInfo->attributes = $this->attributes;
        $user->nickname = $this->nickname;

        $t = \Yii::$app->db->beginTransaction();
        try {
            if (!$user->save()) {
                throw new \Exception($this->getErrorMsg($user));
            }
            $user->generateUid();
            $userInfo->user_id = $user->id;
            $userInfo->code();
            if (!$userInfo->save()) {
                throw new \Exception($this->getErrorMsg($userInfo));
            }
            $identity = $user->identity;
            if (!$identity) {
                $identity = new UserIdentity();
                $identity->user_id = $user->id;
            }
            $identity->user_level = $this->user_level;
            $identity->member_level = $this->member_level ?: 0;
            if (!$identity->save()) {
                throw new \Exception($this->getErrorMsg($identity));
            }
            $userPlatform->user_id = $user->id;
            $userPlatform->password = $user->password;
            if (!$userPlatform->save()) {
                throw new \Exception($this->getErrorMsg($userPlatform));
            }

            $t->commit();
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '保存成功'
            ];
        } catch (\Exception $e) {
            $t->rollBack();
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage()
            ];
        }
    }

    public function saveRemark()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        try {
            /** @var UserInfo $userInfo */
            $userInfo = UserInfo::find()->where(['user_id' => $this->id])->one();
            if (!$userInfo) {
                throw new \Exception('用户信息不存在');
            }

            $userInfo->remark = $this->remark;
            if (!$userInfo->save()) {
                throw new \Exception('备注保存失败');
            }

            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '备注保存成功'
            ];
        } catch (\Exception $e) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage()
            ];
        }
    }
}
