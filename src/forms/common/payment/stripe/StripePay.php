<?php

namespace app\forms\common\payment\stripe;

use app\forms\common\CommonUser;
use app\forms\mall\setting\ConfigForm;
use app\models\Model;
use app\models\StripeOrder;
use app\models\StripeProduct;
use Stripe\Exception\ApiErrorException;
use Stripe\Price;
use Stripe\Product;
use Stripe\StripeClient;

class StripePay extends Model
{
    public $api_key;
    public $api_public_key;

    /** @var StripeClient */
    private $stripe;

    public function init()
    {
        parent::init();
        $this->stripe = new StripeClient($this->api_key);
    }

    public function clearData()
    {
        StripeProduct::deleteAll([
            'mall_id' => \Yii::$app->mall->id,
        ]);
    }

    public function allPrice()
    {
        return $this->stripe->prices->all();
    }

    public function allProduct()
    {
        return $this->stripe->products->all();
    }

    public function allRefund()
    {
        return $this->stripe->refunds->all();
    }

    // https://docs.stripe.com/products-prices/manage-prices
    public function getProduct($type = StripeProduct::TYPE_RECHARGE)
    {
        $model = StripeProduct::findOne ([
            'mall_id' => \Yii::$app->mall->id,
            'type' => $type,
            'is_delete' => 0
        ]);
        if(!$model){
            $model = new StripeProduct();
            $model->mall_id = \Yii::$app->mall->id;
            $model->type = $type;
        }
        if(!$model->prod_id){
            $product = Product::create(['name' => '产品'], ['api_key' => $this->api_key]);
            $model->prod_id = $product->id;
            $model->save();
        }
        return new Product($model->prod_id);
    }

    public function getPrice(Product $product, $amount)
    {
        $form = new ConfigForm();
        $data = $form->config();
        $price = new Price();
        $price->product = $product->id;
        $price->billing_scheme = Price::BILLING_SCHEME_PER_UNIT;
        $price->currency = !empty($data['currency']) ? strtolower($data['currency']) : 'cny';
        $price->nickname = "价格";
        $price->unit_amount = $amount;
        return $price->create($price->toArray(), ['api_key' => $this->api_key]);
    }

    public function checkout($payment_order_union_id, Price $price, $num = 1)
    {
        $res = $this->stripe->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => [
                [
                    'price' => $price->id,
                    'quantity' => $num
                ]
            ],
            'success_url' => CommonUser::userWebUrl("paySuccess"),
            'cancel_url' => CommonUser::userWebUrl("paySuccess"),
        ]);
        $order = new StripeOrder();
        $order->mall_id = \Yii::$app->mall->id;
        $order->payment_order_union_id = $payment_order_union_id;
        $order->checkout_id = $res->id;
        $order->amount = $res->amount_total;
        $order->currency = $res->currency;
        $order->payment_status = $res->payment_status;
        if(!$order->save ()){
            throw new \Exception($this->getErrorMsg($order));
        }
        return $res;
    }

    public function refund($payment_intent, $amount)
    {
        try {
            $res = $this->stripe->refunds->create ([
                'payment_intent' => $payment_intent,
                'amount' => $amount,
            ]);
            if ($res->status != 'succeeded') {
                throw new \Exception('退款失败');
            }
            return $res;
        }catch (\Exception $e){
            if($e instanceof ApiErrorException){
                if($e->getStripeCode() === 'charge_already_refunded'){
                    throw new \Exception('订单已退款');
                }
            }
            throw $e;
        }
    }
}
