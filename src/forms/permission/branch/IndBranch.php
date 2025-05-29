<?php
/**
 * Created by PhpStorm.
 * User: wstianxia
 * @copyright: ©2021 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\forms\permission\branch;

class IndBranch extends BaseBranch
{
    public $ignore = 'ind';

    public function deleteMenu($menu)
    {
        if (isset($menu['ignore']) && in_array($this->ignore, $menu['ignore'])) {
            return true;
        }
        return false;
    }

    public function logoutUrl()
    {
        return \Yii::$app->urlManager->createUrl('admin/index/index');
    }

    public function checkMallUser($user)
    {
        return parent::checkMallUser($user) && $user->mall_id > 0;
    }
}
