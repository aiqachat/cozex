<?php
/**
 * link: https://www.wegouer.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\level;

use app\bootstrap\response\ApiCode;
use app\models\Model;
use app\models\UserLevel;

class LevelEditForm extends Model
{
    public $name;
    public $promotion_commission_ratio;
    public $status;
    public $promotion_status;
    public $promotion_desc;
    public $id;
    public $language_data;

    public function rules()
    {
        return [
            [['name', 'promotion_commission_ratio'], 'required'],
            [['name', 'promotion_desc'], 'string'],
            [['promotion_commission_ratio',], 'number'],
            [['id', 'status', 'promotion_status'], 'integer'],
            [['language_data'], 'safe'],
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        if ($this->promotion_status && ($this->promotion_commission_ratio < 0 || $this->promotion_commission_ratio > 100)) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '推广返佣比例请输入 0 ~ 100之间的值'
            ];
        }

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if ($this->id) {
                $member = UserLevel::findOne(['mall_id' => \Yii::$app->mall->id, 'id' => $this->id]);
                if (!$member) {
                    throw new \Exception('数据异常,该条数据不存在');
                }
            } else {
                $member = new UserLevel();
            }
            $member->attributes = $this->attributes;
            $member->mall_id = \Yii::$app->mall->id;
            $member->language_data = json_encode($member->language_data, JSON_UNESCAPED_UNICODE);
            $res = $member->save();
            if (!$res) {
                throw new \Exception($this->getErrorMsg($member));
            }
            $transaction->commit();
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '保存成功',
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage(),
            ];
        }
    }
}
