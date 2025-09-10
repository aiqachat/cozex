<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\integral;

use app\bootstrap\response\ApiCode;
use app\forms\common\CommonOption;
use app\forms\common\coze\api\BotsList;
use app\forms\common\coze\ApiForm;
use app\models\BotConf;
use app\models\CozeAccount;
use app\models\IntegralExchange;
use app\models\Model;

class IndexForm extends Model
{
    public $id;
    public $name;
    public $pay_price;
    public $send_integral;
    public $language_data;
    public $give_integral;
    public $buy_num;
    public $period;
    public $serial_num;

    public function rules()
    {
        return [
            [['id', 'send_integral', 'give_integral', 'buy_num', 'period', 'serial_num'], 'integer'],
            [['name'], 'string'],
            [['language_data'], 'safe'],
            [['pay_price'], 'number'],
        ];
    }

    public function detail()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $model = IntegralExchange::findOne(['mall_id' => \Yii::$app->mall->id, 'is_delete' => 0, 'id' => $this->id]);
        if (!$model) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '数据不存在'
            ];
        }
        $model->language_data = $model->language_data ? json_decode($model->language_data, true) : [];

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => $model
        ];
    }

    public function del()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $model = IntegralExchange::findOne(['mall_id' => \Yii::$app->mall->id, 'is_delete' => 0, 'id' => $this->id]);
        if (!$model) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '数据不存在'
            ];
        }
        $model->is_delete = 1;
        if (!$model->save()) {
            return $this->getErrorResponse($model);
        }

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功'
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        if ($this->id) {
            $model = IntegralExchange::findOne(['mall_id' => \Yii::$app->mall->id, 'is_delete' => 0, 'id' => $this->id]);
            if (!$model) {
                return [
                    'code' => ApiCode::CODE_ERROR,
                    'msg' => '数据不存在'
                ];
            }
        } else {
            $model = new IntegralExchange();
            $model->mall_id = \Yii::$app->mall->id;
        }
        $model->attributes = $this->attributes;
        $model->language_data = json_encode($this->language_data, JSON_UNESCAPED_UNICODE);
        if (!$model->save()) {
            return $this->getErrorResponse($model);
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功'
        ];
    }
}
