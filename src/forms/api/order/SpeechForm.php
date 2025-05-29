<?php
/**
 * Created by PhpStorm
 * User: chenzs
 * @copyright: ©2020 深圳网商天下科技
 * @link: https://www.netbcloud.com
 */

namespace app\forms\api\order;

use app\bootstrap\payment\PaymentOrder;
use app\bootstrap\response\ApiCode;
use app\forms\mall\setting\PriceForm;
use app\forms\mall\setting\UserConfigForm;
use app\models\Model;
use app\models\SpeechOrders;
use app\models\VolcengineAccount;
use yii\helpers\Json;

class SpeechForm extends Model
{
    public $num;
    public $id;
    public $pay_type;
    public $time;
    public $SpeakerID;
    public $is_renew;
    public $account_id;

    public function rules()
    {
        return [
            [['num', 'pay_type', 'time'], 'required', 'on' => ['buy']],
            [['SpeakerID', 'pay_type', 'time'], 'required', 'on' => ['renew']],
            [['num', 'id', 'time', 'is_renew', 'account_id'], 'integer'],
            [['pay_type', 'SpeakerID'], 'string'],
            [['num'], 'default', 'value' => 1],
        ];
    }

    public function submit()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $t = \Yii::$app->db->beginTransaction();
        try {
            if($this->is_renew){
                $account = VolcengineAccount::findOne([
                    'mall_id' => \Yii::$app->mall->id,
                    'id' => $this->account_id,
                ]);
                $order_no = generate_order_no('RESP');
            }else{
                $account = VolcengineAccount::findOne([
                    'mall_id' => \Yii::$app->mall->id,
                    'is_default' => 1,
                    'is_delete' => 0
                ]);
                $order_no = generate_order_no('SP');
            }
            if (!$account || !$account->key) {
                throw new \Exception('暂时无法使用语音复刻，请联系管理员');
            }
            $unitPrice = (new PriceForm())->config()[$this->is_renew ? 'renewal_unit_price' : 'unit_price'];
            if ($unitPrice <= 0) {
                throw new \Exception('未设置语音复刻价格，请联系管理员');
            }

            $orderData = [
                'goods_name' => '声音复刻大模型-声音复刻',
                'num' => $this->num,
                'time' => $this->time,
                'is_renew' => $this->is_renew ?: 0
            ];
            if($orderData['is_renew']){
                $orderData['speaker_id'] = $this->SpeakerID;
            }
            $order = new SpeechOrders();
            $order->mall_id = \Yii::$app->mall->id;
            $order->account_id = $account->id;
            $order->order_no = $order_no;
            $order->user_id = \Yii::$app->user->id;
            $order->order_data = Json::encode($orderData);
            $order->unit_price = $unitPrice;
            $order->total_pay_price = $unitPrice * $this->num;
//            $order->total_pay_price = 0.01;
            if (!$order->save()) {
                throw new \Exception($this->getErrorMsg($order));
            }

            $amount = floatval($order->total_pay_price);
            if($this->pay_type == \Yii::$app->payment::PAY_TYPE_INTEGRAL) {
                $integral = (new UserConfigForm(['tab' => UserConfigForm::TAB_INTEGRAL]))->config()['integral_rate'];
                $amount = $amount * $integral;
            }
            $payOrder = new PaymentOrder([
                'title' => ($this->is_renew ? '续费' : '购买') . '-声音复刻',
                'amount' => floatval($amount),
                'orderNo' => $order->order_no,
                'notifyClass' => SpeechPayNotify::class,
            ]);
            $id = \Yii::$app->payment->createOrder($payOrder);
            $t->commit();
            $res = \Yii::$app->payment->getPayData($id, $this->pay_type);
            $result = array_merge($res, ["order_id" => $order->id, 'no' => $order->order_no]);
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '订单创建成功',
                'data' => $result
            ];

        } catch (\Exception $e) {
            $t->rollBack ();
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $e->getMessage(),
                'error' => [
                    'line' => $e->getLine()
                ]
            ];
        }
    }

    public function result()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $order = SpeechOrders::findOne($this->id);
        if (!$order) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '订单不存在'
            ];
        }
        $data = Json::decode($order->order_data);
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'retry' => $order->is_pay ? 0 : 1,
                'is_refund' => $order->is_refund,
                'refund_msg' => $data['response']['refund_msg'] ?? '',
            ],
        ];
    }
}
