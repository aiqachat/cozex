<?php
/**
 * Created by PhpStorm.
 * User: wstianxia
 * @copyright: ©2021 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\forms\permission\role;

use app\models\Mall;
use app\models\User;
use app\forms\Menus;
use app\models\Model;

/**
 * @property array $permission
 * @property string $name
 * @property User $user
 * @property Mall $mall
 * @property boolean isSuperAdmin 是否是超级管理员
 */
abstract class BaseRole extends Model
{
    protected $permission;
    protected $name;
    public $user;
    public $mall;
    public $isSuperAdmin = false;

    public function init()
    {
        parent::init();
        $this->setPermission();
    }

    /**
     * @return mixed
     * 获取角色身份
     */
    abstract public function getName();

    /**
     * @param $menu
     * @return boolean true--删除菜单|false--保留菜单
     * @throws \Exception
     * 只删除非本角色权限内的菜单
     */
    abstract public function deleteRoleMenu($menu);

    /**
     * @return mixed
     * 设置角色权限
     */
    abstract public function setPermission();

    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * @param $menu
     * @return bool
     * @throws \Exception
     * 删除非本角色权限内的菜单
     */
    public function deleteMenu($menu)
    {
        if ($this->deleteSuperAdmin($menu)) {
            return true;
        }
        if ($this->deleteRoleMenu($menu)) {
            return true;
        }
        return false;
    }

    /**
     * @param $menu
     * @return bool
     * 去除只允许超级管理员访问的KEY
     */
    public function deleteSuperAdmin($menu)
    {
        if ($this->getName() == 'super_admin') {
            return false;
        }
        if (isset($menu['key']) && in_array($menu['key'], Menus::MALL_SUPER_ADMIN_KEY)) {
            return true;
        }
        return false;
    }

    public $showDetail = false;

    /**
     * @return array
     * 账户权限
     */
    public function getAccountPermission()
    {
        return $this->permission;
    }

    /**
     * @param $item
     * @return bool
     * 校验链接是否显示
     */
    public function checkLink($item)
    {
        if (!isset($item['key'])) {
            return true;
        }
        $permission = $this->getAccountPermission();
        if (in_array($item['key'], $permission)) {
            return true;
        }
        return false;
    }

    /**
     * @return $this
     * @throws \Exception
     * 获取商城所属的子账号或总账号权限
     */
    public function getAccount()
    {
        return $this;
    }
}
