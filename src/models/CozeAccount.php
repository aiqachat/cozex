<?php

namespace app\models;

/**
 * This is the model class for table "{{%coze_account}}".
 *
 * @property int $id
 * @property string $name
 * @property string $remark
 * @property string $coze_secret  访问令牌
 * @property int $is_delete
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class CozeAccount extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%coze_account}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['coze_secret', 'created_at', 'updated_at',], 'required'],
            [['is_delete'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['coze_secret', 'name', 'remark'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'coze_secret' => '访问令牌',
            'name' => 'Name',
            'remark' => 'remark',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
