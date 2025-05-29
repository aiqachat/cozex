<?php

namespace app\forms\admin\user;

use app\models\AdminInfo;
use app\models\Mall;
use yii\base\Component;
use yii\queue\JobInterface;

class UserUpdateJob extends Component implements JobInterface
{
    public $user_id;

    public function execute($queue)
    {
        try {
            \Yii::warning('账号更新Job开始执行');
            /** @var AdminInfo $adminInfo */
            $adminInfo = AdminInfo::find()->where(['user_id' => $this->user_id, 'is_delete' => 0])->one();
            if (!$adminInfo) {
                throw new \Exception('管理员账号不存在');
            }

            if ($adminInfo->expired_at == '0000-00-00 00:00:00') {
                throw new \Exception('账号有效期为永久');
            }

            $expiredAt = strtotime($adminInfo->expired_at) - time();
            if ($expiredAt < 0) {
                $res = Mall::updateAll([
                    'expired_at' => date('Y-m-d H:i:s')
                ], [
                    'user_id' => $adminInfo->user_id,
                    'is_delete' => 0
                ]);
            }

            \Yii::warning('账号更新Job执行完成');

        } catch (\Exception $e) {
            \Yii::error($e->getMessage());
        }
    }
}
