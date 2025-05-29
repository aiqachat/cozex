<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\admin\mall;

use app\bootstrap\response\ApiCode;
use app\forms\common\CommonOption;
use app\models\Model;

class MallOverrunForm extends Model
{
    public $form;

    public function rules()
    {
        return [
            [['form'], 'safe']
        ];
    }

    public function save()
    {
        try {
            $this->checkData();
            $option = CommonOption::set(CommonOption::NAME_OVERRUN, $this->form, 0, 'admin');
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '保存成功'
            ];
        } catch (\Exception $e) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage()
            ];
        }
    }

    public function setting()
    {
        $option = $this->getSetting();
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '请求成功',
            'data' => [
                'setting' => $option
            ]
        ];
    }

    public function getSetting()
    {
        $option = CommonOption::get(CommonOption::NAME_OVERRUN, 0, 'admin', $this->getDefault());

        $option = CommonOption::checkDefault((array)$option, $this->getDefault());

        $option['is_img_overrun'] = $option['is_img_overrun'] == 'true';
        $option['is_video_overrun'] = $option['is_video_overrun'] == 'true';
        return $option;
    }

    public function getDefault()
    {
        return [
            'img_overrun' => 1,
            'is_img_overrun' => false,
            'video_overrun' => 80,
            'is_video_overrun' => false,
        ];
    }

    private function checkData()
    {
        if ($this->form['img_overrun'] == '') {
            throw new \Exception('请输入上传图片限制');
        }

        if ($this->form['video_overrun'] == '') {
            throw new \Exception('请输入上传视频大小限制');
        }
    }
}
