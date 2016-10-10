<?php

if(!pdo_fieldexists('gandl_wall', 'transfer_min')) {
	pdo_query("ALTER TABLE ".tablename('gandl_wall')." ADD COLUMN `transfer_min` int(11) NULL COMMENT '提现最小金额' AFTER `top_line`;");
}

if(!pdo_fieldexists('gandl_wall', 'lang')) {
	pdo_query("ALTER TABLE ".tablename('gandl_wall')." ADD COLUMN `lang` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '展示语言文字配置' AFTER `css`;");
}

if(!pdo_fieldexists('gandl_wall', 'follow_show')) {
	pdo_query("ALTER TABLE ".tablename('gandl_wall')." ADD COLUMN `follow_show` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '关注引导图' AFTER `fake_money`;");
}

if(!pdo_fieldexists('gandl_wall_piece', 'password')) {
	pdo_query("ALTER TABLE ".tablename('gandl_wall_piece')." ADD COLUMN `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '口令' AFTER `link`;");
}


if(!pdo_fieldexists('gandl_wall', 'piece_model')) {

	pdo_query("ALTER TABLE ".tablename('gandl_wall')." MODIFY COLUMN `city` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '限制城市（多个用,号分隔）' AFTER `over_time`;");

	pdo_query("ALTER TABLE ".tablename('gandl_wall')." ADD COLUMN `piece_model` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '开启的撒钱模型（逗号分隔1:普通，2:口令，3:组团）' AFTER `city`;");

	pdo_query("ALTER TABLE ".tablename('gandl_wall')." ADD COLUMN `group_rule` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '团伙人数的规则，每行一个。规则例：25:2（平均每份25分钱以下时，团伙上限为2人）' AFTER `piece_model`;");	

	pdo_query("ALTER TABLE ".tablename('gandl_wall')." ADD COLUMN `total_min2` int(11) NOT NULL COMMENT '红包总额至少钱数(口令模式)' AFTER `fee`;");	
	
	pdo_query("ALTER TABLE ".tablename('gandl_wall')." ADD COLUMN `total_max2` int(11) NOT NULL COMMENT '红包总额最多钱数(口令模式)' AFTER `total_min2`;");

	pdo_query("ALTER TABLE ".tablename('gandl_wall')." ADD COLUMN `avg_min2` int(11) NOT NULL COMMENT '平均单个红包至少钱数(口令模式)' AFTER `total_max2`;");

	pdo_query("ALTER TABLE ".tablename('gandl_wall')." ADD COLUMN `fee2` float NOT NULL COMMENT '服务费率比例%(口令模式)' AFTER `avg_min2`;");

	pdo_query("ALTER TABLE ".tablename('gandl_wall')." ADD COLUMN `total_min3` int(11) NOT NULL COMMENT '红包总额至少钱数(组团模式)' AFTER `fee2`;");

	pdo_query("ALTER TABLE ".tablename('gandl_wall')." ADD COLUMN `total_max3` int(11) NOT NULL COMMENT '红包总额最多钱数(组团模式)' AFTER `total_min3`;");

	pdo_query("ALTER TABLE ".tablename('gandl_wall')." ADD COLUMN `avg_min3` int(11) NOT NULL COMMENT '平均单个红包至少钱数(组团模式)' AFTER `total_max3`;");

	pdo_query("ALTER TABLE ".tablename('gandl_wall')." ADD COLUMN `fee3` float NOT NULL COMMENT '服务费率比例%(组团模式)' AFTER `avg_min3`;");

	pdo_query("ALTER TABLE ".tablename('gandl_wall')." ADD COLUMN `groupmax` smallint(6) NOT NULL COMMENT '团伙最大人数' AFTER `piece_model`;");

	pdo_query("ALTER TABLE ".tablename('gandl_wall')." ADD COLUMN `avg_max` smallint(6) NOT NULL COMMENT '平均单个红包最大允许为平均额下限的几倍' AFTER `avg_min`;");

	pdo_query('UPDATE '.tablename('gandl_wall') .' SET piece_model="1" ');
}


if(!pdo_fieldexists('gandl_wall_user', 'nickname')) {

	pdo_query("ALTER TABLE ".tablename('gandl_wall_user')." ADD COLUMN `nickname` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `user_id`;");

	pdo_query("ALTER TABLE ".tablename('gandl_wall_user')." ADD COLUMN `avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `nickname`;");

	pdo_query("ALTER TABLE ".tablename('gandl_wall_user')." ADD COLUMN `who` tinyint(1) NULL COMMENT '0:未知 1:普通用户号 2：订阅号 3：认证订阅号 4：服务号 5：认证服务号' AFTER `avatar`;");

	pdo_query("ALTER TABLE ".tablename('gandl_wall_user')." ADD COLUMN `home` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `who`;");	
}


