<?php

namespace app\models;

use app\forms\permission\role\AdminRole;
use app\forms\permission\role\BaseRole;
use app\forms\permission\role\SuperAdminRole;

/**
 * This is the model class for table "{{%mall}}".
 *
 * @property string $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $expired_at
 * @property string $name
 * @property string $user_id
 * @property int $is_recycle
 * @property int $is_disable
 * @property int $is_delete
 * @property User $user
 * @property BaseRole $role
 */
class Mall extends ModelActiveRecord
{
    private $role;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%mall}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'deleted_at', 'expired_at'], 'safe'],
            [['user_id', 'is_recycle', 'is_delete', 'is_disable'], 'integer'],
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
            'expired_at' => 'Expired At',
            'name' => '商城名称',
            'user_id' => '用户 ID',
            'is_recycle' => '商城回收状态',
            'is_disable' => '商城禁用状态',
            'is_delete' => 'Is Delete',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return AdminRole|BaseRole|SuperAdminRole
     * @throws \Exception
     * 获取商城所属账户的权限
     */
    public function getRole()
    {
        if (!$this->role) {
            $user = \Yii::$app->mall->user;
            $userIdentity = $user->identity;
            $config = [
                'user' => $user,
                'mall' => \Yii::$app->mall
            ];
            if ($userIdentity->is_super_admin == 1) {
                // 总管理员
                $this->role = new SuperAdminRole($config);
            } elseif ($userIdentity->is_admin == 1) {
                // 子管理员
                $this->role = new AdminRole($config);
            } else {
                throw new \Exception('未知用户权限');
            }
        }
        return $this->role;
    }
}
