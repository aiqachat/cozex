<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\api\finance;

use app\bootstrap\response\ApiCode;
use app\models\IntegralExchange;
use app\models\IntegralLog;
use app\models\Model;

class IntegralForm extends Model
{
    public $start_date;
    public $end_date;
    public $type;

    public function rules()
    {
        return [
            [['start_date', 'end_date'], 'string'],
            [['type'], 'integer'],
        ];
    }

    public function getList()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $query = IntegralLog::find()->alias('i')->where([
            'mall_id' => \Yii::$app->mall->id,
            'user_id' => \Yii::$app->user->id,
        ])->orderBy('id desc');

        if ($this->start_date && $this->end_date) {
            $query->andWhere(['<', 'created_at', $this->end_date])->andWhere(['>', 'created_at', $this->start_date]);
        }

        if ($this->type) {
            $query->andWhere(['type' => $this->type]);
        }

        $list = $query->page($pagination)->asArray()->all();

        foreach ($list as &$v) {
            $desc = json_decode($v['custom_desc'], true) ?? [];
            $v['info_desc'] = $desc;
        };

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $list,
                'pagination' => $pagination,
            ],
        ];
    }

    public function allData()
    {
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '请求成功',
            'data' => [
                'list' => array_map(function(IntegralExchange $var){
                    $var->language_data = $var->language_data ? json_decode($var->language_data, true) : [];
                    return $var;
                }, IntegralExchange::find()->where([
                    'mall_id' => \Yii::$app->mall->id,
                    'is_delete' => 0,
                ])->all ())
            ]
        ];
    }
}
