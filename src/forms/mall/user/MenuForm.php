<?php
/**
 * link: https://www.netbcloud.com//
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\user;

use app\bootstrap\response\ApiCode;
use app\forms\common\CommonOption;
use app\forms\permission\menu\MenusForm;
use app\helpers\ArrayHelper;
use app\models\Model;

class MenuForm extends Model
{
    public $menu_list;

    public function rules()
    {
        return [
            [['menu_list'], 'safe'],
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
        foreach ($res['menus'] as $key => $item){
            if(!empty($item['meta']['hidden'])){
                unset($res['menus'][$key]);
                continue;
            }
            if(isset($item['children'])) {
                foreach ($item['children'] as $k => $child) {
                    if (!empty($child['meta']['hidden'])) {
                        unset($item['children'][$k]);
                        continue;
                    }
                    $item['children'][$k]['meta']['title_en'] = $res_en[$item['name']]['children'][$k]['meta']['title'];
                }
                if(count($item['children']) == 0){
                    unset($res['menus'][$key]);
                    continue;
                }else {
                    $item['children'] = array_values($item['children']);
                }
            }
            $item['meta']['title_en'] = $res_en[$item['name']]['meta']['title'];
            $res['menus'][$key] = $item;
        }
        $res['menus'] = array_values($res['menus']);
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $form->handleUserMenu($res['menus']),
                'original' => $res['menus'],
                'fs' => \Yii::$app->language
            ]
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        CommonOption::set(CommonOption::NAME_USER_MENU_SETTING, $this->menu_list, \Yii::$app->mall->id, CommonOption::GROUP_APP);
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '保存成功',
        ];
    }
}
