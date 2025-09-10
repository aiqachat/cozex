<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\volcengine\data;

use app\bootstrap\response\ApiCode;
use app\forms\common\volcengine\api\TtsAsyncQuery;
use app\forms\common\volcengine\api\TtsAsyncSubmit;
use app\forms\common\volcengine\api\TtsGenerate;
use app\forms\common\volcengine\ApiForm;
use app\jobs\CommonJob;
use app\models\AvData;
use app\models\User;
use yii\helpers\Json;

class SpeechBaseForm extends BaseForm
{
    public $text;
    public $id;
    public $type;
    public $data;
    public $account_id;
    public $user_id;
    public $file;
    public $is_home;

    public function rules()
    {
        return [
            [['text', 'file'], 'string'],
            [['id', 'type', 'account_id', 'user_id', 'is_home'], 'integer'],
            [['data'], 'safe'],
            [['file'], 'default', 'value' => ''],
            [['is_home'], 'default', 'value' => 1],
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        if (!empty($this->data['voice_type'])) {
            $this->data['voice_type'] = str_replace($this->repeat, "", $this->data['voice_type']);
        }
        $model = new AvData();
        $model->attributes = $this->attributes;
        $model->type = $this->type ?: $this->ttsLong;
        $model->user_id = $this->user_id ?: 0;
        $model->mall_id = \Yii::$app->mall->id;
        $this->handleModel($model);
        $model->data = Json::encode($this->data);
        if (!$model->save()) {
            return $this->getErrorResponse($model);
        }
        if ($model->type == $this->tts) {
            $this->id = $model->id;
            $this->handle();
        } else {
            \Yii::$app->queue->delay(0)->push(new CommonJob([
                'type' => 'handle_speech',
                'mall' => \Yii::$app->mall,
                'data' => ['id' => $model->id]
            ]));
        }
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功'
        ];
    }

    public function handleModel(AvData $model)
    {
    }

    public function pay($id, $data)
    {
        if (!empty($data['payment_type'])) {
            $params = [
                'id' => $id,
                'name' => '语音技术消耗'
            ];
            $currency = \Yii::$app->currency->setUser($this->user);
            switch ($data['payment_type']) {
                case \Yii::$app->payment::PAY_TYPE_INTEGRAL:
                    $amount = floatval($data['cost'] ?? 0);
                    $currency->integral->sub(
                        $amount,
                        "账户积分支付：" . $amount,
                        \Yii::$app->serializer->encode($params)
                    );
                    break;
                case \Yii::$app->payment::PAY_TYPE_BALANCE:
                    $amount = floatval($data['cost'] ?? 0);
                    $currency->balance->sub(
                        $amount,
                        "账户余额支付：{$amount}元",
                        \Yii::$app->serializer->encode($params)
                    );
                    break;
                default:
                    throw new \Exception('错误的支付方式');
            }
        }
    }

    private $user;

