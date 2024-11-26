<?php

namespace app\models;

/**
 * This is the model class for table "{{%coze_account}}".
 *
 * @property int $id
 * @property string $name
 * @property string $remark
 * @property string $client_id
 * @property string $client_secret
 * @property string $refresh_token
 * @property string $coze_secret  访问令牌
 * @property int $type 1：个人令牌；2：OAuth
 * @property int $expires_in
 * @property int $is_delete
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class CozeAccount extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%coze_account}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['coze_secret', 'created_at', 'updated_at',], 'required'],
            [['is_delete', 'type', 'expires_in'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['coze_secret', 'name', 'remark', 'client_id', 'client_secret', 'refresh_token'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'coze_secret' => '访问令牌',
            'name' => 'Name',
            'remark' => 'remark',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function saveOauth($data)
    {
        $this->coze_secret = $data['access_token'];
        $this->refresh_token = $data['refresh_token'];
        $this->expires_in = $data['expires_in'];
        return $this->save();
    }
}
