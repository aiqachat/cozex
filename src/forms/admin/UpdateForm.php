<?php
/**
 * @copyright ©2024 深圳网商天下科技有限公司
 * author: wstianxia
 * @link: https://www.netbcloud.com
 */

namespace app\forms\admin;

use app\bootstrap\response\ApiCode;
use app\forms\common\CommonOption;
use app\helpers\CurlHelper;
use app\models\Model;
use GuzzleHttp\Client;
use yii\helpers\FileHelper;

class UpdateForm extends Model
{
    private $projectName = "cozex";
    private $requestUrl = "https://update.netbcloud.com/public/index.php/get/project";
    private $cacheText = 'backUpdate_';

    public function getVersionData()
    {
        $version = app_version();
        $requestParams = ["version_number" => $version, "project_name" => $this->projectName, "domain" => \Yii::$app->request->hostInfo];
        $res = CurlHelper::getInstance ()->httpPost($this->requestUrl, [], $requestParams);
        if(!empty($res['data']['next_version'])){
            $nextVersion = $res['data']['next_version'];
            if($nextVersion['number'] > 1) {
                $pathInfo = pathinfo($nextVersion['path']);
                for ($i = 0; $i < $nextVersion['number']; $i++){
                    $nextVersion['pathList'][] = "{$pathInfo['dirname']}/{$pathInfo['filename']}.{$i}.{$pathInfo['extension']}";
                }
                $nextVersion['step'] = 0;
                $res['data']['next_version'] = $nextVersion;
            }
            \Yii::$app->cache->set($this->cacheText.\Yii::$app->session->getId(), $nextVersion, 7200);
        }
        return $res['data'];
    }

    public function getIndex()
    {
        $requestParams = [
            "version_number" => app_version(),
            "project_name" => $this->projectName,
            "domain" => \Yii::$app->request->hostInfo
        ];
        $res = CurlHelper::getInstance ()->httpPost ($this->requestUrl, [], $requestParams);
        if(!empty($res['data']['next_version'])){
            $nextVersion = $res['data']['next_version'];
            if($nextVersion['number'] > 1) {
                $pathInfo = pathinfo($nextVersion['path']);
                for ($i = 0; $i < $nextVersion['number']; $i++){
                    $nextVersion['pathList'][] = "{$pathInfo['dirname']}/{$pathInfo['filename']}.{$i}.{$pathInfo['extension']}";
                }
                $nextVersion['step'] = 0;
                $res['data']['next_version'] = $nextVersion;
            }
            \Yii::$app->cache->set($this->cacheText.\Yii::$app->session->getId(), $nextVersion, 7200);
        }
        return $res;
    }

    public function doUpdate()
    {
        try{
            $result = $this->update();
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '更新成功。',
                'data' => $result === 2 ? ["reply" => 1] : $result,
            ];
        }catch (\Exception $e){
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage(),
            ];
        }
    }

    private function update()
    {
        $versionData = \Yii::$app->cache->get($this->cacheText.\Yii::$app->session->getId());
        if (empty($versionData)) {
            throw new \Exception('数据异常，请重新获取更新信息');
        }
        cmd_exe("chown -R www:www ".\Yii::$app->basePath);
        cmd_exe("chmod -R 755 ".\Yii::$app->basePath);
        $version = $versionData['version_number'];
        if(!empty($versionData['pathList']) && isset($versionData['step'])){
            $tempFile = \Yii::$app->runtimePath . '/update-package/' . $version . "/src.{$versionData['step']}.zip";
            $this->download($versionData['pathList'][$versionData['step']], $tempFile);
            $versionData['step'] = $versionData['step'] + 1;
            if($versionData['step'] == count($versionData['pathList'])){
                $tempFile = \Yii::$app->runtimePath . '/update-package/' . $version . '/src.zip';
                $fp = fopen($tempFile, 'w+');
                if ($fp === false) {
                    throw new \Exception('无法保存文件，请检查文件写入权限。');
                }
                for ($i = 0; $i < $versionData['number']; $i ++){
                    $block_file = \Yii::$app->runtimePath . '/update-package/' . $version . "/src.{$i}.zip";
                    $handle = fopen($block_file, "rb");
                    fwrite($fp,fread($handle,filesize($block_file)));
                    fclose($handle);
                }
                fclose ($fp);
            }else{
                \Yii::$app->cache->set($this->cacheText.\Yii::$app->session->getId(), $versionData, 7200);
                return 2;
            }
        }else{
            $tempFile = \Yii::$app->runtimePath . '/update-package/' . $version . '/src.zip';
            $this->download($versionData['path'], $tempFile);
        }

        # PHP解压zip文件到目录
        $zippy = new \ZipArchive();
        $res = $zippy->open($tempFile);
        if ($res === TRUE) {
            $zippy->extractTo(\Yii::$app->basePath);
            $zippy->close();
        }else{
            throw new \Exception('解压失败，错误代码：' . $res);
        }

        $currentVersion = CommonOption::get(CommonOption::NAME_VERSION);
        if (!$currentVersion) {
            $currentVersion = '1.0.0';
        }
        $lastVersion = $currentVersion;

        $versions = require \Yii::$app->basePath . '/versions.php';
        foreach ($versions as $v => $f) {
            $lastVersion = $v;
            if (version_compare($v, $currentVersion) > 0) {
                if ($f instanceof \Closure) {
                    try {
                        $f();
                    }catch (\Exception $e){}
                }
            }
        }
        CommonOption::set(CommonOption::NAME_VERSION, $lastVersion);
        FileHelper::removeDirectory(\Yii::$app->runtimePath . '/update-package/' . $version);
        \Yii::$app->cache->delete($this->cacheText.\Yii::$app->session->getId());
        return 1;
    }

    public function download($url, $file)
    {
        if (!is_dir(dirname($file))) {
            if (!make_dir(dirname($file))) {
                throw new \Exception('无法创建目录，请检查文件写入权限。');
            }
        }
        $fp = fopen($file, 'w+');
        if ($fp === false) {
            throw new \Exception('无法保存文件，请检查文件写入权限。');
        }

        $client = new Client(['verify' => \Yii::$app->request->isSecureConnection, 'stream' => true]);
        $response = $client->get($url);
        $body = $response->getBody();
        while (!$body->eof()) {
            fwrite($fp, $body->read(1024));
        }
        fclose($fp);
        return $file;
    }
}
