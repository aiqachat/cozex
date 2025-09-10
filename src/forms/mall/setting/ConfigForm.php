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

use app\bootstrap\response\ApiCode;
use app\forms\common\CommonOption;
use app\validators\DomainValidator;

class ConfigForm extends BasicConfigForm
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['tab'], 'default', 'value' => self::TAB_BASIC],
        ]);
    }

    public function save()
    {
        if ($this->tab === self::TAB_BASIC) {
            if (!$this->validate()) {
                return $this->getErrorResponse();
            }
            if ($this->formData['user_domain']) {
                if (!preg_match((new DomainValidator())->pattern, $this->formData['user_domain'])) {
                    return [
                        'code' => ApiCode::CODE_ERROR,
                        'msg' => '用户端域名格式不正确',
                    ];
                }
                $this->formData['is_user_domain'] = 1;
            }
            $mall = \Yii::$app->mall;
            $mall->name = $this->formData['name'];
            $mall->save();
            unset($this->formData['name']);
            $data = CommonOption::checkDefault($this->formData, $this->getDefault());
            CommonOption::set($this->getName(), $data, \Yii::$app->mall->id, CommonOption::GROUP_APP);
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '保存成功'
            ];
        } else {
            return parent::save();
        }
    }

    public function config()
    {
        $data = parent::config();
        if ($this->tab === self::TAB_BASIC) {
            $data['name'] = \Yii::$app->mall->name;
            $data['currency_name'] = array_column(self::CURRENCY, null, 'id')[$data['currency']]['name'] ?? $data['currency'];
        }
        return $data;
    }

    const TAB_BASIC = 'basic';
    const TAB_CONTENT = 'content';
    const TAB_SMS = 'sms';
    const TAB_RECHARGE = 'recharge';
    const TAB_SUBTITLE = 'subtitle';

    public function getList()
    {
        return [
            self::TAB_BASIC => CommonOption::NAME_MALL_SETTING,
            self::TAB_CONTENT => CommonOption::NAME_MALL_CONTENT,
            self::TAB_SMS => CommonOption::NAME_SMS_SETTING,
            self::TAB_RECHARGE => CommonOption::NAME_RECHARGE_SETTING,
            self::TAB_SUBTITLE => CommonOption::NAME_SUBTITLE_SETTING,
        ];
    }

    public function basic()
    {
        $host = web_url();
        return [
            'name_en' => '',
            'mall_logo_pic' => $host . '/statics/img/mall/poster-big-shop.png', //商城logo
            'system_name_jump_url' => '',
            'user_domain' => '',
            'is_user_domain' => 0,
            'is_wechat_pay' => 1,
            'is_stripe_pay' => 1,
            'currency' => 'CNY',
            'currency_symbol' => '¥',
            'is_show_menu_text' => 1
        ];
    }

    const CURRENCY = [
        ['id' => 'CNY', 'name' => '人民币', 'symbol' => '¥'],
        ['id' => 'HKD', 'name' => '港币', 'symbol' => 'HK$'],
        ['id' => 'USD', 'name' => '美元', 'symbol' => '＄'],
        ['id' => 'EUR', 'name' => '欧元', 'symbol' => '€'],
        ['id' => 'JPY', 'name' => '日元', 'symbol' => '¥'],
        ['id' => 'KRW', 'name' => '韩元', 'symbol' => '₩'],
    ];

    public function content()
    {
        return [
            'voice_text' => '今天天气可好了，我打算和朋友一起去野餐，带上美食和饮料，找个舒适的草坪，什么烦恼都没了，你要不要和我们一起呀？也可以加我微信：xuyy0755',
            'voice_text_en' => 'The weather is so nice today! I plan to go on a picnic with my friends, bringing food and drinks. We\'ll find a comfortable lawn to relax, and all worries will disappear. Would you like to join us? You can also add my WeChat: xuyy0755',
        ];
    }

    public function recharge()
    {
        return [
            'title' => '我已了解：',
            'title_en' => 'I understand:',
            'agreement' => '充值协议',
            'agreement_en' => 'Recharge Agreement',
        ];
    }

    public function sms()
    {
        return [
            'app_id' => '',
            'access_key_id' => '',
            'access_key_secret' => '',
            'template_name' => '',
            'code_template_id' => '',
        ];
    }

    public function subtitle()
    {
        return [
            'account_id' => 0,
            'vc_price' => '1',
            'ata_price' => '1',
            'auc_price' => '1',
        ];
    }
}
