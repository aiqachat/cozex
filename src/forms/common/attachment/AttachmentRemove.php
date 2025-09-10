<?php

namespace app\forms\common\attachment;

use app\models\Attachment;
use app\models\AttachmentStorage;
use app\models\Model;
use Qcloud\Cos\Client;
use OSS\OssClient;
use Qiniu\Auth;
use Tos\Model\DeleteObjectInput;
use Tos\TosClient;

class AttachmentRemove extends Model
{
    /** @var Attachment */
    public $attachment;

    public static function getCommon(Attachment $attachment){
        $obj = new self();
        $obj->attachment = $attachment;
        return $obj;
    }

    public function handle()
    {
        $storage = $this->attachment->storage;
        $type = $storage->type ?? 1;
        $config = $storage ? \Yii::$app->serializer->decode($storage->config) : [];
        switch ($type) {
            case AttachmentStorage::STORAGE_TYPE_LOCAL:
                $this->local();
                break;
            case AttachmentStorage::STORAGE_TYPE_ALIOSS:
                $this->aliOss($config);
                break;
            case AttachmentStorage::STORAGE_TYPE_TXCOS:
                $this->txCos($config);
                break;
            case AttachmentStorage::STORAGE_TYPE_QINIU:
                $this->qiniu($config);
                break;
            case AttachmentStorage::STORAGE_TYPE_TOS:
                $this->tos($config);
                break;
            default:
                throw new \Exception('未知的存储位置: type=' . $this->attachment->storage_id);
        }
        $this->attachment->delete();
    }

    private function getUrl(){
        return array_unique([$this->attachment->url, $this->attachment->thumb_url]);
    }

    public function local()
    {
        foreach ($this->getUrl() as $url){
            $path = @parse_url($url)['path'] ?? '';
            $pos = strpos($path, '/web');
            if($pos > 0){
                $path = substr($path, $pos);
            }
            $url = \Yii::$app->basePath . $path;
            file_exists($url) ? @unlink($url) : "";
        }
    }

    public function aliOss($config)
    {
        $isCName = !empty($config['is_cname']) && $config['is_cname'] == 1;
        $client = new OssClient($config['access_key'], $config['secret_key'], $config['domain'], $isCName);
        try {
            foreach ($this->getUrl() as $url) {
                $item = @parse_url($url);
                if (!empty($item['path'])) {
                    $client->deleteObject($config['bucket'], $item['path']);
                }
            }
        }catch (\Exception $e){
            \Yii::warning('aliOss delete');
            \Yii::error($e);
        }
    }

    public function txCos($config)
    {
        try {
            $client = new Client([
                'region' => $config['region'],
                'credentials' => [
                    'secretId' => $config['secret_id'],
                    'secretKey' => $config['secret_key'],
                ],
            ]);
            foreach ($this->getUrl() as $url) {
                $item = @parse_url($url);
                if (!empty($item['path'])) {
                    $item['path'] = trim($item['path'], '/');
                    $client->deleteObject(['Bucket' => $config['bucket'], 'Key' => $item['path']]);
                }
            }
        }catch (\Exception $e){
            \Yii::warning('txCos delete');
            \Yii::error($e);
        }
    }

    public function qiniu($config)
    {
        \Yii::warning('qiniu delete');
        try {
            $auth = new Auth($config['access_key'], $config['secret_key']);
//            $token = $auth->uploadToken($config['bucket']);
            $bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);
            foreach ($this->getUrl() as $url) {
                $item = @parse_url($url);
                if (!empty($item['path'])) {
                    $err = $bucketManager->delete($config['bucket'], $item['path']);
                    \Yii::warning($err);
                }
            }
        }catch (\Exception $e){
            \Yii::error($e);
        }
    }

    public function tos($config)
    {
        \Yii::warning('tos delete');
        try {
            $client = new TosClient([
                'region' => $config['region'],
                'ak' => $config['access_key'],
                'sk' => $config['secret_key'],
                'endpoint' => $config['endpoint'],
                'enableVerifySSL' => \Yii::$app->request->isSecureConnection,
            ]);
            foreach ($this->getUrl() as $url) {
                $item = @parse_url($url);
                if (!empty($item['path'])) {
                    $item['path'] = trim($item['path'], '/');
                    $input = new DeleteObjectInput($config['bucket'], $item['path']);
                    $client->deleteObject($input);
                }
            }
        }catch (\Exception $e){
            \Yii::error($e);
        }
    }
}
