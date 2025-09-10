<?php
/**
 * Created by PhpStorm
 * User: chenzs
 * Date: 2020/9/29
 * Time: 4:15 下午
 * @copyright: ©2020 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\forms\mall\visual;

use app\forms\api\visual\ConfigForm;
use app\forms\common\CommonOption;
use app\forms\mall\setting\BasicConfigForm;
use app\forms\mall\setting\UserConfigForm;
use app\helpers\ArrayHelper;
use app\models\VisualVideo;

class SettingForm extends BasicConfigForm
{
    public function rules()
    {
        return array_merge (parent::rules (), [
            [['tab'], 'default', 'value' => self::TAB_BASIC],
        ]);
    }

    const TAB_BASIC = 'one'; // 即梦
    const TAB_ARK = 'two'; // 火山国内
    const TAB_ARK_GLOBAL = 'three'; // 火山国际

    public function getList()
    {
        return [
            self::TAB_BASIC => CommonOption::NAME_VISUAL_PRICE,
            self::TAB_ARK => CommonOption::NAME_VISUAL_ARK_SETTING,
            self::TAB_ARK_GLOBAL => CommonOption::NAME_VISUAL_ARK_GLOBAL_SETTING,
        ];
    }

    public function one()
    {
        return [
            'image_generate_price' => 10,
            'img_to_img_generate_price' => 15,
            'key_id' => null,
            'video' => [],
            'img_model_open' => 1,
            'img_model_only' => 1,
            'video_model' => [],
        ];
    }

    public function two()
    {
        return [
            'image_generate_price' => 10,
            'key_id' => null,
            'video' => [],
            'api_key' => '',
            'img_model_name' => 'doubao-seedream-3.0',
            'img_model_pic' => 'https://res.volccdn.com/obj/volc-console-fe/volcengine-ark/static/image/doubao_logo.46103a57.png',
            'img_model_open' => 1,
            'img_model_only' => 0,
            'video_model' => [],
            'img_to_img_generate_price' => 15,
        ];
    }

    public function three()
    {
        return [
            'image_generate_price' => 10,
            'key_id' => null,
            'video' => [],
            'api_key' => '',
            'img_model_name' => 'seedream-3.0',
            'img_model_pic' => 'https://res.volccdn.com/obj/volc-console-fe/volcengine-ark/static/image/doubao_logo.46103a57.png',
            'img_model_open' => 1,
            'img_model_only' => 0,
            'video_model' => [],
            'img_to_img_generate_price' => 15,
        ];
    }

    public function get()
    {
        $form = new UserConfigForm();
        $form->tab = UserConfigForm::TAB_INTEGRAL;
        $config = $form->config();
        $data = parent::get();
        $data['data']['data']['integral'] = $config;

        $form = new ConfigForm();
        $form->is_home = $this->tab == self::TAB_ARK ? 1 : 2;
        if($this->tab == self::TAB_BASIC){
            $form->function_type = VisualVideo::DREAM_NAME;
        }
        $arr = $form->config()['data']['model_data'];
        $newItem = [];
        foreach (ArrayHelper::index($arr, null, 'label') as $k => $item){
            foreach($item as $i){
                if(!isset($newItem[$k])){
                    $newItem[$k] = $i;
                }else{
                    $newItem[$k]['modes'] = array_unique(array_merge($newItem[$k]['modes'], $i['modes']));
                    $newItem[$k]['resolutions'] = array_unique(array_merge($newItem[$k]['resolutions'], $i['resolutions']));
                }
            }
        }
        $data['data']['models'] = array_values($newItem);
        return $data;
    }
}
