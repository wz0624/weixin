/*
 Navicat Premium Data Transfer

 Source Server         : 13306
 Source Server Type    : MySQL
 Source Server Version : 50542
 Source Host           : localhost
 Source Database       : weizan

 Target Server Type    : MySQL
 Target Server Version : 50542
 File Encoding         : utf-8

 Date: 12/29/2015 10:05:52 AM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `ims_yike_activity_coupon`
-- ----------------------------
DROP TABLE IF EXISTS `ims_yike_activity_coupon`;
CREATE TABLE `ims_yike_activity_coupon` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `start_time` int(10) unsigned NOT NULL,
  `end_time` int(10) unsigned NOT NULL,
  `coupon_ids` varchar(256) NOT NULL DEFAULT '',
  `is_activity` tinyint(3) NOT NULL DEFAULT '0',
  `create_time` int(10) NOT NULL,
  `total` int(10) NOT NULL DEFAULT '0',
  `used` int(10) NOT NULL DEFAULT '0',
  `uniacid` int(11) unsigned NOT NULL,
  `thumb` varchar(256) NOT NULL DEFAULT '',
  `url` varchar(512) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ims_yike_activity_coupon_record`
-- ----------------------------
DROP TABLE IF EXISTS `ims_yike_activity_coupon_record`;
CREATE TABLE `ims_yike_activity_coupon_record` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `create_time` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
