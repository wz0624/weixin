<?php 
$sql = "
DROP TABLE IF EXISTS `ims_meepo_bbs_adv`;
CREATE TABLE `ims_meepo_bbs_adv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `advname` varchar(50) DEFAULT '',
  `link` varchar(255) NOT NULL DEFAULT '',
  `thumb` varchar(255) DEFAULT '',
  `displayorder` int(11) DEFAULT '0',
  `enabled` int(11) DEFAULT '0',
  `typeid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `indx_uniacid` (`uniacid`),
  KEY `indx_enabled` (`enabled`),
  KEY `indx_displayorder` (`displayorder`),
  KEY `uniacid` (`uniacid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_bbs_api`;
CREATE TABLE `ims_meepo_bbs_api` (
  `id` int(11) NOT NULL,
  `title` varchar(32) NOT NULL DEFAULT '',
  `description` varchar(132) NOT NULL DEFAULT '',
  `file` varchar(132) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_bbs_begging`;
CREATE TABLE `ims_meepo_bbs_begging` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tid` varchar(64) NOT NULL DEFAULT '',
  `fid` int(11) unsigned NOT NULL DEFAULT '0',
  `type` varchar(32) NOT NULL DEFAULT '',
  `time` int(11) unsigned NOT NULL DEFAULT '0',
  `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
  `fopenid` varchar(42) NOT NULL DEFAULT '',
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `fee` float(6,2) unsigned NOT NULL DEFAULT '0.00',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `transid` varchar(32) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `thumb` text NOT NULL,
  `ttid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_bbs_blacklist`;
CREATE TABLE `ims_meepo_bbs_blacklist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `time` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_bbs_credit_goods`;
CREATE TABLE `ims_meepo_bbs_credit_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL,
  `title` varchar(50) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL DEFAULT '0',
  `deadline` datetime NOT NULL,
  `per_user_limit` int(11) NOT NULL DEFAULT '0',
  `cost` int(11) NOT NULL DEFAULT '0',
  `cost_type` int(11) NOT NULL DEFAULT '1' COMMENT '1系统积分 2会员积分 4,8等留作扩展',
  `price` int(11) NOT NULL DEFAULT '100',
  `content` text NOT NULL,
  `createtime` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_bbs_credit_request`;
CREATE TABLE `ims_meepo_bbs_credit_request` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `openid` varchar(50) NOT NULL,
  `realname` varchar(200) NOT NULL,
  `mobile` varchar(200) NOT NULL,
  `residedist` varchar(200) NOT NULL,
  `note` varchar(200) NOT NULL,
  `goods_id` int(10) unsigned NOT NULL,
  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_bbs_ec_chong_log`;
CREATE TABLE `ims_meepo_bbs_ec_chong_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tid` varchar(50) NOT NULL DEFAULT '',
  `type` varchar(32) NOT NULL DEFAULT '',
  `fee` float unsigned NOT NULL DEFAULT '0',
  `time` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
  `num` float unsigned NOT NULL DEFAULT '0',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `transid` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_bbs_home_message`;
CREATE TABLE `ims_meepo_bbs_home_message` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fromopenid` varchar(50) NOT NULL DEFAULT '',
  `toopenid` varchar(50) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `time` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `tid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fromopenid` (`fromopenid`) USING BTREE,
  KEY `toopenid` (`toopenid`) USING BTREE,
  KEY `tid` (`tid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=14111 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_bbs_log`;
CREATE TABLE `ims_meepo_bbs_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `openid` varchar(42) NOT NULL DEFAULT '',
  `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `time` int(11) unsigned NOT NULL DEFAULT '0',
  `log` text NOT NULL,
  `type` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_bbs_msg_template`;
CREATE TABLE `ims_meepo_bbs_msg_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `title` varchar(500) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '模板标题',
  `tpl_id` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '模板id',
  `template` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '模板内容',
  `tags` varchar(1000) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '模板标签',
  `set` text NOT NULL,
  `type` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_bbs_msg_template_data`;
CREATE TABLE `ims_meepo_bbs_msg_template_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(52) NOT NULL DEFAULT '',
  `set` text NOT NULL,
  `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
  `tpl_id` varchar(124) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_bbs_navs`;
CREATE TABLE `ims_meepo_bbs_navs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
  `icon` varchar(132) NOT NULL DEFAULT '',
  `name` varchar(32) NOT NULL DEFAULT '',
  `link` varchar(132) NOT NULL DEFAULT '',
  `time` int(11) unsigned NOT NULL DEFAULT '0',
  `displayorder` int(11) unsigned NOT NULL DEFAULT '0',
  `enabled` tinyint(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_bbs_o2o_user`;
CREATE TABLE `ims_meepo_bbs_o2o_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `shopid` int(11) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL DEFAULT '',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `set` text NOT NULL,
  `time` int(11) unsigned NOT NULL DEFAULT '0',
  `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
  `acid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_bbs_o2o_user_log`;
CREATE TABLE `ims_meepo_bbs_o2o_user_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
  `type` varchar(32) NOT NULL DEFAULT '',
  `time` int(11) unsigned NOT NULL DEFAULT '0',
  `cid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_bbs_reply_ups`;
CREATE TABLE `ims_meepo_bbs_reply_ups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rid` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `caretetime` int(11) unsigned NOT NULL DEFAULT '0',
  `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `rid` (`rid`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE,
  KEY `uniacid` (`uniacid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_bbs_rss`;
CREATE TABLE `ims_meepo_bbs_rss` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(32) NOT NULL DEFAULT '',
  `url` varchar(132) NOT NULL DEFAULT '',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `fid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_bbs_set`;
CREATE TABLE `ims_meepo_bbs_set` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
  `set` text,
  `createtime` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_bbs_share`;
CREATE TABLE `ims_meepo_bbs_share` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
  `set` text NOT NULL,
  `createtime` int(11) unsigned NOT NULL DEFAULT '0',
  `tid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_bbs_task`;
CREATE TABLE `ims_meepo_bbs_task` (
  `taskid` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) unsigned NOT NULL,
  `available` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `note` text NOT NULL,
  `num` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `maxnum` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `image` varchar(150) NOT NULL DEFAULT '',
  `filename` varchar(50) NOT NULL DEFAULT '',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `nexttime` int(10) unsigned NOT NULL DEFAULT '0',
  `nexttype` varchar(20) NOT NULL DEFAULT '',
  `credit` smallint(6) NOT NULL DEFAULT '0',
  `displayorder` smallint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`taskid`),
  KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_bbs_task_user`;
CREATE TABLE `ims_meepo_bbs_task_user` (
  `uid` mediumint(8) unsigned NOT NULL,
  `username` char(15) NOT NULL DEFAULT '',
  `taskid` smallint(6) unsigned NOT NULL DEFAULT '0',
  `credit` smallint(6) NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `isignore` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`,`taskid`),
  KEY `isignore` (`isignore`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_bbs_threadclass`;
CREATE TABLE `ims_meepo_bbs_threadclass` (
  `typeid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fid` int(11) unsigned NOT NULL DEFAULT '0',
  `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `displayorder` int(11) unsigned NOT NULL DEFAULT '0',
  `icon` varchar(255) NOT NULL DEFAULT '',
  `moderators` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `content` text,
  `group` varchar(132) DEFAULT NULL,
  `look_group` varchar(232) DEFAULT NULL,
  `post_group` varchar(232) DEFAULT NULL,
  `isgood` tinyint(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`typeid`),
  KEY `uniacid` (`uniacid`) USING BTREE,
  KEY `fid` (`fid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=288 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_bbs_topic`;
CREATE TABLE `ims_meepo_bbs_topic` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
  `tid` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(132) DEFAULT NULL,
  `content` text,
  `createtime` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE,
  KEY `tid` (`tid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_bbs_topics`;
CREATE TABLE `ims_meepo_bbs_topics` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(320) DEFAULT NULL,
  `tab` varchar(32) DEFAULT NULL,
  `last_reply_at` int(11) unsigned NOT NULL DEFAULT '0',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0',
  `replycredit` int(11) unsigned NOT NULL DEFAULT '0',
  `tags` varchar(150) DEFAULT NULL,
  `ratetimes` int(11) unsigned NOT NULL DEFAULT '0',
  `rate` int(11) unsigned NOT NULL DEFAULT '0',
  `invisible` tinyint(1) DEFAULT NULL,
  `tid` int(11) DEFAULT NULL,
  `fid` int(11) DEFAULT NULL,
  `content` text,
  `rnum` int(11) unsigned NOT NULL DEFAULT '0',
  `lnum` int(11) unsigned NOT NULL DEFAULT '0',
  `thumb` text,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`) USING BTREE,
  KEY `tid` (`tid`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=917 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ims_meepo_bbs_topic_like`;
CREATE TABLE `ims_meepo_bbs_topic_like` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `openid` varchar(40) NOT NULL DEFAULT '',
  `tid` int(11) unsigned NOT NULL DEFAULT '0',
  `time` int(11) unsigned NOT NULL DEFAULT '0',
  `num` int(11) unsigned NOT NULL DEFAULT '0',
  `fid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`) USING BTREE,
  KEY `fid` (`fid`) USING BTREE,
  KEY `openid` (`openid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=496 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS `ims_meepo_bbs_topic_read`;
CREATE TABLE `ims_meepo_bbs_topic_read` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `openid` varchar(40) NOT NULL DEFAULT '',
  `time` int(11) unsigned NOT NULL DEFAULT '0',
  `tid` int(11) unsigned NOT NULL DEFAULT '0',
  `num` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `openid` (`openid`) USING BTREE,
  KEY `tid` (`tid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=17240 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_bbs_topic_replie`;
CREATE TABLE `ims_meepo_bbs_topic_replie` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
  `content` text,
  `create_at` int(11) unsigned NOT NULL DEFAULT '0',
  `tid` int(11) unsigned NOT NULL DEFAULT '0',
  `thumb` text NOT NULL,
  `fid` int(11) unsigned NOT NULL DEFAULT '0',
  `beggingid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE,
  KEY `tid` (`tid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=427 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_bbs_topic_share`;
CREATE TABLE `ims_meepo_bbs_topic_share` (
  `id` int(11) NOT NULL,
  `openid` varchar(40) NOT NULL DEFAULT '',
  `time` int(11) unsigned NOT NULL DEFAULT '0',
  `tid` int(11) unsigned NOT NULL DEFAULT '0',
  `num` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `openid` (`openid`) USING BTREE,
  KEY `tid` (`tid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_meepo_bbs_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '规则id',
  `acid` int(11) unsigned NOT NULL DEFAULT '0', 
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '公众号ID',
  `openid` varchar(50) NOT NULL DEFAULT '' COMMENT '用户openid',
  `avatar` varchar(512) NOT NULL DEFAULT '' COMMENT '微信头像',  
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '微信昵称',
  `realname` varchar(20) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',  
  `qq` varchar(15) NOT NULL DEFAULT '' COMMENT '联系QQ号码',
  `email` varchar(50) NOT NULL DEFAULT '' COMMENT '联系邮箱',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '联系地址',
  `gender` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别',
  `time` int(11) unsigned NOT NULL DEFAULT '0',
  `online` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(32) NOT NULL DEFAULT '',
  `user_image` varchar(200) DEFAULT NULL COMMENT '用户头像',
  `from_user` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `indx_rid` (`rid`),
  KEY `indx_uniacid` (`uniacid`)
) ENGINE = MYISAM DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `ims_meepo_common_adv` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
  `module` varchar(32) NOT NULL DEFAULT '' COMMENT '模块',
  `starttime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `endtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `displayorder` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '顺序',
  `title` varchar(132) NOT NULL DEFAULT '' COMMENT '标题',
  `link` varchar(132) NOT NULL DEFAULT '' COMMENT '链接',
  `image` varchar(132) NOT NULL DEFAULT '' COMMENT '广告图片',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `isfull` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否全屏',
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所属广告位',
  `images` text NOT NULL COMMENT '其他随机图片',
  `set` text NOT NULL COMMENT '其他配置项',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_meepo_common_setting` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(32) NOT NULL DEFAULT '',
  `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
  `set` text NOT NULL,
  `time` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_module`;
CREATE TABLE `ims_meepo_module` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(32) NOT NULL DEFAULT '',
  `set` text NOT NULL,
  `time` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_sub`;
CREATE TABLE `ims_meepo_sub` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `num` int(11) unsigned NOT NULL DEFAULT '0',
  `level` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE,
  KEY `uniacid` (`uniacid`) USING BTREE,
  KEY `level` (`level`) USING BTREE,
  KEY `num` (`num`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=73117 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_meepo_sub_log`;
CREATE TABLE `ims_meepo_sub_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
  `acid` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `fid` int(11) unsigned NOT NULL DEFAULT '0',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) DEFAULT '',
  `level` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE,
  KEY `uniacid` (`uniacid`) USING BTREE,
  KEY `acid` (`acid`) USING BTREE,
  KEY `fid` (`fid`) USING BTREE,
  KEY `level` (`level`) USING BTREE,
  KEY `createtime` (`createtime`) USING BTREE,
  KEY `IDX_LEVEL_FID` (`level`,`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_father` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(20) NOT NULL,
  `time` int(11) NOT NULL,
  `num` int(11) NOT NULL DEFAULT '1',
  `success` tinyint(2) NOT NULL,
  `openid` varchar(30) NOT NULL,
  `father` varchar(30) NOT NULL,
  `father_id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

";
pdo_query($sql);

