<?php
/**
 * Created by PhpStorm.
 * User: wstianxia
 * @copyright: ©2021 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\forms\permission\role;

class AdminRole extends BaseRole
{
    public function getName()
    {
        return 'admin';
    }

    public function deleteRoleMenu($menu)
    {
        $permission = $this->getPermission();
        if (isset($menu['key']) && !in_array($menu['key'], $permission)) {
            return true;
        }
        return false;
    }

    private $allow = ['cache_manage', 'small_procedure'];

    public function setPermission()
    {
        // 总账户授予单独子账户的权限
        $permission = $this->getPluginPermission();
        // 所有子账户公有的权限
        $permission = array_merge($permission, $this->allow);
        $this->permission = $permission;
    }

    // 插件相关权限
    public $pluginPermission;

    public function getPluginPermission()
    {
        if ($this->pluginPermission) {
            return $this->pluginPermission;
        }
        $adminInfo = $this->user->adminInfo;
        $permission = \Yii::$app->branch->childPermission($adminInfo);
        $this->pluginPermission = $permission;
        return $permission;
    }
}
