<?php
/**
 * Created by PhpStorm.
 * User: wstianxia
 * Date: 2019/1/30
 * Time: 16:29
 * @copyright: ©2021 深圳网商天下科技
 * @link: https://www.netbcloud.com/
 */

namespace app\bootstrap\currency;


interface BaseCurrency
{
    // 收入
    public function add($price, $desc, $customDesc);

    // 支出
    public function sub($price, $desc, $customDesc);

    // 查询
    public function select();

    // 退款
    public function refund($price, $desc, $customDesc);
}
