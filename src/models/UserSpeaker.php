<?php

namespace app\models;

/**
 * This is the model class for table "{{%user_speaker}}".
 *
 * @property int $id
 * @property int $mall_id
 * @property int $user_id
 * @property int $account_id
 * @property string $speaker_id
 * @property string $name
 * @property int $is_delete
 * @property string $created_at
 * @property string $updated_at
 * @property VolcengineAccount $account
 */
class UserSpeaker extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_speaker}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mall_id', 'created_at', 'updated_at',], 'required'],
            [['is_delete', 'user_id', 'account_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['speaker_id', 'name'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'speaker_id' => 'speaker_id',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getAccount()
    {
        return $this->hasOne(VolcengineAccount::className(), ['id' => 'account_id']);
    }
}
