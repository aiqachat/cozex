<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\api\finance;

use app\bootstrap\response\ApiCode;
use app\forms\mall\setting\ConfigForm;
use app\models\BalanceLog;
use app\models\Model;
use app\models\RechargeOrders;

class RechargeForm extends Model
{
    public $id;
    public $start_date;
    public $end_date;
    public $type;

    public function rules()
    {
        return [
            [['id', 'type'], 'integer'],
            [['start_date', 'end_date'], 'string'],
        ];
    }

    public function getList()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $query = BalanceLog::find()->alias('i')->where([
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
        }

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $list,
                'pagination' => $pagination,
            ],
        ];
    }

    public function getSetting()
    {
        $form = new ConfigForm();
        $form->tab = ConfigForm::TAB_RECHARGE;
        $data = $form->config();
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '请求成功',
            'data' => $data
        ];
    }

    public function result()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $order = RechargeOrders::findOne($this->id);
        if (!$order) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '订单不存在'
            ];
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'retry' => $order->is_pay ? 0 : 1,
            ],
        ];
    }
}
