/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50730
Source Host           : localhost:3306
Source Database       : temp1

Target Server Type    : MYSQL
Target Server Version : 50730
File Encoding         : 65001

Date: 2022-10-08 14:45:58
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wstx_admin_info
-- ----------------------------
DROP TABLE IF EXISTS `wstx_admin_info`;
CREATE TABLE `wstx_admin_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `app_max_count` int(11) NOT NULL DEFAULT '-1' COMMENT '创建小程序最大数量-1.无限制',
  `permissions` text NOT NULL COMMENT '账户权限',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `expired_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '账户过期时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否使用默认权限',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `is_delete` (`is_delete`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for wstx_attachment
-- ----------------------------
DROP TABLE IF EXISTS `wstx_attachment`;
CREATE TABLE `wstx_attachment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `storage_id` int(11) NOT NULL,
  `attachment_group_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `size` int(11) NOT NULL COMMENT '大小：字节',
  `url` varchar(2080) NOT NULL,
  `thumb_url` varchar(2080) NOT NULL DEFAULT '',
  `type` tinyint(2) NOT NULL COMMENT '类型：1=图片，2=视频',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL,
  `deleted_at` timestamp NOT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `is_recycle` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否加入回收站 0.否|1.是',
  PRIMARY KEY (`id`),
  KEY `attachment_group_id` (`attachment_group_id`),
  KEY `type` (`type`),
  KEY `is_delete` (`is_delete`),
  KEY `is_recycle` (`is_recycle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='附件、文件';

-- ----------------------------
-- Records of wstx_attachment
-- ----------------------------

-- ----------------------------
-- Table structure for wstx_attachment_group
-- ----------------------------
DROP TABLE IF EXISTS `wstx_attachment_group`;
CREATE TABLE `wstx_attachment_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `is_delete` smallint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_recycle` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否加入回收站 0.否|1.是',
  `type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0 图片 1商品',
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of wstx_attachment_group
-- ----------------------------

-- ----------------------------
-- Table structure for wstx_attachment_storage
-- ----------------------------
DROP TABLE IF EXISTS `wstx_attachment_storage`;
CREATE TABLE `wstx_attachment_storage` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '存储类型：1=本地，2=阿里云，3=腾讯云，4=七牛',
  `config` longtext NOT NULL COMMENT '存储配置',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：0=未启用，1=已启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '1' COMMENT '存储设置所属账号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='附件存储器';

-- ----------------------------
-- Records of wstx_attachment_storage
-- ----------------------------

-- ----------------------------
-- Table structure for wstx_core_action_log
-- ----------------------------
DROP TABLE IF EXISTS `wstx_core_action_log`;
CREATE TABLE `wstx_core_action_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '操作人ID',
  `model` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '模型名称',
  `model_id` int(11) NOT NULL COMMENT '模模型ID',
  `before_update` longtext COLLATE utf8mb4_german2_ci,
  `after_update` longtext COLLATE utf8mb4_german2_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `remark` varchar(255) COLLATE utf8mb4_german2_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;

-- ----------------------------
-- Records of wstx_core_action_log
-- ----------------------------

-- ----------------------------
-- Table structure for wstx_core_exception_log
-- ----------------------------
DROP TABLE IF EXISTS `wstx_core_exception_log`;
CREATE TABLE `wstx_core_exception_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `level` tinyint(4) NOT NULL DEFAULT '1' COMMENT '异常等级1.报错|2.警告|3.记录信息',
  `title` mediumtext NOT NULL COMMENT '异常标题',
  `content` mediumtext NOT NULL COMMENT '异常内容',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `is_delete` (`is_delete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of wstx_core_exception_log
-- ----------------------------

-- ----------------------------
-- Table structure for wstx_core_queue
-- ----------------------------
DROP TABLE IF EXISTS `wstx_core_queue`;
CREATE TABLE `wstx_core_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `channel` varchar(64) NOT NULL,
  `job` blob NOT NULL,
  `pushed_at` int(11) NOT NULL,
  `ttr` int(11) NOT NULL,
  `delay` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) unsigned NOT NULL DEFAULT '1024',
  `reserved_at` int(11) DEFAULT NULL,
  `attempt` int(11) DEFAULT NULL,
  `done_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `channel` (`channel`),
  KEY `reserved_at` (`reserved_at`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of wstx_core_queue
-- ----------------------------

-- ----------------------------
-- Table structure for wstx_core_session
-- ----------------------------
DROP TABLE IF EXISTS `wstx_core_session`;
CREATE TABLE `wstx_core_session` (
  `id` char(40) NOT NULL,
  `expire` int(11) DEFAULT NULL,
  `DATA` blob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of wstx_core_session
-- ----------------------------

-- ----------------------------
-- Table structure for wstx_option
-- ----------------------------
DROP TABLE IF EXISTS `wstx_option`;
CREATE TABLE `wstx_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group` varchar(100) NOT NULL DEFAULT '',
  `name` varchar(150) NOT NULL,
  `value` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `group` (`group`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for wstx_user
-- ----------------------------
DROP TABLE IF EXISTS `wstx_user`;
CREATE TABLE `wstx_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `password` varchar(128) NOT NULL,
  `nickname` varchar(100) NOT NULL DEFAULT '',
  `auth_key` varchar(128) NOT NULL,
  `access_token` varchar(128) NOT NULL,
  `mobile` varchar(255) NOT NULL DEFAULT '',
  `unionid` varchar(64) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `access_token` (`access_token`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for wstx_user_identity
-- ----------------------------
DROP TABLE IF EXISTS `wstx_user_identity`;
CREATE TABLE `wstx_user_identity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户身份表',
  `user_id` int(11) NOT NULL,
  `is_super_admin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为超级管理员',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为管理员',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `is_super_admin` (`is_super_admin`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for wstx_user_info
-- ----------------------------
DROP TABLE IF EXISTS `wstx_user_info`;
CREATE TABLE `wstx_user_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `platform_user_id` varchar(255) NOT NULL DEFAULT '' COMMENT '用户所属平台的用户id',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `total_balance` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '总余额',
  `is_blacklist` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否黑名单',
  `contact_way` varchar(255) NOT NULL DEFAULT '' COMMENT '联系方式',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `remark_name` varchar(60) NOT NULL DEFAULT '' COMMENT '备注名',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `platform_user_id` (`platform_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of wstx_user_info
-- ----------------------------

DROP TABLE IF EXISTS `wstx_knowledge`;
CREATE TABLE `wstx_knowledge` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `dataset_id` varchar(100) NOT NULL DEFAULT '' COMMENT '知识库ID',
  `name` varchar(100) DEFAULT '',
  `desc` varchar(255) DEFAULT '' COMMENT '描述',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dataset_id` (`dataset_id`),
  KEY `is_delete` (`is_delete`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='扣子知识库';
alter table wstx_knowledge add `format_type` tinyint(1) DEFAULT null COMMENT '0：文档类型；1：表格类型；2：照片类型';
alter table wstx_knowledge add `size` bigint(10) DEFAULT 0 COMMENT '文件的大小，单位为字节';


DROP TABLE IF EXISTS `wstx_coze_account`;
CREATE TABLE `wstx_coze_account` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT '',
  `remark` varchar(150) DEFAULT '',
  `coze_secret` varchar(150) NOT NULL DEFAULT '' COMMENT '访问令牌',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `is_delete` (`is_delete`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='coze授权账号';

alter table wstx_knowledge add `account_id` int(10) DEFAULT 0 COMMENT '授权账号';
alter table wstx_knowledge add `space_id` varchar(32) DEFAULT 0 COMMENT '所属空间';
alter table wstx_knowledge add index (`account_id`);


DROP TABLE IF EXISTS `wstx_av_data`;
CREATE TABLE `wstx_av_data` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `file` varchar(255) NOT NULL DEFAULT '' COMMENT '音视频文件',
    `text` text DEFAULT null COMMENT '字幕文本',
    `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:转字幕；2：字幕打轴',
    `job_id` varchar(255) NOT NULL DEFAULT '' COMMENT '任务id',
    `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:处理中；2：处理完成；3：失败',
    `err_msg` varchar(255) DEFAULT '' COMMENT '失败原因',
    `result` varchar(255) DEFAULT '' COMMENT '最终结果',
    `is_delete` tinyint(1) NOT NULL DEFAULT '0',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL,
    PRIMARY KEY (`id`),
    KEY `type` (`type`),
    KEY `status` (`status`),
    KEY `is_delete` (`is_delete`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='音视频处理数据';

DROP TABLE IF EXISTS `wstx_bot_conf`;
CREATE TABLE `wstx_bot_conf` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `bot_id` varchar(50) NOT NULL DEFAULT '' COMMENT '智能体ID',
    `version` varchar(50) NOT NULL DEFAULT '' COMMENT '版本号',
    `title` varchar(250) DEFAULT '' COMMENT '智能体名字',
    `icon` varchar(250) DEFAULT '' COMMENT '智能体的显示图标',
    `lang` varchar(50) DEFAULT '' COMMENT '智能体的系统语言',
    `layout` varchar(50) DEFAULT '' COMMENT '智能体窗口的布局风格',
    `is_width` tinyint(1) DEFAULT 1 COMMENT '1: 默认，2：自定义',
    `width` int(10) DEFAULT 0 COMMENT '智能体窗口的宽度',
    `is_delete` tinyint(1) NOT NULL DEFAULT '0',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL,
    PRIMARY KEY (`id`),
    KEY `bot_id` (`bot_id`),
    KEY `is_delete` (`is_delete`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='coze智能体配置';