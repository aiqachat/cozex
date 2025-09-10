<?php
/**
 * Created by PhpStorm.
 * User: wstianxia
 */

namespace app\bootstrap\mail;

use app\forms\mall\setting\ConfigForm;
use app\forms\mall\setting\UserConfigForm;
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
        $params['language'] = \Yii::$app->language;
        \Yii::$app->queue->delay(0)->push(new MailJob([
            'class' => $this,
            'view' => $view,
            'params' => $params
        ]));
    }

    public function job($view, $params)
    {
        \Yii::$app->language = $params['language'] ?? 'zh'; // 设置多语言，默认中文
        $this->mailSetting->switchData();
        $config = (new ConfigForm())->config();
        $userConfig = (new UserConfigForm(['tab' => UserConfigForm::TAB_SETTING]))->config();
        $params = array_merge($params, [
            'logo' => $config['mall_logo_pic'],
            'desc' => $this->mailSetting->desc,
            'title' => $userConfig['title_' . \Yii::$app->language] ?? $userConfig['title'],
        ]);
        $messages = [];
        $receiveMail = !empty($params['email']) ? (array)$params['email'] : [];
        try {
            $subject_name = $this->mailSetting->subject_name;
            if($view == 'code'){
                $subject_name .= ($subject_name ? ' - ' : '') . sprintf(\Yii::t('common', 'code标题'), $params['code']);
            }elseif($view == 'test'){
                $subject_name .= ($subject_name ? ' - ' : '') . "这是一条测试邮件信息";
            }
            /* @var Mailer $mailer */
            $mailer = \Yii::$app->mailer;
            $mailer->setTransport([
                'class' => 'Swift_SmtpTransport',
                'host' => $this->mailSetting->send_platform ?: 'smtp.qq.com',
                'username' => $this->mailSetting->send_mail,
                'password' => $this->mailSetting->send_pwd,
                'port' => '465',
                'encryption' => 'ssl',//    tls | ssl
            ]);
            foreach ($receiveMail as $mail) {
                $compose = $mailer->compose($view, $params);
                $compose->setFrom([$this->mailSetting->send_mail => $this->mailSetting->send_name ?: $this->mailSetting->send_mail]);
                $compose->setTo($mail); //要发送给那个人的邮箱
                $compose->setSubject($subject_name); //邮件主题
                $messages[] = $compose;
            }
            return $mailer->sendMultiple($messages);
        }catch (\Exception $e){
            \Yii::error($e);
            return null;
        }
    }

    public function test($email)
    {
        return $this->job('test', ['email' => $email]);
    }

    public function getMailSetting()
    {
        if(!$this->mailSetting) {
            $this->mailSetting = MailSetting::findOne([
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
                if(!isset($code['code'])){
                    throw new \Exception('缺少code验证码');
                }
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
