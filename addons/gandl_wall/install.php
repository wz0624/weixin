<?php

$sql =<<<EOF
DROP TABLE IF EXISTS `ims_gandl_wall`;
CREATE TABLE `ims_gandl_wall` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `topic` varchar(255) NOT NULL COMMENT '主题',
  `banner` varchar(255) DEFAULT NULL COMMENT '顶部宣传图',
  `slider` text COMMENT '统计开关0:关闭，1：开启',
  `static` tinyint(1) NOT NULL COMMENT '是否开启评论：0关闭，1：开启',
  `reply_on` tinyint(1) DEFAULT NULL COMMENT '是否开启评论：0关闭，1：开启',
  `reply_verify` tinyint(1) DEFAULT NULL COMMENT '评论审核模式：0：默认后置审核，1：前置审核',
  `reply_mana` tinyint(1) DEFAULT NULL COMMENT '评论管理：0：不允许商户管理，1：允许商户管理',
  `task_follow` smallint(6) NOT NULL DEFAULT '600' COMMENT '关注可缩短冷却时间，秒，默认10分钟',
  `task_invite` smallint(6) NOT NULL DEFAULT '60' COMMENT '介绍一个好友可缩短冷却时间，秒，默认60秒',
  `task_invite_max` smallint(6) NOT NULL DEFAULT '600' COMMENT '介绍好友任务上限，秒，默认10分钟',
  `background` varchar(255) DEFAULT NULL COMMENT '背景图',
  `css` text COMMENT '界面样式',
  `lang` text COMMENT '展示语言文字配置',
  `detail` text NOT NULL COMMENT '详细规则',
  `remark` text COMMENT '发红包说明',
  `notice` text COMMENT '公告',
  `start_time` int(11) NOT NULL COMMENT '活动开始日期时间',
  `end_time` int(11) NOT NULL COMMENT '活动结束日期时间',
  `begin_time` tinyint(2) NOT NULL COMMENT '每天开抢时间',
  `over_time` tinyint(2) NOT NULL COMMENT '每天结束时间',
  `province` varchar(300) DEFAULT NULL COMMENT '限制省（多个用,号分隔）',
  `city` varchar(300) DEFAULT NULL COMMENT '限制城市（多个用,号分隔）',
  `district` varchar(300) DEFAULT NULL COMMENT '限制区县（多个用,号分隔）',
  `piece_model` varchar(255) DEFAULT NULL COMMENT '开启的撒钱模型（逗号分隔1:普通，2:口令，3:组团）',
  `groupmax` smallint(6) DEFAULT NULL COMMENT '团伙最大人数',
  `group_rule` text COMMENT '[废弃]团伙人数的规则，每行一个。规则例：25:2（平均每份25分钱以下时，团伙上限为2人）',
  `password` varchar(255) DEFAULT NULL COMMENT '访问密码,TODO 增加地理位置限制',
  `cold_time` smallint(6) NOT NULL COMMENT '抢红包冷却时间(秒)',
  `hot_rule` text NOT NULL COMMENT '发布的消息预热展示时间规则(秒)',
  `total_min` int(11) NOT NULL COMMENT '红包总额至少钱数',
  `total_max` int(11) NOT NULL COMMENT '红包总额最多钱数',
  `avg_min` int(11) NOT NULL COMMENT '平均单个红包至少钱数',
  `avg_max` smallint(6) NOT NULL COMMENT '平均单个红包最大允许为平均额下限的几倍',
  `fee` float NOT NULL COMMENT '服务费率比例%',
  `total_min2` int(11) NOT NULL COMMENT '红包总额至少钱数(口令模式)',
  `total_max2` int(11) NOT NULL COMMENT '红包总额最多钱数(口令模式)',
  `avg_min2` int(11) NOT NULL COMMENT '平均单个红包至少钱数(口令模式)',
  `fee2` float NOT NULL COMMENT '服务费率比例%(口令模式)',
  `total_min3` int(11) NOT NULL COMMENT '红包总额至少钱数(组团模式)',
  `total_max3` int(11) NOT NULL COMMENT '红包总额最多钱数(组团模式)',
  `avg_min3` int(11) NOT NULL COMMENT '平均单个红包至少钱数(组团模式)',
  `fee3` float NOT NULL COMMENT '服务费率比例%(组团模式)',
  `top_line` int(11) NOT NULL COMMENT '置顶线，满多少钱可自动获得置顶（0为不置顶）',
  `transfer_min` int(11) DEFAULT NULL COMMENT '提现最小金额',
  `up_rob_fee` float NOT NULL COMMENT '��Ǯ�ϼ����',
  `up_send_fee` float NOT NULL COMMENT '��Ǯ�ϼ����',
  `fake_user` int(11) NOT NULL COMMENT '虚假参与人数',
  `fake_money` int(11) NOT NULL COMMENT '虚假发放金额基数（分）',
  `fake_online` int(11) DEFAULT NULL COMMENT '虚假在线人数基数',
  `follow_show` varchar(255) DEFAULT NULL COMMENT '关注引导图',
  `follow_url` varchar(255) DEFAULT NULL COMMENT '关注地址',
  `notify` tinyint(1) NOT NULL COMMENT '新撒钱通知，0:关闭，1：开启',
  `notify_tpl` text COMMENT '消息通知模板序列化存储',
  `share` text,
  `status` tinyint(1) NOT NULL COMMENT '1:正常，2停止',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `piece_verify` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ims_gandl_wall_group
