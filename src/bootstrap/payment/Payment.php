<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/11/16 16:27
 */


namespace app\bootstrap\payment;

use app\forms\common\payment\refund\BaseRefund;
use app\models\PaymentOrderUnion;
use app\models\PaymentRefund;
use app\models\User;
use yii\base\Component;

class Payment extends Component
{
    const PAY_TYPE_BALANCE = 'balance';
    const PAY_TYPE_WECHAT = 'wechat';
    const PAY_TYPE_INTEGRAL = 'integral';
    const PAY_TYPE_STRIPE = 'stripe'; // 全球支付

    /**
     * @param PaymentOrder|PaymentOrder[] $paymentOrders 支付订单数据，支持单个或多个订单
     * @return int
     * @throws PaymentException
     * @throws \yii\db\Exception
     */
    public function createOrder($paymentOrders, $user = null)
    {
        $user = $user ?: $this->getUser();

        if (!is_array($paymentOrders)) {
            if ($paymentOrders instanceof PaymentOrder) {
                $paymentOrders = [$paymentOrders];
            } else {
                throw new PaymentException('`$paymentOrders`不是有效的PaymentOrder对象。');
            }
        }
        if (!count($paymentOrders)) {
            throw new PaymentException("`$paymentOrders`不能为空。");
        }
        $orderNos = [];
        $amount = 0;
        $title = '';
        foreach ($paymentOrders as $paymentOrder) {
            $orderNos[] = $paymentOrder->orderNo;
            $amount = $amount + $paymentOrder->amount;
            $title = $title . str_replace(';', '', filter_emoji($paymentOrder->title)) . ';';
        }
        sort($orderNos);
        $orderNos[] = $amount;
        $unionOrderNo = 'HM' . mb_substr(md5(json_encode($orderNos) . time()), 2);
        $title = mb_substr($title, 0, 32);
        $paymentOrderUnion = new PaymentOrderUnion();
        $paymentOrderUnion->mall_id = \Yii::$app->mall->id;
        $paymentOrderUnion->user_id = $user->id;
        $paymentOrderUnion->order_no = $unionOrderNo;
        $paymentOrderUnion->amount = $amount;
        $paymentOrderUnion->title = $title;
        $t = \Yii::$app->db->beginTransaction();
        try {
            if (!$paymentOrderUnion->save()) {
                throw new PaymentException();
            }
            foreach ($paymentOrders as $paymentOrder) {
                $model = new \app\models\PaymentOrder();
                $model->payment_order_union_id = $paymentOrderUnion->id;
                $model->order_no = $paymentOrder->orderNo;
                $model->amount = $paymentOrder->amount;
                $model->title = $paymentOrder->title;
                $model->notify_class = $paymentOrder->notifyClass;
                if (!$model->save()) {
                    throw new PaymentException();
                }
            }
            $t->commit();
        } catch (\Exception $e) {
            $t->rollBack();
            throw $e;
        }
        return $paymentOrderUnion->id;
    }

    // @czs
    public function getCheckPayData($id)
    {
        $paymentOrderUnion = PaymentOrderUnion::findOne(['id' => $id]);
        if (!$paymentOrderUnion) {
            throw new PaymentException('待支付订单不存在。');
        }
        if ($paymentOrderUnion->is_pay) {
            throw new PaymentException('支付订单已支付。');
        }

        foreach ($paymentOrderUnion->paymentOrder as $paymentOrder) {
            if ($paymentOrder->is_pay == 1) {
                throw new PaymentException('订单已支付');
            }
        }
        return $paymentOrderUnion;
    }