    public function handle()
    {
        $model = AvData::findOne(['id' => $this->id, 'mall_id' => \Yii::$app->mall->id]);
        $t = \Yii::$app->db->beginTransaction();
        try {
            if (!$model || !$model->account) {
                throw new \Exception('数据不存在');
            }
            $this->user = User::findOne($model->user_id);
            \Yii::$app->user->setIdentity($this->user);
            $data = $model->data ? Json::decode($model->data) : [];
            if (!$model->text) {
                $text = @file_get_contents($model->localFile()) ?: '';
            } else {
                $text = $model->text;
            }
            $this->pay($model->id, $data);
            $api = ApiForm::common([
                'appid' => $data['app_id'] ?? '',
                'token' => $data['access_token'] ?? '',
                'account' => $model->account
            ]);
            if ($model->type == $this->ttsLong) {
                $obj = new TtsAsyncSubmit();
                $obj->setVersion($data['version']);
                $obj->style = $data['style'] ?? '';
                $obj->language = $data['language'] ?? '';
                $obj->speed = floatval($data['speed'] ?? 1);
                $obj->enable_subtitle = 1;
            } else {
                $obj = new TtsGenerate();
                $obj->speed_ratio = floatval($data['speed'] ?? 1);
                if(!empty($data['style'])){
                    $obj->emotion = $data['style'];
                }
                if(!empty($data['language'])){
                    $obj->language = $data['language'];
                }
                if(isset($data['enable_emotion']) && $data['enable_emotion'] == 'true'){
                    $obj->enable_emotion = true;
                    $obj->emotion = $data['emotion'];
                    $obj->emotion_scale = floatval($data['emotion_scale']);
                }
                if(isset($data['encoding'])){
                    $obj->encoding = $data['encoding'];
                }
                if(isset($data['rate'])){
                    $obj->rate = intval($data['rate']);
                }
                if(isset($data['bitrate'])){
                    $obj->bitrate = intval($data['bitrate']);
                }
                if(isset($data['loudness_ratio'])){
                    $obj->loudness_ratio = floatval($data['loudness_ratio']);
                }
            }
            if ($model->type == $this->ttsMega) {
                $obj->cluster = TtsGenerate::TWO;
            }
            $obj->voice_type = $data['voice_type'];
            $obj->text = $text;
            $res = $api->setObject($obj)->request();

            if ($model->type == $this->ttsLong) {
                $model->job_id = $res['task_id'] ?? '';
                $queryObj = new TtsAsyncQuery();
                $queryObj->setVersion($data['version']);
                $queryObj->task_id = $model->job_id;
                do {
                    sleep(2);
                    $res = $api->setObject($queryObj)->request();
                } while (empty($res['audio_url']));
                $ext = $obj->format;
                $content = @file_get_contents($res['audio_url']);
            } else {
                $ext = $obj->encoding;
                $content = base64_decode($res['data']);
            }

            $fileRes = file_uri(AvData::FILE_DIR . date("Y-m-d") . "/");
            if ($model->file) {
                $pathInfo = pathinfo($model->file);
                $baseName = $pathInfo['filename'];
                $counter = 1;
                $name = "{$baseName}.{$ext}";
                while (file_exists($fileRes['local_uri'] . $name)) {
                    $name = $baseName . '_' . $counter . '.' . $ext;
                    $counter++;
                }
                $file = $fileRes['local_uri'] . $name;
            }else {
                do {
                    $name = uniqid() . ".{$ext}";
                    $file = $fileRes['local_uri'] . $name;
                } while (file_exists ($file));
            }
            file_put_contents($file, $content);
            $model->result = $fileRes['web_uri'] . $name;
            $model->status = 2;
            $return = [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '成功'
            ];
            $t->commit();
        } catch (\Exception $e) {
            $t->rollBack();
            if($model) {
                $model->status = 3;
                $model->err_msg = $e->getMessage ();
            }
            $return = [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage() . $e->getLine() . $e->getFile()
            ];
        }
        if ($model) {
            if(!$model->save()) {
                \Yii::error ("model 保存失败");
                return $this->getErrorResponse($model);
            }
            if($model->status == 2){
                \Yii::$app->queue1->delay($model::DELETE_FILE_DAY * 86400)->push(new CommonJob([
                    'type' => 'delete_avData',
                    'mall' => \Yii::$app->mall,
                    'data' => ['id' => $model->id]
                ]));
            }
        }
        return $return;
    }

    public function handleDel()
    {
        try {
            $model = AvData::findOne(['id' => $this->id, 'mall_id' => \Yii::$app->mall->id]);
            if (!$model) {
                throw new \Exception('数据不存在');
            }
            $model->deleteData();
            $return = [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '成功'
            ];
        } catch (\Exception $e) {
            $return = [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage() . $e->getLine() . $e->getFile()
            ];
        }
        return $return;
    }

    public function download()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $model = AvData::findOne(['id' => $this->id, 'mall_id' => \Yii::$app->mall->id]);
        if (!$model) {
            return [
                'code' => 1,
                'msg' => '数据不存在'
            ];
        }

        if ($model->status != 2 || empty($model->result)) {
            return [
                'code' => 1,
                'msg' => '尚未生成或生成失败'
            ];
        }

        $outputPath = $model->localFile($model->result);

        if (!file_exists($outputPath)) {
            return [
                'code' => 1,
                'msg' => '文件不存在'
            ];
        }

        // 设置响应头，强制下载
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($outputPath) . '"');
        header('Content-Length: ' . filesize($outputPath));
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        // 读取文件并输出
        readfile($outputPath);
        exit();
    }
}