-- ----------------------------
DROP TABLE IF EXISTS `ims_gandl_wall_group`;
CREATE TABLE `ims_gandl_wall_group` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='抢钱团表';

-- ----------------------------
-- Table structure for ims_gandl_wall_piece
-- ----------------------------
DROP TABLE IF EXISTS `ims_gandl_wall_piece`;
CREATE TABLE `ims_gandl_wall_piece` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `wall_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `model` tinyint(1) DEFAULT NULL COMMENT '模型(1:普通模型，2：团伙模型)',
  `total_amount` int(11) NOT NULL COMMENT '发出总金额',
  `total_num` int(11) NOT NULL COMMENT '发出总个数',
  `fee` int(11) NOT NULL COMMENT '缴纳的服务费金额',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `content` text COMMENT '内容',
  `images` text COMMENT '图片',
  `link` varchar(255) DEFAULT NULL COMMENT '链接',
  `password` varchar(255) DEFAULT NULL COMMENT '抢钱口令',
  `password_show` tinyint(1) DEFAULT NULL COMMENT '0:不显示抢钱口令，1：显示抢钱口令',
  `group_size` smallint(6) DEFAULT NULL COMMENT '团伙人数',
  `publish_time` int(11) DEFAULT NULL COMMENT '展示开始时间',
  `hot_time` int(11) NOT NULL COMMENT '预热展示时间(秒)',
  `top_level` int(11) NOT NULL COMMENT '置顶级别，超过置顶线的piece，置顶级别即为总金额数，否则为0',
  `rob_start_time` int(11) DEFAULT NULL COMMENT '开抢时间（展示开始时间+预热展示时间）',
  `total_pay` int(11) NOT NULL COMMENT '应付总额（分）',
  `pay` int(11) NOT NULL COMMENT '实付总额(分)',
  `status` tinyint(1) NOT NULL COMMENT '0:未生效（未付款），1：有效，2：下架',
  `views` int(11) NOT NULL COMMENT '被查看的次数',
  `links` int(11) NOT NULL COMMENT '链接被点击的次数',
  `rob_plan` text NOT NULL COMMENT '红包分配方案',
  `rob_amount` int(11) NOT NULL COMMENT '已被抢走的金额',
  `rob_users` int(11) NOT NULL COMMENT '已抢的用户数',
  `rob_end_time` int(11) DEFAULT NULL COMMENT '抢结束时间（rob_users等于total_num时）',
  `create_time` int(11) NOT NULL COMMENT '该记录创建时间',
  `notify_time` int(11) NOT NULL COMMENT '上次推送时间',
  `notify_cnt` int(11) NOT NULL COMMENT '推送到达人数',
  `op` tinyint(1) DEFAULT NULL COMMENT '管理员操作（0：无操作，1：禁止访问）',
  `op_remark` text COMMENT '操作说明',
  `op_admin` int(11) DEFAULT NULL COMMENT '操作的管理员user_id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ims_gandl_wall_reply
-- ----------------------------
DROP TABLE IF EXISTS `ims_gandl_wall_reply`;
CREATE TABLE `ims_gandl_wall_reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `wall_id` int(11) NOT NULL,
  `piece_id` int(11) NOT NULL,
  `mine_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '1：未审核 2：审核通过 3：审核不通过',
  `status_time` int(11) NOT NULL,
  `op_id` int(11) DEFAULT NULL,
  `op_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ims_gandl_wall_rob
