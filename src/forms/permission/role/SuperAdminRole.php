<?php
/**
 * Created by PhpStorm.
 * User: wstianxia
 * @copyright: ©2024 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\forms\permission\role;

use app\forms\common\CommonAuth;
use app\models\UserIdentity;

class SuperAdminRole extends BaseRole
{
    public static $superAdmin;
    public $isSuperAdmin = true;

    public function getName()
    {
        return 'super_admin';
    }

    public function deleteRoleMenu($menu)
    {
        return false;
    }

    public function setPermission()
    {
        $this->permission = CommonAuth::getAllPermission();
    }

    public $showDetail = true;

    public function getSecondaryPermission()
    {
        return CommonAuth::getSecondaryPermissionAll();
    }

    /**
     * @return UserIdentity|null
     * 获取总管理员账号
     */
    public static function getSuperAdmin()
    {
        if (self::$superAdmin) {
            return self::$superAdmin;
        }
        self::$superAdmin = UserIdentity::findOne(['is_super_admin' => 1]);
        return self::$superAdmin;
    }
}
