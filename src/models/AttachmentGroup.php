<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%attachment_group}}".
 *
 * @property int $id
 * @property string $name
 * @property int $is_delete
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property int $is_recycle;
 * @property int $type;
 */
class AttachmentGroup extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%attachment_group}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['is_delete', 'is_recycle', 'type'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
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
            'name' => 'Name',
            'is_delete' => 'Is Delete',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
            'is_recycle' => '是否加入回收站 0.否|1.是',
            'type' => '0 图片 1商品',
        ];
    }
}