    public function getPayData($id, $payType)
    {
        $paymentOrderUnion = $this->getCheckPayData($id); // @czs

        /** @var User $user */
        $user = $this->getUser();
        switch ($payType) {
            case static::PAY_TYPE_BALANCE:
                $data = [
                    'balance_amount' => price_format(\Yii::$app->currency->setUser($user)->balance->select()),
                    'order_amount' => $paymentOrderUnion->amount,
                ];
                break;
            case static::PAY_TYPE_INTEGRAL:
                $data = [
                    'integral' => price_format(\Yii::$app->currency->setUser($user)->integral->select()),
                    'order_amount' => $paymentOrderUnion->amount,
                ];
                break;
            case static::PAY_TYPE_WECHAT:
                $wechatPay = \Yii::$app->wechatPay;
                $data = $wechatPay->unifiedOrder([
                    'body' => $paymentOrderUnion->title,
                    'out_trade_no' => $paymentOrderUnion->order_no,
                    'total_fee' => $paymentOrderUnion->amount * 100,
                    'notify_url' => $this->getNotifyUrl($wechatPay::NOTIFY_URL),
                    'trade_type' => $wechatPay::TRADE_TYPE_NATIVE,
                ]);
                if (empty($data['code_url'])) {
                    throw new \Exception("获取支付扫码失败");
                }
                $data['url'] = \Yii::$app->request->hostInfo . \Yii::$app->request->scriptUrl . '?r=site/qr-code&url=' . urlencode($data['code_url']);
                unset($data['code_url']);
                break;
            case static::PAY_TYPE_STRIPE:
                try {
                    $stripePay = \Yii::$app->stripePay;
                    $product = $stripePay->getProduct();
                    $product->name = $paymentOrderUnion->title;
                    $product->description = $paymentOrderUnion->title;
                    $product->images = [];
                    $data = $product->toArray();
                    unset($data['id']);
                    $product = $product->update($product->id, $data, $stripePay->attributes);
                    $price = $stripePay->getPrice($product, $paymentOrderUnion->amount * 100);
                    $checkout = $stripePay->checkout($paymentOrderUnion->id, $price);
                    $data = ['url' => $checkout->url];
                } catch (\Exception $e) {
                    if(\Yii::$app->language == 'zh') {
                        if (strpos ($e->getMessage (), 'must convert to at least 400 cents') !== false) {
                            throw new PaymentException('Stripe支付金额必须大于等于4美元');
                        }
                    }
                    throw new PaymentException($e->getMessage());
                }
                break;
            default:
                throw new PaymentException('未知的支付方式。');
        }
        return array_merge([
            'pay_type' => $payType,
            'id' => $paymentOrderUnion->id,
        ], $data ?? []);
    }

    /**
     * @param $id
     * @return bool
     * @throws PaymentException
     * @throws \yii\db\Exception
     */
    public function payBuyBalance($id)
    {
        if ($this->getIsGuest()) {
            throw new PaymentException('用户未登录。');
        }
        $user = $this->getUser();
        $paymentOrderUnion = $this->getCheckPayData($id); // @czs

        $t = \Yii::$app->db->beginTransaction();
        try {
            $paymentOrders = $paymentOrderUnion->paymentOrder;
            $totalAmount = 0;
            foreach ($paymentOrders as $paymentOrder) {
                $totalAmount += (float) $paymentOrder->amount;
            }
            $balanceAmount = \Yii::$app->currency->setUser($user)->balance->select();
            if ($balanceAmount < $totalAmount) {
                throw new PaymentException('账户余额不足。');
            }
            $paymentOrderUnion->is_pay = 1;
            $paymentOrderUnion->pay_type = 2;
            if (!$paymentOrderUnion->save()) {
                throw new \Exception($paymentOrderUnion->getFirstErrors());
            }

            foreach ($paymentOrders as $paymentOrder) {
                $paymentOrder->is_pay = 1;
                $paymentOrder->pay_type = 2;
                if (!$paymentOrder->save()) {
                    throw new \Exception($paymentOrder->getFirstErrors());
                }
                $NotifyClass = $paymentOrder->notify_class;
                /** @var PaymentNotify $notifyObject */
                $notifyObject = new $NotifyClass();
                $po = new PaymentOrder([
                    'orderNo' => $paymentOrder->order_no,
                    'amount' => (float) $paymentOrder->amount,
                    'title' => $paymentOrder->title,
                    'notifyClass' => $paymentOrder->notify_class,
                ]);
                if ($po->amount > 0) {
                    $customDesc = \Yii::$app->serializer->encode(['order_no' => $paymentOrder->order_no]);
                    if (
                        !\Yii::$app->currency->setUser($user)->balance
                            ->sub($po->amount, '账户余额支付：' . $po->amount . '元', $customDesc, $paymentOrder->order_no)
                    ) {
                        throw new PaymentException('余额操作失败。');
                    }
                }
                try {
                    $notifyObject->notify($po);
                } catch (\Exception $exception) {
                    \Yii::error($exception->getMessage());
                }
            }
            $t->commit();
        } catch (\Exception $e) {
            $t->rollBack();
            throw $e;
        }
        return true;
    }

