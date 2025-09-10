<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/12/5 15:00
 */

namespace app\forms\api\user\login;

use app\forms\api\user\LoginForm;
use app\forms\api\user\LoginUserInfo;
use app\forms\common\CommonUser;
use app\models\UserPlatform;

class PassportForm extends LoginForm
{
    public $username;
    public $password;
    public $type;

   private $attemptsKey;
   private $lockKey;

    public function rules()
    {
        return [
            [['password', 'username', 'type'], 'required'],
        ];
    }

    protected function getUserInfo()
    {
        // TODO: Implement getUserInfo() method.
        if (!$this->validate()) {
            throw new \Exception($this->getErrorMsg());
        }

        $userPlatform = CommonUser::userAccount($this->type, $this->username);
        if (!$userPlatform || !$userPlatform->user) {
            throw new \Exception(\Yii::t('common', '账号未注册'));
        }

        $this->attemptsKey = 'login_attempts_' . \Yii::$app->mall->id . '_' . $this->type . '_' . $this->username;
        $this->lockKey = 'login_lock_' . \Yii::$app->mall->id . '_' . $this->type . '_' . $this->username;
        // 检查账户是否被锁定
        $this->checkAccountLocked();
        
        if (!\Yii::$app->getSecurity()->validatePassword($this->password, $userPlatform->password)) {
            // 记录密码错误次数
            $this->recordFailedAttempt();
            throw new \Exception(\Yii::t('common', '密码错误'));
        }
        
        // 密码正确，清除错误记录
        $this->clearFailedAttempts();

        $userInfo = new LoginUserInfo();
        $userInfo->userPlatform = $userPlatform;
        $userInfo->username = $this->username;
        $userInfo->nickname = $userPlatform->user->nickname ?? '昵称';
        return $userInfo;
    }
    
    /**
     * 检查账户是否被锁定
     * @throws \Exception 如果账户被锁定，则抛出异常
     */
    private function checkAccountLocked()
    {
        $cache = \Yii::$app->cache;
        $lockTime = $cache->get($this->lockKey);
        
        if ($lockTime !== false) {
            $remainingSeconds = $lockTime - time();
            if ($remainingSeconds > 0) {
                $remainingMinutes = ceil($remainingSeconds / 60);
                throw new \Exception(sprintf(\Yii::t("common", '账户已锁定'), $remainingMinutes));
            }
        }
    }
    
    /**
     * 记录密码错误次数
     */
    private function recordFailedAttempt()
    {
        $cache = \Yii::$app->cache;

        $attempts = $cache->get($this->attemptsKey);
        if ($attempts === false) {
            $attempts = 1;
        } else {
            $attempts++;
        }
        
        // 保存错误次数
        $cache->set($this->attemptsKey, $attempts, 86400); // 24小时内有效
        
        // 如果错误次数达到5次，锁定账户30分钟
        if ($attempts == 5) {
            $lockTime = time() + 1800; // 30分钟锁定
            $cache->set($this->lockKey, $lockTime, 86400); // 24小时内有效
        }
        // 如果错误次数超过5次，每次增加1小时
        else if ($attempts > 5) {
            $lockTime = $cache->get($this->lockKey);
            if ($lockTime !== false) {
                $lockTime += 3600; // 增加1小时
                $cache->set($this->lockKey, $lockTime, 86400 * 7); // 锁定时间可能很长，设置缓存7天有效期
            }
        }
    }
    
    /**
     * 清除错误记录
     */
    private function clearFailedAttempts()
    {
        $cache = \Yii::$app->cache;
        
        $cache->delete($this->attemptsKey);
        $cache->delete($this->lockKey);
    }
}
