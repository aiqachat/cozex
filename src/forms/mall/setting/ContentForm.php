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

class ContentForm extends BasicConfigForm
{
    public function rules()
    {
        return array_merge (parent::rules (), [
            [['tab'], 'default', 'value' => self::TAB_BASIC],
        ]);
    }

    const TAB_BASIC = 'one';
    const TAB_SQUARE = 'two';

    public function getList()
    {
        return [
            self::TAB_BASIC => CommonOption::NAME_CONTENT_SETTING,
            self::TAB_SQUARE => CommonOption::NAME_CONTENT_SQUARE_SETTING,
        ];
    }

    public function one()
    {
        return [
            'image_storage_time' => 72,
            'video_storage_time' => 72,
            'attachment_storage_time' => 12,
            'is_img_audit' => 0,
            'img_audit_type' => 1,
            'img_audit_time' => '',
            'is_video_audit' => 0,
            'video_audit_type' => 1,
            'video_audit_time' => '',
        ];
    }

    public function two()
    {
        return [
            'square_title' => '',
            'square_title_en' => '',
            'square_subtitle' => '',
            'square_subtitle_en' => '',
            'square_bg_list' => [],
            'square_button_text' => '开始使用',
            'square_button_text_en' => '',
        ];
    }
}
