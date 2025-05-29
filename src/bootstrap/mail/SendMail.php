<?php
/**
 * Created by PhpStorm.
 * User: wstianxia
 */

namespace app\bootstrap\mail;

use app\jobs\MailJob;
use app\models\MailSetting;
use app\models\Mall;
use yii\base\Component;
use yii\swiftmailer\Mailer;

/**
 * @property Mall $mall
 * @property MailSetting $mailSetting
 */
class SendMail extends Component
{
    public $mall;
    public $mailSetting;

    /**
     * @param $view
     * @param $params
     * @return bool
     * 邮件发送配置
     */
    protected function send($view, $params)
    {
        \Yii::$app->queue->delay(0)->push(new MailJob([
            'class' => $this,
            'view' => $view,
            'params' => $params
        ]));
    }

    public function job($view, $params)
    {
        $mailSetting = $this->mailSetting;
        $messages = [];
        $receiveMail = !empty($params['email']) ? (array)$params['email'] : [];
        /* @var Mailer $mailer */
        $mailer = \Yii::$app->mailer;
        $mailer->setTransport([
            'class' => 'Swift_SmtpTransport',
            'host' => $mailSetting->send_platform ?: 'smtp.qq.com',
            'username' => $mailSetting->send_mail,
            'password' => $mailSetting->send_pwd,
            'port' => '465',
            'encryption' => 'ssl',//    tls | ssl
        ]);
        foreach ($receiveMail as $mail) {
            $compose = $mailer->compose($view, $params);
            $compose->setFrom($mailSetting->send_mail); //要发送给那个人的邮箱
            $compose->setTo($mail); //要发送给那个人的邮箱
            $compose->setSubject($mailSetting->send_name); //邮件主题
            $messages[] = $compose;
        }
        return $mailer->sendMultiple($messages);
    }

    public function test($email)
    {
        return $this->job('test', ['email' => $email]);
    }

    public function getMailSetting()
    {
        if(!$this->mailSetting) {
            $this->mailSetting = MailSetting::findOne ([
                'mall_id' => $this->mall->id,
                'is_delete' => 0,
            ]);
            if (!$this->mailSetting) {
                throw new \Exception('商城未设置邮件发送');
            }
        }
        return $this->mailSetting;
    }

    public function codedMsg($email, $code)
    {
        try {
            $data = ['email' => $email];
            if(is_array($code)){
                $data = array_merge($data, $code);
            }else{
                $data['code'] = $code;
            }
            $this->getMailSetting();
            $this->send('code', $data);
            return true;
        } catch (\Exception $exception) {
            \Yii::error('--发送邮件：--' . $exception->getMessage());
            throw new \Exception($exception->getMessage());
        }
    }
}
