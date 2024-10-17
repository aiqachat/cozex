<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%core_exception_log}}".
 *
 * @property int $id
 * @property int $level 异常等级1.报错|2.警告|3.记录信息
 * @property string $title 异常标题
 * @property string $content 异常内容
 * @property string $created_at
 * @property int $is_delete
 */
class CoreExceptionLog extends ModelActiveRecord
{
    public $isLog = false;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%core_exception_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content', 'created_at'], 'required'],
            [['level', 'is_delete'], 'integer'],
            [['content'], 'string'],
            [['created_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'level' => 'Level',
            'title' => 'Title',
            'content' => 'Content',
            'created_at' => 'Created At',
            'is_delete' => 'Is Delete',
        ];
    }
}
