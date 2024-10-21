<?php

namespace app\models;

/**
 * This is the model class for table "{{%bot_conf}}".
 *
 * @property int $id
 * @property string $bot_id 智能体ID
 * @property string $version 版本号
 * @property string $title  智能体名字
 * @property string $icon  智能体的显示图标
 * @property string $lang  智能体的系统语言
 * @property string $layout 智能体窗口的布局风格
 * @property int $is_width  1: 默认，2：自定义
 * @property int $width  智能体窗口的宽度
 * @property int $is_delete
 * @property string $created_at
 * @property string $updated_at
 */
class BotConf extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%bot_conf}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'bot_id'], 'required'],
            [['is_delete', 'width', 'is_width'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['icon', 'lang', 'layout', 'title', 'bot_id', 'icon', 'version'], 'string'],
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
        ];
    }
}
