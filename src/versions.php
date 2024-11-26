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
  `content` longtext DEFAULT '' COMMENT '内容',
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
];
