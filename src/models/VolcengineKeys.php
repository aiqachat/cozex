<?php

namespace app\models;

/**
 * This is the model class for table "{{%volcengine_keys}}".
 *
 * @property int $id
 * @property int $mall_id
 * @property string $name
 * @property int $account_id
 * @property string $access_id
 * @property string $secret_key
 * @property int $is_delete
 * @property int $type
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property VolcengineAccount[] $accounts
 * @property VolcengineKeysRelation[] $keysRelation
 */
class VolcengineKeys extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%volcengine_keys}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mall_id', 'secret_key', 'created_at', 'updated_at',], 'required'],
            [['is_delete', 'account_id', 'type'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['secret_key', 'name', 'access_id'], 'string'],
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
            'access_id' => 'access_id',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getAccounts()
    {
        return $this->hasMany(VolcengineAccount::className(), ['id' => 'account_id'])->andWhere(['is_delete' => 0])
            ->via("keysRelation");
    }

    public function getKeysRelation()
    {
        return $this->hasMany(VolcengineKeysRelation::className(), ['key_id' => 'id'])->andWhere(['is_delete' => 0]);
    }
}
