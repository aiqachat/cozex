<?php

namespace app\forms\api\user;

use app\models\UserPlatform;
use yii\base\Component;

class LoginUserInfo extends Component
{
    public $nickname;
    public $username;
    public $avatar;
    public $email;
    public $mobile;
    public $password = '';

    /** @var UserPlatform */
    public $userPlatform;
}
