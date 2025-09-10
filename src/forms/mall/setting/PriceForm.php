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

class PriceForm extends BasicConfigForm
{
    public function rules()
    {
        return array_merge (parent::rules (), [
            [['tab'], 'default', 'value' => self::TAB_BASIC],
        ]);
    }

    const TAB_BASIC = 'one';
    const TAB_ABROAD = 'two';

    public function getList()
    {
        return [
            self::TAB_BASIC => CommonOption::NAME_VOLCENGINE_PRICE,
            self::TAB_ABROAD => CommonOption::NAME_VOLCENGINE_ABROAD_PRICE,
        ];
    }

    public function one()
    {
        return [
            'unit_price' => 0,
            'renewal_unit_price' => 0,
            'tts_mega_exchange' => 1,
            'tts_big_exchange' => 1,
            'tts_long_exchange' => 1,
            'tts_exchange' => 1,
        ];
    }

    public function two()
    {
        return [
//            'unit_price' => 0,
//            'renewal_unit_price' => 0,
//            'tts_mega_exchange' => 1,
            'tts_big_exchange' => 1,
//            'tts_long_exchange' => 1,
//            'tts_exchange' => 1,
        ];
    }

    public function get()
    {
        $form = new UserConfigForm();
        $form->tab = UserConfigForm::TAB_INTEGRAL;
        $config = $form->config();
        $data = parent::get();
        $data['data']['data']['integral'] = $config;
        return $data;
    }
}
