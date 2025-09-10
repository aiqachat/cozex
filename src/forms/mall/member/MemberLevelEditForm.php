<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\member;

use app\bootstrap\response\ApiCode;
use app\models\MemberLevel;
use app\models\MemberLevelPermission;
use app\models\Model;

class MemberLevelEditForm extends Model
{
    public $id;
    public $name;
    public $slogan;
    public $monthly_price;
    public $monthly_discount_price;
    public $yearly_price;
    public $yearly_discount_price;
    public $monthly_points_refresh;
    public $daily_points_refresh;
    public $storage_space_mb;
    public $status;
    public $sort_order;
    public $is_default;
    public $language_data;
    public $permissions;

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['id', 'status', 'sort_order', 'is_default'], 'integer'],
            [['monthly_price', 'monthly_discount_price', 'yearly_price', 'yearly_discount_price'], 'number'],
            [['monthly_points_refresh', 'daily_points_refresh', 'storage_space_mb'], 'integer'],
            [['name', 'slogan'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => [MemberLevel::STATUS_ENABLED, MemberLevel::STATUS_DISABLED]],
            [['is_default'], 'in', 'range' => [0, 1]],
            [['language_data'], 'safe'],
            [['permissions'], 'safe'],
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if ($this->id) {
                // 更新
                /** @var MemberLevel $level */
                $level = MemberLevel::find()
                    ->where(['id' => $this->id, 'mall_id' => \Yii::$app->mall->id, 'is_delete' => 0])
                    ->one();
                if (!$level) {
                    throw new \Exception('会员等级不存在');
                }
            } else {
                // 创建
                $level = new MemberLevel();
                $level->mall_id = \Yii::$app->mall->id;
                $level->created_at = date('Y-m-d H:i:s');
            }

            // 如果设置为默认等级，先取消其他默认等级
            if ($this->is_default == 1) {
                MemberLevel::updateAll(
                    ['is_default' => 0],
                    ['mall_id' => \Yii::$app->mall->id, 'is_default' => 1, 'is_delete' => 0]
                );
            }

            $level->name = $this->name;
            $level->slogan = $this->slogan;
            $level->monthly_price = $this->monthly_price ?: 0;
            $level->monthly_discount_price = $this->monthly_discount_price ?: 0;
            $level->yearly_price = $this->yearly_price ?: 0;
            $level->yearly_discount_price = $this->yearly_discount_price ?: 0;
            $level->monthly_points_refresh = $this->monthly_points_refresh ?: 0;
            $level->daily_points_refresh = $this->daily_points_refresh ?: 0;
            $level->storage_space_mb = $this->storage_space_mb ?: 0;
            $level->status = $this->status ?: MemberLevel::STATUS_ENABLED;
            $level->sort_order = $this->sort_order ?: 0;
            $level->is_default = $this->is_default ?: 0;
            $level->updated_at = date('Y-m-d H:i:s');
            $level->language_data = json_encode($this->language_data, JSON_UNESCAPED_UNICODE);
            $res = $level->save();
            if (!$res) {
                throw new \Exception($this->getErrorMsg($level));
            }

            if ($this->permissions) {
                $level->setPermissions($this->permissions);
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