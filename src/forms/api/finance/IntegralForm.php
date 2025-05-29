<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\api\finance;

use app\bootstrap\response\ApiCode;
use app\models\IntegralExchange;
use app\models\Model;

class IntegralForm extends Model
{
//    public $id;

//    public function rules()
//    {
//        return [
//            [['id'], 'integer'],
//        ];
//    }

    public function allData()
    {
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '请求成功',
            'data' => [
                'list' => array_map(function(IntegralExchange $var){
                    $var->language_data = $var->language_data ? json_decode($var->language_data, true) : [];
                    return $var;
                }, IntegralExchange::find ()->where([
                    'mall_id' => \Yii::$app->mall->id,
                    'is_delete' => 0,
                ])->all ())
            ]
        ];
    }
}
