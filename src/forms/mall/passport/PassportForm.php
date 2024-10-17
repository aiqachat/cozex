<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\passport;

use app\bootstrap\response\ApiCode;
use app\jobs\UserActionJob;
use app\models\AdminInfo;
use app\models\Model;
use app\models\User;

class PassportForm extends Model
{
    public $username;
    public $password;
    public $mall_id;
    public $pic_captcha;
    public $checked;

    const DES_KEY = "des_song_123456"; // 加密key @czs

    public function rules()
    {
        return [
            [['username', 'password', 'pic_captcha', 'checked'], 'required'],
            [['mall_id'], 'string'],
            [['pic_captcha'], 'captcha', 'captchaAction' => 'site/pic-captcha'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'password' => '密码',
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
            $key = md5(self::DES_KEY);
            $this->password = @openssl_decrypt(
                base64_decode($this->password),
                'des-ede3-cbc',
                $key,
                OPENSSL_RAW_DATA,
                substr($key, 0, 8)
            ); // 解密密码 @czs

            $query = User::find()->alias('u')->joinWith(['identity' => function ($query) {
                $query->andWhere([
                    'or',
                    ['is_super_admin' => 1],
                    ['is_admin' => 1]
                ]);
            }])->andWhere(['u.username' => $this->username, 'u.is_delete' => 0]);

            /** @var User $user */
            $user = $query->one();
            if (!$user) {
                throw new \Exception('用户不存在');
            }

            if (!\Yii::$app->getSecurity()->validatePassword($this->password, $user->password)) {
                throw new \Exception('密码错误');
            }

            $adminInfo = AdminInfo::find()->where(['user_id' => $user->id])->one();
            // 加判断是为了排除员工账号
            if (($user->identity->is_admin === 1 || $user->identity->is_super_admin === 1) && !$adminInfo) {
                throw new \Exception('账户异常：账户信息不存在');
            }

            if ($user->identity->is_admin === 1 &&
                $adminInfo->expired_at !== '0000-00-00 00:00:00' &&
                time() > strtotime($adminInfo->expired_at)) {
                throw new \Exception('账户已过期！请联系管理员');
            }

            $duration = $this->checked == 'true' ? 86400 : 0;
            \Yii::$app->user->login($user, $duration);
            setcookie('__login_route', '/mall/passport/login');
            $route = 'mall/statistic/index';
            setcookie('__login_role', 'admin');

            $dataArr = [
                'newBeforeUpdate' => [],
                'newAfterUpdate' => [],
                'modelName' => 'app\models\User',
                'modelId' => $user->id,
                'remark' => '管理员登录',
                'user_id' => $user->id,
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
                    'line' => $e->getLine()
                ]
            ];
        }
    }
}
