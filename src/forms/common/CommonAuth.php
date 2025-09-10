<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司有限公司
 * author: wstianxia
 */

namespace app\forms\common;

use app\forms\common\attachment\CommonAttachment;
use app\forms\Menus;

class CommonAuth
{
    private $notPermissionRoutes = [];

    /**
     * 获取总管理员可分配的权限列表
     */
    public static function getPermissionsList()
    {
        return [
            'mall' => [
                [
                    'display_name' => '用户管理',
                    'name' => 'user',
                ],
                [
                    'display_name' => '上传设置',
                    'name' => 'attachment',
                ],
                [
                    'display_name' => '语音技术',
                    'name' => 'voice',
                ],
                [
                    'display_name' => '视觉智能',
                    'name' => 'visual',
                ],
                [
                    'display_name' => '字幕技术',
                    'name' => 'subtitle',
                ],
                [
                    'display_name' => 'coze资源',
                    'name' => 'coze',
                ],
            ],
        ];
    }

    /**
     * 获取子账号管理员不能访问的路由
     */
    public static function getPermissionsRouteList()
    {
        // TODO 此处要使用缓存
        $adminMenus = Menus::getAdminMenus();
        $mallMenus = Menus::getMallMenus();
        $menus = array_merge($adminMenus, $mallMenus);

        $commonAuth = new CommonAuth();

        $adminPermissionKeys = \Yii::$app->role->permission;
        $superAdminPermissionKeys = Menus::MALL_SUPER_ADMIN_KEY;

        $commonAuth->getMenusRoute($menus, $adminPermissionKeys, $superAdminPermissionKeys);

        return $commonAuth->notPermissionRoutes;
    }

    private function getMenusRoute($menus, $adminKeys, $superAdminKeys, $contain = false)
    {
        foreach ($menus as $k => $item) {
            if (isset($item['key']) && !in_array($item['key'], $adminKeys)) {
                $this->notPermissionRoutes[] = $item['route'] ?? '';
                if (isset($item['children'])) {
                    $this->getMenusRoute($item['children'], $adminKeys, $superAdminKeys, true);
                }
            }

            if (isset($item['key']) && in_array($item['key'], $superAdminKeys)) {
                $this->notPermissionRoutes[] = $item['route'] ?? '';
                if (isset($item['children'])) {
                    $this->getMenusRoute($item['children'], $adminKeys, $superAdminKeys, true);
                }
            }

            if($contain && !isset($item['key'])){
                $this->notPermissionRoutes[] = $item['route'] ?? '';
            }

            if (isset($item['children'])) {
                $menus[$k]['children'] = $this->getMenusRoute($item['children'], $adminKeys, $superAdminKeys, $contain);
            }
        }

        return $menus;
    }

    public static function getAllPermission()
    {
        $permissions = self::getPermissionsList();
        $list = [];
        foreach ($permissions as $permission) {
            if (is_array($permission)) {
                foreach ($permission as $value) {
                    if (isset($value['name'])) {
                        $list[] = $value['name'];
                    }
                }
            }
        }
        return $list;
    }

    /**
     * @return array
     * 二级菜单的所有权限
     */
    public static function getSecondaryPermissionAll()
    {
        return [
            'attachment' => CommonAttachment::getCommon()->getDefaultAuth(),
        ];
    }

    /**
     * @return array
     * 二级菜单的没有权限
     */
    public static function getSecondaryPermission()
    {
        return [
            'attachment' => [],
        ];
    }

    /**
     * @return array
     * 二级权限的默认权限
     */
    public static function secondaryDefault()
    {
        return [
            'attachment' => CommonAttachment::getCommon()->getDefaultAuth(),
        ];
    }

    /**
     * @param $json
     * @return array
     * 兼容新的二级权限
     */
    public static function getSecondaryPermissionList($json)
    {
        $secondaryDefault = CommonAuth::secondaryDefault();
        if ($json) {
            $secondaryPermissions = json_decode($json, true);
            foreach ($secondaryDefault as $key => $item) {
                if (!isset($secondaryPermissions[$key])) {
                    $secondaryPermissions[$key] = $item;
                }
            }
        } else {
            $secondaryPermissions = $secondaryDefault;
        }
        return $secondaryPermissions;
    }
}
