<?php
/**
 * @copyright ©2024 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 */

namespace app\controllers;

//use app\forms\common\coze\ApiForm;

use app\forms\common\volcengine\api\TtsAsyncQuery;
use app\forms\common\volcengine\api\TtsAsyncSubmit;
use app\forms\common\volcengine\api\TtsGenerate;
use app\forms\common\volcengine\ApiForm;

class DemoController extends Controller
{
    public function actionIndex($testCode = 200)
    { // 7428112161492434981
//        $req = new ChatMsgList();
//        $req->conversation_id = "7428112161492434981";
//        $req->chat_id = "7428120645109547043";
//        $req->user_id = "wstianxia";
        $req = new TtsGenerate();
        $req->text = "火山引擎异步长文本合成。sdfs你是地方看老师发了为二五七四法令纹i罚款开发费微软妇女三顿饭就开始发酒疯多久啊圣诞节发掘的就开始发i哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦哦";
        $req->voice_type = "zh_male_M392_conversation_wvae_bigtts";
//        $req = new TtsAsyncQuery(); //
//        $req->task_id = "H-Ms20_5G1vAjv_thbeS4iCzGb5BTIsM";
        $res = ApiForm::common (['object' => $req])->request ();
        file_put_contents (__DIR__."/11.mp3", base64_decode ($res['data']));
        echo "<pre>";print_r($res);echo "<pre>";print_r(base64_decode ($res['data']));die();
    }
}
