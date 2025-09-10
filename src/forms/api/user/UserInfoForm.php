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
use app\forms\mall\member\MemberLevelForm;
use app\models\MemberLevel;
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
        if ($userInfo && !$userInfo->invite_code) {
            $userInfo->code();
            $userInfo->save();
        }

        // 获取会员权限
        $memberLevelForm = new MemberLevelForm();
        $memberLevelForm->id = $user->identity->member_level;
        $member_permission = $memberLevelForm->getPermissions();

        $result = [
            'id' => $user->id,
            'username' => $user->username,
            'nickname' => $user->nickname,
            'uid' => $user->uid,
            'info' => $userInfo,
            'level_name' => $user->identity->level->name ?? '--',
            'promotion_ratio' => $user->identity->level->promotion_commission_ratio ?? 0,
            'member_level' => $user->identity->memberLevel->name ?? '--',
            'member_permission' => $member_permission
        ];

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => $result,
        ];
    }
}
