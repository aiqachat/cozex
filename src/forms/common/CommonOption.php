<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common;

use app\models\Option;

class CommonOption
{
    const GROUP_ADMIN = 'admin';
    const GROUP_APP = 'app';

    const NAME_IND_SETTING = 'ind_setting'; // saas设置
    const NAME_MALL_SETTING = 'mall_setting'; // 基础设置
    const NAME_MALL_CONTENT = 'mall_content'; // 内容设置
    const NAME_COZE_WEB_SDK = 'coze_web_sdk'; // coze web设置
    const NAME_VERSION = 'version'; // 更新记录的系统版本号
    const NAME_OVERRUN = 'overrun';
    const NAME_WX_PAY = 'wx_pay_config'; // 微信支付设置
    const NAME_STRIPE_PAY = 'stripe_pay_config'; // 全球支付设置
    const NAME_USER_INTEGRAL_SETTING = 'user_integral_setting'; // 积分设置
    const NAME_VOLCENGINE_PRICE = 'volcengine_price'; // 价格设置
    const NAME_VOLCENGINE_ABROAD_PRICE = 'volcengine_abroad_price'; // 价格设置
    const NAME_SMS_SETTING = 'sms_setting'; // 短信设置
    const NAME_USER_REGISTER_LOGIN = 'user_register_login'; // 用户注册登录
    const NAME_USER_SETTING = 'user_global_setting'; // 用户全局设置
    const NAME_USER_MENU_SETTING = 'user_menu_setting'; // 用户菜单设置
    const NAME_RECHARGE_SETTING = 'recharge_setting'; //充值相关
    const NAME_VISUAL_PRICE = 'visual_price'; // 视觉智能价格设置
    const NAME_VISUAL_ARK_SETTING = 'visual_ark_setting'; // 视觉智能-火山方舟设置
    const NAME_VISUAL_ARK_GLOBAL_SETTING = 'visual_ark_global_setting'; // 视觉智能(国际版)-火山方舟设置
    const NAME_SUBTITLE_SETTING = 'subtitle_setting'; // 字幕设置
    const NAME_CONTENT_SETTING = 'content_setting'; // 内容设置
    const NAME_CONTENT_SQUARE_SETTING = 'content_square_setting'; // 内容广场设置

    private static $loadedOptions = [];

    /**
     * @param $name string Name
     * @param $value mixed Value
     * @param $mall_id integer Integer
     * @param $group string Name
     * @return boolean
     */
    public static function set($name, $value, $mall_id = null, $group = '')
    {
        if (empty($name)) {
            return false;
        }
        if($mall_id === null){
            try {
                $mall_id = \Yii::$app->mall->id;
            }catch (\Exception $e){
                $mall_id = 0;
            }
        }
        $model = Option::findOne([
            'name' => $name,
            'mall_id' => $mall_id,
            'group' => $group,
        ]);
        if (!$model) {
            $model = new Option();
            $model->name = $name;
            $model->mall_id = $mall_id;
            $model->group = $group;
        }
        $model->value = \Yii::$app->serializer->encode($value);
        $result = $model->save();
        if ($result) {
            $loadedOptionKey = md5(json_encode([
                'name' => $name,
                'mall_id' => $mall_id,
                'group' => $group,
            ]));
            self::$loadedOptions[$loadedOptionKey] = $value;
        }
        return $result;
    }

    /**
     * @param $name string Name
     * @param $mall_id integer Integer
     * @param $group string Name
     * @param $default string Name
     * @return null|array|string|object
     */
    public static function get($name, $mall_id = null, $group = '', $default = null)
    {
        if($mall_id === null){
            try {
                $mall_id = \Yii::$app->mall->id;
            }catch (\Exception $e){
                $mall_id = 0;
            }
        }
        $loadedOptionKey = md5(json_encode([
            'name' => $name,
            'mall_id' => $mall_id,
            'group' => $group,
        ]));
        if (array_key_exists($loadedOptionKey, self::$loadedOptions)) {
            return self::$loadedOptions[$loadedOptionKey];
        }
        $model = Option::findOne([
            'name' => $name,
            'mall_id' => $mall_id,
            'group' => $group
        ]);

        if (!$model) {
            $result = $default;
        } else {
            $result = \Yii::$app->serializer->decode($model->value);
        }
        self::$loadedOptions[$loadedOptionKey] = $result;
        return $result;
    }

    /**
     * @param array $data
     * @param array $default
     * @return array
     * 处理新增的默认数据
     */
    public static function checkDefault($data, $default, $unset = true, $recursion = true)
    {
        foreach ($default as $key => $item) {
            if (!isset($data[$key])) {
                $data[$key] = $item;
                continue;
            }
            if (is_array($item)) {
                if($recursion) {
                    $data[$key] = self::checkDefault((array)$data[$key], $item, $unset, $recursion);
                }
            }else{
                if(is_int($item)){
                    $data[$key] = intval($data[$key]);
                }elseif(is_numeric($item)){
                    $data[$key] = floatval($data[$key]);
                }
            }
        }
        if($unset){
            foreach ($data as $key => $item){
                if(is_numeric($key)){
                    $default = null;
                    break;
                }
            }
            if($default) {
                $data = array_intersect_key($data, $default);
            }
        }
        return $data;
    }

    public static function getWeChatV3Key()
    {
        $v3_key = self::get('wechat_v3_default_key');
        if (empty($v3_key)) {
            $v3_key = \Yii::$app->security->generateRandomString();
            $v3_key = str_replace (['-', '_'], ['U', 9], $v3_key);
            CommonOption::set('wechat_v3_default_key', $v3_key);
        }
        return $v3_key;
    }
}
