<?php

namespace app\forms\admin;

use app\bootstrap\response\ApiCode;
use app\models\Model;

class DataForm extends Model
{
    public function search()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $res = [
            [
                'name' => '操作系统',
                'value' => PHP_OS_FAMILY,
            ],
            [
                'name' => 'IP地址',
                'value' => gethostbyname($_SERVER['SERVER_NAME']),
            ],
            [
                'name' => 'PHP版本',
                'value' => PHP_VERSION,
            ],
            [
                'name' => '数据库版本',
                'value' => \Yii::$app->db->createCommand("SELECT VERSION() as version")->queryOne()['version'],
            ],
            [
                'name' => '服务器上传限制',
                'value' => ini_get('upload_max_filesize'),
            ],
        ];

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'info' => $res,
            ]
        ];
    }
}