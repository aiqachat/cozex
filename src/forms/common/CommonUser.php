<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common;

use app\forms\mall\setting\ConfigForm;
use app\models\AdminInfo;
use app\models\User;
use app\models\UserIdentity;
use app\models\UserInfo;
use app\models\UserPlatform;

class CommonUser
{
    public static function getUserInfo($columns = '')
    {
        if($columns) {
            return UserInfo::find()->where([
                'user_id' => \Yii::$app->user->id,
                'is_delete' => 0
            ])->select($columns)->one();
        }else{
            return \Yii::$app->user->identity->userInfo;
        }
    }

    /**
     * @param string $columns
     * @return array|\yii\db\ActiveRecord|null|AdminInfo
     */
    public static function getAdminInfo($columns = '')
    {
        if($columns) {
            $adminInfo = AdminInfo::find()->where([
                'user_id' => \Yii::$app->user->id,
                'is_delete' => 0
            ])->select($columns)->one();
        }else{
            $adminInfo = \Yii::$app->user->identity->adminInfo;
        }
        if ($adminInfo && isset($adminInfo->app_max_count)) {
            /* @var User $user */
            $user = \Yii::$app->user->identity;
            if ($user->identity->is_super_admin == 1) {
                $adminInfo->app_max_count = -1;
            }
        }
        return $adminInfo;
    }

    /**
     * @param string $columns
     * @return array|\yii\db\ActiveRecord|null|UserIdentity
     */
    public static function getUserIdentity($columns = '')
    {
        if($columns) {
            return UserIdentity::find()->where([
                'user_id' => \Yii::$app->user->id,
            ])->select($columns)->one();
        }

        return \Yii::$app->user->identity->identity;
    }

    public static function userWebUrl($path = '', $param = [])
    {
        if($path){
            $path = "/" . trim($path, "/");
        }
        $param['_mall_id'] = \Yii::$app->mall->id;
        $indSetting = (new ConfigForm())->config();
        if(!empty($indSetting['user_domain'])){
            $url = (\Yii::$app->request->isSecureConnection ? 'https://' : 'http://') . $indSetting['user_domain'];
            $url .= "/#{$path}?" . http_build_query($param);
        }else {
            $url = \Yii::$app->request->hostInfo .
                rtrim(dirname(str_replace('/notify', '', \Yii::$app->request->baseUrl)), "/") .
                "/user/#{$path}?" . http_build_query($param);
        }
        return $url;
    }

    public static function userAccount($type, $username)
    {
        switch ($type){
            case UserPlatform::PLATFORM_EMAIL:
            case UserPlatform::PLATFORM_MOBILE:
            case UserPlatform::PLATFORM_GOOGLE:
                break;
            default:
                throw new \Exception('账号类型错误');
        }
        return UserPlatform::findOne([
            'mall_id' => \Yii::$app->mall->id,
            'platform_account' => $username,
            'platform_id' => $type,
        ]);
    }
}