<?php


/**
 *砍价活动
 */
$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('mon_egg') . " (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rid` int(10) NOT NULL,
  `weid` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `starttime` int(10) DEFAULT NULL,
  `endtime` int(10) DEFAULT NULL,
  `intro` text,
  `music` varchar(500) DEFAULT NULL,
  `banner_bg` varchar(1000) DEFAULT NULL,
  `bg_img` varchar(1000) DEFAULT NULL,
  `share_bg` varchar(1000) DEFAULT NULL,
  `day_count` int(10) DEFAULT NULL,
  `prize_limit` int(10) DEFAULT NULL,
  `dpassword` varchar(20) DEFAULT NULL,
  `follow_url` varchar(1000) DEFAULT NULL,
  `copyright` varchar(100) NOT NULL,
  `follow_dlg_tip` varchar(500) DEFAULT NULL,
  `follow_btn_name` varchar(20) DEFAULT NULL,
  `share_enable` int(1) DEFAULT '0',
  `share_times` int(10) DEFAULT '0',
  `share_award_count` int(10) DEFAULT '0',
  `new_icon` varchar(200) DEFAULT NULL,
  `new_content` varchar(200) DEFAULT NULL,
  `new_title` varchar(200) DEFAULT NULL,
  `share_title` varchar(200) DEFAULT NULL,
  `share_icon` varchar(200) DEFAULT NULL,
  `share_content` varchar(200) DEFAULT NULL,
  `createtime` int(10) DEFAULT '0',
  `updatetime` int(10) DEFAULT NULL,
  `exchangeEnable` int(1) DEFAULT NULL,
  `xhjf_enable` int(1) DEFAULT NULL,
  `xhjf` int(10) DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
pdo_query($sql);




/**
 * 扎金蛋奖品
 */
$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('mon_egg_prize') . " (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sn` varchar(100) DEFAULT NULL,
  `egid` int(10) DEFAULT NULL,
  `plevel` varchar(50) DEFAULT NULL,
  `pname` varchar(50) DEFAULT NULL,
  `pimg` varchar(500) DEFAULT NULL,
  `ptype` int(1) DEFAULT NULL,
  `pb` int(10) DEFAULT '0',
  `jf` int(10) DEFAULT '0',
  `pcount` int(10) DEFAULT NULL,
  `display_order` int(3) DEFAULT NULL,
  `createtime` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
pdo_query($sql);

/**
 * zjd记录
 */
$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('mon_egg_record') . " (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `egid` int(10) NOT NULL,
  `pid` int(10) DEFAULT NULL,
  `pname` varchar(200) DEFAULT NULL,
  `uid` int(10) DEFAULT NULL,
  `openid` varchar(200) NOT NULL,
  `status` int(1) DEFAULT NULL,
  `createtime` int(10) DEFAULT '0',
  `dhtime` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
pdo_query($sql);

/**
 * 金蛋share
 */
$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('mon_egg_share') . " (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `egid` int(10) NOT NULL,
  `uid` int(10) DEFAULT NULL,
  `openid` varchar(300) DEFAULT NULL,
  `createtime` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
pdo_query($sql);

/**
 * 参加用户
 */

$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('mon_egg_user') . " (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `egid` int(10) NOT NULL,
  `openid` varchar(200) NOT NULL,
  `nickname` varchar(100) NOT NULL,
  `headimgurl` varchar(200) NOT NULL,
  `createtime` int(10) DEFAULT '0',
  `uname` varchar(100) DEFAULT NULL,
  `tel` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
pdo_query($sql);





