<?php
/**
 * Created by PhpStorm
 * User: wstianxia
 * Date: 2020/12/12
 * Time: 11:16 上午
 * @copyright: ©2020 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\forms\common\attachment;

use app\models\Attachment;
use app\models\AttachmentStorage;
use app\models\Model;
use Grafika\Grafika;
use Grafika\ImageInterface;
use OSS\OssClient;
use Qcloud\Cos\Client;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Tos\Model\PutObjectInput;
use Tos\TosClient;
use yii\web\UploadedFile;
use function GuzzleHttp\Psr7\mimetype_from_filename;

class AttachmentUpload extends Model
{
    public $storage;
    public $file;
    public $attachment_group_id;
    public $type;

    public $saveFileFolder;
    public $saveThumbFolder;
    public $saveFileName;
    public $url;
    public $thumbUrl;

    protected $docExt = ['txt', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'csv', 'pdf', 'md'];
    protected $imageExt = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp',];
    protected $videoExt = ['mp4', 'ogg', 'm4a',];

    public function getMallFolder()
    {
        return '';
    }

    /**
     * @return Attachment
     * @throws \Exception
     */
    public function upload()
    {
        $dateFolder = date('Ymd');
        $this->saveFileFolder = '/uploads/' . $this->getMallFolder() . $dateFolder;
        $this->saveThumbFolder = '/uploads/thumbs/' . $this->getMallFolder() . $dateFolder;
        $this->saveFileName = md5_file($this->file->tempName) . '.' . $this->file->getExtension();
        if (!$this->storage) {
            $this->saveToLocal();
        } else {
            switch ($this->storage->type) {
                case AttachmentStorage::STORAGE_TYPE_LOCAL:
                    $this->saveToLocal();
                    break;
                case AttachmentStorage::STORAGE_TYPE_ALIOSS:
                    $this->saveToAliOss();
                    break;
                case AttachmentStorage::STORAGE_TYPE_TXCOS:
                    $this->saveToTxCos();
                    break;
                case AttachmentStorage::STORAGE_TYPE_QINIU:
                    $this->saveToQiniu();
                    break;
                case AttachmentStorage::STORAGE_TYPE_TOS:
                    $this->saveToTos();
                    break;
                default:
                    throw new \Exception('未知的存储位置: type=' . $this->storage->type);
            }
        }
        return $this->attachmentSave();
    }

    public function attachmentSave()
    {
        $attachment = new Attachment();
        $attachment->storage_id = $this->storage ? $this->storage->id : 0;
        $attachment->user_id = 0;
        $attachment->name = $this->file->name;
        $attachment->size = $this->file->size;
        $attachment->is_delete = 0;
        $attachment->url = $this->url;
        $attachment->thumb_url = $this->thumbUrl;
        $attachment->attachment_group_id = $this->attachment_group_id;
        $attachment->type = $this->type;
        if (!$attachment->save()) {
            throw new \Exception($this->getErrorMsg($attachment));
        }
        return $attachment;
    }

    private function saveToLocal()
    {
        $baseWebPath = \Yii::$app->basePath . '/web';
        $baseWebUrl = \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl;
        $saveFile = $baseWebPath . $this->saveFileFolder . '/' . $this->saveFileName;
        $saveThumbFile = $baseWebPath . $this->saveThumbFolder . '/' . $this->saveFileName;
        if (!is_dir($baseWebPath . $this->saveFileFolder)) {
            if (!make_dir($baseWebPath . $this->saveFileFolder)) {
                throw new \Exception('上传失败，无法创建文件夹`'
                    . $baseWebPath
                    . $this->saveFileFolder
                    . '`，请检查目录写入权限。');
            }
        }
        if (!is_dir($baseWebPath . $this->saveThumbFolder)) {
            if (!make_dir($baseWebPath . $this->saveThumbFolder)) {
                throw new \Exception('上传失败，无法创建文件夹`'
                    . $baseWebPath
                    . $this->saveThumbFolder
                    . '`，请检查目录写入权限。');
            }
        }
        if (!$this->file->saveAs($saveFile)) {
            if (!copy($this->file->tempName, $saveFile)) {
                throw new \Exception('文件保存失败，请检查目录写入权限。');
            }
        }
        $this->url = $baseWebUrl . $this->saveFileFolder . '/' . $this->saveFileName;
        try {
            $editor = Grafika::createEditor(get_supported_image_lib());
            /** @var ImageInterface $image */
            $editor->open($image, $saveFile);
            $editor->resizeFit($image, 200, 200);
            $editor->save($image, $saveThumbFile);
            $this->thumbUrl = $baseWebUrl . $this->saveThumbFolder . '/' . $this->saveFileName;
        } catch (\Exception $e) {
            $this->thumbUrl = '';
        }
    }

    public function saveToAliOss()
    {
        $config = \Yii::$app->serializer->decode($this->storage->config);
        $isCName = !empty($config['is_cname']) && $config['is_cname'] == 1;
        $client = new OssClient($config['access_key'], $config['secret_key'], $config['domain'], $isCName);

        $object = trim($this->saveFileFolder . '/' . $this->saveFileName, '/');
        $client->uploadFile($config['bucket'], $object, $this->file->tempName);
        if (!$isCName) {
            $endpointNameStart = mb_stripos($config['domain'], '://') + 3;
            $urlPrefix = mb_substr($config['domain'], 0, $endpointNameStart)
                . $config['bucket']
                . '.'
                . mb_substr($config['domain'], $endpointNameStart);
        } else {
            $urlPrefix = $config['domain'];
        }
        $this->url = $urlPrefix . $this->saveFileFolder . '/' . $this->saveFileName;
        if (in_array($this->file->extension, $this->imageExt) && isset($config['style_api']) && $config['style_api']) {
            $this->url = $this->url . $config['style_api'];
        }
        $this->thumbUrl = $this->url;
    }

    public function saveToTxCos()
    {
        $config = \Yii::$app->serializer->decode($this->storage->config);
        $client = new Client([
            'region' => $config['region'],
            'credentials' => [
                'secretId' => $config['secret_id'],
                'secretKey' => $config['secret_key'],
            ],
        ]);

        $key = trim($this->saveFileFolder . '/' . $this->saveFileName, '/');
        /** @var \GuzzleHttp\Command\Result $result */
        $result = $client->putObject([
            'Bucket' => $config['bucket'],
            'Key' => $key,
            'Body' => fopen($this->file->tempName, 'rb'),
        ]);
        if (!empty($config['domain'])) {
            $this->url = trim($config['domain'], ' /') . '/' . $key;
        } else {
            $this->url = 'https://' . $result->offsetGet('Location');
        }
        $this->thumbUrl = $this->url;
    }

    public function saveToQiniu()
    {
        $config = \Yii::$app->serializer->decode($this->storage->config);
        $uploadManager = new UploadManager();
        $auth = new Auth($config['access_key'], $config['secret_key']);
        $token = $auth->uploadToken($config['bucket']);

        $key = trim($this->saveFileFolder . '/' . $this->saveFileName, '/');
        list($result, $error) = $uploadManager->putFile(
            $token,
            $key,
            $this->file->tempName
        );
        $this->url = $config['domain'] . '/' . $result['key'];
        if (in_array($this->file->extension, $this->imageExt) && isset($config['style_api']) && $config['style_api']) {
            $this->url = $this->url . $config['style_api'];
        }
        $this->thumbUrl = $this->url;
    }

    public function saveToTos()
    {
        $config = \Yii::$app->serializer->decode($this->storage->config);
        $client = new TosClient([
            'region' => $config['region'],
            'ak' => $config['access_key'],
            'sk' => $config['secret_key'],
            'endpoint' => $config['endpoint'],
            'enableVerifySSL' => \Yii::$app->request->isSecureConnection,
        ]);
        $key = trim($this->saveFileFolder . '/' . $this->saveFileName, '/');
        $input = new PutObjectInput($config['bucket'], $key, fopen($this->file->tempName, 'rb'));
        $client->putObject($input);
        $this->url = "https://{$config['bucket']}.{$config['endpoint']}/{$key}";
        $this->thumbUrl = $this->url;
    }

    public static function getInstanceFromFile($localFilePath)
    {
        if (!is_string($localFilePath)) {
            throw new \Exception('文件名称不是字符串。');
        }
        if (!file_exists($localFilePath)) {
            throw new \Exception('文件`' . $localFilePath . '`不存在。');
        }
        $localFilePath = str_replace('\\', '/', $localFilePath);
        $pathInfo = pathinfo($localFilePath);
        $name = $pathInfo['basename'];
        $size = filesize($localFilePath);
        $type = mimetype_from_filename($localFilePath);
        return new UploadedFile([
            'name' => $name,
            'type' => $type,
            'tempName' => $localFilePath,
            'error' => 0,
            'size' => $size,
        ]);
    }
}
