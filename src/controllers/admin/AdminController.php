<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/11/7 18:17
 */

namespace app\controllers\admin;

use app\controllers\admin\behaviors\PermissionsBehavior;
use app\controllers\behaviors\LoginFilter;
use app\controllers\Controller;

class AdminController extends Controller
{
    public $layout = 'admin';

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'loginFilter' => [
                'class' => LoginFilter::class,
                'safeRoutes' => [
                    'admin/passport/login',
                    'admin/passport/logout',
                    'admin/passport/register',
                    'admin/passport/sms-captcha',
                    'admin/passport/check-user-exists',
                    'admin/passport/send-reset-password-captcha',
                    'admin/passport/reset-password',
                ],
            ],
            'adminPermissions' => [
                'class' => PermissionsBehavior::class,
                'safeRoute' => [
                    'admin/cache/clean',
                    'admin/passport/login',
                    'admin/index/back-index',
                ],
            ],
        ]);
    }
}
