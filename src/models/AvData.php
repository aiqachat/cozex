<?php

namespace app\models;

use app\forms\common\attachment\AttachmentRemove;
use app\forms\common\volcengine\data\BaseForm;
use app\forms\common\volcengine\data\VoiceForm;
use app\forms\mall\setting\PriceForm;
use app\forms\mall\setting\UserConfigForm;

/**
 * This is the model class for table "{{%av_data}}".
 *
 * @property int $id
 * @property int $mall_id
 * @property int $account_id 账号id
 * @property int $user_id 用户id
 * @property string $file 音视频文件
 * @property string $text  字幕文本
 * @property string $job_id  任务id
 * @property string $result  最终结果
 * @property string $err_msg
 * @property string $data
 * @property int $type  1:转字幕；2：字幕打轴；3：大模型录音识别；4：大模型一次性语音合成；5：精品语音合成 - 异步
 * @property int $status 1:处理中；2：处理完成；3：失败
 * @property int $is_home 1：国内站；2：国际站
 * @property int $is_delete
 * @property string $created_at
 * @property string $updated_at
 * @property VolcengineAccount $account
 */
class AvData extends ModelActiveRecord
{
    const DELETE_FILE_DAY = 3;
    const FILE_DIR = '/web/uploads/av_file/';

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
            [['mall_id', 'created_at', 'updated_at'], 'required'],
            [['is_delete', 'status', 'type', 'account_id', 'user_id', 'is_home'], 'integer'],
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

    public function getAccount()
    {
        return $this->hasOne(VolcengineAccount::className(), ['id' => 'account_id']);
    }

    public function localFile($file = '', $generate = true)
    {
        $file = $file ?: $this->file;
        if(!$file){
            return '';
        }

        $path = @parse_url($file)['path'] ?? '';
        $pos = strpos($path, '/web');
        if($pos > 0){
            $path = substr($path, $pos);
        }
        $resultFile = \Yii::$app->basePath . $path;

        if(file_exists($resultFile)) {
            return $resultFile;
        }else if($generate) {
            $res = file_uri('/web/temp/');
            $name = @basename($file);
            @file_put_contents($res['local_uri'] . $name, @file_get_contents($file));
            return $res['local_uri'] . $name;
        }else{
            return $file;
        }
    }

    public function cost()
    {
        $length = mb_strlen($this->text);
        $data = is_string($this->data) ? json_decode($this->data, true) : $this->data;
        if(empty($data['payment_type'])){
            return $data;
        }
        $priceConfig = (new PriceForm())->config();
        $form = new BaseForm();
        if($this->type == $form->ttsLong){
            $exchange = $priceConfig['tts_long_exchange'];
        }elseif($this->type == $form->tts){
            $exchange = $priceConfig['tts_exchange'];
        }elseif($this->type == $form->ttsBig){
            $exchange = $priceConfig['tts_big_exchange'];
        }elseif($this->type == $form->ttsMega){
            $exchange = $priceConfig['tts_mega_exchange'];
        }else{
            $exchange = 0;
        }
        if($exchange <= 0){
            $data['cost'] = 0;
        }else{
            if($data['payment_type'] == \Yii::$app->payment::PAY_TYPE_INTEGRAL) {
                $data['cost'] = price_format($length / $exchange, PRICE_FORMAT_FLOAT, \Yii::$app->precision);
            }elseif($data['payment_type'] == \Yii::$app->payment::PAY_TYPE_BALANCE) {
                $form = new UserConfigForm();
                $form->tab = UserConfigForm::TAB_INTEGRAL;
                $config = $form->config();
                if($config['integral_rate'] <= 0){
                    $data['cost'] = 0;
                }else{
                    $data['cost'] = price_format($length / ($config['integral_rate'] * $exchange), PRICE_FORMAT_FLOAT, \Yii::$app->precision);
                }
            }
        }
        return $data;
    }

    public function voice($voice = '', $type = null)
    {
        if(!$voice || !is_string($voice)){
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
        $fun((new VoiceForm())->voiceType($type, false));
        $list = array_column($list, 'name', 'id');
        if(!isset($list[$voice]) && $voice && strpos($voice, "S_") !== false){
            return "复刻音色($voice)";
        }
        return $list[$voice];
    }

    public function deleteData()
    {
        if($this->result){
            @unlink($this->localFile($this->result, false));
        }
        if($this->file){
            @unlink($this->localFile($this->file, false));
            $attachment = Attachment::findOne([
                'url' => $this->file,
                'is_delete' => 0,
                'type' => 2,
                'mall_id' => $this->mall_id
            ]);
            if ($attachment) {
                AttachmentRemove::getCommon($attachment)->handle();
            }
        }
        $this->delete();
    }
}
