<?php

namespace app\models;

/**
 * This is the model class for table "{{%knowledge}}".
 *
 * @property int $id
 * @property string $account_id 授权账号
 * @property string $space_id  所属空间
 * @property string $dataset_id  知识库ID
 * @property string $name
 * @property string $desc
 * @property int $num  文件的数量
 * @property int $format_type 0：文档类型；1：表格类型；2：照片类型
 * @property int $is_delete
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property CozeAccount $account
 */
class Knowledge extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%knowledge}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dataset_id', 'created_at', 'updated_at', 'account_id', 'space_id'], 'required'],
            [['is_delete', 'format_type', 'num', 'account_id'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['dataset_id', 'name', 'desc', 'space_id'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dataset_id' => '知识库ID',
            'name' => 'Name',
            'desc' => 'desc',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getAccount()
    {
        return $this->hasOne(CozeAccount::className(), ['id' => 'account_id']);
    }
}
