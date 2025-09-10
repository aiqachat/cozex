<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/12/5 15:00
 */

namespace app\forms\api\user\login;

use app\bootstrap\response\ApiCode;
use app\forms\api\user\LoginForm;
use app\forms\api\user\LoginUserInfo;
use app\forms\common\CommonUser;
use app\forms\common\oauth\BasicForm;
use app\models\Mall;
use app\models\UserPlatform;
use GuzzleHttp\Psr7\Query;
use yii\helpers\Json;

class OauthForm extends LoginForm
{
    public $type;
    public $code;
    public $state;

    public function rules()
    {
        return [
            [['type'], 'required'],
            [['code', 'state', 'type', 'invite'], 'string'],
        ];
    }

    public function getUrl()
    {
        if (!$this->validate()) {
            throw new \Exception($this->getErrorMsg());
        }

        $class = 'app\\forms\\common\\oauth\\' . $this->type . '\\ClientForm';
        if(!class_exists($class)){
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '错误的授权类型',
            ];
        }
        /** @var BasicForm $object */
        $object = new $class();
        $object->invite = $this->invite;

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功',
            'data' => [
                'url' => $object->createAuthUrl(),
            ],
        ];
    }

    private $userInfo;

    public function handleNotify()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        if($this->state){
            $this->state = Json::decode($this->state);
            $this->invite = $this->state['invite'] ?? '';
        }
        try{
            if(!$this->code){
                throw new \Exception('未同意授权用户信息');
            }
            if(!empty($this->state['mall_id'])){
                \Yii::$app->setMall(Mall::findOne($this->state['mall_id']));
            }
            $class = 'app\\forms\\common\\oauth\\' . $this->type . '\\ClientForm';
            if(!class_exists($class)){
                throw new \Exception('错误的授权类型');
            }
            /** @var BasicForm $form */
            $form = new $class();
            $accessToken = $form->fetchAccessTokenWithAuthCode($this->code);
            $form->setAccessToken($accessToken);
            $this->userInfo = $form->getUserInfo();
            $res = $this->login();
            if($res['code'] == ApiCode::CODE_ERROR){
                throw new \Exception($res['msg']);
            }
            $url = CommonUser::userWebUrl() . "&";
            return \Yii::$app->response->redirect($url . Query::build($res['data']));
        }catch (\Exception $e){
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage(),
            ];
        }
    }

    protected function getUserInfo()
    {
        // TODO: Implement getUserInfo() method.
        $userPlatform = CommonUser::userAccount($this->type, $this->userInfo['id']);
        if(!$userPlatform){
            $userPlatform = new UserPlatform();
            $userPlatform->platform_account = $this->userInfo['id'];
            $userPlatform->platform_id = $this->type;
            $userPlatform->mall_id = \Yii::$app->mall->id;
            $userPlatform->user_id = 0;
            $userPlatform->password = \Yii::$app->security
                ->generatePasswordHash(\Yii::$app->security->generateRandomString());
        }

        $userInfo = new LoginUserInfo();
        $userInfo->userPlatform = $userPlatform;
        $userInfo->username = $this->userInfo['id'];
        $userInfo->nickname = $this->userInfo['name'];
        $userInfo->avatar = $this->userInfo['picture'];
        $userInfo->email = $this->userInfo['email'];
        return $userInfo;
    }
}
