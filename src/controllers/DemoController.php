<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/11/7 16:56
 */

namespace app\controllers;

use app\forms\common\volcengine\api\AucBigModelQuery;
use app\forms\common\volcengine\api\AucBigModelSubmit;
use app\forms\common\volcengine\ApiForm;

class DemoController extends Controller
{
    public function actionIndex($testCode = 200)
    {
        $req = new AucBigModelSubmit();
        $req->url = "https://osc.gitgit.org/003.wav";
        $extension = pathinfo($req->url, PATHINFO_EXTENSION);
        $extension = strtolower($extension);
        $req->format = $extension;

        $res = ApiForm::common (['object' => $req])->request ();

        $req = new AucBigModelQuery();
        $req->id = $res['id'];

        echo "<pre>";print_r(ApiForm::common (['object' => $req])->request ());die();
    }
}
