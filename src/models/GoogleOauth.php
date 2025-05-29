<?php

namespace app\models;

/**
 * This is the model class for table "{{%google_oauth}}".
 *
 * @property int $id
 * @property int $mall_id
 * @property string $client_id  客户端id
 * @property string $client_secret  客户端密钥
 * @property int $is_delete
 * @property string $created_at
 * @property string $updated_at
 */
class GoogleOauth extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%google_oauth}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mall_id', 'created_at', 'updated_at',], 'required'],
            [['is_delete'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['client_secret', 'client_id'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_secret' => '客户端密钥',
            'client_id' => 'client_id',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
