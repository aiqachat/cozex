<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\api\finance;

use app\bootstrap\response\ApiCode;
use app\forms\mall\setting\ConfigForm;
use app\models\Model;
use app\models\RechargeOrders;

class RechargeForm extends Model
{
    public $id;

    public function rules()
    {
        return [
            [['id'], 'integer'],
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

        $order = RechargeOrders::findOne ($this->id);
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
