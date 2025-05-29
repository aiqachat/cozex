<?php
/**
 * Created by PhpStorm.
 * User: wstianxia
 * Date: 2019/1/29
 * Time: 11:14
 * @copyright: ©2021 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\forms\api\user;

use app\bootstrap\response\ApiCode;
use app\models\Model;
use app\models\User;

class UserInfoForm extends Model
{
    public function getInfo()
    {
        if (\Yii::$app->user->isGuest) {
            return ['code' => ApiCode::CODE_NOT_LOGIN];
        }

        /** @var User $user */
        $user = \Yii::$app->user->identity;
        $userInfo = $user->userInfo;
        $result = [
            'id' => $user->id,
            'username' => $user->username,
            'nickname' => $user->nickname,
            'uid' => $user->uid,
            'info' => $userInfo,
            'platform_account' => $user->platform->platform_account ?? $user->username,
            'platform_id' => $user->platform->platform_id ?? '',
        ];

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => $result,
        ];
    }
}
