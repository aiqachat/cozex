<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2019/1/24 9:24
 */

namespace app\events;

use app\models\User;
use yii\base\Event;

class UserEvent extends Event
{
    /** @var User $user */
    public $user;
}
