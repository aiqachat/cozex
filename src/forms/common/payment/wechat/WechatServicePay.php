<?php

namespace app\forms\common\payment\wechat;

class WechatServicePay extends WechatPay
{
    public $sub_appid;
    public $sub_mch_id;

    /**
     * @param $api
     * @param $args
     * @return array
     */
    public function v2Send($api, $args)
    {
        if(empty($args['sub_mch_id'])){
            $args['sub_mch_id'] = $this->sub_mch_id;
        }
        if(empty($args['sub_appid']) && $this->sub_appid){
            $args['sub_appid'] = $this->sub_appid;
        }
        return parent::v2Send($api, $args); // TODO: Change the autogenerated stub
    }

    /**
     * @param $api
     * @param $args
     * @return array
     */
    protected function v2SendWithPem($api, $args)
    {
        if(empty($args['sub_mch_id'])){
            $args['sub_mch_id'] = $this->sub_mch_id;
        }
        if(empty($args['sub_appid']) && $this->sub_appid){
            $args['sub_appid'] = $this->sub_appid;
        }
        return parent::v2SendWithPem($api, $args); // TODO: Change the autogenerated stub
    }
}
