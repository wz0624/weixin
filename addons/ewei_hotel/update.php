<?php
$sql ="
CREATE TABLE IF NOT EXISTS  `ims_hotel2_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `hotelid` int(11) DEFAULT '0',
  `uid` int(11) DEFAULT '0',
  `createtime` int(11) DEFAULT '0',
  `comment` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
pdo_query($sql);

if(pdo_fieldexists('hotel2_order', 'oprice')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_order')." CHANGE `oprice` `oprice` decimal(10,2) DEFAULT '0.00' COMMENT '原价';");
}
if(pdo_fieldexists('hotel2_order', 'cprice')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_order')." CHANGE `cprice`  `cprice` decimal(10,2) DEFAULT '0.00' COMMENT '现价';");
}
if(pdo_fieldexists('hotel2_order', 'mprice')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_order')." CHANGE `mprice`   `mprice` decimal(10,2) DEFAULT '0.00' COMMENT '会员价';");
}
if(pdo_fieldexists('hotel2_order', 'sum_price')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_order')." CHANGE `sum_price`   `sum_price` decimal(10,2) DEFAULT '0.00' COMMENT '总价';");
}
if(pdo_fieldexists('hotel2_room', 'oprice')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_room')." CHANGE `oprice` `oprice` decimal(10,2) DEFAULT '0.00' COMMENT '原价';");
}
if(pdo_fieldexists('hotel2_room', 'cprice')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_room')." CHANGE `cprice`  `cprice` decimal(10,2) DEFAULT '0.00' COMMENT '现价';");
}
if(pdo_fieldexists('hotel2_room', 'mprice')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_room')." CHANGE `mprice`   `mprice` decimal(10,2) DEFAULT '0.00' COMMENT '会员价';");
}

if(pdo_fieldexists('hotel2_room_price', 'oprice')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_room_price')." CHANGE `oprice` `oprice` decimal(10,2) DEFAULT '0.00' COMMENT '原价';");
}
if(pdo_fieldexists('hotel2_room_price', 'cprice')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_room_price')." CHANGE `cprice`  `cprice` decimal(10,2) DEFAULT '0.00' COMMENT '现价';");
}
if(pdo_fieldexists('hotel2_room_price', 'mprice')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_room_price')." CHANGE `mprice`   `mprice` decimal(10,2) DEFAULT '0.00' COMMENT '会员价';");
}

if(!pdo_fieldexists('hotel2_set', 'email')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_set')." ADD `email` varchar(255) NOT NULL DEFAULT '' COMMENT '提醒接受邮箱';");
}
if(!pdo_fieldexists('hotel2_set', 'mobile')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_set')." ADD `mobile` varchar(32) NOT NULL DEFAULT '' COMMENT '提醒接受手机';");
}
if(!pdo_fieldexists('hotel2_order', 'remark')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_order')." ADD   `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注';");
}
if(!pdo_fieldexists('hotel2_set', 'template')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_set')." ADD    `template` varchar(32) NOT NULL DEFAULT '' COMMENT '发送模板消息';");
}
if(!pdo_fieldexists('hotel2_set', 'templateid')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_set')." ADD    `templateid` varchar(255) NOT NULL DEFAULT '' COMMENT '模板ID';");
}
if(pdo_fieldexists('hotel2_room', 'mprice')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_room')." CHANGE  `mprice`   `mprice` varchar(255) NOT NULL DEFAULT '';");
}
if(!pdo_fieldexists('hotel2_member', 'clerk')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_member')." ADD   `clerk` varchar(32) NOT NULL DEFAULT '';");
}
if(!pdo_fieldexists('hotel2_member', 'nickname')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_member')." ADD    `nickname` varchar(255) NOT NULL DEFAULT '';");
}
if(!pdo_fieldexists('hotel2_set', 'smscode')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_set')." ADD     `smscode` int(3) NOT NULL DEFAULT '0';");
}
if(!pdo_fieldexists('hotel2_set', 'is_sms')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_set')." ADD   `is_sms` int(10) NOT NULL DEFAULT '0' COMMENT '是否开启短信';");
}
if(!pdo_fieldexists('hotel2_set', 'sms_id')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_set')." ADD   `sms_id` varchar(20) NOT NULL COMMENT '短信模板ID';");
}

if(!pdo_fieldexists('hotel2_order', 'comment')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_order')." ADD   `comment` int(3) NOT NULL DEFAULT '0';");
}
if(!pdo_fieldexists('hotel2_room', 'service')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_room')." ADD    `service` int(10) NOT NULL DEFAULT '0';");
}
if(!pdo_fieldexists('hotel2_set', 'refund')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_set')." ADD    `refund` int(3) NOT NULL DEFAULT '0';");
}