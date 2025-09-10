<?php

namespace app\models;

use app\forms\mall\setting\ContentForm;

/**
 * This is the model class for table "{{%visual_video}}".
 *
 * @property int $id
 * @property int $mall_id
 * @property int $key_id 账号id
 * @property int $user_id 用户id
 * @property string $prompt 提示词
 * @property string $image_urls 图片链接数组
 * @property string $err_msg
 * @property string $data
 * @property string $aspect_ratio 视频尺寸
 * @property string $task_id 任务id
 * @property string $video_url 视频地址
 * @property string $mode
 * @property int $seed
 * @property int $status 状态 1:处理中 2:处理成功 3:处理失败
 * @property int $type 1：即梦AI 2：火山方舟
 * @property int $is_home 1：国内站；2：国际站
 * @property int $is_delete
 * @property int $is_user_public
 * @property int $is_admin_public
 * @property int $is_permanent_public 0：否，2：一直驳回；1：一直通过 ；3：后台操作
 * @property int $is_saved  0：不保存，1：长期保存
 * @property string $created_at
 * @property string $updated_at
 * @property VolcengineKeys $key
 * @property User $user
 */
class VisualVideo extends ModelActiveRecord
{
    const FILE_DIR = '/web/uploads/visual_file/';
    /**
     * 即梦AI
     */
    const DREAM_NAME = 'dream';
    /**
     * 火山方舟国内版
     */
    const ARK_NAME = 'ark';
    /**
     * 火山方舟国际版
     */
    const ARK_ABROAD_NAME = 'ark_abroad';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%visual_video}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mall_id', 'created_at', 'updated_at'], 'required'],
            [['is_delete', 'key_id', 'user_id', 'seed', 'status', 'type', 'is_saved',
                'is_home', 'is_user_public', 'is_admin_public', 'is_permanent_public'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['image_urls', 'err_msg', 'prompt', 'data', 'task_id', 'aspect_ratio',
                'video_url', 'mode'], 'string'],
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
            throw new \Exception('展示的视频无法删除');
        }
        if($this->video_url){
            $model = new AvData();
            @unlink($model->localFile($this->video_url, false));
        }
        $this->delete();
    }

    public function deleteTime()
    {
        $config = (new ContentForm())->config();
        return $config['video_storage_time'];
    }
}
