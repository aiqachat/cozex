<?php
/**
 * Created by PhpStorm
 * User: chenzs
 * @copyright: ©2020 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\forms\mall\index;

use app\bootstrap\mail\SendMail;
use app\bootstrap\response\ApiCode;
use app\models\MailSetting;
use app\models\Model;

class MailForm extends Model
{
    public $send_mail;
    public $send_pwd;
    public $send_name;
    public $subject_name;
    public $send_platform;
    public $desc;
    public $language_data;

    /** @var MailSetting */
    public $model;

    public $test;
    public $receive_mail;

    public function rules()
    {
        return [
            [['test'], 'integer'],
            [['send_mail', 'send_pwd', 'send_name', 'send_platform', 'receive_mail', 'desc'], 'string'],
            [['send_mail', 'send_pwd', 'send_name', 'subject_name'], 'trim'],
            [['language_data'], 'safe'],
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $this->config();
        $this->model->attributes = $this->attributes;
        if ($this->test) {
            return $this->test();
        }
        if ($this->model->isNewRecord) {
            $this->model->is_delete = 0;
            $this->model->mall_id = \Yii::$app->mall->id;
        }
        if(is_array($this->model->language_data)){
            $this->model->language_data = json_encode($this->model->language_data, JSON_UNESCAPED_UNICODE);
        }
        if ($this->model->save()) {
            return [
                'code' => 0,
                'msg' => '保存成功'
            ];
        } else {
            return $this->getErrorResponse($this->model);
        }
    }

    public function get(){
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '成功',
            'data' => [
                'model' => $this->config()
            ]
        ];
    }

    public function config()
    {
        $model = MailSetting::findOne([
            'mall_id' => \Yii::$app->mall->id,
            'is_delete' => 0,
        ]);
        if (!$model) {
            $model = new MailSetting();
            $model->mall_id = \Yii::$app->mall->id;
        }
        if (!$model->send_platform) {
            $model->send_platform = 'smtp.qq.com';
        }
        $model->language_data = $model->language_data ? json_decode($model->language_data, true) : [];
        $this->model = $model;
        return $model;
    }

    public function test()
    {
        try {
            $mailer = new SendMail();
            $mailer->mall = \Yii::$app->mall;
            $mailer->mailSetting = $this->model;
            $res = $mailer->test($this->receive_mail);
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '发送成功',
                'data' => $res
            ];
        } catch (\Exception $exception) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '发送失败，请检查发件人邮箱、授权码及收件人邮箱是否正确',
                'data' => $exception->getMessage ()
            ];
        }
    }
}
