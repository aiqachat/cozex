<?php
/**
 * Created by PhpStorm.
 * User: wstianxia
 * @copyright: ©2021 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\forms\permission\role;

class UserRole extends BaseRole
{
    public function getName()
    {
        return 'user';
    }

    public function deleteRoleMenu($menu)
    {
        $permission = $this->getPermission();
        if (isset($menu['key']) && !in_array($menu['key'], $permission)) {
            return true;
        }
        return false;
    }

    private $allow = [];

    public function setPermission()
    {
        // 普通用户所属商城管理员的权限
        $permission = $this->mall->role->permission;
        // 合并普通用户公有的权限
        $permission = array_merge($permission, $this->allow);
        $this->permission = $permission;
    }
}
