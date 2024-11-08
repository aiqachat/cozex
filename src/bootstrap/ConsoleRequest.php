<?php
/**
 * Created by IntelliJ IDEA.
 * author: wstianxia
 * Date: 2019/4/12
 * Time: 18:12
 */

namespace app\bootstrap;


use yii\console\Request;

class ConsoleRequest extends Request
{
    public $enableCsrfCookie;
    public $isSecureConnection = false;

    public function getUserIp()
    {
        return '0.0.0.0';
    }

    public function getCsrfToken()
    {
        return null;
    }
}
