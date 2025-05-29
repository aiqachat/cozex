<?php

use app\forms\common\CommonOption;

return [
    '1.0.0' => function () {
        $sql = <<<EOF
EOF;
        sql_execute($sql);
    },
    '1.0.1' => function () {
        $sql = <<<EOF
alter table wstx_av_data add `data` text DEFAULT null COMMENT '数据，包括配置等等';
EOF;
        sql_execute($sql);
    },
    '1.0.2' => function () {
        $sql = <<<EOF
CREATE TABLE `wstx_knowledge_file` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `knowledge_id` int(10) DEFAULT 0 COMMENT '知识库id',
  `document_id` varchar(64) DEFAULT '' COMMENT '文档id',
  `name` varchar(155) DEFAULT '' COMMENT '名称',
  `content` longtext DEFAULT null COMMENT '内容',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `knowledge_id` (`knowledge_id`),
  KEY `document_id` (`document_id`),
  KEY `is_delete` (`is_delete`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

alter table `wstx_knowledge` drop column `size`;
alter table `wstx_knowledge` add `num` int(10) DEFAULT 0 COMMENT '文件数量';

CREATE TABLE `wstx_volcengine_account` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT '',
  `app_id` varchar(20) NOT NULL DEFAULT '' COMMENT 'APP ID',
  `access_token` varchar(100) NOT NULL DEFAULT '' COMMENT 'Access Token',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `is_delete` (`is_delete`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='火山引擎应用账号';

alter table `wstx_av_data` add `account_id` int(10) DEFAULT 0 COMMENT '授权账号';
EOF;
        sql_execute($sql);
        try{
            $setting = CommonOption::get('volcengine_setting', CommonOption::GROUP_APP);
            if($setting && !empty($setting['access_token'])){
                $data = [
                    'name' => '应用',
                    'app_id' => $setting['app_id'],
                    'access_token' => $setting['access_token'],
                    'created_at' => mysql_timestamp (),
                    'updated_at' => mysql_timestamp (),
                ];
                $res = [];
                foreach ($data as $k => $v) {
                    $res[] = "`{$k}`='{$v}'";
                }
                $sql = "insert into wstx_volcengine_account set " . implode (',', $res);
                $res = Yii::$app->db->createCommand($sql)->execute();
                if($res){
                    $sql = "delete from wstx_option where name='volcengine_setting' and `group`='" . CommonOption::GROUP_APP . "'";
                    Yii::$app->db->createCommand($sql)->execute();
                    $sql = "update wstx_av_data set account_id=1";
                    Yii::$app->db->createCommand($sql)->execute();
                }
            }
        }catch (Exception $e){
            Yii::error ($e);
        }
    },
    '1.0.3' => function () {
        $sql = <<<EOF
alter table `wstx_coze_account` add `type` tinyint(1) DEFAULT 1 COMMENT '1：个人令牌；2：OAuth';
alter table `wstx_coze_account` add `client_id` varchar(64) DEFAULT '' COMMENT '客户端id';
alter table `wstx_coze_account` add `client_secret` varchar(64) DEFAULT '' COMMENT '客户端密钥';
alter table `wstx_coze_account` add `expires_in` int(10) DEFAULT null COMMENT '访问令牌过期时间';
alter table `wstx_coze_account` add `refresh_token` varchar(150) DEFAULT '' COMMENT '刷新令牌';
alter table wstx_coze_account modify column `refresh_token` varchar(150) DEFAULT '' COMMENT '刷新令牌';
EOF;
        sql_execute($sql);
    },
    '1.0.5' => function () {
        $sql = <<<EOF
CREATE TABLE `wstx_volcengine_keys` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT '',
  `access_id` varchar(50) NOT NULL DEFAULT '' COMMENT 'Access Key ID',
  `secret_key` varchar(100) NOT NULL DEFAULT '' COMMENT 'Secret Access Key',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `is_delete` (`is_delete`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='火山引擎密钥';
CREATE TABLE `wstx_volcengine_keys_relation` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key_id` int(11) NOT NULL COMMENT '密钥id',
  `account_id` int(11) NOT NULL COMMENT '应用id',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `is_delete` (`is_delete`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='密钥与账号关联表';
EOF;
        sql_execute($sql);
    },
    '1.0.8' => function () {
        $sql = <<<EOF
alter table wstx_attachment add `mall_id` int(11) NOT NULL COMMENT '商城' AFTER `id`;
update wstx_attachment set mall_id=1;
alter table wstx_attachment_group add `mall_id` int(11) NOT NULL COMMENT '商城' AFTER `id`;
update wstx_attachment_group set mall_id=1;
alter table wstx_attachment_storage add `mall_id` int(11) NOT NULL COMMENT '商城' AFTER `id`;
update wstx_attachment_storage set mall_id=1;
alter table wstx_av_data add `mall_id` int(11) NOT NULL COMMENT '商城' AFTER `id`;
update wstx_av_data set mall_id=1;
alter table wstx_bot_conf add `mall_id` int(11) NOT NULL COMMENT '商城' AFTER `id`;
update wstx_bot_conf set mall_id=1;
alter table wstx_core_action_log add `mall_id` int(11) NOT NULL COMMENT '商城' AFTER `id`;
update wstx_core_action_log set mall_id=1;
alter table wstx_core_exception_log add `mall_id` int(11) NOT NULL COMMENT '商城' AFTER `id`;
update wstx_core_exception_log set mall_id=1;
alter table wstx_coze_account add `mall_id` int(11) NOT NULL COMMENT '商城' AFTER `id`;
update wstx_coze_account set mall_id=1;
alter table wstx_knowledge add `mall_id` int(11) NOT NULL COMMENT '商城' AFTER `id`;
update wstx_knowledge set mall_id=1;
alter table wstx_knowledge_file add `mall_id` int(11) NOT NULL COMMENT '商城' AFTER `id`;
update wstx_knowledge_file set mall_id=1;
alter table wstx_option add `mall_id` int(11) NOT NULL COMMENT '商城' AFTER `id`;
update wstx_option set mall_id=1;
alter table wstx_user add `mall_id` int(11) NOT NULL COMMENT '商城' AFTER `id`;
alter table wstx_volcengine_account add `mall_id` int(11) NOT NULL COMMENT '商城' AFTER `id`;
update wstx_volcengine_account set mall_id=1;
alter table wstx_volcengine_keys add `mall_id` int(11) NOT NULL COMMENT '商城' AFTER `id`;
update wstx_volcengine_keys set mall_id=1;
CREATE TABLE `wstx_mall` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `user_id` int(11) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL,
  `deleted_at` timestamp NOT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `is_recycle` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否回收',
  `is_disable` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否禁用',
  `expired_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `is_delete` (`is_delete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商城';
CREATE TABLE `wstx_admin_register` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码',
  `mobile` varchar(255) NOT NULL DEFAULT '' COMMENT '手机号',
  `name` varchar(45) NOT NULL DEFAULT '' COMMENT '姓名/企业名',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '申请原因',
  `wechat_id` varchar(64) NOT NULL DEFAULT '' COMMENT '微信号',
  `id_card_front_pic` varchar(2000) NOT NULL DEFAULT '' COMMENT '身份证正面',
  `id_card_back_pic` varchar(2000) NOT NULL DEFAULT '' COMMENT '身份证反面',
  `business_pic` varchar(2000) NOT NULL DEFAULT '' COMMENT '营业执照',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '审核状态：0=待审核，1=通过，2=不通过',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL,
  `deleted_at` timestamp NOT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `is_delete` (`is_delete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
alter table wstx_admin_info add `secondary_permissions` longtext COMMENT '二级权限';
update wstx_option set name='mall_setting',`group` = 'app' where name = 'ind_setting';
EOF;
        sql_execute($sql);
    },
    '1.0.9' => function () {
        $sql = <<<EOF
alter table wstx_attachment add index (`mall_id`);
alter table wstx_attachment_group add index (`mall_id`);
alter table wstx_attachment_storage add index (`mall_id`);
alter table wstx_av_data add index (`mall_id`);
alter table wstx_bot_conf add index (`mall_id`);
alter table wstx_core_action_log add index (`mall_id`);
alter table wstx_core_exception_log add index (`mall_id`);
alter table wstx_coze_account add index (`mall_id`);
alter table wstx_knowledge add index (`mall_id`);
alter table wstx_knowledge_file add index (`mall_id`);
alter table wstx_option add index (`mall_id`);
alter table wstx_user add index (`mall_id`);
alter table wstx_volcengine_account add index (`mall_id`);
alter table wstx_volcengine_keys add index (`mall_id`);
alter table wstx_admin_register add index (`username`);

EOF;
        sql_execute($sql);
    },
    '1.0.11' => function () {
        $sql = <<<EOF
alter table wstx_admin_info add `mobile` varchar(100) DEFAULT '' COMMENT '手机号';
update wstx_admin_info i LEFT JOIN wstx_user u on u.id = i.user_id set i.mobile = u.mobile;
EOF;
        sql_execute($sql);
        $sql = <<<EOF
alter table `wstx_user` drop column `mobile`;
alter table `wstx_user` drop column `unionid`;
alter table `wstx_user_info` drop column `platform_user_id`;
alter table `wstx_user_info` drop column `contact_way`;
alter table `wstx_user_info` drop column `remark_name`;
alter table wstx_user_info add `mobile` varchar(100) DEFAULT '' COMMENT '联系方式';
alter table wstx_user_info add `email` varchar(100) DEFAULT '' COMMENT '邮箱';
CREATE TABLE `wstx_mail_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mall_id` int(11) NOT NULL DEFAULT '-1',
  `send_platform` varchar(200) DEFAULT 'smtp.qq.com' COMMENT '发送平台',
  `send_mail` longtext CHARACTER SET utf8 NOT NULL COMMENT '发件人邮箱',
  `send_pwd` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '授权码',
  `send_name` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '发件人名称',
  `is_delete` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL,
  `deleted_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mall_id` (`mall_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE `wstx_core_validate_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `target` varchar(255) NOT NULL,
  `code` varchar(128) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL,
  `is_validated` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已验证：0=未验证，1-已验证',
  PRIMARY KEY (`id`),
  KEY `target` (`target`),
  KEY `code` (`code`),
  KEY `created_at` (`created_at`),
  KEY `updated_at` (`updated_at`),
  KEY `is_validated` (`is_validated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='短信、邮箱验证码';
alter table wstx_av_data add `user_id` int(10) DEFAULT 0 COMMENT '用户id';
alter table wstx_user_info add `integral` int(11) NOT NULL DEFAULT '0' COMMENT '积分';
alter table wstx_user_info add `total_integral` int(11) NOT NULL DEFAULT '0' COMMENT '最高积分';
CREATE TABLE `wstx_user_platform` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mall_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `platform_id` varchar(35) NOT NULL DEFAULT '' COMMENT '用户所属平台标识',
  `platform_account` varchar(255) NOT NULL DEFAULT '' COMMENT '用户所属平台的用户账号',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '平台使用的密码',
  PRIMARY KEY (`id`),
  KEY `platform_id` (`platform_id`),
  KEY `mall_id` (`mall_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
alter table wstx_user add `uid` varchar(15) NOT NULL DEFAULT '' COMMENT '用户uid';
CREATE TABLE `wstx_balance_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mall_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL COMMENT '类型：1=收入，2=支出',
  `money` decimal(10,2) NOT NULL COMMENT '变动金额',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '变动说明',
  `custom_desc` longtext NOT NULL COMMENT '自定义详细说明|记录',
  `order_no` varchar(255) NOT NULL DEFAULT '' COMMENT '订单号',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `mall_id` (`mall_id`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE `wstx_integral_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mall_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL COMMENT '类型：1=收入，2=支出',
  `integral` int(11) NOT NULL COMMENT '变动积分',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '变动说明',
  `custom_desc` longtext NOT NULL COMMENT '自定义详细说明|记录',
  `order_no` varchar(255) NOT NULL DEFAULT '' COMMENT '订单号',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `mall_id` (`mall_id`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE `wstx_recharge_orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mall_id` int(11) NOT NULL,
  `order_no` varchar(32) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL,
  `pay_price` decimal(10,2) NOT NULL COMMENT '充值金额',
  `send_price` decimal(10,2) NOT NULL COMMENT '赠送金额',
  `pay_type` tinyint(4) NOT NULL COMMENT '支付方式 1.微信支付',
  `is_pay` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否支付 0--未支付 1--支付',
  `pay_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL,
  `deleted_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mall_id` (`mall_id`),
  KEY `user_id` (`user_id`),
  KEY `is_delete` (`is_delete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE `wstx_payment_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_order_union_id` int(11) NOT NULL,
  `order_no` varchar(32) NOT NULL,
  `amount` decimal(9,2) NOT NULL,
  `is_pay` int(1) NOT NULL DEFAULT '0' COMMENT '支付状态：0=未支付，1=已支付',
  `pay_type` int(1) NOT NULL DEFAULT '0' COMMENT '支付方式：1=微信支付',
  `title` varchar(128) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `notify_class` varchar(512) NOT NULL,
  `refund` decimal(9,2) NOT NULL DEFAULT '0.00' COMMENT '已退款金额',
  PRIMARY KEY (`id`),
  KEY `payment_order_union_id` (`payment_order_union_id`),
  KEY `order_no` (`order_no`),
  KEY `is_pay` (`is_pay`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE `wstx_payment_order_union` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mall_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `order_no` varchar(32) NOT NULL,
  `amount` decimal(9,2) NOT NULL,
  `is_pay` int(1) NOT NULL DEFAULT '0' COMMENT '支付状态：0=未支付，1=已支付',
  `pay_type` int(1) NOT NULL DEFAULT '0' COMMENT '支付方式：1=微信支付',
  `title` varchar(128) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mall_id` (`mall_id`),
  KEY `user_id` (`user_id`),
  KEY `order_no` (`order_no`),
  KEY `is_pay` (`is_pay`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
alter table `wstx_volcengine_account` add `is_default` tinyint(1) DEFAULT 0 COMMENT '默认账户' AFTER `access_token`;
EOF;
        sql_execute($sql);
    },
    '1.0.12' => function () {
        $sql = <<<EOF
CREATE TABLE `wstx_google_oauth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mall_id` int(11) NOT NULL,
  `client_id` varchar(100) NOT NULL DEFAULT '' COMMENT '客户端id',
  `client_secret` varchar(64) NOT NULL DEFAULT '' COMMENT '客户端密钥',
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mall_id` (`mall_id`),
  KEY `is_delete` (`is_delete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE `wstx_speech_orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mall_id` int(11) NOT NULL,
  `order_no` varchar(32) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL,
  `order_data` text DEFAULT NULL COMMENT '订单数据',
  `total_pay_price` decimal(10,2) NOT NULL COMMENT '实际支付总费用',
  `unit_price` decimal(10,2) NOT NULL COMMENT '单价',
  `is_pay` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否支付 0--未支付 1--支付',
  `is_refund` tinyint(1) DEFAULT 0 COMMENT '是否退款',
  `pay_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL,
  `deleted_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mall_id` (`mall_id`),
  KEY `user_id` (`user_id`),
  KEY `order_no` (`order_no`),
  KEY `is_delete` (`is_delete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='声音订单';
CREATE TABLE `wstx_payment_refund` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mall_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_no` varchar(120) NOT NULL DEFAULT '' COMMENT '退款单号',
  `amount` decimal(9,2) NOT NULL DEFAULT '0.00' COMMENT '退款金额',
  `is_pay` int(1) NOT NULL DEFAULT '0' COMMENT '支付状态 0--未支付|1--已支付',
  `pay_type` int(1) NOT NULL DEFAULT '0' COMMENT '支付方式：1=微信支付，2=余额支付',
  `title` varchar(128) NOT NULL DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `out_trade_no` varchar(255) NOT NULL DEFAULT '' COMMENT '支付单号',
  PRIMARY KEY (`id`),
  KEY `mall_id` (`mall_id`),
  KEY `out_trade_no` (`out_trade_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='退款订单';
alter table `wstx_speech_orders` add `account_id` int(10) DEFAULT 0 COMMENT '授权账号' after `order_no`;
CREATE TABLE `wstx_user_speaker` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mall_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `account_id` int(10) DEFAULT 0 COMMENT '授权账号',
  `speaker_id` varchar(100) NOT NULL DEFAULT '' COMMENT '声音id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mall_id` (`mall_id`),
  KEY `user_id` (`user_id`),
  KEY `is_delete` (`is_delete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='用户音色列表';
alter table `wstx_user_speaker` add `name` varchar(100) DEFAULT '' COMMENT '名称' after `speaker_id`;
CREATE TABLE `wstx_integral_exchange` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mall_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '名称',
  `pay_price` decimal(10,2) NOT NULL COMMENT '支付价格',
  `send_integral` int(10) NOT NULL DEFAULT '0' COMMENT '兑换积分',
  `is_delete` smallint(1) NOT NULL DEFAULT '0' COMMENT '删除',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mall_id` (`mall_id`),
  KEY `is_delete` (`is_delete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='积分兑换管理';
CREATE TABLE `wstx_integral_orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mall_id` int(11) NOT NULL,
  `order_no` varchar(32) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL,
  `order_data` text DEFAULT NULL COMMENT '订单数据',
  `total_pay_price` decimal(10,2) NOT NULL COMMENT '实际支付总费用',
  `is_pay` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否支付 0--未支付 1--支付',
  `pay_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL,
  `deleted_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mall_id` (`mall_id`),
  KEY `user_id` (`user_id`),
  KEY `order_no` (`order_no`),
  KEY `is_delete` (`is_delete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='积分订单';
EOF;
        sql_execute($sql);
    },
    '1.0.13' => function () {
        $sql = <<<EOF
update wstx_admin_info set permissions = '["attachment","voice","subtitle","coze"]' where permissions != '[]'               
CREATE TABLE `wstx_stripe_product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mall_id` int(11) NOT NULL,
  `prod_id` varchar(50) NOT NULL DEFAULT '' COMMENT '产品id',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1：充值',
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mall_id` (`mall_id`),
  KEY `type` (`type`),
  KEY `is_delete` (`is_delete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='stripe产品';    
CREATE TABLE `wstx_stripe_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mall_id` int(11) NOT NULL,
  `payment_order_union_id` int(11) NOT NULL,
  `checkout_id` varchar(100) NOT NULL COMMENT '单据id',
  `amount` int(11) NOT NULL COMMENT '金额，单位分',
  `currency` varchar(32) NOT NULL COMMENT '货币',
  `payment_intent` varchar(50) NOT NULL DEFAULT '' COMMENT '支付单号',
  `payment_status` varchar(32) NOT NULL DEFAULT '' COMMENT '支付状态',
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mall_id` (`mall_id`),
  KEY `checkout_id` (`checkout_id`),
  KEY `is_delete` (`is_delete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='stripe订单';
alter table wstx_balance_log modify column `money` decimal(12,4) NOT NULL COMMENT '变动金额';
alter table wstx_payment_order_union modify column `amount` decimal(12,4) NOT NULL;
alter table wstx_payment_order modify column `amount` decimal(12,4) NOT NULL;
alter table wstx_payment_order modify column `refund` decimal(12,4) NOT NULL DEFAULT '0.00' COMMENT '已退款金额';
alter table wstx_payment_refund modify column `amount` decimal(11,4) NOT NULL DEFAULT '0.00' COMMENT '退款金额';
alter table wstx_user_info modify column `integral` decimal(13,4) NOT NULL DEFAULT '0' COMMENT '积分';
alter table wstx_user_info modify column `total_integral` decimal(14,4) NOT NULL DEFAULT '0' COMMENT '最高积分';
alter table wstx_user_info modify column `balance` decimal(12,4) NOT NULL DEFAULT '0.00' COMMENT '余额';
alter table wstx_user_info modify column `total_balance` decimal(14,4) NOT NULL DEFAULT '0.00' COMMENT '总余额';
alter table wstx_integral_log modify column `integral` decimal(12, 4) NOT NULL COMMENT '变动积分';
EOF;
        sql_execute($sql);
    },
    '1.0.14' => function () {
        $sql = <<<EOF
alter table `wstx_bot_conf` add `nickname` varchar(120) DEFAULT '' COMMENT '用户的昵称' after `width`;
alter table `wstx_bot_conf` add `user_avatar` varchar(200) DEFAULT '' COMMENT '用户头像' after `nickname`;
alter table `wstx_bot_conf` add `show_footer` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否展示底部文案' after `user_avatar`;
alter table `wstx_bot_conf` add `footer_text` varchar(150) DEFAULT '' COMMENT '底部显示的文案信息' after `show_footer`;
alter table `wstx_bot_conf` add `footer_link` varchar(500) DEFAULT '' COMMENT '底部文案中的链接文案与链接地址' after `footer_text`;
alter table `wstx_bot_conf` add `is_upload` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否开启聊天框的上传能力' after `footer_link`;
alter table `wstx_integral_exchange` add `language_data` text DEFAULT NULL COMMENT '多语言数据';
EOF;
        sql_execute($sql);
    },
    '1.0.16' => function () {
        $sql = <<<EOF
alter table `wstx_bot_conf` add `account_id` int(11) DEFAULT null COMMENT '账号' after `bot_id`;
alter table `wstx_bot_conf` add `audio_conf` text DEFAULT NULL COMMENT '语音聊天配置' after `is_upload`;
EOF;
        sql_execute($sql);
    },
    '1.0.17' => function () {
        $sql = <<<EOF
alter table wstx_av_data add `is_data_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '数据是否删除';
EOF;
        sql_execute($sql);
    },
    '1.0.18' => function () {
        $sql = <<<EOF
alter table wstx_av_data modify column `text` longtext DEFAULT null COMMENT '字幕文本';
EOF;
        sql_execute($sql);
        try {
            Yii::$app->queue1->delay(5)->push(new \app\jobs\CommonJob(['type' => "del_data_log"]));
        }catch (Exception $e){
            Yii::error($e);
        }
    },
];
