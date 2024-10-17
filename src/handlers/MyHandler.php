<?php

namespace app\handlers;

class MyHandler extends HandlerBase
{

    /**
     * 事件处理注册
     */
    public function register()
    {
        \Yii::$app->on(\app\models\User::EVENT_REGISTER, function ($event) {
            // todo 事件相应处理
            try {

            } catch (\Exception $exception) {
                \Yii::error('注册事件');
            }
        });
    }
}
