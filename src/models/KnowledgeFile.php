<?php

namespace app\models;

/**
 * This is the model class for table "{{%knowledge_file}}".
 *
 * @property int $id
 * @property string $document_id  文档id
 * @property string $name
 * @property string $content
 * @property int $knowledge_id  知识库id
 * @property int $is_delete
 * @property string $created_at
 * @property string $updated_at
 * @property Knowledge $knowledge
 */
class KnowledgeFile extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%knowledge_file}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'knowledge_id', 'document_id'], 'required'],
            [['is_delete', 'knowledge_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'content', 'document_id'], 'string'],
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
            'content' => 'content',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getKnowledge()
    {
        return $this->hasOne(Knowledge::className(), ['id' => 'knowledge_id']);
    }
}
