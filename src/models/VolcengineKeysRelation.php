<?php

namespace app\models;

/**
 * This is the model class for table "{{%volcengine_keys_relation}}".
 *
 * @property int $id
 * @property int $key_id
 * @property int $account_id
 * @property int $is_delete
 * @property string $created_at
 * @property VolcengineAccount $account
 * @property VolcengineKeys $key
 */
class VolcengineKeysRelation extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%volcengine_keys_relation}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key_id', 'created_at', 'account_id',], 'required'],
            [['is_delete', 'key_id', 'account_id'], 'integer'],
            [['created_at'], 'safe'],
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
        ];
    }

    public function getAccount()
    {
        return $this->hasOne(VolcengineAccount::className(), ['id' => 'account_id']);
    }

    public function getKey()
    {
        return $this->hasOne(VolcengineKeys::className(), ['id' => 'key_id']);
    }
}
