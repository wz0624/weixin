<?php
if(!pdo_fieldexists('stonefish_lottery_reply', 'startimg')) {
	pdo_query("ALTER TABLE ".tablename('stonefish_lottery_reply')." ADD `startimg` varchar(255) DEFAULT '' COMMENT '进入活动背景图' AFTER `homepic`;");
	pdo_query("ALTER TABLE ".tablename('stonefish_lottery_reply')." ADD `bmimg` varchar(255) DEFAULT '' COMMENT '开始报名背景图' AFTER `startimg`;");
}