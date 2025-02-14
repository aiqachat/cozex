<?php
/**
 * @copyright ©2024 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 */

namespace app\controllers;

use app\forms\common\coze\api\BotsList;
use app\forms\common\coze\api\Chat;
use app\forms\common\coze\api\ChatMsgList;
use app\forms\common\coze\api\ChatRetrieve;
use app\forms\common\coze\api\ConversationMsgList;
use app\forms\common\coze\api\CreateConversation;
use app\forms\common\coze\api\CreateMessage;
use app\forms\common\coze\ApiForm;
use app\models\CozeAccount;

class DemoController extends Controller
{
    public function actionIndex($testCode = 200)
    {
        $content = file_get_contents ('https://b.faloo.com/1372135_2.html');
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML ($content);
        $path = new \DOMXPath($dom);
        $res = $path->query ('//p');
        $pC = '';
        foreach ($res as $p) {
            $pC .= $p->textContent;
        }
        $pC = explode ("\n", $pC)[0];

        $obj = new BotsList();
        $obj->space_id= '7437395705830260771';

        $obj = new CreateConversation([
            'messages' => new CreateMessage([
                'role' => 'user',
                'content' => '你好',
            ])
        ]);

//        $obj = new Chat();
//        $obj->bot_id = '7437395816803483711';
//        $obj->conversation_id = "7438449697141899275";
//        $obj->user_id = "chenzs";
//        $obj->additional_messages = new CreateMessage([
//            'role' => 'user',
//            'content' => $pC,
//        ]);
//        $res = ApiForm::common (['object' => $obj, 'account' => CozeAccount::findOne (9)])->request ();
//        echo "<pre>";print_r($res);

        $obj = new ChatRetrieve();
        $obj->conversation_id = '7438449697141899275';
        $obj->chat_id = '7438459075991486504';
//
        $obj = new ConversationMsgList();
        $obj->conversation_id = '7438449697141899275';
        echo "<pre>";print_r(ApiForm::common (['object' => $obj, 'account' => CozeAccount::findOne (9)])->request ());die();
    }
}
