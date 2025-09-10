<?php

namespace app\models;

use yii\helpers\Json;

/**
 * This is the model class for table "{{%user_level}}".
 *
 * @property int $id 
 * @property int $mall_id
 * @property string $name
 * @property float $promotion_commission_ratio
 * @property int $status
 * @property string $promotion_desc
 * @property int $promotion_status
 * @property int $is_default
 * @property int $is_delete 是否删除
 * @property string $language_data
 * @property string $created_at
 * @property string $updated_at
 */
class UserLevel extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_level}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'is_default', 'mall_id', 'promotion_status', 'is_delete'], 'integer'],
            [['name', 'promotion_desc', 'language_data', 'created_at', 'updated_at'], 'string'],
            [['promotion_commission_ratio'], 'number'],
        ];
    }

    public function switchData()
    {
        if(is_string($this->language_data)) {
            $this->language_data = Json::decode ($this->language_data);
        }
        if(\Yii::$app->language != 'zh'){
            $data = $this->language_data[\Yii::$app->language] ?? [];
            $this->promotion_desc = !empty($data['promotion_desc']) ? $data['promotion_desc'] : $this->promotion_desc;
        }
    }
}
