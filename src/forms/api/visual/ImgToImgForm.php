<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\api\visual;

use app\bootstrap\response\ApiCode;

class ImgToImgForm extends ImgForm
{
    public $image_urls;
    public $scale;
    public $type = 3;

    public function rules()
    {
        return array_merge(parent::rules(),[
            [['image_urls'], 'safe'],
            [['scale'], 'number'],
        ]);
    }

    public function getList()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $res = $this->setUserId(\Yii::$app->user->id)
            ->setData($this->attributes, $this->type);
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功',
            'data' => $res
        ];
    }
}
