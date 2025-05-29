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

class IntegralForm extends Model
{
    public $user_id;
    public $type;
    public $num;
    public $pic_url;
    public $remark;

    public function rules()
    {
        return [
            [['num', 'type', 'user_id'], 'required'],
            [['type', 'user_id'], 'integer'],
            [['pic_url', 'remark'], 'string', 'max' => 255],
            [['num'], 'integer', 'min' => 1, 'max' => 999999999],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => '用户ID',
            'type' => '类型',
            'num' => '积分',
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
            $user = User::find()->alias('u')->InnerJoinwith('userInfo')
                ->where(['u.id' => $this->user_id, 'u.mall_id' => \Yii::$app->mall->id])->one();
            $custom_desc = [
                'remark' => $this->remark,
                'pic_url' => $this->pic_url,
            ];

            if ($this->type == 1) {
                $desc = "管理员： " . $admin->nickname . " 后台操作账号：" . $user->nickname . " 积分充值：" . $this->num . " 积分";
                \Yii::$app->currency->setUser($user)->integral->add((float)$this->num, $desc, json_encode($custom_desc));
            } else {
                $desc = "管理员： " . $admin->nickname . " 后台操作账号：" . $user->nickname . " 积分扣除：" . $this->num . " 积分";
                \Yii::$app->currency->setUser($user)->integral->sub((float)$this->num, $desc, json_encode($custom_desc));
            }
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '处理成功'
            ];
        }catch(\Exception $e) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage(),
            ];
        }
    }
}
