<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\member;

use app\bootstrap\response\ApiCode;
use app\models\MemberLevel;
use app\models\MemberPermission;
use app\models\MemberLevelPermission;
use app\models\Model;

class MemberLevelForm extends Model
{
    public $id;
    public $keyword;
    public $status;
    public $sort_order;
    public $is_default;
    public $permissions;

    public function rules()
    {
        return [
            [['id'], 'required', 'on' => ['toggleStatus', 'delete', 'setDefault', 'setPermissions', 'getDetail']],
            [['id'], 'integer'],
            [['status', 'sort_order', 'is_default'], 'integer'],
            [['status'], 'in', 'range' => [MemberLevel::STATUS_ENABLED, MemberLevel::STATUS_DISABLED]],
            [['is_default'], 'in', 'range' => [0, 1]],
            [['keyword'], 'string', 'max' => 255],
            [['permissions'], 'safe'],
        ];
    }

    public function getList()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $query = MemberLevel::find()
            ->where(['is_delete' => 0, 'mall_id' => \Yii::$app->mall->id])
            ->with('permissions.permission')
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_DESC]);

        $list = $query->page($pagination)->all();

        $data = [];

        if ($list) {
            /** @var MemberLevel $level */
            foreach ($list as $level) {
                // 处理多语言数据
                $languageData = $level->language_data ? json_decode($level->language_data, true) : [];
                
                $levelData = [
                    'id' => $level->id,
                    'name' => $level->name,
                    'slogan' => $level->slogan,
                    'monthly_price' => $level->monthly_price,
                    'monthly_discount_price' => $level->monthly_discount_price,
                    'yearly_price' => $level->yearly_price,
                    'yearly_discount_price' => $level->yearly_discount_price,
                    'monthly_points_refresh' => $level->monthly_points_refresh,
                    'daily_points_refresh' => $level->daily_points_refresh,
                    'storage_space_mb' => $level->storage_space_mb,
                    'status' => $level->status,
                    'sort_order' => $level->sort_order,
                    'is_default' => $level->is_default,
                    'language_data' => $languageData,
                    'permissions' => [],
                ];

                if ($level->permissions) {
                    // 添加权限信息
                    foreach ($level->permissions as $levelPermission) {
                        if ($levelPermission->permission && $levelPermission->permission->status == MemberPermission::STATUS_ENABLED) {
                            $levelData['permissions'][] = [
                                'id' => $levelPermission->permission->id,
                                'name' => $levelPermission->permission->name,
                                'code' => $levelPermission->permission->code,
                                'description' => $levelPermission->permission->description,
                                'type' => $levelPermission->permission->permission_type,
                            ];
                        }
                    }
                }

                $data[] = $levelData;
            }
        }

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'aaa' => $list,
                'list' => $data,
                'pagination' => $pagination,
            ],
        ];
    }

    public function getDetail()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        /** @var MemberLevel $detail */
        $detail = MemberLevel::find()
            ->where(['id' => $this->id, 'mall_id' => \Yii::$app->mall->id, 'is_delete' => 0])
            ->with('permissions.permission')
            ->one();
        if (!$detail) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '会员等级不存在',
            ];
        }

        // 处理多语言数据
        $detail->language_data = $detail->language_data ? json_decode($detail['language_data'], true) : [];

        $detailData = new \stdClass();
        $detailData->id = $detail->id;
        $detailData->name = $detail->name;
        $detailData->slogan = $detail->slogan;
        $detailData->monthly_price = $detail->monthly_price;
        $detailData->monthly_discount_price = $detail->monthly_discount_price;
        $detailData->yearly_price = $detail->yearly_price;
        $detailData->yearly_discount_price = $detail->yearly_discount_price;
        $detailData->monthly_points_refresh = $detail->monthly_points_refresh;
        $detailData->daily_points_refresh = $detail->daily_points_refresh;
        $detailData->storage_space_mb = $detail->storage_space_mb;
        $detailData->status = $detail->status;
        $detailData->sort_order = $detail->sort_order;
        $detailData->is_default = $detail->is_default;
        $detailData->language_data = $detail->language_data;
        $detailData->permissions = [];

        if ($detail->permissions) {
            // 添加权限信息
            foreach ($detail->permissions as $levelPermission) {
                if ($levelPermission->permission && $levelPermission->permission->status == MemberPermission::STATUS_ENABLED) {
                    $permission = new \stdClass();
                    $permission->id = $levelPermission->permission->id;
                    $permission->name = $levelPermission->permission->name;
                    $permission->code = $levelPermission->permission->code;
                    $permission->description = $levelPermission->permission->description;
                    $permission->type = $levelPermission->permission->permission_type;
                    $detailData->permissions[] = $permission;
                }
            }
        }

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'detail' => $detailData
            ],
        ];
    }

    public function delete()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            /** @var MemberLevel $level */
            $level = MemberLevel::find()
                ->where(['id' => $this->id, 'mall_id' => \Yii::$app->mall->id, 'is_delete' => 0])
                ->one();
            if (!$level) {
                throw new \Exception('会员等级不存在');
            }

            // 检查是否为默认等级
            if ($level->is_default == 1) {
                throw new \Exception('默认等级不能删除，请先设置其他等级为默认');
            }

            $level->is_delete = 1;
            $res = $level->save();
            if (!$res) {
                throw new \Exception($this->getErrorMsg($level));
            }

            $transaction->commit();
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '删除成功',
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '等级删除失败：' . $e->getMessage(),
            ];
        }
    }

    public function toggleStatus()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            /** @var MemberLevel $level */
            $level = MemberLevel::findOne(['id' => $this->id, 'mall_id' => \Yii::$app->mall->id, 'is_delete' => 0]);
            if (!$level) {
                throw new \Exception('会员等级不存在');
            }

            $level->status = $level->status == MemberLevel::STATUS_ENABLED
                ? MemberLevel::STATUS_DISABLED
                : MemberLevel::STATUS_ENABLED;
            $res = $level->save();
            if (!$res) {
                throw new \Exception($this->getErrorMsg($level));
            }

            $transaction->commit();
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '状态切换成功',
                'data' => [
                    'status' => $level->status,
                ],
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '状态切换失败：' . $e->getMessage(),
            ];
        }
    }

    public function setDefault()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            /** @var MemberLevel $level */
            $level = MemberLevel::findOne(['id' => $this->id, 'mall_id' => \Yii::$app->mall->id, 'is_delete' => 0]);
            if (!$level) {
                throw new \Exception('会员等级不存在');
            }

            // 先取消其他默认等级
            MemberLevel::updateAll(
                ['is_default' => 0],
                ['mall_id' => \Yii::$app->mall->id, 'is_default' => 1, 'is_delete' => 0]
            );

            // 设置当前等级为默认
            $level->is_default = 1;
            $res = $level->save();
            if (!$res) {
                throw new \Exception($this->getErrorMsg($level));
            }

            $transaction->commit();
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '默认等级设置成功',
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '默认等级设置失败：' . $e->getMessage(),
            ];
        }
    }

    public function setPermissions()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            /** @var MemberLevel $level */
            $level = MemberLevel::findOne(['id' => $this->id, 'mall_id' => \Yii::$app->mall->id, 'is_delete' => 0]);
            if (!$level) {
                throw new \Exception('会员等级不存在');
            }

            MemberLevelPermission::setLevelPermissions($this->id, $this->permissions ?: []);

            $transaction->commit();
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '权限设置成功',
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '权限设置失败：' . $e->getMessage(),
            ];
        }
    }

    public function getPermissions()
    {
        /** @var MemberLevel $detail */
        $detail = MemberLevel::find()
            ->where(['id' => $this->id, 'mall_id' => \Yii::$app->mall->id, 'is_delete' => 0])
            ->with('permissions.permission')
            ->one();

        $system_data = [
            'custom_image_watermark' => 0,
            'custom_video_watermark' => 0,
            'image_resource_save' => 0,
            'video_resource_save' => 0,
            'private_image_protection' => 0,
            'private_video_protection' => 0
        ];

        $custom_data = [];

        if ($detail && $detail->permissions) {
            foreach ($detail->permissions as $levelPermission) {
                if ($levelPermission->permission && $levelPermission->permission->status == MemberPermission::STATUS_ENABLED) {
                    if ($levelPermission->permission->permission_type == MemberPermission::PERMISSION_TYPE_SYSTEM) {
                        $system_data[$levelPermission->permission->code] = 1;
                    } else {
                        $custom_data = array_merge($custom_data, [$levelPermission->permission->code => 1]);
                    }
                }
            }
        }

        return [
            'system_data' => $system_data,
            'custom_data' => $custom_data
        ];
    }
} 