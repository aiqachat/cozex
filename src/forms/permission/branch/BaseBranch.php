<?php
/**
 * Created by PhpStorm.
 * User: wstianxia
 * @copyright: ©2021 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\forms\permission\branch;

use app\forms\common\CommonAuth;
use app\models\AdminInfo;
use app\models\Model;
use app\models\User;

abstract class BaseBranch extends Model
{
    public $ignore;

    /**
     * @param $menu
     * @return mixed
     * @throws \Exception
     * 删除非本分支菜单
     */
    abstract public function deleteMenu($menu);

    /**
     * @return mixed
     * 获取商城退出跳转链接
     */
    abstract public function logoutUrl();

    /**
     * @param AdminInfo $adminInfo
     * @return array
     * 获取子账户权限
     */
    public function childPermission($adminInfo)
    {
        $all = CommonAuth::getAllPermission();
        if ($adminInfo->identity->is_super_admin == 1) {
            return $all;
        }
        $permission = [];
        if ($adminInfo->permissions) {
            $permission = json_decode($adminInfo->permissions, true);
        }
        return array_intersect($permission, $all);
    }

    protected function getKey($list)
    {
        $newList = [];
        foreach ($list as $item) {
            if (isset($item['name'])) {
                $newList[] = $item['name'];
            } elseif (is_array($item)) {
                $newList = array_merge($newList, $this->getKey($item));
            } else {
                continue;
            }
        }
        return $newList;
    }

    /**
     * @param AdminInfo $adminInfo
     * @return array|mixed
     */
    public function getSecondaryPermission($adminInfo)
    {
        if ($adminInfo->identity->is_super_admin == 1) {
            return CommonAuth::getSecondaryPermissionAll();
        }
        $permission = [];
        if ($adminInfo->secondary_permissions) {
            $permission = json_decode($adminInfo->secondary_permissions, true);
        }
        return $permission;
    }

    /**
     * @param User $user
     * @return bool
     * 校验用户是否具备登录后台的权限
     */
    public function checkMallUser($user)
    {
        return !$user->identity;
    }
}
