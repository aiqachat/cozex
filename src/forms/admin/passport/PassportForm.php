<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\admin\passport;

use app\bootstrap\response\ApiCode;
use app\jobs\UserActionJob;
use app\models\AdminRegister;
use app\models\Mall;
use app\models\Model;
use app\models\User;
use app\models\UserIdentity;

class PassportForm extends Model
{
    public $username;
    public $password;
    public $user_type;
    public $mall_id;
    public $pic_captcha;
    public $checked;

    const DES_KEY = "des_song_123456"; // 加密key @czs

    public function rules()
    {
        return [
            [['username', 'password', 'user_type', 'pic_captcha', 'checked'], 'required'],
            [['mall_id'], 'string'],
            [['pic_captcha'], 'captcha', 'captchaAction' => 'site/pic-captcha'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'password' => '密码',
            'user_type' => '用户类型',
            'mall_id' => '商城ID',
            'pic_captcha' => '验证码',
        ];
    }

    public function login()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        try {
            $mallId = base64_decode($this->mall_id);
            $key = md5(self::DES_KEY);
            $this->password = @openssl_decrypt(
                base64_decode($this->password),
                'des-ede3-cbc',
                $key,
                OPENSSL_RAW_DATA,
                substr($key, 0, 8)
            ); // 解密密码 @czs

            $query = User::find()->alias('u')
                ->innerJoin(['i' => UserIdentity::tableName()], 'i.user_id = u.id')
                ->andWhere(['u.username' => $this->username, 'u.is_delete' => 0]);

            if ((int)$this->user_type === 1) {
                $query->andWhere([
                    'or',
                    ['i.is_super_admin' => 1],
                    ['i.is_admin' => 1]
                ]);
            }

            if ((int)$this->user_type === 2) {
                $query->andWhere(['u.mall_id' => $mallId]);
            }

            /** @var User $user */
            $user = $query->one();
            if (!$user) {
                $registerExist = AdminRegister::find()->where([
                    'username' => $this->username,
                    'status' => 0,
                    'is_delete' => 0,
                ])->exists();
                if ($registerExist) {
                    return [
                        'code' => ApiCode::CODE_ERROR,
                        'msg' => '用户审核中',
                        'data' => [
                            'register' => true,
                        ],
                    ];
                }
                throw new \Exception('用户不存在');
            }

            // 其它账号登录需判断 商城是否过期
            if ($this->user_type != 1) {
                $mall = Mall::findOne($user->mall_id);
                if (!$mall) {
                    throw new \Exception('商城不存在，ID:' . $user->mall_id);
                }
                if ($mall->expired_at != '0000-00-00 00:00:00' && strtotime($mall->expired_at) < time()) {
                    throw new \Exception('商城已过期，无法登录，请联系管理员');
                }
            }

            if (!\Yii::$app->getSecurity()->validatePassword($this->password, $user->password)) {
                throw new \Exception('密码错误');
            }

            $adminInfo = $user->adminInfo;
            if (($user->identity->is_admin === 1 || $user->identity->is_super_admin === 1) && !$adminInfo) {
                throw new \Exception('账户异常：账户信息不存在');
            }

            if ($user->identity->is_admin === 1 &&
                $adminInfo->expired_at !== '0000-00-00 00:00:00' &&
                time() > strtotime($adminInfo->expired_at)) {
                throw new \Exception('账户已过期！请联系管理员');
            }

            // 一天只更新一次token
            if(date("Y-m-d 00:00:00", strtotime($user->updated_at)) < date("Y-m-d 00:00:00", time())){
                $user->access_token = \Yii::$app->security->generateRandomString();
                $user->isLog = false;
                $user->save();
            }

            $duration = $this->checked == 'true' ? 86400 : 0;
            \Yii::$app->user->login($user, $duration);
            if ($this->user_type == 1) {
                // 管理员
                $route = 'admin/index/index';
                $user->setLoginData(User::LOGIN_ADMIN, \Yii::$app->requestedRoute);
            } else {
                $route = 'netb/statistic/index';
//                $user->setLoginData(User::LOGIN_STAFF, \Yii::$app->requestedRoute . '&mall_id=' . base64_encode($mallId));
            }

            $dataArr = [
                'newBeforeUpdate' => [],
                'newAfterUpdate' => [],
                'modelName' => 'app\models\User',
                'modelId' => $user->id,
                'remark' => $this->user_type == 1 ? '管理员登录' : '其它登录',
                'user_id' => $user->id,
                'mall_id' => $mallId
            ];
            $class = new UserActionJob($dataArr);
            \Yii::$app->queue->delay(0)->push($class);

            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '登录成功',
                'data' => [
                    'url' => $route
                ]
            ];
        } catch (\Exception $e) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage(),
                'error' => [
                    'line' => $e->getLine(),
                ]
            ];
        }
    }
}
