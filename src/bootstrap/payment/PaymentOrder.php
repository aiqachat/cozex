<?php
/**
 * @copyright ©2018 深圳网商天下科技
 * author: wstianxia
 * @link https://www.netbcloud.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/12/11 11:26
 */


namespace app\bootstrap\payment;


use yii\base\Model;

class PaymentOrder extends Model
{
    public $orderNo;
    public $amount;
    public $title;
    public $notifyClass;
    /** @var integer 支付方式：1=微信支付 2=余额支付 3=积分支付 4=stripe支付 */
    public $pay_type;

    public function rules()
    {
        return [
            [['orderNo', 'amount', 'title', 'notifyClass'], 'required',],
            [['pay_type'], 'integer'],
            [['orderNo'], 'string', 'max' => 32],
            [['title'], 'string', 'max' => 128],
            [['notifyClass'], 'string', 'max' => 512],
            [['amount'], function ($attribute, $params) {
                if (!is_float($this->amount) && !is_int($this->amount) && !is_double($this->amount)) {
                    $this->addError($attribute, '`amount`必须是数字类型。');
                }
            }],
            [['amount'], 'number', 'min' => 0, 'max' => 100000000],
        ];
    }

    /**
     * PaymentOrder constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        if (!$this->validate()) {
            dd($this->errors);
        }
    }
}
