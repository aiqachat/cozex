<?php

namespace app\models;

/**
 * This is the model class for table "{{%user_identity}}".
 *
 * @property int $id 用户身份表
 * @property int $user_id
 * @property int $is_super_admin 是否为超级管理员
 * @property int $is_admin 是否为管理员
 * @property int $is_delete 是否删除
 */
class UserIdentity extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_identity}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_super_admin', 'is_admin', 'user_id', 'is_delete'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'is_super_admin' => 'Is Super Admin',
            'is_admin' => 'Is Admin',
            'is_delete' => 'Is Delete',
        ];
    }
}
