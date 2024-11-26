<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\mall\volcengine;

use app\bootstrap\response\ApiCode;
use app\forms\common\volcengine\api\MegaTtsUpload;
use app\forms\common\volcengine\ApiForm;
use app\models\Attachment;
use app\models\AvData;
use app\models\Model;
use app\models\VolcengineAccount;
use yii\web\UploadedFile;

class SoundReprintForm extends Model
{
    public $text;
    public $id;
    public $file_id;
    public $type;
    public $account_id;
    public $speaker_id;
    public $model_type;
    public $language;

    public function rules()
    {
        return [
            [['account_id', 'type', 'speaker_id'], 'required'],
            [['text', 'speaker_id', 'language'], 'string'],
            [['id', 'type', 'account_id', 'file_id', 'model_type'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'account_id' => '应用',
            'speaker_id' => '声音id',
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        try {
            $account = VolcengineAccount::findOne ($this->account_id);
            if (!$account) {
                throw new \Exception('账号不存在');
            }
            $obj = new MegaTtsUpload();
            if ($this->type == 1) {
                $file = UploadedFile::getInstanceByName ("file");
                $name = $file->tempName;
                $obj->audio_format = "wav";
            } else {
                $attachment = Attachment::findOne ($this->file_id);
                if (!$attachment) {
                    throw new \Exception('文件不存在');
                }
                $name = (new AvData())->localFile ($attachment->url);
                $obj->audio_format = pathinfo ($name, PATHINFO_EXTENSION);
            }
            $obj->audio_bytes = base64_encode (file_get_contents ($name));
            $obj->speaker_id = $this->speaker_id;
            $obj->language = MegaTtsUpload::languageList[$this->language] ?? 0;
            $obj->model_type = intval ($this->model_type);
            ApiForm::common (['account' => $account, 'object' => $obj])->request ();
            $return = [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '成功',
            ];
        }catch (\Exception $e){
            $return = [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage ()
            ];
        }
        if(!empty($attachment) && isset($name)){
            @unlink ($name);
            $attachment->delete();
        }
        return $return;
    }
}