    public function payBuyIntegral($id)
    {
        if ($this->getIsGuest()) {
            throw new PaymentException('用户未登录。');
        }
        $user = $this->getUser();
        $paymentOrderUnion = $this->getCheckPayData($id); // @czs

        $t = \Yii::$app->db->beginTransaction();
        try {
            $paymentOrders = $paymentOrderUnion->paymentOrder;
            $totalAmount = 0;
            foreach ($paymentOrders as $paymentOrder) {
                $totalAmount += (float) $paymentOrder->amount;
            }
            $integralAmount = \Yii::$app->currency->setUser($user)->integral->select();
            if ($integralAmount < $totalAmount) {
                throw new PaymentException('账户积分不足。');
            }
            $paymentOrderUnion->is_pay = 1;
            $paymentOrderUnion->pay_type = 3;
            if (!$paymentOrderUnion->save()) {
                throw new \Exception($paymentOrderUnion->getFirstErrors());
            }

            foreach ($paymentOrders as $paymentOrder) {
                $paymentOrder->is_pay = 1;
                $paymentOrder->pay_type = 3;
                if (!$paymentOrder->save()) {
                    throw new \Exception($paymentOrder->getFirstErrors());
                }
                $NotifyClass = $paymentOrder->notify_class;
                /** @var PaymentNotify $notifyObject */
                $notifyObject = new $NotifyClass();
                $po = new PaymentOrder([
                    'orderNo' => $paymentOrder->order_no,
                    'amount' => (float) $paymentOrder->amount,
                    'title' => $paymentOrder->title,
                    'notifyClass' => $paymentOrder->notify_class,
                ]);
                if ($po->amount > 0) {
                    $customDesc = \Yii::$app->serializer->encode(['order_no' => $paymentOrder->order_no]);
                    if (
                        !\Yii::$app->currency->setUser($user)->integral
                            ->sub($po->amount, '账户积分支付：' . $po->amount, $customDesc, $paymentOrder->order_no)
                    ) {
                        throw new PaymentException('积分操作失败。');
                    }
                }
                try {
                    $notifyObject->notify($po);
                } catch (\Exception $exception) {
                    \Yii::error($exception->getMessage());
                }
            }
            $t->commit();
        } catch (\Exception $e) {
            $t->rollBack();
            throw $e;
        }
        return true;
    }

    /**
     * @param string $orderNo 订单号
     * @param double $price 退款金额
     * @param PaymentOrder $paymentOrder
     * @return bool
     * @throws PaymentException
     * @throws \yii\db\Exception
     */
    public function refund($orderNo, $price, $paymentOrder = null)
    {
        if (!$paymentOrder) {
            $paymentOrder = \app\models\PaymentOrder::findOne([
                'order_no' => $orderNo,
                'is_pay' => 1
            ]);
        }
        if (!$paymentOrder) {
            throw new PaymentException('无效的订单号');
        }

        if (price_format($paymentOrder->amount - $paymentOrder->refund) < price_format($price)) {
            throw new PaymentException('退款金额大于可退款金额');
        }

        $paymentOrderUnion = $paymentOrder->paymentOrderUnion;
        $newOrderNo = generate_order_no('HM');
        $order_sn = 'HM' . substr(md5($paymentOrder->order_no), 6) . substr($newOrderNo, -4);

        $t = \Yii::$app->db->beginTransaction();
        try {
            $paymentRefund = new PaymentRefund();
            $paymentRefund->mall_id = $paymentOrderUnion->mall_id;
            $paymentRefund->user_id = $paymentOrderUnion->user_id;
            $paymentRefund->amount = $price;
            $paymentRefund->order_no = $order_sn;
            $paymentRefund->is_pay = 0;
            $paymentRefund->pay_type = 0;
            $paymentRefund->title = "订单退款:{$orderNo}";
            $paymentRefund->created_at = mysql_timestamp();
            $paymentRefund->out_trade_no = $paymentOrderUnion->order_no;

            $class = $this->refundClass($paymentOrderUnion->pay_type);
            if ($class->refund($paymentRefund, $paymentOrderUnion)) {
                $paymentOrder->refund += $price;
                if (!$paymentOrder->save()) {
                    throw new PaymentException();
                }
                $t->commit();
                return true;
            } else {
                throw new PaymentException();
            }
        } catch (PaymentException $e) {
            $t->rollBack();
            throw $e;
        }
    }

    /**
     * @param $payType
     * @return BaseRefund
     * @throws PaymentException
     */
    private function refundClass($payType)
    {
        switch ($payType) {
            case 1:
                $class = 'app\\forms\\common\\payment\\refund\\WxRefund';
                break;
            case 2:
                $class = 'app\\forms\\common\\payment\\refund\\BalanceRefund';
                break;
            case 3:
                $class = 'app\\forms\\common\\payment\\refund\\IntegralRefund';
                break;
            case 4:
                $class = 'app\\forms\\common\\payment\\refund\\StripeRefund';
                break;
            default:
                throw new PaymentException('无效的支付方式');
        }

        if (!class_exists($class)) {
            throw new PaymentException('未实现功能');
        }

        return new $class();
    }

    private function getNotifyUrl($file)
    {
        $url = \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl . '/pay-notify/' . $file;
        return str_replace('http://', 'https://', $url);
    }

    public function getUser()
    {
        return User::findOne(\Yii::$app->user->id);
    }

    public function getIsGuest()
    {
        return \Yii::$app->user->isGuest;
    }
}
