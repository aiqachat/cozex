<?php
/**
 * Created by PhpStorm
 * User: wstianxia
 */

namespace app\jobs;

use app\bootstrap\mail\SendMail;
use yii\queue\JobInterface;

class MailJob extends BaseJob implements JobInterface
{
    /**
     * @var SendMail $class
     */
    public $class;

    public $view;
    public $params;

    public function execute($queue)
    {
        $this->setRequest();
        $this->class->job($this->view, $this->params);
    }
}
