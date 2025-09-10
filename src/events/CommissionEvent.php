<?php

namespace app\events;

use app\models\User;
use yii\base\Event;

class CommissionEvent extends Event
{
    const EVENT_COMMISSION = 'commission';

    /** @var User $user */
    public $user;
    public $order_money;
}
