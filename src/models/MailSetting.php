<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%mail_setting}}".
 *
 * @property int $id
 * @property int $mall_id
 * @property string $send_platform  发送平台
 * @property string $send_mail 发件人邮箱
 * @property string $send_pwd 授权码
 * @property string $send_name 发件人名称
 * @property int $is_delete
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class MailSetting extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%mail_setting}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mall_id', 'is_delete'], 'integer'],
            [['send_mail', 'send_platform'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'required'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['send_pwd', 'send_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mall_id' => 'Mall ID',
            'send_mail' => 'Send Mail',
            'send_pwd' => 'Send Pwd',
            'send_name' => 'Send Name',
            'is_delete' => 'Is Delete',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
        ];
    }
}
