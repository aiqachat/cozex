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
                    if(isset($item['children'][0]['route'])) {
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
            $list[$key]['id'] = (string)$id;
            $list[$key]['pid'] = (string)$pid;

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
        if ($database) {
            $menu = ArrayHelper::index($menu, "name");
            foreach ($database as $item){
                if(isset($menu[$item['name']])){
                    $item['is_show'] = $item['is_show'] == 'true';
                    if($foreground && !$item['is_show']){
                        unset($menu[$item['name']]);
                        continue;
                    }
                    if(isset($item['children'])){
                        foreach ($item['children'] as $k => $child){
                            $item['children'][$k]['is_show'] = $child['is_show'] == 'true';
                            if($foreground && !$item['children'][$k]['is_show']){
                                unset($item['children'][$k], $menu[$item['name']]['children'][$k]);
                            }
                        }
                        if(count($item['children']) == 0){
                            unset($menu[$item['name']]);
                            continue;
                        }
                    }
                    $menu[$item['name']] = CommonOption::checkDefault($item, $menu[$item['name']], false);
                }
            }
            $menu = array_values($menu);
            ArrayHelper::multisort($menu, 'sort', SORT_DESC);
            foreach ($menu as &$item){
                if($foreground && isset($item['meta']['title_' . \Yii::$app->language])){
                    $item['meta']['title'] = $item['meta']['title_' . \Yii::$app->language];
                    unset($item['meta']['title_' . \Yii::$app->language]);
                }
                if(isset($item['children'])){
                    ArrayHelper::multisort($item['children'], 'sort', SORT_DESC);
                    if($foreground) {
                        foreach ($item['children'] as &$child) {
                            if (isset($child['meta']['title_' . \Yii::$app->language])) {
                                $child['meta']['title'] = $child['meta']['title_' . \Yii::$app->language];
                                unset($child['meta']['title_' . \Yii::$app->language]);
                            }
                        }
                        unset($child);
                    }
                }
            }
            unset($item);
        }
        return $menu;
    }
}
