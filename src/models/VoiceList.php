<?php

namespace app\models;

use yii\helpers\Json;

/**
 * This is the model class for table "{{%voice_list}}".
 *
 * @property int $id 用户身份表
 * @property string $name
 * @property string $voice_id  音色id
 * @property string $voice_type 音色类型
 * @property string $audio 试听地址
 * @property string $pic 封面图
 * @property int $sex 1男；2女
 * @property int $age 1青年；2少年/少女；3中年；4老年；5儿童
 * @property int $status
 * @property string $emotion 情感
 * @property string $language 语言
 * @property string $language_data
 */
class VoiceList extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%voice_list}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'voice_id', 'voice_type', 'audio', 'pic', 'language', 'language_data', 'emotion'], 'string'],
            [['sex', 'age', 'status'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
        ];
    }

    public function switchData()
    {
        if(is_string($this->language_data)) {
            $this->language_data = Json::decode ($this->language_data);
        }
        if(\Yii::$app->language != 'zh'){
            $data = $this->language_data[\Yii::$app->language] ?? [];
            $this->name = !empty($data['name']) ? $data['name'] : $this->name;
        }
    }
}
