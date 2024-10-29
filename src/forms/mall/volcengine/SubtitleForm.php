<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\volcengine;

use app\bootstrap\response\ApiCode;
use app\forms\common\volcengine\api\AtaQuery;
use app\forms\common\volcengine\api\AtaSubmit;
use app\forms\common\volcengine\api\AucBigModelQuery;
use app\forms\common\volcengine\api\AucBigModelSubmit;
use app\forms\common\volcengine\api\VcQuery;
use app\forms\common\volcengine\api\VcSubmit;
use app\forms\common\volcengine\ApiForm;
use app\jobs\CommonJob;
use app\models\Attachment;
use app\models\AvData;
use app\models\Model;

class SubtitleForm extends Model
{
    public $file;
    public $text;
    public $id;
    public $type;
    public $is_del;

    const TYPE_VC = 1; // 音视频转字幕
    const TYPE_ATA = 2; // 音频打轴
    const TYPE_AUC = 3; // 大模型录音文件转字幕

    public function rules()
    {
        return [
            [['file'], 'required'],
            [['file', 'text'], 'string'],
            [['id', 'type', 'is_del'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $model = new AvData();
        $model->attributes = $this->attributes;
        $model->type = $this->type ?: 1;
        if(!$model->save ()){
            return $this->getErrorResponse($model);
        }
        \Yii::$app->queue->delay (0)->push (new CommonJob([
            'type' => 'handle_subtitle',
            'data' => ['id' => $model->id, 'is_del' => $this->is_del]
        ]));
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功'
        ];
    }

    public function handle(){
        $model = AvData::findOne (['id' => $this->id]);
        if(!$model) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '数据不存在'
            ];
        }
        try{
            if($model->type == self::TYPE_AUC) {
                $obj = new AucBigModelSubmit();
                $queryObj = new AucBigModelQuery();
            }else if($model->type == self::TYPE_VC) {
                $obj = new VcSubmit();
                $queryObj = new VcQuery();
            }else{
                $obj = new AtaSubmit();
                $obj->audio_text = $model->text;
                $queryObj = new AtaQuery();
            }
            if($model->type == self::TYPE_AUC) {
                $obj->url = $model->file;
                $extension = pathinfo($obj->url, PATHINFO_EXTENSION);
                $extension = strtolower($extension);
                $obj->format = $extension;
            }else {
                $obj->url = $model->localFile ($model->file);
            }
            $res = ApiForm::common (['object' => $obj])->request();
            $model->job_id = $res['id'] ?? '';
            $queryObj->id = $model->job_id;

            if($model->type == self::TYPE_AUC) {
                do{
                    sleep (1);
                    $res = ApiForm::common (['object' => $queryObj])->request ();
                }while(empty($res['result']['text']));
                $res = ['utterances' => $res['result']['utterances']];
            }else {
                $res = ApiForm::common (['object' => $queryObj])->request ();
            }
            $text = '';
            foreach ($res['utterances'] as $k => $item){
                $k++;
                $text .= "{$k}\r\n" . $this->times($item['start_time']) . " --> "
                    . $this->times($item['end_time']) . "\r\n" . $item['text'] . "\r\n\r\n";
            }
            $fileRes = file_uri('/web/uploads/av_file/');
            $file = $fileRes['local_uri'] . "{$model->id}.srt";
            file_put_contents($file, $text);
            $model->result = $fileRes['web_uri'] . "{$model->id}.srt";
            $model->status = 2;
            $return = [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '成功'
            ];
        }catch (\Exception $e){
            $model->status = 3;
            $model->err_msg = $e->getMessage();
            $return = [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage() . $e->getLine() . $e->getFile()
            ];
        }
        if($this->is_del){
            if(isset($obj) && file_exists ($obj->url)){
                @unlink($obj->url);
            }
            $attachment = Attachment::findOne(['url' => $model->file, 'is_delete' => 0, 'type' => 2]);
            if($attachment){
                $attachment->delete();
            }
        }
        if(!$model->save()){
            \Yii::error ("model 保存失败");
            \Yii::error ($model->attributes);
            return $this->getErrorResponse($model);
        }
        return $return;
    }

    function times($milliseconds): string
    {
        // 将毫秒转换为秒
        $seconds = intval($milliseconds / 1000);

        // 计算小时、分钟和秒
        $hours = intval($seconds / 3600);
        $minutes = intval(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        // 返回格式化的字符串
        return sprintf('%02d:%02d:%02d,%03d', $hours, $minutes, $seconds, $milliseconds % 1000);
    }
}
