<?php

namespace app\models;

/**
 * This is the model class for table "{{%volcengine_account}}".
 *
 * @property int $id
 * @property string $name
 * @property string $app_id
 * @property string $access_token  访问token
 * @property int $is_delete
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property VolcengineKeys $key
 */
class VolcengineAccount extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%volcengine_account}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['access_token', 'created_at', 'updated_at',], 'required'],
            [['is_delete'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['access_token', 'name', 'app_id'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'access_token' => '访问令牌',
            'name' => 'Name',
            'app_id' => 'app_id',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getKey()
    {
        return $this->hasOne(VolcengineKeys::className(), ['id' => 'key_id'])
            ->viaTable(VolcengineKeysRelation::tableName(), ['account_id' => 'id'], function ($query) {
                $query->andWhere(['is_delete' => 0]);
            })->andWhere(['is_delete' => 0]);
    }
}
