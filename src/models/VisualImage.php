<?php

namespace app\models;

use app\forms\mall\setting\ContentForm;

/**
 * This is the model class for table "{{%visual_image}}".
 *
 * @property int $id
 * @property int $mall_id
 * @property int $key_id 账号id
 * @property int $user_id 用户id
 * @property string $prompt 提示词
 * @property string $image_url  图片地址
 * @property string $data
 * @property int $type 1：即梦AI-文生图；2：火山方舟 - 文生图；3：即梦-图生图；4：火山方舟 - 图生图
 * @property int $is_home 1：国内站；2：国际站
 * @property int $status 状态 1:处理中 2:处理成功 3:处理失败
 * @property int $is_delete
 * @property int $is_user_public
 * @property int $is_admin_public 0：否；1：通过
 * @property int $is_permanent_public 0：否，2：一直驳回；1：一直通过 ；3：后台操作
 * @property int $is_saved  0：不保存，1：长期保存
 * @property string $created_at
 * @property string $updated_at
 * @property string $err_msg
 * @property VolcengineKeys $key
 * @property User $user
 */
class VisualImage extends ModelActiveRecord
{
    const FILE_DIR = VisualVideo::FILE_DIR;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%visual_image}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mall_id', 'created_at', 'updated_at'], 'required'],
            [['is_delete', 'type', 'key_id', 'user_id', 'is_home', 'is_saved', 'status',
                'is_user_public', 'is_admin_public', 'is_permanent_public'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['image_url', 'prompt', 'data', 'err_msg'], 'string'],
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
            'updated_at' => 'Updated At',
        ];
    }

    public function getKey()
    {
        return $this->hasOne(VolcengineKeys::className(), ['id' => 'key_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function deleteData($del = false)
    {
        if(!$del && $this->is_admin_public == 1 && $this->is_user_public == 1){
            throw new \Exception('展示的图片无法删除');
        }
        if($this->image_url){
            $model = new AvData();
            @unlink($model->localFile($this->image_url, false));
        }
        $this->delete();
    }

    public function deleteTime()
    {
        $config = (new ContentForm())->config();
        return $config['image_storage_time'];
    }
}
