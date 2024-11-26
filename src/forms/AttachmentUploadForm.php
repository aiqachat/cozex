<?php
/**
 * @copyright ©2024 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 */

namespace app\forms;

use app\forms\common\attachment\AttachmentUpload;
use app\forms\common\attachment\CommonAttachment;
use app\models\Model;
use yii\web\UploadedFile;

class AttachmentUploadForm extends Model
{
    /** @var UploadedFile */
    public $file;

    public $type;

    public $attachment_group_id;

    protected $docExt = ['txt', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'csv', 'pdf', 'md'];
    protected $imageExt = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp',];
    protected $videoExt = ['mp4', 'ogg', 'm4a', 'wav', 'mp3'];

    public function rules()
    {
        return [
            [['file'], 'file'],
            [['attachment_group_id'], 'integer'],
            [['type'], 'string'],
            [['file'], 'validateExt'],
        ];
    }

    public function validateExt($a, $p)
    {
        if(!$this->type || $this->type != 'video') {
            $supportExt = array_merge ($this->docExt, $this->imageExt, $this->videoExt);
            if (!in_array ($this->file->extension, $supportExt)) {
                $this->addError ($a, '不支持的文件类型: ' . $this->file->extension);
            }
        }else{
            $finfo = finfo_open(FILEINFO_MIME_TYPE); // 打开文件信息处理
            $mimeType = finfo_file($finfo, $this->file->tempName); // 获取 MIME 类型
            finfo_close($finfo); // 关闭文件信息处理
            if (strpos($mimeType, 'video') === false && strpos($mimeType, 'audio') === false) {
                $this->addError($a, '不支持的音视频类型: ' . $mimeType);
            }
        }

        if (($this->type == 'image' || in_array($this->file->extension, $this->imageExt))){
            if ($this->file->size > (2 * 1024 * 1024)) {
                $this->addError($a, '图片大小超出限制,当前大小为: '
                    . (round($this->file->size / 1024 / 1024, 4)) . 'MB,最大限制为:'
                    . '2MB');
            }
        }else{
            $filesize = ini_get('upload_max_filesize');
            $size = intval($filesize);
            if(strtolower(str_replace($size, "", $filesize)) != 'm'){
                $size = 10;
            }
            if ($this->file->size > ($size * 1024 * 1024)) {
                $this->addError($a, '文件大小超出限制,当前大小为: '
                    . (round($this->file->size / 1024 / 1024, 4)) . 'MB,最大限制为:'
                    . "{$size}MB");
            }
        }
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse($this);
        }

        $user = null;
        if (!\Yii::$app->user->isGuest) {
            $user = \Yii::$app->user->identity;
        }
        try {
            $storage = CommonAttachment::getCommon($user)->getAttachment();
        } catch (\Exception $exception) {
            return [
                'code' => 1,
                'msg' => $exception->getMessage(),
            ];
        }

        if ($this->type === 'image') {
            $type = 1;
        } elseif ($this->type === 'video') {
            $type = 2;
        } elseif ($this->type === 'file') {
            $type = 3;
        } else {
            if (in_array($this->file->extension, $this->imageExt)) {
                $type = 1;
            } elseif (in_array($this->file->extension, $this->videoExt)) {
                $type = 2;
            } elseif (in_array($this->file->extension, $this->docExt)) {
                $type = 3;
            }
        }

        try {
            $attachmentUpload = new AttachmentUpload([
                'storage' => $storage,
                'file' => $this->file,
                'type' => $type ?? 0,
                'attachment_group_id' => $this->attachment_group_id ?: 0
            ]);
            $attachment = $attachmentUpload->upload();
            $attachment->thumb_url = $attachment->thumb_url ? $attachment->thumb_url : $attachment->url;
            $attachment->size = space_unit($attachment->size);
            return [
                'code' => 0,
                'data' => $attachment,
            ];
        } catch (\Exception $exception) {
            return [
                'code' => 1,
                'msg' => $exception->getMessage()
            ];
        }
    }

    public static function getInstanceFromFile($localFilePath)
    {
        return AttachmentUpload::getInstanceFromFile($localFilePath);
    }
}
