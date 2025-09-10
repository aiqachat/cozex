<?php

namespace app\models;

/**
 * This is the model class for table "{{%member_level_permission}}".
 *
 * @property int $id
 * @property int $level_id 会员等级ID
 * @property int $permission_id 权限ID
 * @property string $created_at 创建时间
 */
class MemberLevelPermission extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%member_level_permission}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['level_id', 'permission_id'], 'required'],
            [['level_id', 'permission_id'], 'integer'],
            [['created_at'], 'safe'],
            [['level_id', 'permission_id'], 'unique', 'targetAttribute' => ['level_id', 'permission_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'level_id' => '会员等级ID',
            'permission_id' => '权限ID',
            'created_at' => '创建时间',
        ];
    }

    /**
     * 获取会员等级关联
     */
    public function getLevel()
    {
        return $this->hasOne(MemberLevel::class, ['id' => 'level_id']);
    }

    /**
     * 获取权限关联
     */
    public function getPermission()
    {
        return $this->hasOne(MemberPermission::class, ['id' => 'permission_id']);
    }

    /**
     * 批量设置会员等级权限
     */
    public static function setLevelPermissions($levelId, $permissionIds)
    {
        // 删除现有权限
        self::deleteAll(['level_id' => $levelId]);
        
        // 添加新权限
        foreach ($permissionIds as $permissionId) {
            $model = new self();
            $model->level_id = $levelId;
            $model->permission_id = $permissionId;
            $model->created_at = date('Y-m-d H:i:s');
            $model->save();
        }
    }

    /**
     * 获取会员等级的所有权限ID
     */
    public static function getLevelPermissionIds($levelId)
    {
        $permissions = self::find()
            ->select('permission_id')
            ->where(['level_id' => $levelId])
            ->column();
        
        return $permissions;
    }

    /**
     * 检查会员等级是否有指定权限
     */
    public static function hasPermission($levelId, $permissionCode)
    {
        return self::find()
            ->alias('mlp')
            ->innerJoin(MemberPermission::tableName() . ' mp', 'mlp.permission_id = mp.id')
            ->where([
                'mlp.level_id' => $levelId,
                'mp.code' => $permissionCode,
                'mp.status' => MemberPermission::STATUS_ENABLED,
                'mp.is_delete' => 0
            ])
            ->exists();
    }
} 