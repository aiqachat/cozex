<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/12/5 15:00
 */

namespace app\forms\api\user;

use app\bootstrap\response\ApiCode;
use app\events\UserEvent;
use app\models\Model;
use app\models\ModelActiveRecord;
use app\models\User;
use app\models\UserIdentity;
use app\models\UserInfo;

abstract class LoginForm extends Model
{
    /**
     * @return LoginUserInfo
     */
    abstract protected function getUserInfo();

    public function login()
    {
        ModelActiveRecord::$log = false; // 不记录日志了
        try {
            $userInfo = $this->getUserInfo();
        } catch (\Exception $exception) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $exception->getMessage(),
            ];
        }

        $register = false;
        $t = \Yii::$app->db->beginTransaction();
        $user = $userInfo->userPlatform->user;
        if (!$user) {
            $register = true;
            $user = new User();
            $user->mall_id = \Yii::$app->mall->id;
            $user->access_token = \Yii::$app->security->generateRandomString();
            $user->auth_key = \Yii::$app->security->generateRandomString();
            $user->username = $userInfo->username;
            $user->password = \Yii::$app->security
                ->generatePasswordHash($userInfo->password ?: \Yii::$app->security->generateRandomString());
        }else {
            // 一天只更新一次token
            if (date ("Y-m-d 00:00:00", strtotime ($user->updated_at)) < date ("Y-m-d 00:00:00", time ())) {
                $user->access_token = \Yii::$app->security->generateRandomString ();
            }
        }
        if(!$user->nickname) {
            $user->nickname = $userInfo->nickname;
        }
        if (!$user->save()) {
            $t->rollBack();
            return $this->getErrorResponse($user);
        }
        $user->generateUid();

        // 用户信息表
        $uInfo = $user->userInfo;
        if (!$uInfo) {
            $uInfo = new UserInfo();
            $uInfo->user_id = $user->id;
            if($userInfo->avatar) {
                $uInfo->avatar = $userInfo->avatar;
            }
            if($userInfo->email){
                $uInfo->email = $userInfo->email;
            }
            if($userInfo->mobile){
                $uInfo->mobile = $userInfo->mobile;
            }
            $uInfo->is_delete = 0;
            if (!$uInfo->save()) {
                $t->rollBack();
                return $this->getErrorResponse($uInfo);
            }
        }

        // 用户角色表
        $userIdentity = $user->identity;
        if (!$userIdentity) {
            $userIdentity = new UserIdentity();
            $userIdentity->user_id = $user->id;
            if (!$userIdentity->save()) {
                $t->rollBack();
                return $this->getErrorMsg($userIdentity);
            }
        }

        if($register){
            $userInfo->userPlatform->user_id = $user->id;
            if(!$userInfo->userPlatform->save()){
                $t->rollBack();
                return $this->getErrorMsg($userInfo->userPlatform);
            }
        }
        $t->commit();

        if($uInfo->is_blacklist){
            throw new \Exception('账号已禁用，请联系管理员');
        }
        $this->triggerEvent($user, $register);
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '登录成功',
            'data' => [
                'access_token' => $user->access_token,
                'id' => $userInfo->userPlatform->id,
                'route' => '/voice/ttsModel'
            ],
        ];
    }

    public function triggerEvent($user, $register = false)
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
