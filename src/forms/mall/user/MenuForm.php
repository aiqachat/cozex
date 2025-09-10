<?php
/**
 * link: https://www.netbcloud.com//
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\user;

use app\bootstrap\response\ApiCode;
use app\forms\common\CommonOption;
use app\forms\mall\setting\ConfigForm;
use app\forms\permission\menu\MenusForm;
use app\helpers\ArrayHelper;
use app\models\Model;

class MenuForm extends Model
{
    public $menu_list;
    public $is_show_menu_text;

    public function rules()
    {
        return [
            [['menu_list'], 'safe'],
            [['is_show_menu_text'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
        ];
    }

    public function get()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        \Yii::$app->user->setIdentity(\Yii::$app->mall->user);
        $form = new MenusForm();
        $form->isExist = true;
        \Yii::$app->language = 'zh';
        $res = $form->getMenus('user');
        \Yii::$app->language = 'en';
        $res_en = $form->getMenus('user');
        $res_en = ArrayHelper::index($res_en['menus'], "name");

        $res['menus'] = $this->processMenuItems($res['menus'], $res_en);
        $data = (new ConfigForm())->config();

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $form->handleUserMenu($res['menus']),
                'original' => $res['menus'],
                'is_show_menu_text' => $data['is_show_menu_text']
            ]
        ];
    }

    /**
     * 递归处理菜单项及其子菜单
     * @param array $menuItems 菜单项数组
     * @param array $menuItemsEn 英文对照菜单数组
     * @return array 处理后的菜单项数组
     */
    private function processMenuItems($menuItems, $menuItemsEn)
    {
        $result = [];

        foreach ($menuItems as $item) {
            // 如果菜单项被标记为隐藏，则跳过
            if (!empty($item['meta']['hidden'])) {
                continue;
            }

            // 为菜单项添加英文标题
            if (isset($menuItemsEn[$item['name']]['meta']['title'])) {
                $item['meta']['title_en'] = $menuItemsEn[$item['name']]['meta']['title'];
            }

            // 递归处理子菜单
            if (isset($item['children']) && is_array($item['children'])) {
                $childrenEn = $menuItemsEn[$item['name']]['children'] ?? [];
                $childrenEn = ArrayHelper::index($childrenEn, 'name');

                $processedChildren = $this->processMenuItems($item['children'], $childrenEn);

                // 如果处理后子菜单非空，则保留该菜单项
                if (!empty($processedChildren)) {
                    $item['children'] = array_values($processedChildren);
                    $result[] = $item;
                }
            } else {
                // 没有子菜单的菜单项直接添加到结果中
                $result[] = $item;
            }
        }

        return $result;
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        CommonOption::set(CommonOption::NAME_USER_MENU_SETTING, $this->menu_list, \Yii::$app->mall->id, CommonOption::GROUP_APP);
        $form = new ConfigForm();
        $data = $form->config();
        $data['is_show_menu_text'] = $this->is_show_menu_text;
        $form->formData = $data;
        $form->save();
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '保存成功',
        ];
    }
}
