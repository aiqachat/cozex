<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\admin\user;

use app\bootstrap\response\ApiCode;
use app\models\AdminInfo;
use app\models\Model;

class BatchPermissionForm extends Model
{
    public $formData;

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        try {
            $this->formData = json_decode($this->formData, true);
            $res = AdminInfo::updateAll([
                'permissions' => json_encode($this->formData['basePermission']),
                'secondary_permissions' => json_encode($this->formData['secondaryPermissions'])
            ], [
                'user_id' => $this->formData['chooseList'],
                'is_delete' => 0
            ]);

            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '更新成功',
                'data' => [
                    'num' => $res
                ]
            ];
        } catch (\Exception $exception) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $exception->getMessage(),
                'error' => [
                    'line' => $exception->getLine()
                ]
            ];
        }
    }
}