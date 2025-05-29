<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2019/2/20 17:17
 */


namespace app\controllers\admin;


class AppController extends AdminController
{
    public function actionRecycle()
    {
        return $this->render('recycle');
    }
}
