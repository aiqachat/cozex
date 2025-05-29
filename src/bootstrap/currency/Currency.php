<?php
/**
 * Created by PhpStorm.
 * User: wstianxia
 * Date: 2019/1/30
 * Time: 16:28
 * @copyright: ©2021 深圳网商天下科技
 * @link: https://www.netbcloud.com/
 */

namespace app\bootstrap\currency;

use app\models\User;
use yii\base\Component;
use yii\db\Exception;

/**
 * @property BalanceModel $balance;
 * @property IntegralModel $integral;
 * @property User $user;
 */
class Currency extends Component
{
    private $integral;
    private $balance;
    private $user;


    /**
     * @return BalanceModel
     * @throws Exception
     */
    public function getBalance()
    {
        $form = new BalanceModel();
        $form->user = $this->getUser();
        $form->mall = \Yii::$app->mall;
        return $form;
    }

    /**
     * @return IntegralModel
     * @throws Exception
     */
    public function getIntegral()
    {
        $form = new IntegralModel();
        $form->user = $this->getUser();
        $form->mall = \Yii::$app->mall;
        return $form;
    }

    /**
     * @param $user
     * @return $this
     * @throws Exception
     */
    public function setUser($user)
    {
        if ($user instanceof User) {
            $this->user = $user;
        } else {
            throw new Exception('用户不存在');
        }
        return $this;
    }

    /**
     * @return User
     * @throws Exception
     */
    public function getUser()
    {
        if ($this->user instanceof User) {
            return $this->user;
        } else {
            throw new Exception('用户不存在');
        }
    }
}
