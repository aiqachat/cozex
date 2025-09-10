<?php
/**
 * link: https://www.netbcloud.com/
 * copyright: Copyright (c) 2018 深圳网商天下科技有限公司
 * author: wstianxia
 */

namespace app\forms\api\order;

use app\bootstrap\payment\PaymentNotify;
use app\events\CommissionEvent;
use app\forms\common\volcengine\RequestForm;
use app\forms\common\volcengine\sdk\BatchListMegaTTSTrainStatus;
use app\forms\common\volcengine\sdk\MegaTtsOrder;
use app\forms\common\volcengine\sdk\RenewMegaTtsOrder;
use app\jobs\CommonJob;
use app\models\SpeechOrders;
use app\models\UserSpeaker;
use yii\helpers\Json;

class SpeechPayNotify extends PaymentNotify
{
    public function notify($paymentOrder)
    {
        $t = \Yii::$app->db->beginTransaction();
        try {
            /* @var SpeechOrders $order */
            $order = SpeechOrders::find()->where(['order_no' => $paymentOrder->orderNo])->one();
            if (!$order) {
                throw new \Exception('订单不存在:' . $paymentOrder->orderNo);
            }
            $account = $order->account;

            $order->is_pay = 1;
            $order->pay_time = mysql_timestamp();
            $order->is_refund = $account && $account->key ? 0 : 1;
            $orderData = Json::decode($order->order_data);

            if($order->is_refund === 0){
                try {
                    if($orderData['is_renew']){
                        $obj = new RenewMegaTtsOrder([
                            'Times' => intval($orderData['time']),
                            'SpeakerIDs' => [$orderData['speaker_id']],
                            'type' => $account->type
                        ]);
                    }else {
                        $obj = new MegaTtsOrder([
                            'AppID' => $account->app_id,
                            'Times' => intval($orderData['time']),
                            'Quantity' => intval($orderData['num']),
                            'type' => $account->type
                        ]);
                    }
                    $form = new RequestForm(['account' => $account->key, 'object' => $obj]);
                    $orderData['response'] = $form->request();
                }catch (\Exception $e){
                    $order->is_refund = 1;
                    $orderData['response'] = ['msg' => $e->getMessage()];
                }
            }

            if($order->is_refund === 1){
                try {
                    \Yii::$app->payment->refund($paymentOrder->orderNo, $order->total_pay_price);
                }catch (\Exception $e){
                    $orderData['response']['refund_msg'] = $e->getMessage();
                }
            }
            $order->order_data = Json::encode($orderData);
            if (!$order->save()) {
                throw new \Exception('充值订单支付状态更新失败');
            }
            $t->commit();

            if($order->is_refund === 0 && empty($orderData['is_renew'])){
                \Yii::$app->queue->delay(1)->push(new CommonJob([
                    'type' => 'handle_speech_pay',
                    'mall' => \Yii::$app->mall,
                    'data' => ['id' => $order->id]
                ]));
                if(in_array($paymentOrder->pay_type, [1, 4])) {
                    \Yii::$app->trigger(CommissionEvent::EVENT_COMMISSION, new CommissionEvent([
                        'user' => $order->user,
                        'order_money' => $order->total_pay_price
                    ]));
                }
            }
        } catch (\Exception $e) {
            $t->rollBack();
            \Yii::error($e);
            throw $e;
        }
    }

    public function handle($id)
    {
        \Yii::warning ('购买声音复刻后的处理：' . $id);
        $order = SpeechOrders::findOne($id);
        if (!$order) {
            return;
        }
        $orderData = Json::decode($order->order_data);
        try {
            $obj = new BatchListMegaTTSTrainStatus(['type' => $order->account->type]);
            $obj->AppID = $order->account->app_id;
            $obj->State = 'Unknown';
            $obj->PageSize = 100;
            $obj->OrderTimeStart = intval(strtotime($order->pay_time) . '000');
            $obj->ExpireTimeEnd = intval((strtotime("+{$orderData['time']} month", strtotime($order->pay_time) + 86400)) . '000');
            $form = new RequestForm(['account' => $order->account->key, 'object' => $obj]);
            $res = $form->request();
            if(!empty($res['Statuses'])){
                $count = 0;
                foreach ($res['Statuses'] as $item){
                    $exists = UserSpeaker::find()->where([
                        'mall_id' => $order->mall_id,
                        'account_id' => $order->account_id,
                        'speaker_id' => $item['SpeakerID'],
                        'is_delete' => 0
                    ])->exists();
                    if($exists){
                        continue;
                    }
                    $model = new UserSpeaker();
                    $model->user_id = $order->user_id;
                    $model->mall_id = $order->mall_id;
                    $model->account_id = $order->account_id;
                    $model->speaker_id = $item['SpeakerID'];
                    if(!$model->save()){
                        throw new \Exception(Json::encode($model->getErrors()));
                    }
                    $count++;
                    if($count >= $orderData['num']){
                        break;
                    }
                }
            }
        }catch (\Exception $e){
            \Yii::error($e);
        }
    }
}
