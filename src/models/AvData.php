<?php

namespace app\models;

use app\forms\mall\volcengine\SpeechForm;

/**
 * This is the model class for table "{{%av_data}}".
 *
 * @property int $id
 * @property int $account_id 账号id
 * @property string $file 音视频文件
 * @property string $text  字幕文本
 * @property string $job_id  任务id
 * @property string $result  最终结果
 * @property string $err_msg
 * @property string $data
 * @property int $type  1:转字幕；2：字幕打轴；3：大模型录音识别；4：大模型一次性语音合成；5：精品语音合成 - 异步
 * @property int $status 1:处理中；2：处理完成；3：失败
 * @property int $is_delete
 * @property string $created_at
 * @property string $updated_at
 * @property VolcengineAccount $account
 */
class AvData extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%av_data}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'required'],
            [['is_delete', 'status', 'type', 'account_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['job_id', 'result', 'err_msg', 'text', 'file', 'job_id', 'data', 'file'], 'string'],
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

    public function localFile($file = '')
    {
        $file = $file ?: $this->file;
        $res = file_uri ('/web');
        $resultFile = str_replace($res['web_uri'], $res['local_uri'], $file);
        if(file_exists ($resultFile)){
            return $resultFile;
        }else{
            $res = file_uri ('/web/temp/');
            $name = @basename($this->file);
            file_put_contents($res['local_uri']. $name, @file_get_contents($file));
            return $res['local_uri']. $name;
        }
    }

    public function voice($voice = '')
    {
        if(!$voice){
            return "--";
        }
        $list = [];
        $fun = function ($data) use (&$list, &$fun){
            foreach ($data as $item){
                if(!isset($item['children'])){
                    $list[] = $fun($item);
                }else{
                    foreach ($item['children'] as $child){
                        $list[] = $child;
                    }
                }
            }
        };
        $fun((new SpeechForm())->voiceType(null, false));
        $list = array_column($list, 'name', 'id');
        if(!isset($list[$voice]) && strpos($voice, "S_") !== false){
            return "复刻音色";
        }
        return $list[$voice];
    }

    public function getAccount()
    {
        return $this->hasOne(VolcengineAccount::className(), ['id' => 'account_id']);
    }
}
