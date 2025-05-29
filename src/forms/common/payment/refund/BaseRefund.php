<?php
/**
 * Created by PhpStorm.
 * User: wstianxia
 * Date: 2019/2/18
 * Time: 11:54
 * @copyright: ©2021 深圳网商天下科技
 * @link: https://www.wegouer.com
 */

namespace app\forms\common\payment\refund;


use app\models\Model;
use app\models\PaymentOrderUnion;
use app\models\PaymentRefund;

abstract class BaseRefund extends Model
{
    /**
     * @param PaymentRefund $paymentRefund
     * @param PaymentOrderUnion $paymentOrderUnion
     * @return mixed
     */
    abstract public function refund($paymentRefund, $paymentOrderUnion);
}
