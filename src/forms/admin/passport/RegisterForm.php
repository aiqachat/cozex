<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\admin\passport;

use app\forms\admin\ConfigForm;
use app\bootstrap\response\ApiCode;
use app\forms\admin\user\UserUpdateJob;
use app\forms\common\CommonAdminUser;
use app\forms\MessageForm;
use app\models\AdminRegister;
use app\models\Model;
use app\models\User;
use app\models\UserIdentity;
use app\validators\PhoneNumberValidator;

class RegisterForm extends Model
{
    public $username;
    public $pass;
    public $checkPass;
    public $mobile;
    public $remark;
    public $name;
    public $captcha;
    public $validate_code_id;

    public $wechat_id;
    public $id_card_front_pic;
    public $id_card_back_pic;
    public $business_pic;

    public function rules()
    {
        return [
            // [['username', 'pass', 'checkPass', 'mobile', 'remark', 'name', 'captcha', 'validate_code_id'], 'required'],
            [['username', 'pass', 'checkPass', 'mobile', 'remark', 'name', 'validate_code_id'], 'required'],
            [['mobile'], PhoneNumberValidator::className()],
            // [
            //     ['captcha'], ValidateCodeValidator::class,
            //     'mobileAttribute' => 'mobile',
            //     'validateCodeIdAttribute' => 'validate_code_id',
            // ],
            [['wechat_id', 'id_card_front_pic', 'id_card_back_pic', 'business_pic',], 'trim'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'pass' => '密码',
            'checkPass' => '密码',
            'name' => '姓名/企业名',
            'mobile' => '手机号',
            'remark' => '申请原因',
            'captcha' => '验证码',
            'wechat_id' => '微信号',
            'id_card_front_pic' => '身份证正面',
            'id_card_back_pic' => '身份证反面',
            'business_pic' => '营业执照',
        ];
    }

    public function register()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $transaction = \Yii::$app->db->beginTransaction();

        try {
            if ($this->pass !== $this->checkPass) {
                throw new \Exception('两次密码输入不一致');
            }

            $adminRegister = AdminRegister::find()->where(['username' => $this->username, 'is_delete' => 0])
                ->andWhere([
                    'or',
                    ['status' => AdminRegister::AUDIT_STATUS_ING],
                    ['status' => AdminRegister::AUDIT_STATUS_TRUE],
                ])->one();

            $userExist = User::find()->alias('u')->where(['u.username' => $this->username, 'u.is_delete' => 0])
                ->innerJoin(['i' => UserIdentity::tableName()], 'i.user_id = u.id')
                ->andWhere([
                    'or',
                    ['i.is_admin' => 1],
                    ['i.is_super_admin' => 1]
                ])->one();

            if ($adminRegister && !($adminRegister->status == AdminRegister::AUDIT_STATUS_TRUE && !$userExist)) {
                throw new \Exception('您已提交过申请，请勿重复提交');
            }

            if ($userExist) {
                throw new \Exception('用户已存在');
            }

            $indSetting = (new ConfigForm())->config();
            if ($indSetting['is_required'] == 1) {
                if (!$this->id_card_front_pic) {
                    throw new \Exception('请上传身份证正面照');
                }
                if (!$this->id_card_back_pic) {
                    throw new \Exception('请上传身份证反面照');
                }
                if (!$this->business_pic) {
                    throw new \Exception('请上传营业执照');
                }
            }

            $adminRegister = new AdminRegister();
            $adminRegister->username = $this->username;
            $adminRegister->password = $this->checkPass;
            $adminRegister->mobile = $this->mobile;
            $adminRegister->name = $this->name;
            $adminRegister->remark = $this->remark;
            $adminRegister->wechat_id = $this->wechat_id;
            $adminRegister->id_card_front_pic = $this->id_card_front_pic;
            $adminRegister->id_card_back_pic = $this->id_card_back_pic;
            $adminRegister->business_pic = $this->business_pic;
            $res = $adminRegister->save();

            if (!$res) {
                throw new \Exception($this->getErrorMsg($adminRegister));
            }

            if ($indSetting['open_verify'] == 0) {
                $this->addUser($adminRegister, $indSetting);
                $msg = '注册成功,可直接登录';
            } else {
                try {
                    if ($indSetting) {
                        $user = User::findOne(1);
                        if(!empty($user->adminInfo->mobile)) {
                            $form = new MessageForm([
                                'template' => $indSetting['ind_sms']['aliyun']['register_apply_tpl_id'],
                            ]);
                            \Yii::$app->sms->module (\Yii::$app->sms::MODULE_ADMIN)->send ($user->adminInfo->mobile, $form->adminRegisterApply ([]));
                        }
                    }
                } catch (\Exception $exception) {
                    \Yii::error($exception);
                }
                $msg = '注册信息已提交，请等待管理员审核';
            }

            $transaction->commit();
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => $msg
            ];
        } catch (\Exception $e) {
            $transaction->rollback();
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage(),
                'line' => $e->getLine()
            ];
        }
    }

    public function addUser($adminRegister, $indSetting)
    {
        try {
            $adminRegister->status = 1;
            $res = $adminRegister->save();
            if (!$res) {
                throw new \Exception($this->getErrorMsg($adminRegister));
            }
            // 审核通过
            $expiredAt = date('Y-m-d H:i:s', time() + $indSetting['use_days'] * 24 * 60 * 60);
            $adminUser = CommonAdminUser::createAdminUser([
                'username' => $adminRegister->username,
                'password' => $adminRegister->password,
                'mobile' => $adminRegister->mobile,
                'app_max_count' => $indSetting['create_num'],
                'remark' => $adminRegister->remark,
                'expired_at' => $expiredAt,
                'permissions' => array_merge($indSetting['mall_permissions'], $indSetting['plugin_permissions']),
                'secondary_permissions' => $indSetting['secondary_permissions']
            ]);
            \Yii::$app->queue->delay(strtotime($expiredAt))->push(new UserUpdateJob([
                'user_id' => $adminUser->user_id
            ]));
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
