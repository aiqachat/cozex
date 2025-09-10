<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2024 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\common\oauth;

use app\models\Model;
use GuzzleHttp\Client;

abstract class BasicForm extends Model
{
    public $invite;

    /**
     * @var string 回调地址
     */
    protected $redirect_uri;

    abstract public function createAuthUrl();

    protected function getClient(): Client
    {
        return new Client(['verify' => \Yii::$app->request->isSecureConnection]);
    }
}
