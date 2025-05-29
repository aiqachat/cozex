<?php

/**
 * link: https://www.netbcloud.com//
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\user;

use app\bootstrap\response\ApiCode;
use app\models\Model;
use app\models\User;

class BalanceForm extends Model
{
    public $user_id;
    public $type;
    public $price;
    public $pic_url;
    public $remark;

    public function rules()
    {
        return [
            [['price', 'type', 'user_id'], 'required'],
            [['type', 'user_id'], 'integer'],
            [['pic_url', 'remark'], 'string', 'max' => 255],
            [['price'], 'number', 'min' => 0.01, 'max' => 99999999],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => '用户ID',
            'type' => '类型',
            'price' => '金额',
            'pic_url' => '图片',
            'remark' => '备注'
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        };


        try {
            $admin = \Yii::$app->user->identity;

            $user = User::find()->alias('u')->InnerJoinwith('userInfo')->where([
                'u.id' => $this->user_id,
                'u.mall_id' => \Yii::$app->mall->id
            ])->one();

            if (!$user) {
                throw new \Exception('用户不存在');
            }

            $custom_desc = [
                'remark' => $this->remark,
                'pic_url' => $this->pic_url,
            ];

            if ($this->type == 1) {
                if (
                    bcadd($user->userInfo->total_balance, $this->price) > 9999999999 ||
                    bcadd($user->userInfo->balance, $this->price) > 99999999
                ) {
                    throw new \Exception('充值额度已达上限');
                }
                $desc = "管理员： " . $admin->nickname . " 后台操作账号："
                    . $user->nickname . " 余额充值：" . $this->price . "元";
                \Yii::$app->currency->setUser($user)->balance->add(
                    (float)$this->price,
                    $desc,
                    json_encode($custom_desc)
                );
            } else {
                $desc = "管理员： " . $admin->nickname . " 后台操作账号："
                    . $user->nickname . " 余额扣除：" . $this->price . " 元";
                \Yii::$app->currency->setUser($user)->balance->sub(
                    (float)$this->price,
                    $desc,
                    json_encode($custom_desc)
                );
            }

            return [
                'code' => 0,
                'msg' => 'success'
            ];
        } catch (\Exception $e) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage(),
                'error' => [
                    'line' => $e->getLine()
                ]
            ];
        }
    }
}
