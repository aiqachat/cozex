<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/12/5 15:00
 */


namespace app\forms\api;


use app\bootstrap\response\ApiCode;
use app\events\UserEvent;
use app\models\Model;
use app\models\User;
use app\models\UserIdentity;
use app\models\UserInfo;
use yii\helpers\Json;

abstract class LoginForm extends Model
{
    /**
     * @return LoginUserInfo
     */
    abstract protected function getUserInfo();

    public function login()
    {
        try {
            $userInfo = $this->getUserInfo();
            $userInfo->user_platform = $userInfo->user_platform ?: $userInfo->platform;
            $userInfo->user_platform_user_id = $userInfo->user_platform_user_id ?: $userInfo->platform_user_id;
        } catch (\Exception $exception) {
            \Yii::warning($exception);
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $exception->getMessage(),
            ];
        }
        $user = User::findOne([
            'username' => $userInfo->username,
            'is_delete' => 0,
        ]);
        \Yii::warning(Json::encode($userInfo, JSON_UNESCAPED_UNICODE));
        if ($userInfo->scope == 'auth_base') {
            if ($user) {
                $this->triggerEvent($user, false);
                return [
                    'code' => 0,
                    'data' => [
                        'access_token' => $user->access_token,
                    ],
                ];
            } else {
                return [
                    'code' => 1,
                    'data' => [
                        'access_token' => null,
                    ],
                ];
            }
        }
        $t = \Yii::$app->db->beginTransaction();
        $register = false;
        // 微信小程序和微信公众平台数据以unionid进行互通
        if (!$user && $userInfo->unionId) {
            $user = User::findOne([
                'unionid' => $userInfo->unionId,
                'is_delete' => 0,
            ]);
        }
        if (!$user) {
            $register = true;
            $user = new User();
            $user->access_token = \Yii::$app->security->generateRandomString();
            $user->auth_key = \Yii::$app->security->generateRandomString();
            $user->username = $userInfo->username;
            $user->password = \Yii::$app->security
                ->generatePasswordHash(\Yii::$app->security->generateRandomString(), 5);
        }
        $user->unionid = $userInfo->unionId;
        $user->nickname = $userInfo->nickname;
        if (!$user->save()) {
            $t->rollBack();
            return $this->getErrorResponse($user);
        }

        // 用户信息表
        $uInfo = UserInfo::findOne([
            'user_id' => $user->id,
            'is_delete' => 0,
        ]);
        if (!$uInfo) {
            $uInfo = new UserInfo();
            $uInfo->user_id = $user->id;
            $uInfo->avatar = $userInfo->avatar;
            $uInfo->platform_user_id = $userInfo->platform_user_id;
            $uInfo->is_delete = 0;
        } else {
            $uInfo->avatar = $userInfo->avatar;
        }
        if (!$uInfo->save()) {
            $t->rollBack();
            return $this->getErrorResponse($uInfo);
        }

        // 用户角色表
        $userIdentity = UserIdentity::findOne([
            'user_id' => $user->id,
            'is_delete' => 0
        ]);
        if (!$userIdentity) {
            $userIdentity = new UserIdentity();
            $userIdentity->user_id = $user->id;
        }
        if (!$userIdentity->save()) {
            $t->rollBack();
            return $this->getErrorMsg($userIdentity);
        }
        $t->commit();
        $this->triggerEvent($user, $register);
        return [
            'code' => 0,
            'data' => [
                'access_token' => $user->access_token,
            ],
        ];
    }

    private function triggerEvent($user, $register = false)
    {
        $event = new UserEvent();
        $event->sender = $this;
        $event->user = $user;
        if ($register) {
            \Yii::$app->trigger(User::EVENT_REGISTER, $event);
        }
        \Yii::$app->trigger(User::EVENT_LOGIN, $event);
    }
}
