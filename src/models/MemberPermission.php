<?php

namespace app\models;

/**
 * This is the model class for table "{{%member_permission}}".
 *
 * @property int $id
 * @property int mall_id 商城ID
 * @property string $name 权限名称
 * @property string $permission_type 权限类型：system系统权限，custom自定义权限
 * @property string $description 权限描述
 * @property string $code 权限代码
 * @property int $status 状态：1启用，0禁用
 * @property int $sort_order 排序
 * @property string $language_data 多语言数据
 * @property int $is_delete 是否删除
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class MemberPermission extends ModelActiveRecord
{
    const PERMISSION_TYPE_SYSTEM = 'system';
    const PERMISSION_TYPE_CUSTOM = 'custom';
    
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%member_permission}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mall_id', 'name', 'permission_type', 'code'], 'required'],
            [['mall_id', 'status', 'sort_order', 'is_delete'], 'integer'],
            [['name', 'permission_type', 'code'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 500],
            [['language_data', 'created_at', 'updated_at'], 'safe'],
            [['permission_type'], 'in', 'range' => [self::PERMISSION_TYPE_SYSTEM, self::PERMISSION_TYPE_CUSTOM]],
            [['status'], 'in', 'range' => [self::STATUS_ENABLED, self::STATUS_DISABLED]],
            [['is_delete'], 'in', 'range' => [0, 1]],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mall_id' => 'Mall ID',
            'name' => '权限名称',
            'permission_type' => '权限类型',
            'description' => '权限描述',
            'code' => '权限代码',
            'status' => '状态',
            'sort_order' => '排序',
            'language_data' => '多语言数据',
            'is_delete' => '是否删除',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 获取权限类型列表
     */
    public static function getPermissionTypeList()
    {
        return [
            self::PERMISSION_TYPE_SYSTEM => '系统',
            self::PERMISSION_TYPE_CUSTOM => '自定义',
        ];
    }

    /**
     * 获取状态列表
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_ENABLED => '启用',
            self::STATUS_DISABLED => '禁用',
        ];
    }

    /**
     * 获取权限类型文本
     */
    public function getPermissionTypeText()
    {
        $list = self::getPermissionTypeList();
        return $list[$this->permission_type] ?? '';
    }

    /**
     * 获取状态文本
     */
    public function getStatusText()
    {
        $list = self::getStatusList();
        return $list[$this->status] ?? '';
    }

    /**
     * 获取会员等级关联
     */
    public function getMemberLevels()
    {
        return $this->hasMany(MemberLevelPermission::class, ['permission_id' => 'id'])
            ->with('level');
    }

    /**
     * 检查权限是否被使用
     */
    public function isUsed()
    {
        return $this->getMemberLevels()->count() > 0;
    }
} 