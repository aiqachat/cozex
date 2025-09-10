<?php
/**
 * @copyright ©2024 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 */

namespace app\forms;

use app\forms\admin\mall\MallOverrunForm;
use app\forms\common\attachment\AttachmentUpload;
use app\forms\common\attachment\CommonAttachment;
use app\forms\common\RedisTimeQueue;
use app\forms\mall\setting\ContentForm;
use app\models\Model;
use app\models\User;
use GuzzleHttp\Psr7\MimeType;
use yii\web\UploadedFile;

class AttachmentUploadForm extends Model
{
    /** @var UploadedFile */
    public $file;

    public $type;

    public $exclude;

    public $attachment_group_id;

    protected $docExt = ['txt', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'csv', 'pdf', 'md'];
    protected $imageExt = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp',];
    protected $videoExt = ['mp4', 'ogg', 'm4a', 'wav', 'mp3'];

    public function rules()
    {
        return [
            [['file'], 'file'],
            [['attachment_group_id', 'exclude'], 'integer'],
            [['type'], 'string'],
            [['file'], 'validateExt'],
        ];
    }

    public function validateExt($a, $p)
    {
        // 获取真实文件类型，判断跟文件后缀是否一致
//        $mimeType = FileHelper::getMimeType($this->file->tempName, null, false);
//        $extensionsByMimeType = FileHelper::getExtensionsByMimeType($mimeType);
//        if (!in_array(mb_strtolower($this->file->extension, 'UTF-8'), $extensionsByMimeType, true)) {
//            $this->addError ($a, '文件错误');
//        }

        // 根据图片后缀得到类型
        $extension = strtolower(pathinfo($this->file->name, PATHINFO_EXTENSION));
        $mimeType = MimeType::fromExtension($extension);
        if ($mimeType === null || ($mimeType !== $this->file->type && strpos($mimeType, $extension) === false)) {
            $this->addError ($a, '文件错误');
        }
        if($this->type != 'video') {
            $supportExt = array_merge($this->docExt, $this->imageExt, $this->videoExt);
            if (!in_array ($this->file->extension, $supportExt)) {
                $this->addError ($a, '不支持的文件类型: ' . $this->file->extension);
            }
        }else{
            if (strpos($mimeType, 'video') === false && strpos($mimeType, 'audio') === false) {
                $this->addError($a, '不支持的音视频类型: ' . $mimeType);
            }
        }

        $option = (new MallOverrunForm())->getSetting();
        if (($this->type == 'image' || in_array($this->file->extension, $this->imageExt))){
            if (!$option['is_img_overrun'] && $this->file->size > ($option['img_overrun'] * 1024 * 1024)) {
                $this->addError($a, '图片大小超出限制,当前大小为: '
                    . (round($this->file->size / 1024 / 1024, 4)) . 'MB,最大限制为:'
                    . $option['img_overrun'] . 'MB');
            }
        }else{
            if (!$option['is_video_overrun'] && $this->file->size > ($option['video_overrun'] * 1024 * 1024)) {
                $this->addError($a, '文件大小超出限制,当前大小为: '
                    . (round($this->file->size / 1024 / 1024, 4)) . 'MB,最大限制为:'
                    . $option['video_overrun'] . 'MB');
            }
        }
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse($this);
        }

        try {
            $mall = \Yii::$app->mall;
        } catch (\Exception $e) {
            $mall = null;
        }

        $user = null;
        $user_id = 0;
        if (!\Yii::$app->user->isGuest) {
            /** @var User $user */
            $user = \Yii::$app->user->identity;
            $userIdentity = $user->identity;
            if (
                $userIdentity && !$userIdentity->is_super_admin && !$userIdentity->is_admin
            ) {
                $user_id = $user->id;
            }
        }
        try {
            $storage = CommonAttachment::getCommon($user, $mall)->getAttachment();
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
            $mallId = $mall ? $mall->id : 0;

            $attachmentUpload = new AttachmentUpload([
                'storage' => $storage,
                'file' => $this->file,
                'mall_id' => $mallId,
                'type' => $type ?? 0,
                'attachment_group_id' => $this->attachment_group_id ?: 0,
                'user_id' => $user_id,
                'exclude' => $this->exclude,
            ]);
            $attachment = $attachmentUpload->upload();
            $attachment->thumb_url = $attachment->thumb_url ? $attachment->thumb_url : $attachment->url;
            if($user_id && $mallId && $attachment->id){
                $config = (new ContentForm())->config();
                (new RedisTimeQueue())->push($attachment::REDIS_KEY, [
                    'id' => $attachment->id,
                ], time() + $config['attachment_storage_time'] * 3600);
            }
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
