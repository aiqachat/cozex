<?php

namespace app\models;

/**
 * This is the model class for table "{{%option}}".
 *
 * @property int $id
 * @property string $group
 * @property string $name
 * @property string $value
 * @property string $created_at
 * @property string $updated_at
 */
class Option extends ModelActiveRecord
{
    const GROUP_ADMIN = 'admin';
    const GROUP_APP = 'app';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%option}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'value', 'created_at', 'updated_at',], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['value'], 'string'],
            [['group', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'group' => 'Group',
            'name' => 'Name',
            'value' => 'Value',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
