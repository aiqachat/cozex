<?php
/**
 * @copyright ©2024 深圳网商天下科技有限公司
 * author: wstianxia
 * @link: https://www.netbcloud.com
 */

namespace app\forms\mall;

use app\bootstrap\response\ApiCode;
use app\forms\common\CommonOption;
use app\helpers\CurlHelper;
use app\models\Model;
use app\models\Option;
use GuzzleHttp\Client;
use yii\helpers\FileHelper;

class UpdateForm extends Model
{
    public function getIndex()
    {

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
