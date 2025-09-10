<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\member;

use app\bootstrap\response\ApiCode;
use app\models\MemberPermission;
use app\models\Model;

class MemberPermissionForm extends Model
{
    public $id;
    public $keyword;
    public $permission_type;
    public $status;
    public $sort_order;

    public function rules()
    {
        return [
            [['id', 'status', 'sort_order'], 'integer'],
            [['permission_type'], 'string', 'max' => 100],
            [['keyword'], 'string', 'max' => 255],
            [['permission_type'], 'in', 'range' => [MemberPermission::PERMISSION_TYPE_SYSTEM, MemberPermission::PERMISSION_TYPE_CUSTOM]],
            [['status'], 'in', 'range' => [MemberPermission::STATUS_ENABLED, MemberPermission::STATUS_DISABLED]],
        ];
    }

    public function getList()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $query = MemberPermission::find()
            ->where(['is_delete' => 0, 'mall_id' => \Yii::$app->mall->id]);

         if ($this->keyword) {
             $query->andWhere(['like', 'name', $this->keyword]);
         }

         if ($this->permission_type) {
             $query->andWhere(['permission_type' => $this->permission_type]);
         }

         if ($this->status) {
             $query->andWhere(['status' => $this->status]);
         }

         $query->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_DESC]);

        $list = $query->page($pagination)
            ->asArray()
            ->all();

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $list,
                'pagination' => $pagination,
            ],
        ];
    }

    public function getSelectList()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $query = MemberPermission::find()
            ->where(['is_delete' => 0, 'mall_id' => \Yii::$app->mall->id, 'status' => 1]);

        if ($this->keyword) {
            $query->andWhere(['like', 'name', $this->keyword]);
        }

        $list = $query->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_DESC])
            ->all();

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $list
            ],
        ];
    }

    public function getDetail()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        /** @var MemberPermission $detail */
        $detail = MemberPermission::find()
            ->where(['id' => $this->id, 'is_delete' => 0])
            ->one();

        if (!$detail) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '权限不存在',
            ];
        }
        $detail->language_data = $detail->language_data ? json_decode($detail['language_data'], true) : [];

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'detail' => $detail
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
            /** @var MemberPermission $permission */
            $permission = MemberPermission::find()
                ->where(['id' => $this->id, 'is_delete' => 0])
                ->one();
            if (!$permission) {
                throw new \Exception('权限不存在');
            }

            // 检查权限类型
            if ($permission->permission_type == MemberPermission::PERMISSION_TYPE_SYSTEM) {
                throw new \Exception('系统权限不能删除');
            }

            // 检查权限是否被使用
            if ($permission->isUsed()) {
                throw new \Exception('该权限已被使用，无法删除');
            }

            $permission->is_delete = 1;
            $res = $permission->save();
            if (!$res) {
                throw new \Exception($this->getErrorMsg($permission));
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
                'msg' => '权限删除失败：' . $e->getMessage(),
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
            /** @var MemberPermission $permission */
            $permission = MemberPermission::find()
                ->where(['id' => $this->id, 'is_delete' => 0])
                ->one();
            if (!$permission) {
                throw new \Exception('权限不存在');
            }

            $permission->status = $permission->status == MemberPermission::STATUS_ENABLED
                ? MemberPermission::STATUS_DISABLED
                : MemberPermission::STATUS_ENABLED;

            $res = $permission->save();
            if (!$res) {
                throw new \Exception($this->getErrorMsg($permission));
            }

            $transaction->commit();
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '状态切换成功',
                'data' => [
                    'status' => $permission->status,
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

    public function updateSort()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $sorts = $this->sort_order;
            if (empty($sorts)) {
                throw new \Exception('参数错误');
            }

            foreach ($sorts as $id => $sort) {
                MemberPermission::updateAll(
                    ['sort_order' => (int)$sort],
                    ['id' => $id]
                );
            }

            $transaction->commit();
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '排序更新成功',
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '排序更新失败：' . $e->getMessage(),
            ];
        }
    }
} 