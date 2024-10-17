<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%core_action_log}}".
 *
 * @property int $id
 * @property int $user_id 操作人ID
 * @property string $model 模型名称
 * @property int $model_id 模型ID
 * @property string $before_update 更新之前的数据
 * @property string $after_update 更新之后的数据
 * @property string $created_at 创建时间
 * @property int $is_delete
 * @property string $remark
 * @property $user
 */
class CoreActionLog extends ModelActiveRecord
{
    public $isLog = false;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%core_action_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'model_id', 'before_update', 'after_update', 'created_at'], 'required'],
            [['user_id', 'model_id', 'is_delete'], 'integer'],
            [['before_update', 'after_update', 'model'], 'string'],
            [['created_at'], 'safe'],
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
            'model_id' => 'Model ID',
            'model' => 'Model',
            'before_update' => 'Before Update',
            'after_update' => 'After Update',
            'created_at' => 'Created At',
            'is_delete' => 'Is Delete',
            'remark' => 'Remark',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
