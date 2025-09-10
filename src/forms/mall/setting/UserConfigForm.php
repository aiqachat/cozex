<?php
/**
 * Created by PhpStorm
 * User: chenzs
 * Date: 2020/9/29
 * Time: 4:15 下午
 * @copyright: ©2020 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\forms\mall\setting;

use app\forms\common\CommonOption;

class UserConfigForm extends BasicConfigForm
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['tab'], 'default', 'value' => self::TAB_REGISTER],
        ]);
    }

    const TAB_REGISTER = 'basic';
    const TAB_INTEGRAL = 'integral';
    const TAB_SETTING = 'setting';

    public function getList()
    {
        return [
            self::TAB_REGISTER => CommonOption::NAME_USER_REGISTER_LOGIN,
            self::TAB_INTEGRAL => CommonOption::NAME_USER_INTEGRAL_SETTING,
            self::TAB_SETTING => CommonOption::NAME_USER_SETTING,
        ];
    }

    public function basic()
    {
        return [
            'email_register_login' => 1,
            'mobile_register_login' => 0,
            'google_login' => 0,
            'is_check' => 0,
            'agreement_title' => '用户协议',
            'agreement_title_en' => 'User Agreement',
            'agreement' => '',
            'agreement_en' => '',
            'privacy_policy_title' => '隐私政策',
            'privacy_policy_title_en' => 'Privacy Policy',
            'privacy_policy' => '',
            'privacy_policy_en' => '',
        ];
    }

    public function integral()
    {
        return [
            'integral_rate' => 100,
            'give_integral' => 0,
        ];
    }

    public function setting()
    {
        return [
            'title' => \Yii::$app->mall->name,
            'title_en' => '',
            'keywords' => '',
            'keywords_en' => '',
            'description' => '',
            'description_en' => '',
        ];
    }
}
