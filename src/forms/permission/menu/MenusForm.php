<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\permission\menu;

use app\forms\common\CommonOption;
use app\forms\Menus;
use app\forms\permission\branch\BaseBranch;
use app\forms\permission\role\BaseRole;
use app\helpers\ArrayHelper;
use app\models\Model;

/**
 * @property BaseBranch $branch
 * @property BaseRole $role
 */
class MenusForm extends Model
{
    private $branch;
    private $role;

    public $currentRouteInfo = [];
    public $currentRoute;
    public $type;
    public $isExist = false;

    /**
     * 有实际页面且不菜单列表中的路由填写在此处
     */
    const existList = [
        //        'netb/statistic/index',
        'admin/cache/clean',
    ];

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->branch = \Yii::$app->branch;
        $this->role = \Yii::$app->role;
    }

    /**
     * @param $type string 有效参数mall|plugin
     * @return array
     * @throws \Exception
     * 获取菜单
     */
    public function getMenus($type)
    {
        if (!in_array($type, ['admin', 'netb', 'user'])) {
            throw new \Exception('type 传入参数无效');
        }

        switch ($type) {
            case 'admin':
                $originalMenus = Menus::getAdminMenus();
                break;
            case 'netb':
                $originalMenus = Menus::getMallMenus();
                break;
            case 'user':
                $originalMenus = Menus::getUserMenus();
                break;
            default:
                throw new \Exception('type 传入参数无效');
        }
        // 去除不需显示的菜单
        $menus = $this->deleteMenu($originalMenus);
        // 菜单列表
        $menus = $this->resetMenus($menus);

        if (!$this->isExist) {
            if (!in_array($this->currentRoute, self::existList)) {
                // 开启调试模式才显示
                if (env('YII_DEBUG')) {
                    throw new \Exception('页面路由未正常配置（会导致账号无法进入该页面）,请检查');
                }
            }
        }
        return [
            'menus' => $menus,
            'currentRouteInfo' => $this->currentRouteInfo,
        ];
    }

    /**
     * @param $menus
     * @return array
     * @throws \Exception
     * 去除非本分支和本角色拥有的菜单
     */
    public function deleteMenu($menus)
    {
        foreach ($menus as $index => $item) {
            //插件统计左侧菜单隐藏
            if (isset($item['is_statistics_show']) && !$item['is_statistics_show']) {
                unset($menus[$index]);
                continue;
            }
            if ($this->branch->deleteMenu($item)) {
                unset($menus[$index]);
                continue;
            }
            $item['is_show'] = true;
            if (isset($item['children']) && is_array($item['children'])) {
                $item['children'] = $this->deleteMenu($item['children']);
                if (count($item['children']) <= 0) {
                    unset($menus[$index]);
                    continue;
                } else {
                    if (isset($item['children'][0]['route'])) {
                        $item['route'] = $item['children'][0]['route'];
                    }
                }
            }
            if ($this->role->deleteMenu($item)) {
                unset($menus[$index]);
                continue;
            }
            $menus[$index] = $item;
        }
        return array_values($menus);
    }

    /**
     * 给自定义路由列表 追加ID 及 PID
     * @param array $list 自定义的多维路由数组
     * @param int $id 权限ID
     * @param int $pid 权限PID
     * @return mixed
     */
    private function resetMenus(array $list, &$id = 1, $pid = 0)
    {
        foreach ($list as $key => $item) {
            $list[$key]['id'] = (string) $id;
            $list[$key]['pid'] = (string) $pid;

            // 前端选中的菜单
            if (isset($list[$key]['route']) && $this->currentRoute === $list[$key]['route']) {
                $this->currentRouteInfo = $list[$key];
                $list[$key]['is_active'] = true;
                $this->isExist = true;
            }
            if (isset($list[$key]['action'])) {
                foreach ($list[$key]['action'] as $aItem) {
                    if (isset($aItem['route']) && $aItem['route'] === $this->currentRoute) {
                        $list[$key]['is_active'] = true;
                        $this->isExist = true;
                    }
                }
            }

            if (isset($item['children'])) {
                $id++;
                $list[$key]['children'] = $this->resetMenus($item['children'], $id, $id - 1);
                foreach ($list[$key]['children'] as $child) {
                    if (isset($child['is_active']) && $child['is_active']) {
                        $list[$key]['is_active'] = true;
                    }
                }
            }

            if (isset($item['action'])) {
                $id++;
                $list[$key]['action'] = $this->resetMenus($item['action'], $id, $id - 1);
            }

            !isset($item['children']) && !isset($item['action']) ? $id++ : $id;
        }

        return $list;
    }

    public function handleUserMenu($menu, $foreground = false)
    {
        $database = CommonOption::get(
            CommonOption::NAME_USER_MENU_SETTING,
            \Yii::$app->mall->id,
            CommonOption::GROUP_APP
        );

        if (!$database) {
            return $menu;
        }

        $menu = ArrayHelper::index($menu, "name");

        // 处理一级菜单
        foreach ($database as $item) {
            // 跳过不存在的菜单
            if (!isset($menu[$item['name']])) {
                continue;
            }

            // 转换is_show为布尔值
            $item['is_show'] = $item['is_show'] === 'true';

            // 前台模式下隐藏不显示的菜单
            if ($foreground && !$item['is_show']) {
                unset($menu[$item['name']]);
                continue;
            }

            // 处理子菜单
            if (isset($item['children']) && isset($menu[$item['name']]['children'])) {
                $subMenu = ArrayHelper::index($menu[$item['name']]['children'], "name");
                $this->processChildrenMenu($item['children'], $subMenu, $foreground);

                // 如果第一个子菜单为空，则移除父菜单
                if (empty($subMenu)) {
                    unset($menu[$item['name']]);
                    continue;
                }
            }

            $meta = CommonOption::checkDefault($item['meta'], $menu[$item['name']]['meta'], false);
            $menu[$item['name']] = CommonOption::checkDefault($item, $menu[$item['name']], false, false);
            $menu[$item['name']]['meta'] = $meta;
            if(!empty($subMenu)){
                // 更新子菜单
                $menu[$item['name']]['children'] = $subMenu;
            }else{
                unset($menu[$item['name']]['children']);
            }
        }

        // 转换回数组并排序
        $menu = array_values($menu);
        ArrayHelper::multisort($menu, 'sort', SORT_DESC);

        // 处理多语言及子菜单排序
        foreach ($menu as &$item) {
            // 处理多语言标题（会递归处理所有子菜单）
            if ($foreground) {
                $this->processMenuTitle($item);
            }
        }
        unset($item);
        return $menu;
    }

    /**
     * 处理子菜单
     * @param array $children 配置中的子菜单
     * @param array &$subMenu 实际子菜单
     * @param bool $foreground 是否为前台模式
     */
    private function processChildrenMenu($children, &$subMenu, $foreground)
    {
        foreach ($children as $child) {
            if (!isset($subMenu[$child['name']])) {
                continue;
            }

            $child['is_show'] = $child['is_show'] === 'true';
            if ($foreground && !$child['is_show']) {
                unset($subMenu[$child['name']]);
                continue;
            }

            // 处理更深层次的子菜单
            if (isset($child['children']) && isset($subMenu[$child['name']]['children'])) {
                $nextLevelSubMenu = ArrayHelper::index($subMenu[$child['name']]['children'], "name");
                $this->processChildrenMenu($child['children'], $nextLevelSubMenu, $foreground);
            }

            $meta = CommonOption::checkDefault($child['meta'], $subMenu[$child['name']]['meta'], false);
            $subMenu[$child['name']] = CommonOption::checkDefault($child, $subMenu[$child['name']], false, false);
            $subMenu[$child['name']]['meta'] = $meta;
            // 更新子菜单
            if (!empty($nextLevelSubMenu)) {
                $subMenu[$child['name']]['children'] = $nextLevelSubMenu;
            } else {
                // 如果没有可显示的子菜单，则移除children属性
                unset($subMenu[$child['name']]['children']);
            }
        }
        $subMenu = array_values($subMenu);
        if(count($subMenu) > 1) {
            // 对子菜单排序
            ArrayHelper::multisort($subMenu, 'sort', SORT_DESC);
        }
    }

    /**
     * 处理菜单多语言标题
     * @param array &$item 菜单项
     */
    private function processMenuTitle(&$item)
    {
        if (isset($item['meta']['title_' . \Yii::$app->language])) {
            $item['meta']['title'] = $item['meta']['title_' . \Yii::$app->language];
            unset($item['meta']['title_' . \Yii::$app->language]);
        }

        // 递归处理子菜单的多语言标题
        if (isset($item['children']) && is_array($item['children'])) {
            foreach ($item['children'] as &$childItem) {
                $this->processMenuTitle($childItem);
            }
            unset($childItem);
        }
    }
}
