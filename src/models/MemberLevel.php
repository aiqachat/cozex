<?php

namespace app\models;

use yii\helpers\Json;

/**
 * This is the model class for table "{{%member_level}}".
 *
 * @property int $id
 * @property int $mall_id 商城ID
 * @property string $name 名称
 * @property string $slogan 宣传语
 * @property float $monthly_price 月付价
 * @property float $monthly_discount_price 月付优惠价
 * @property float $yearly_price 年付价
 * @property float $yearly_discount_price 年付优惠价
 * @property int $monthly_points_refresh 每月积分刷新(Token)
 * @property int $daily_points_refresh 每日积分刷新(Token)
 * @property int $storage_space_mb 存储空间大小(MB)
 * @property int $status 状态：1启用，0禁用
 * @property int $sort_order 排序
 * @property string $language_data 多语言数据
 * @property int $is_default 是否默认等级
 * @property int $is_delete 是否删除
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @property MemberPermission permissions
 */
class MemberLevel extends ModelActiveRecord
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%member_level}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mall_id', 'name'], 'required'],
            [['mall_id', 'status', 'sort_order', 'is_default', 'is_delete'], 'integer'],
            [['monthly_price', 'monthly_discount_price', 'yearly_price', 'yearly_discount_price'], 'number'],
            [['monthly_points_refresh', 'daily_points_refresh', 'storage_space_mb'], 'integer'],
            [['name', 'slogan'], 'string', 'max' => 255],
            [['language_data', 'created_at', 'updated_at'], 'safe'],
            [['status'], 'in', 'range' => [self::STATUS_ENABLED, self::STATUS_DISABLED]],
            [['is_default'], 'in', 'range' => [0, 1]],
            [['is_delete'], 'in', 'range' => [0, 1]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mall_id' => '商城ID',
            'name' => '名称',
            'slogan' => '宣传语',
            'monthly_price' => '月付价',
            'monthly_discount_price' => '月付优惠价',
            'yearly_price' => '年付价',
            'yearly_discount_price' => '年付优惠价',
            'monthly_points_refresh' => '每月积分刷新(Token)',
            'daily_points_refresh' => '每日积分刷新(Token)',
            'storage_space_mb' => '存储空间大小(MB)',
            'status' => '状态',
            'sort_order' => '排序',
            'language_data' => '多语言数据',
            'is_default' => '是否默认等级',
            'is_delete' => '是否删除',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 获取权限关联
     */
    public function getPermissions()
    {
        return $this->hasMany(MemberLevelPermission::class, ['level_id' => 'id'])
            ->with('permission');
    }

    /**
     * 获取权限ID列表
     */
    public function getPermissionIds()
    {
        $permissions = $this->getPermissions()->all();
        return array_column($permissions, 'permission_id');
    }

    /**
     * 设置权限
     */
    public function setPermissions($permissionIds)
    {
        // 删除现有权限
        MemberLevelPermission::deleteAll(['level_id' => $this->id]);
        
        // 添加新权限
        foreach ($permissionIds as $permissionId) {
            $levelPermission = new MemberLevelPermission();
            $levelPermission->level_id = $this->id;
            $levelPermission->permission_id = $permissionId;
            $levelPermission->save();
        }
    }

    /**
     * 获取当前语言的名称
     */
    public function getCurrentName()
    {
        return \Yii::$app->language == 'zh' ? $this->name_zh : $this->name_en;
    }

    /**
     * 获取当前语言的宣传语
     */
    public function getCurrentSlogan()
    {
        return \Yii::$app->language == 'zh' ? $this->slogan_zh : $this->slogan_en;
    }
} 