-- ----------------------------
DROP TABLE IF EXISTS `ims_gandl_wall_rob`;
CREATE TABLE `ims_gandl_wall_rob` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `wall_id` int(11) NOT NULL,
  `piece_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `money` int(11) DEFAULT NULL COMMENT '抢到的金额',
  `get_money` int(11) DEFAULT NULL COMMENT '实际获得的金额',
  `up_money` int(11) DEFAULT NULL COMMENT '上交的金额',
  `is_luck` int(11) DEFAULT NULL COMMENT '是否手气最佳',
  `is_shit` int(11) DEFAULT NULL COMMENT '是否手气最差',
  `create_time` int(11) NOT NULL COMMENT '该记录创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ims_gandl_wall_up_rob
-- ----------------------------
DROP TABLE IF EXISTS `ims_gandl_wall_up_rob`;
CREATE TABLE `ims_gandl_wall_up_rob` (
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
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- ----------------------------
-- Table structure for ims_gandl_wall_user
-- ----------------------------
DROP TABLE IF EXISTS `ims_gandl_wall_user`;
CREATE TABLE `ims_gandl_wall_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `wall_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nickname` varchar(80) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `who` tinyint(1) DEFAULT NULL COMMENT '0:未知 1:普通用户号 2：订阅号 3：认证订阅号 4：服务号 5：认证服务号',
  `home` varchar(255) DEFAULT NULL,
  `notify_newpiece` tinyint(1) NOT NULL DEFAULT '1' COMMENT '新撒钱通知，0:关闭，1：开启',
  `notify_newpiece_time` int(11) NOT NULL COMMENT '上次发送新撒钱通知时间',
  `followed` tinyint(1) NOT NULL COMMENT '是否关注过了（未：0,1：已关注过）。这个量仅用于结合fan[''fllow'']一起判断是否首次关注',
  `follow` tinyint(1) NOT NULL DEFAULT '-1' COMMENT '是否正在关注（-1:未知，未：0,1：正关注）。这个量用于优化性能，不使用查询fan表来实现了解用户是否正关注公众号',
  `money` int(11) NOT NULL COMMENT '我的余额',
  `money_in` int(11) NOT NULL COMMENT '我的充值总额',
  `money_out` int(11) NOT NULL COMMENT '我的提现总额',
  `send_times` int(11) NOT NULL COMMENT '发表次数',
  `send_total` int(11) NOT NULL COMMENT '发出总金额',
  `send_last_time` int(11) NOT NULL COMMENT '上次发表时间',
  `in_position` tinyint(1) DEFAULT NULL COMMENT '0:未定位 1:在范围内 2：不在范围内',
  `last_city` varchar(60) DEFAULT NULL COMMENT '上次定位所在城市',
  `last_position` text NOT NULL COMMENT '上次定位所在地',
  `last_position_exp` int(11) NOT NULL COMMENT '上次定位过期时间',
  `rob_times` int(11) NOT NULL COMMENT '参与次数',
  `rob_total` int(11) NOT NULL COMMENT '收到总金额',
  `rob_last_time` int(11) NOT NULL COMMENT '上次参与时间',
  `rob_fast` int(11) NOT NULL COMMENT '抢钱加速器（单位：秒）',
  `rob_next_time` int(11) NOT NULL COMMENT '下次参与时间(以这个为准)',
  `rob_luck` int(11) NOT NULL COMMENT '运气（正常应该是和收到的总金额一样，收到的越多，运气越差）',
  `create_time` int(11) NOT NULL COMMENT '该用户记录创建时间',
  `last_active_time` int(11) NOT NULL COMMENT '上次活动时间',
  `inviter_id` int(11) NOT NULL COMMENT '介绍人ID',
  `black` tinyint(1) NOT NULL COMMENT '0:非黑名单,1:黑名单',
  `black_why` varchar(255) DEFAULT NULL COMMENT '被列入黑名单的原因',
  `admin` tinyint(1) NOT NULL COMMENT '是否是管理员：0否，大于0，是',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ims_gandl_wall_user_help
-- ----------------------------
DROP TABLE IF EXISTS `ims_gandl_wall_user_help`;
CREATE TABLE `ims_gandl_wall_user_help` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `wall_id` int(11) NOT NULL,
  `help` int(11) NOT NULL COMMENT '被帮助者用户参与记录ID',
  `helper_id` int(11) NOT NULL COMMENT '施助者ID',
  `create_time` int(11) NOT NULL COMMENT '施助创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ims_gandl_wall_user_transfer
-- ----------------------------
DROP TABLE IF EXISTS `ims_gandl_wall_user_transfer`;
CREATE TABLE `ims_gandl_wall_user_transfer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `wall_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `openid` varchar(100) NOT NULL COMMENT '用户的openid',
  `money` int(11) NOT NULL COMMENT '提现金额（分）',
  `money_before` int(11) NOT NULL COMMENT '提现前账户金额',
  `money_after` int(11) NOT NULL COMMENT '提现后账户金额',
  `status` tinyint(1) NOT NULL COMMENT '0：发起\n1：成功\n2：失败（未退帐）\n3：失败（已退帐）',
  `channel` tinyint(1) DEFAULT NULL COMMENT '1:微信红包，2：企业转账',
  `mch_billno` varchar(50) DEFAULT NULL COMMENT '商户订单号（本系统内业务订单号）',
  `out_billno` varchar(50) DEFAULT NULL COMMENT '外部订单号（对接系统的订单号）',
  `out_money` int(11) DEFAULT NULL COMMENT '接口交易订单中的实际金额（分）',
  `tag` text COMMENT '接口订单信息序列化存储',
  `remark` text,
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户提现记录表';
EOF;
pdo_run($sql);
