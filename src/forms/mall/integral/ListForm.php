<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\integral;

use app\bootstrap\response\ApiCode;
use app\models\IntegralExchange;
use app\models\Model;

class ListForm extends Model
{
    public $id;
    public $keyword;

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['keyword'], 'string'],
        ];
    }

    public function getList()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        };

        $query = IntegralExchange::find()->where([
            'mall_id' => \Yii::$app->mall->id,
            'is_delete' => 0,
        ]);

        $list = $query->keyword($this->keyword, ['like', 'name', $this->keyword])
            ->orderBy('id DESC,created_at DESC')
            ->asArray()
            ->all();
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $list,
            ]
        ];
    }
}
