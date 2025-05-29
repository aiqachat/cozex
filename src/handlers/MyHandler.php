<?php

namespace app\handlers;

use app\events\UserEvent;
use app\forms\mall\setting\UserConfigForm;
use app\models\User;

class MyHandler extends HandlerBase
{

    /**
     * 事件处理注册
     */
    public function register()
    {
        \Yii::$app->on(User::EVENT_REGISTER, function ($event) {
            // todo 事件相应处理
            try {
                /* @var UserEvent $event */
                // 新用户送积分
                $userConfigForm = new UserConfigForm();
                $userConfigForm->tab = UserConfigForm::TAB_INTEGRAL;
                $data = $userConfigForm->config();
                if($data['give_integral']){
                    \Yii::$app->currency->setUser($event->user)->integral
                        ->add((float)$data['give_integral'], "新用户注册送积分");
                }
            } catch (\Exception $exception) {
                \Yii::error('注册事件');
            }
        });
    }
}
