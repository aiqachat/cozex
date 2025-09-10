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

class MemberPermissionEditForm extends Model
{
    public $id;
    public $name;
    public $permission_type;
    public $description;
    public $code;
    public $status;
    public $sort_order;
    public $language_data;

    public function rules()
    {
        return [
            [['name', 'permission_type', 'code'], 'required'],
            [['id', 'status', 'sort_order'], 'integer'],
            [['name', 'permission_type', 'code'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 500],
            [['permission_type'], 'in', 'range' => [MemberPermission::PERMISSION_TYPE_SYSTEM, MemberPermission::PERMISSION_TYPE_CUSTOM]],
            [['status'], 'in', 'range' => [MemberPermission::STATUS_ENABLED, MemberPermission::STATUS_DISABLED]],
            [['language_data'], 'safe'],
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
                /** @var MemberPermission $permission */
                $permission = MemberPermission::find()
                    ->where(['id' => $this->id, 'mall_id' => \Yii::$app->mall->id, 'is_delete' => 0])
                    ->one();

                if (!$permission) {
                    throw new \Exception('权限不存在');
                }

                // 检查权限类型
                if ($permission->permission_type == MemberPermission::PERMISSION_TYPE_SYSTEM) {
                    throw new \Exception('系统权限不能编辑');
                }
            } else {
                // 创建
                $permission = new MemberPermission();
                $permission->mall_id = \Yii::$app->mall->id;
                $permission->created_at = date('Y-m-d H:i:s');
            }

            $permission->name = $this->name;
            $permission->permission_type = $this->permission_type;
            $permission->description = $this->description;
            $permission->code = $this->code;
            $permission->status = $this->status ?: MemberPermission::STATUS_ENABLED;
            $permission->sort_order = $this->sort_order ?: 0;
            $permission->updated_at = date('Y-m-d H:i:s');
            $permission->language_data = json_encode($this->language_data, JSON_UNESCAPED_UNICODE);
            $res = $permission->save();
            if (!$res) {
                throw new \Exception($this->getErrorMsg($permission));
            }

            $transaction->commit();
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '保存成功',
                'data' => [
                    'id' => $permission->id,
                ],
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