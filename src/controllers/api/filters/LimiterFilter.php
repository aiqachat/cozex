<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/12/5 17:01
 */

namespace app\controllers\api\filters;

use app\bootstrap\response\ApiCode;
use app\forms\RateLimiter;
use yii\base\ActionFilter;

class LimiterFilter extends ActionFilter
{
    public $ignore;
    public $only;

    public $num = 1; // 1秒请求1次

    /**
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        $id = $action->id;
        if (is_array($this->ignore) && in_array($id, $this->ignore)) {
            return true;
        }
        if (is_array($this->only) && !in_array($id, $this->only)) {
            return true;
        }
        $limiter = new RateLimiter();
        $key = "api_{$id}_interview_" . \Yii::$app->mall->id;
        if ($limiter->tooManyAttempts($key, $this->num)){
            \Yii::$app->response->data = [
                'code' => ApiCode::CODE_ERROR,
                'msg' => "操作频繁，请稍后重试",
            ];
            return false;
        }
        $limiter->hit($key);
        return true;
    }
}
