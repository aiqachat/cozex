<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\admin;

use app\bootstrap\response\ApiCode;
use app\models\Model;

class UploadForm extends Model
{
    public $file;

    public function save()
    {
        try {
            if ($this->file->extension !== 'ico') {
                throw new \Exception('文件格式不正确, 请上传 .ico 格式文件');
            }

            $saveFile = \Yii::$app->basePath . '/' . 'favicon.ico';
            if ($this->file->saveAs($saveFile)) {
                return [
                    'code' => ApiCode::CODE_SUCCESS,
                    'msg' => '上传成功',
                ];
            } else {
                throw new \Exception('文件保存失败，请检查目录写入权限。');
            }
        } catch (\Exception $e) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage(),
            ];
        }
    }
}