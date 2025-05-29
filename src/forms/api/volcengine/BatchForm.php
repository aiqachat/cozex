<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\api\volcengine;

use app\forms\mall\volcengine\BatchDataForm;
use app\models\VolcengineAccount;

class BatchForm extends BatchDataForm
{
    public function save()
    {
        $this->user_id = \Yii::$app->user->id;
        if(!$this->account_id) {
            $account = VolcengineAccount::findOne (['mall_id' => \Yii::$app->mall->id, 'is_default' => 1, 'is_delete' => 0]);
            $this->account_id = $account->id ?? 0;
        }
        return parent::save ();
    }
}
