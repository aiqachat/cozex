<?php

namespace app\handlers;

use app\events\CommissionEvent;
use app\events\UserEvent;
use app\forms\mall\setting\UserConfigForm;
use app\models\User;

class MyHandler extends HandlerBase
{
    const EVENT_COMMISSION = 'commission';

    /**
     * 事件处理注册
     */
    public function register()
    {
        \Yii::$app->on(User::EVENT_REGISTER, function ($event) {
            \Yii::warning('注册事件');
            try {
                /* @var UserEvent $event */
                // 新用户送积分
                $userConfigForm = new UserConfigForm();
                $userConfigForm->tab = UserConfigForm::TAB_INTEGRAL;
                $data = $userConfigForm->config();
                if($data['give_integral']){
                    \Yii::$app->currency->setUser(User::findOne($event->user->id))->integral
                        ->add((float)$data['give_integral'], "新用户注册送积分");
                }
            } catch (\Exception $exception) {
                \Yii::error($exception);
            }
        });

        \Yii::$app->on(CommissionEvent::EVENT_COMMISSION, function ($event) {
            // todo 事件相应处理
            try {
                \Yii::warning('发放佣金事件');
                /* @var CommissionEvent $event */
                if(!$event->order_money || !$event->user->userInfo->parent){
                    return;
                }
                $parent = $event->user->userInfo->parent;
                $ratio = $parent->identity->level->promotion_commission_ratio;
                if(!$ratio){
                    return;
                }
                $price = price_format($event->order_money * $ratio / 100, PRICE_FORMAT_FLOAT);
                if(!$price){
                    return;
                }
                $desc = '推广消费佣金返现：' . $price . '元';
                \Yii::$app->currency->setUser($parent)->balance->add(
                    $price,
                    $desc,
                    \Yii::$app->serializer->encode(['money' => $event->order_money])
                );
                $parent->userInfo->award_money += $price;
                $parent->userInfo->save();
            } catch (\Exception $exception) {
                \Yii::error($exception);
            }
        });
    }
}