if(!pdo_fieldexists('gandl_wall_piece', 'model')) {

	pdo_query("ALTER TABLE ".tablename('gandl_wall_piece')." ADD COLUMN `model` tinyint(1) NULL COMMENT '模型(1:普通模型，2：团伙模型)' AFTER `user_id`;");

	pdo_query("ALTER TABLE ".tablename('gandl_wall_piece')." ADD COLUMN `password_show` tinyint(1) NULL COMMENT '0:不显示抢钱口令，1：显示抢钱口令' AFTER `password`;");

	pdo_query("ALTER TABLE ".tablename('gandl_wall_piece')." ADD COLUMN `group_size` smallint(6) NULL COMMENT '团伙人数' AFTER `password_show`;");
}


pdo_query("CREATE TABLE IF NOT EXISTS `ims_gandl_wall_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `wall_id` int(11) NOT NULL,
  `piece_id` int(11) NOT NULL,
  `captain_id` int(11) NOT NULL COMMENT '团长在当前圈子中的ID',
  `mine_id` int(11) NOT NULL COMMENT '我在圈子中的ID',
  `user_id` int(11) NOT NULL COMMENT '团队用户ID',
  `nickname` varchar(80) DEFAULT NULL COMMENT '团员昵称',
  `avatar` varchar(255) DEFAULT NULL COMMENT '团员头像',
  `create_time` int(11) NOT NULL COMMENT '加入时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='抢钱团表';");

// 2.2-2.3
if(!pdo_fieldexists('gandl_wall', 'slider')) {
	pdo_query("ALTER TABLE ".tablename('gandl_wall')." MODIFY COLUMN `banner` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '顶部宣传图' AFTER `topic`;");
	pdo_query("ALTER TABLE ".tablename('gandl_wall')." ADD COLUMN `slider` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '幻灯片' AFTER `banner`;");
	pdo_query("ALTER TABLE ".tablename('gandl_wall')." ADD COLUMN `up_rob_fee` float NOT NULL COMMENT '抢钱上级提成' AFTER `transfer_min`;");
	pdo_query("ALTER TABLE ".tablename('gandl_wall')." ADD COLUMN `up_send_fee` float NOT NULL COMMENT '撒钱上级提成' AFTER `up_rob_fee`;");
	pdo_query("ALTER TABLE ".tablename('gandl_wall')." ADD COLUMN `fake_online` int(11) NULL COMMENT '虚假在线人数基数' AFTER `fake_money`;");
	pdo_query("ALTER TABLE ".tablename('gandl_wall')." ADD COLUMN `static` tinyint(1) NOT NULL COMMENT '统计开关0:关闭，1：开启' AFTER `slider`;");

	pdo_query("ALTER TABLE ".tablename('gandl_wall_user')." ADD COLUMN `black` tinyint(1) NOT NULL COMMENT '0:非黑名单,1:黑名单' AFTER `create_time`;");
	pdo_query("ALTER TABLE ".tablename('gandl_wall_user')." ADD COLUMN `black_why` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '被列入黑名单的原因' AFTER `black`;");
	pdo_query("ALTER TABLE ".tablename('gandl_wall_user')." ADD COLUMN `inviter_id` int(11) NOT NULL COMMENT '介绍人ID' AFTER `create_time`;");
	pdo_query("ALTER TABLE ".tablename('gandl_wall_user')." ADD COLUMN `last_active_time` int(11) NOT NULL COMMENT '上次活动时间' AFTER `create_time`;");
}
// 2.2-2.4 更新补丁
if(!pdo_fieldexists('gandl_wall_rob', 'up_money')) {
	pdo_query("ALTER TABLE ".tablename('gandl_wall_rob')."  ADD COLUMN `up_money` int(11) NULL COMMENT '上交的金额' AFTER `money`;"); 
	pdo_query("ALTER TABLE ".tablename('gandl_wall_rob')." 	ADD COLUMN `get_money` int(11) NULL COMMENT '实际获得的金额' AFTER `money`;");

	pdo_query("CREATE TABLE IF NOT EXISTS `ims_gandl_wall_up_rob` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`uniacid` int(11) NOT NULL,
		`wall_id` int(11) NOT NULL,
		`piece_id` int(11) NOT NULL,
		`mine_id` int(11) NOT NULL,
		`user_id` int(11) NOT NULL,
		`up_id` int(11) NOT NULL,
		`up_fee` float NOT NULL COMMENT '上交的比例（%）',
		`up_money` int(11) NOT NULL COMMENT '上交的钱',
		`rob_money` int(11) NOT NULL COMMENT '抢到的钱',
		`create_time` int(11) NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=MyISAM
	CHECKSUM=0
	DELAY_KEY_WRITE=0;");
}


// 2.5-2.6
if(!pdo_fieldexists('gandl_wall_user', 'in_position')) {
	pdo_query("ALTER TABLE ".tablename('gandl_wall_user')." ADD COLUMN `in_position` tinyint(1) NULL COMMENT '0:未定位 1:在范围内 2：不在范围内' AFTER `send_last_time`;");
	pdo_query("ALTER TABLE ".tablename('gandl_wall_user')." ADD COLUMN `last_position` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '上次定位所在地' AFTER `last_city`;");
	pdo_query("ALTER TABLE ".tablename('gandl_wall_user')." ADD COLUMN `last_position_exp` int(11) NOT NULL COMMENT '上次定位过期时间' AFTER `last_position`;");
	pdo_query("ALTER TABLE ".tablename('gandl_wall_user')." ADD COLUMN `follow` tinyint(1) NOT NULL DEFAULT -1 COMMENT '是否正在关注（-1:未知，未：0,1：正关注）。这个量用于优化性能，不使用查询fan表来实现了解用户是否正关注公众号' AFTER `followed`;");
	pdo_query("ALTER TABLE ".tablename('gandl_wall_user')." ADD COLUMN `notify_newpiece` tinyint(1) NOT NULL DEFAULT 1 COMMENT '新撒钱通知，0:关闭，1：开启' AFTER `home`;");
	pdo_query("ALTER TABLE ".tablename('gandl_wall_user')." ADD COLUMN `notify_newpiece_time` int(11) NOT NULL COMMENT '上次发送新撒钱通知时间' AFTER `notify_newpiece`;");

	pdo_query("ALTER TABLE ".tablename('gandl_wall')."	ADD COLUMN `province` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '限制省（多个用,号分隔）' AFTER `over_time`;");
	pdo_query("ALTER TABLE ".tablename('gandl_wall')."	ADD COLUMN `district` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '限制区县（多个用,号分隔）' AFTER `city`;");
	pdo_query("ALTER TABLE ".tablename('gandl_wall')."	ADD COLUMN `reply_on` tinyint(1) NULL COMMENT '是否开启评论：0关闭，1：开启' AFTER `static`;");
	pdo_query("ALTER TABLE ".tablename('gandl_wall')."	ADD COLUMN `reply_verify` tinyint(1) NULL COMMENT '评论审核模式：0：默认后置审核，1：前置审核' AFTER `reply_on`;");
	pdo_query("ALTER TABLE ".tablename('gandl_wall')."	ADD COLUMN `reply_mana` tinyint(1) NULL COMMENT '评论管理：0：不允许商户管理，1：允许商户管理' AFTER `reply_verify`;");
	pdo_query("ALTER TABLE ".tablename('gandl_wall')."	ADD COLUMN `task_follow` smallint(6) NOT NULL DEFAULT 600 COMMENT '关注可缩短冷却时间，秒，默认10分钟' AFTER `reply_mana`;");
	pdo_query("ALTER TABLE ".tablename('gandl_wall')."	ADD COLUMN `task_invite` smallint(6) NOT NULL DEFAULT 60 COMMENT '介绍一个好友可缩短冷却时间，秒，默认60秒' AFTER `task_follow`;");
	pdo_query("ALTER TABLE ".tablename('gandl_wall')."	ADD COLUMN `task_invite_max` smallint(6) NOT NULL DEFAULT 600 COMMENT '介绍好友任务上限，秒，默认10分钟' AFTER `task_invite`;");
	pdo_query("ALTER TABLE ".tablename('gandl_wall')."	ADD COLUMN `notify` tinyint(1) NOT NULL COMMENT '消息通知开关,0:关闭 1：开启' AFTER `follow_url`;");
	pdo_query("ALTER TABLE ".tablename('gandl_wall')."	ADD COLUMN `notify_tpl` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '消息通知模板序列化存储' AFTER `notify`;");

	pdo_query("ALTER TABLE ".tablename('gandl_wall_piece')." ADD COLUMN `notify_time` int(11) NOT NULL COMMENT '上次推送时间' AFTER `create_time`;");
	pdo_query("ALTER TABLE ".tablename('gandl_wall_piece')." ADD COLUMN `notify_cnt` int(11) NOT NULL COMMENT '推送到达人数' AFTER `notify_time`;");
}

pdo_query("CREATE TABLE IF NOT EXISTS `ims_gandl_wall_reply` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`uniacid` int(11) NOT NULL,
	`wall_id` int(11) NOT NULL,
	`piece_id` int(11) NOT NULL,
	`mine_id` int(11) NOT NULL,
	`user_id` int(11) NOT NULL,
	`content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
	`create_time` int(11) NOT NULL,
	`update_time` int(11) NOT NULL,
	`status` tinyint(1) NOT NULL COMMENT '1：未审核 2：审核通过 3：审核不通过',
	`status_time` int(11) NOT NULL,
	`op_id` int(11) NULL,
	`op_time` int(11) NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
CHECKSUM=0
DELAY_KEY_WRITE=0;");






