<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%attachment}}".
 *
 * @property int $id
 * @property int $storage_id
 * @property int $attachment_group_id
 * @property int $user_id
 * @property string $name
 * @property int $size 大小：字节
 * @property string $url
 * @property string $thumb_url
 * @property int $type 类型：1=图片，2=视频
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property int $is_delete
 * @property int $is_recycle;
 */
class Attachment extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%attachment}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['storage_id', 'user_id', 'name', 'size', 'url', 'type', 'created_at', 'updated_at', 'deleted_at'], 'required'],
            [['storage_id', 'attachment_group_id', 'user_id', 'size', 'type', 'is_delete', 'is_recycle'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['name'], 'string', 'max' => 128],
            [['url', 'thumb_url'], 'string', 'max' => 2080],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'storage_id' => 'Storage ID',
            'attachment_group_id' => 'Attachment Group ID',
            'user_id' => 'User ID',
            'name' => 'Name',
            'size' => '大小：字节',
            'url' => 'Url',
            'thumb_url' => 'Thumb Url',
            'type' => '类型：1=图片，2=视频',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
            'is_delete' => 'Is Delete',
            'is_recycle' => '是否加入回收站 0.否|1.是',
        ];
    }

    public function getThumbUrl()
    {
        return $this->thumb_url ? $this->thumb_url : $this->url;
    }
}
