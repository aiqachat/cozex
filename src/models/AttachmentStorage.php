<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%attachment_storage}}".
 *
 * @property string $id
 * @property int $type 存储类型：1=本地，2=阿里云，3=腾讯云，4=七牛
 * @property string $config 存储配置
 * @property int $status 状态：0=未启用，1=已启用
 * @property string $created_at
 * @property string $updated_at
 * @property int $user_id 存储设置所属账号
 */
class AttachmentStorage extends ModelActiveRecord
{
    const STORAGE_TYPE_LOCAL = 1;
    const STORAGE_TYPE_ALIOSS = 2;
    const STORAGE_TYPE_TXCOS = 3;
    const STORAGE_TYPE_QINIU = 4;
    const STORAGE_TYPE_TOS = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%attachment_storage}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'status', 'user_id'], 'integer'],
            [['config'], 'required'],
            [['config'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '存储类型：1=本地，2=阿里云，3=腾讯云，4=七牛',
            'config' => '存储配置',
            'status' => '状态：0=未启用，1=已启用',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'user_id' => '存储设置所属账号',
        ];
    }
}
