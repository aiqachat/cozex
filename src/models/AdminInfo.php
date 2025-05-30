<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%admin_info}}".
 *
 * @property int $id
 * @property int $user_id
 * @property int $app_max_count 创建最大数量
 * @property string $permissions 账户权限
 * @property string $remark 备注
 * @property string $is_delete 是否删除
 * @property string $expired_at 账户过期时间
 * @property string $secondary_permissions 二级权限
 * @property string $mobile
 * @property User $user
 * @property UserIdentity $identity
 */
class AdminInfo extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%admin_info}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'permissions'], 'required'],
            [['user_id', 'app_max_count', 'is_delete'], 'integer'],
            [['permissions', 'secondary_permissions', 'mobile'], 'string'],
            [['expired_at'], 'safe'],
            [['remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户 ID',
            'app_max_count' => 'App Max Count',
            'permissions' => '用户权限',
            'remark' => 'Remark',
            'mobile' => 'mobile',
            'secondary_permissions' => '二级权限',
            'is_delete' => 'Is Delete',
            'expired_at' => 'Expired At',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getIdentity()
    {
        return $this->hasOne(UserIdentity::class, ['user_id' => 'user_id']);
    }
}
