<?php

$sql = "
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_member_message_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT '',
  `template_id` varchar(255) DEFAULT '',
  `first` text NOT NULL COMMENT '键名',
  `firstcolor` varchar(255) DEFAULT '',
  `data` text NOT NULL COMMENT '颜色',
  `remark` text NOT NULL COMMENT '键值',
  `remarkcolor` varchar(255) DEFAULT '',
  `url` varchar(255) NOT NULL,
  `createtime` int(11) DEFAULT '0',
  `sendtimes` int(11) DEFAULT '0',
  `sendcount` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_createtime` (`createtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_ewei_shop_plugin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `displayorder` int(11) DEFAULT '0',
  `identity` varchar(50) DEFAULT '',
  `name` varchar(50) DEFAULT '',
  `version` varchar(10) DEFAULT '',
  `author` varchar(20) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_displayorder` (`displayorder`),
  FULLTEXT KEY `idx_identity` (`identity`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_ewei_shop_poster` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `type` tinyint(3) DEFAULT '0' COMMENT '1 首页 2 小店 3 商城 4 自定义',
  `title` varchar(255) DEFAULT '',
  `bg` varchar(255) DEFAULT '',
  `data` text,
  `keyword` varchar(255) DEFAULT '',
  `times` int(11) DEFAULT '0',
  `follows` int(11) DEFAULT '0',
  `isdefault` tinyint(3) DEFAULT '0',
  `resptitle` varchar(255) DEFAULT '',
  `respthumb` varchar(255) DEFAULT '',
  `createtime` int(11) DEFAULT '0',
  `respdesc` varchar(255) DEFAULT '',
  `respurl` varchar(255) DEFAULT '',
  `waittext` varchar(255) DEFAULT '',
  `oktext` varchar(255) DEFAULT '',
  `subcredit` int(11) DEFAULT '0',
  `submoney` decimal(10,2) DEFAULT '0.00',
  `reccredit` int(11) DEFAULT '0',
  `recmoney` decimal(10,2) DEFAULT '0.00',
  `scantext` varchar(255) DEFAULT '',
  `subtext` varchar(255) DEFAULT '',
  `beagent` tinyint(3) DEFAULT '0',
  `bedown` tinyint(3) DEFAULT '0',
  `isopen` tinyint(3) DEFAULT '0',
  `opentext` varchar(255) DEFAULT '',
  `openurl` varchar(255) DEFAULT '',
  `paytype` tinyint(1) DEFAULT '0',
  `templateid` varchar(255) DEFAULT '',
  `subpaycontent` text,
  `recpaycontent` text,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_type` (`type`),
  KEY `idx_times` (`times`),
  KEY `idx_isdefault` (`isdefault`),
  KEY `idx_createtime` (`createtime`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_ewei_shop_poster_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '',
  `posterid` int(11) DEFAULT '0',
  `from_openid` varchar(255) DEFAULT '',
  `subcredit` int(11) DEFAULT '0',
  `submoney` decimal(10,2) DEFAULT '0.00',
  `reccredit` int(11) DEFAULT '0',
  `recmoney` decimal(10,2) DEFAULT '0.00',
  `createtime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_createtime` (`createtime`),
  KEY `idx_posterid` (`posterid`),
  FULLTEXT KEY `idx_from_openid` (`from_openid`)
) ENGINE=MyISAM AUTO_INCREMENT=262 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS  `ims_ewei_shop_poster_qr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `acid` int(10) unsigned NOT NULL,
  `openid` varchar(100) NOT NULL DEFAULT '',
  `type` tinyint(3) DEFAULT '0',
  `sceneid` int(11) DEFAULT '0',
  `mediaid` varchar(255) DEFAULT '',
  `ticket` varchar(250) NOT NULL,
  `url` varchar(80) NOT NULL,
  `createtime` int(10) unsigned NOT NULL,
  `goodsid` int(11) DEFAULT '0',
  `qrimg` varchar(1000) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_acid` (`acid`),
  KEY `idx_sceneid` (`sceneid`),
  KEY `idx_type` (`type`),
  FULLTEXT KEY `idx_openid` (`openid`)
) ENGINE=MyISAM AUTO_INCREMENT=85 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_ewei_shop_poster_scan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `posterid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '',
  `from_openid` varchar(255) DEFAULT '',
  `scantime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_posterid` (`posterid`),
  KEY `idx_scantime` (`scantime`),
  FULLTEXT KEY `idx_openid` (`openid`)
) ENGINE=MyISAM AUTO_INCREMENT=68 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_ewei_shop_saler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `storeid` int(11) DEFAULT '0',
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '',
  `status` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_storeid` (`storeid`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_ewei_shop_store` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `storename` varchar(255) DEFAULT '',
  `address` varchar(255) DEFAULT '',
  `tel` varchar(255) DEFAULT '',
  `lat` varchar(255) DEFAULT '',
  `lng` varchar(255) DEFAULT '',
  `status` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_ewei_shop_perm_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT '0',
  `uniacid` int(11) DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `type` varchar(255) DEFAULT '',
  `op` text,
  `createtime` int(11) DEFAULT '0',
  `ip` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`),
  KEY `idx_createtime` (`createtime`),
  KEY `idx_uniacid` (`uniacid`),
  FULLTEXT KEY `idx_type` (`type`),
  FULLTEXT KEY `idx_op` (`op`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `ims_ewei_shop_perm_plugin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `acid` int(11) DEFAULT '0',
  `uid` int(11) DEFAULT '0',
  `type` tinyint(3) DEFAULT '0',
  `plugins` text,
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`),
  KEY `idx_acid` (`acid`),
  KEY `idx_type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `ims_ewei_shop_perm_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `rolename` varchar(255) DEFAULT '',
  `status` tinyint(3) DEFAULT '0',
  `perms` text,
  `deleted` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_status` (`status`),
  KEY `idx_deleted` (`deleted`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `ims_ewei_shop_perm_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `uid` int(11) DEFAULT '0',
  `username` varchar(255) DEFAULT '',
  `password` varchar(255) DEFAULT '',
  `roleid` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '0',
  `perms` text,
  `deleted` tinyint(3) DEFAULT '0',
  `realname` varchar(255) DEFAULT '',
  `mobile` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_uid` (`uid`),
  KEY `idx_roleid` (`roleid`),
  KEY `idx_status` (`status`),
  KEY `idx_deleted` (`deleted`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `ims_ewei_shop_creditshop_adv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `advname` varchar(50) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `thumb` varchar(255) DEFAULT '',
  `displayorder` int(11) DEFAULT '0',
  `enabled` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_enabled` (`enabled`),
  KEY `idx_displayorder` (`displayorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `ims_ewei_shop_creditshop_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0' COMMENT '所属帐号',
  `name` varchar(50) DEFAULT NULL COMMENT '分类名称',
  `thumb` varchar(255) DEFAULT NULL COMMENT '分类图片',
  `displayorder` tinyint(3) unsigned DEFAULT '0' COMMENT '排序',
  `enabled` tinyint(1) DEFAULT '1' COMMENT '是否开启',
  `advimg` varchar(255) DEFAULT '',
  `advurl` varchar(500) DEFAULT '',
  `isrecommand` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_displayorder` (`displayorder`),
  KEY `idx_enabled` (`enabled`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `ims_ewei_shop_creditshop_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `displayorder` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT '',
  `cate` int(11) DEFAULT '0',
  `thumb` varchar(255) DEFAULT '',
  `price` decimal(10,2) DEFAULT '0.00',
  `type` tinyint(3) DEFAULT '0',
  `credit` int(11) DEFAULT '0',
  `money` decimal(10,2) DEFAULT '0.00',
  `total` int(11) DEFAULT '0',
  `totalday` int(11) DEFAULT '0',
  `chance` int(11) DEFAULT '0',
  `chanceday` int(11) DEFAULT '0',
  `detail` text,
  `rate1` int(11) DEFAULT '0',
  `rate2` int(11) DEFAULT '0',
  `endtime` int(11) DEFAULT '0',
  `joins` int(11) DEFAULT '0',
  `views` int(11) DEFAULT '0',
  `createtime` int(11) DEFAULT '0',
  `status` tinyint(3) DEFAULT '0',
  `deleted` tinyint(3) DEFAULT '0',
  `showlevels` text,
  `buylevels` text,
  `showgroups` text,
  `buygroups` text,
  `vip` tinyint(3) DEFAULT '0',
  `istop` tinyint(3) DEFAULT '0',
  `isrecommand` tinyint(3) DEFAULT '0',
  `istime` tinyint(3) DEFAULT '0',
  `timestart` int(11) DEFAULT '0',
  `timeend` int(11) DEFAULT '0',
  `share_title` varchar(255) DEFAULT '',
  `share_icon` varchar(255) DEFAULT '',
  `share_desc` varchar(500) DEFAULT '',
  `followneed` tinyint(3) DEFAULT '0',
  `followtext` varchar(255) DEFAULT '',
  `subtitle` varchar(255) DEFAULT '',
  `subdetail` text,
  `noticedetail` text,
  `usedetail` varchar(255) DEFAULT '',
  `goodsdetail` text,
  `isendtime` tinyint(3) DEFAULT '0',
  `usecredit2` tinyint(3) DEFAULT '0',
  `area` varchar(255) DEFAULT '',
  `dispatch` decimal(10,2) DEFAULT '0.00',
  `storeids` text,
  `noticeopenid` varchar(255) DEFAULT '',
  `noticetype` tinyint(3) DEFAULT '0',
  `isverify` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_type` (`type`),
  KEY `idx_endtime` (`endtime`),
  KEY `idx_createtime` (`createtime`),
  KEY `idx_status` (`status`),
  KEY `idx_displayorder` (`displayorder`),
  KEY `idx_deleted` (`deleted`),
  KEY `idx_istop` (`istop`),
  KEY `idx_isrecommand` (`isrecommand`),
  KEY `idx_istime` (`istime`),
  KEY `idx_timestart` (`timestart`),
  KEY `idx_timeend` (`timeend`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `ims_ewei_shop_creditshop_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `logno` varchar(255) DEFAULT '',
  `eno` varchar(255) DEFAULT '' COMMENT '兑换码',
  `openid` varchar(255) DEFAULT '',
  `goodsid` int(11) DEFAULT '0',
  `createtime` int(11) DEFAULT '0',
  `status` tinyint(3) DEFAULT '0' COMMENT '0 只生成记录未参加 1 未中奖 2 已中奖 3 已发奖',
  `paystatus` tinyint(3) DEFAULT '0' COMMENT '支付状态 -1 不需要支付 0 未支付 1 已支付',
  `paytype` tinyint(3) DEFAULT '-1' COMMENT '支付类型 -1 不需要支付 0 余额 1 微信',
  `dispatchstatus` tinyint(3) DEFAULT '0' COMMENT '运费状态 -1 不需要运费 0 未支付 1 已支付',
  `creditpay` tinyint(3) DEFAULT '0' COMMENT '积分支付 0 未支付 1 已支付',
  `addressid` int(11) DEFAULT '0' COMMENT '收货地址',
  `dispatchno` varchar(255) DEFAULT '' COMMENT '运费支付单号',
  `usetime` int(11) DEFAULT '0',
  `express` varchar(255) DEFAULT '',
  `expresssn` varchar(255) DEFAULT '',
  `expresscom` varchar(255) DEFAULT '',
  `verifyopenid` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `ims_ewei_shop_designer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0' COMMENT '公众号',
  `pagename` varchar(255) NOT NULL DEFAULT '' COMMENT '页面名称',
  `pagetype` tinyint(3) NOT NULL DEFAULT '0' COMMENT '页面类型',
  `pageinfo` text NOT NULL,
  `createtime` varchar(255) NOT NULL DEFAULT '' COMMENT '页面创建时间',
  `keyword` varchar(255) DEFAULT '',
  `savetime` varchar(255) NOT NULL DEFAULT '' COMMENT '页面最后保存时间',
  `setdefault` tinyint(3) NOT NULL DEFAULT '0' COMMENT '默认页面',
  `datas` text NOT NULL COMMENT '数据',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_pagetype` (`pagetype`),
  FULLTEXT KEY `idx_keyword` (`keyword`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;


CREATE TABLE IF NOT EXISTS `ims_ewei_shop_virtual_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0' COMMENT '所属帐号',
  `name` varchar(50) DEFAULT NULL COMMENT '分类名称',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_ewei_shop_virtual_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `typeid` int(11) NOT NULL DEFAULT '0' COMMENT '类型id',
  `pvalue` varchar(255) DEFAULT '' COMMENT '主键键值',
  `fields` text NOT NULL COMMENT '字符集',
  `openid` varchar(255) NOT NULL DEFAULT '' COMMENT '使用者openid',
  `usetime` int(11) NOT NULL DEFAULT '0' COMMENT '使用时间',
  `orderid` int(11) DEFAULT '0',
  `ordersn` varchar(255) DEFAULT '',
  `price` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_typeid` (`typeid`),
  KEY `idx_usetime` (`usetime`),
  KEY `idx_orderid` (`orderid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS  `ims_ewei_shop_virtual_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `cate` int(11) DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '分类名称',
  `fields` text NOT NULL COMMENT '字段集',
  `usedata` int(11) NOT NULL DEFAULT '0' COMMENT '已用数据',
  `alldata` int(11) NOT NULL DEFAULT '0' COMMENT '全部数据',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_cate` (`cate`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS  `ims_ewei_shop_designer_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `menuname` varchar(255) DEFAULT '',
  `isdefault` tinyint(3) DEFAULT '0',
  `createtime` int(11) DEFAULT '0',
  `menus` text,
  `params` text,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_isdefault` (`isdefault`),
  KEY `idx_createtime` (`createtime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `ims_ewei_shop_plugin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `displayorder` int(11) DEFAULT '0',
  `identity` varchar(50) DEFAULT '',
  `name` varchar(50) DEFAULT '',
  `version` varchar(10) DEFAULT '',
  `author` varchar(20) DEFAULT '',
  `status` tinyint(3) DEFAULT '0',
  `category` varchar(255) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_displayorder` (`displayorder`),
  FULLTEXT KEY `idx_identity` (`identity`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `ims_ewei_shop_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_title` varchar(255) NOT NULL DEFAULT '' COMMENT '文章标题',
  `resp_desc` text NOT NULL COMMENT '回复介绍',
  `resp_img` text NOT NULL COMMENT '回复图片',
  `article_content` longtext,
  `article_category` int(11) NOT NULL DEFAULT '0' COMMENT '文章分类',
  `article_date_v` varchar(20) NOT NULL DEFAULT '' COMMENT '虚拟发布时间',
  `article_date` varchar(20) NOT NULL DEFAULT '' COMMENT '文章发布时间',
  `article_mp` varchar(50) NOT NULL DEFAULT '' COMMENT '公众号',
  `article_author` varchar(20) NOT NULL DEFAULT '' COMMENT '发布作者',
  `article_readnum_v` int(11) NOT NULL DEFAULT '0' COMMENT '虚拟阅读量',
  `article_readnum` int(11) NOT NULL DEFAULT '0' COMMENT '真实阅读量',
  `article_likenum_v` int(11) NOT NULL DEFAULT '0' COMMENT '虚拟点赞数',
  `article_likenum` int(11) NOT NULL DEFAULT '0' COMMENT '真实点赞数',
  `article_linkurl` varchar(300) NOT NULL DEFAULT '' COMMENT '阅读原文链接',
  `article_rule_daynum` int(11) NOT NULL DEFAULT '0' COMMENT '每人每天参与次数',
  `article_rule_allnum` int(11) NOT NULL DEFAULT '0' COMMENT '所有参与次数',
  `article_rule_credit` int(11) NOT NULL DEFAULT '0' COMMENT '增加y积分',
  `article_rule_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '增加z余额',
  `page_set_option_nocopy` int(1) NOT NULL DEFAULT '0' COMMENT '页面禁止复制url',
  `page_set_option_noshare_tl` int(1) NOT NULL DEFAULT '0' COMMENT '页面禁止分享至朋友圈',
  `page_set_option_noshare_msg` int(1) NOT NULL DEFAULT '0' COMMENT '页面禁止发送给好友',
  `article_keyword` varchar(255) NOT NULL DEFAULT '' COMMENT '页面关键字',
  `article_report` int(1) NOT NULL DEFAULT '0' COMMENT '举报按钮',
  `product_advs_type` int(1) NOT NULL DEFAULT '0' COMMENT '营销显示产品',
  `product_advs_title` varchar(255) NOT NULL DEFAULT '' COMMENT '营销产品标题',
  `product_advs_more` varchar(255) NOT NULL DEFAULT '' COMMENT '推广产品底部标题',
  `product_advs_link` varchar(255) NOT NULL DEFAULT '' COMMENT '推广产品底部链接',
  `product_advs` text NOT NULL COMMENT '营销商品',
  `article_state` int(1) NOT NULL DEFAULT '0',
  `network_attachment` varchar(255) DEFAULT '',
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `article_rule_credittotal` int(11) DEFAULT '0',
  `article_rule_moneytotal` decimal(10,2) DEFAULT '0.00',
  `article_rule_credit2` int(11) DEFAULT '0',
  `article_rule_money2` decimal(10,2) DEFAULT '0.00',
  `article_rule_creditm` int(11) DEFAULT '0',
  `article_rule_moneym` decimal(10,2) DEFAULT '0.00',
  `article_rule_creditm2` int(11) DEFAULT '0',
  `article_rule_moneym2` decimal(10,2) DEFAULT '0.00',
  `article_readtime` int(11) DEFAULT '0',
  `article_areas` varchar(255) DEFAULT '',
  `article_endtime` int(11) DEFAULT '0',
  `article_hasendtime` tinyint(3) DEFAULT '0',
  `displayorder` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_article_title` (`article_title`),
  KEY `idx_article_content` (`article_content`(10)),
  KEY `idx_article_keyword` (`article_keyword`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS  `ims_ewei_shop_article_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL DEFAULT '' COMMENT '分类名称',
  `uniacid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_category_name` (`category_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS  `ims_ewei_shop_article_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aid` int(11) NOT NULL DEFAULT '0' COMMENT '文章id',
  `read` int(11) NOT NULL DEFAULT '0',
  `like` int(11) NOT NULL DEFAULT '0',
  `openid` varchar(255) NOT NULL DEFAULT '' COMMENT '用户openid',
  `uniacid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_aid` (`aid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS  `ims_ewei_shop_article_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mid` int(11) NOT NULL DEFAULT '0',
  `openid` varchar(255) NOT NULL DEFAULT '',
  `aid` int(11) DEFAULT '0',
  `cate` varchar(255) NOT NULL DEFAULT '',
  `cons` varchar(255) NOT NULL DEFAULT '',
  `uniacid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS   `ims_ewei_shop_article_share` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aid` int(11) NOT NULL DEFAULT '0',
  `share_user` int(11) NOT NULL DEFAULT '0' COMMENT '分享人',
  `click_user` int(11) NOT NULL DEFAULT '0' COMMENT '点击人',
  `click_date` varchar(20) NOT NULL DEFAULT '' COMMENT '执行时间',
  `add_credit` int(11) NOT NULL DEFAULT '0' COMMENT '添加的积分',
  `add_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '添加的余额',
  `uniacid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_aid` (`aid`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS   `ims_ewei_shop_article_sys` (
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `article_message` varchar(255) NOT NULL DEFAULT '',
  `article_title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `article_image` varchar(300) NOT NULL DEFAULT '' COMMENT '图片',
  `article_shownum` int(11) NOT NULL DEFAULT '0' COMMENT '每页数量',
  `article_keyword` varchar(255) NOT NULL DEFAULT '' COMMENT '关键字',
  `article_temp` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uniacid`),
  KEY `idx_article_message` (`article_message`),
  KEY `idx_article_keyword` (`article_keyword`),
  KEY `idx_article_title` (`article_title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS  `ims_ewei_shop_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `catid` int(11) DEFAULT '0',
  `couponname` varchar(255) DEFAULT '',
  `gettype` tinyint(3) DEFAULT '0',
  `getmax` int(11) DEFAULT '0',
  `usetype` tinyint(3) DEFAULT '0' COMMENT '消费方式 0 付款使用 1 下单使用',
  `returntype` tinyint(3) DEFAULT '0' COMMENT '退回方式 0 不可退回 1 取消订单(未付款) 2.退款可以退回',
  `bgcolor` varchar(255) DEFAULT '',
  `enough` decimal(10,2) DEFAULT '0.00',
  `timelimit` tinyint(3) DEFAULT '0' COMMENT '0 领取后几天有效 1 时间范围',
  `coupontype` tinyint(3) DEFAULT '0' COMMENT '0 优惠券 1 充值券',
  `timedays` int(11) DEFAULT '0',
  `timestart` int(11) DEFAULT '0',
  `timeend` int(11) DEFAULT '0',
  `discount` decimal(10,2) DEFAULT '0.00' COMMENT '折扣',
  `deduct` decimal(10,2) DEFAULT '0.00' COMMENT '抵扣',
  `backtype` tinyint(3) DEFAULT '0',
  `backmoney` varchar(50) DEFAULT '' COMMENT '返现',
  `backcredit` varchar(50) DEFAULT '' COMMENT '返积分',
  `backredpack` varchar(50) DEFAULT '',
  `backwhen` tinyint(3) DEFAULT '0',
  `thumb` varchar(255) DEFAULT '',
  `desc` text,
  `createtime` int(11) DEFAULT '0',
  `total` int(11) DEFAULT '0' COMMENT '数量 -1 不限制',
  `status` tinyint(3) DEFAULT '0' COMMENT '可用',
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '购买价格',
  `respdesc` text COMMENT '推送描述',
  `respthumb` varchar(255) DEFAULT '' COMMENT '推送图片',
  `resptitle` varchar(255) DEFAULT '' COMMENT '推送标题',
  `respurl` varchar(255) DEFAULT '',
  `credit` int(11) DEFAULT '0',
  `usecredit2` tinyint(3) DEFAULT '0',
  `remark` varchar(1000) DEFAULT '',
  `descnoset` tinyint(3) DEFAULT '0',
  `pwdkey` varchar(255) DEFAULT '',
  `pwdsuc` text,
  `pwdfail` text,
  `pwdurl` varchar(255) DEFAULT '',
  `pwdask` text,
  `pwdstatus` tinyint(3) DEFAULT '0',
  `pwdtimes` int(11) DEFAULT '0',
  `pwdfull` text,
  `pwdwords` text,
  `pwdopen` tinyint(3) DEFAULT '0',
  `pwdown` text,
  `pwdexit` varchar(255) DEFAULT '',
  `pwdexitstr` text,
  `displayorder` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_coupontype` (`coupontype`),
  KEY `idx_timestart` (`timestart`),
  KEY `idx_timeend` (`timeend`),
  KEY `idx_timelimit` (`timelimit`),
  KEY `idx_status` (`status`),
  KEY `idx_givetype` (`backtype`),
  KEY `idx_catid` (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_ewei_shop_coupon_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `displayorder` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_displayorder` (`displayorder`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_coupon_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '',
  `couponid` int(11) DEFAULT '0',
  `gettype` tinyint(3) DEFAULT '0' COMMENT '获取方式 0 发放 1 领取 2 积分商城',
  `used` int(11) DEFAULT '0',
  `usetime` int(11) DEFAULT '0',
  `gettime` int(11) DEFAULT '0' COMMENT '获取时间',
  `senduid` int(11) DEFAULT '0',
  `ordersn` varchar(255) DEFAULT '',
  `back` tinyint(3) DEFAULT '0',
  `backtime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_couponid` (`couponid`),
  KEY `idx_gettype` (`gettype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_ewei_shop_coupon_guess` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `couponid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '',
  `times` int(11) DEFAULT '0',
  `pwdkey` varchar(255) DEFAULT '',
  `ok` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_couponid` (`couponid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_ewei_shop_coupon_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `logno` varchar(255) DEFAULT '',
  `openid` varchar(255) DEFAULT '',
  `couponid` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '0',
  `paystatus` tinyint(3) DEFAULT '0',
  `creditstatus` tinyint(3) DEFAULT '0',
  `createtime` int(11) DEFAULT '0',
  `paytype` tinyint(3) DEFAULT '0',
  `getfrom` tinyint(3) DEFAULT '0' COMMENT '0 发放 1 中心 2 积分兑换',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_couponid` (`couponid`),
  KEY `idx_status` (`status`),
  KEY `idx_paystatus` (`paystatus`),
  KEY `idx_createtime` (`createtime`),
  KEY `idx_getfrom` (`getfrom`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_ewei_shop_diyform_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0' COMMENT '所属帐号',
  `name` varchar(50) DEFAULT NULL COMMENT '分类名称',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;


CREATE TABLE  IF NOT EXISTS `ims_ewei_shop_diyform_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `typeid` int(11) NOT NULL DEFAULT '0' COMMENT '类型id',
  `cid` int(11) DEFAULT '0' COMMENT '关联id',
  `diyformfields` text,
  `fields` text NOT NULL COMMENT '字符集',
  `openid` varchar(255) NOT NULL DEFAULT '' COMMENT '使用者openid',
  `type` tinyint(2) DEFAULT '0' COMMENT '该数据所属模块',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_typeid` (`typeid`),
  KEY `idx_cid` (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;


CREATE TABLE  IF NOT EXISTS `ims_ewei_shop_diyform_temp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `typeid` int(11) DEFAULT '0',
  `cid` int(11) NOT NULL DEFAULT '0' COMMENT '关联id',
  `diyformfields` text,
  `fields` text NOT NULL COMMENT '字符集',
  `openid` varchar(255) NOT NULL DEFAULT '' COMMENT '使用者openid',
  `type` tinyint(1) DEFAULT '0' COMMENT '类型',
  `diyformid` int(11) DEFAULT '0',
  `diyformdata` text,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_cid` (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

CREATE TABLE  IF NOT EXISTS `ims_ewei_shop_diyform_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `cate` int(11) DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '分类名称',
  `fields` text NOT NULL COMMENT '字段集',
  `usedata` int(11) NOT NULL DEFAULT '0' COMMENT '已用数据',
  `alldata` int(11) NOT NULL DEFAULT '0' COMMENT '全部数据',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_cate` (`cate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `ims_ewei_shop_postera` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `type` tinyint(3) DEFAULT '0' COMMENT '1 首页 2 小店 3 商城 4 自定义',
  `days` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT '',
  `bg` varchar(255) DEFAULT '',
  `data` text,
  `keyword` varchar(255) DEFAULT '',
  `isdefault` tinyint(3) DEFAULT '0',
  `resptitle` varchar(255) DEFAULT '',
  `respthumb` varchar(255) DEFAULT '',
  `createtime` int(11) DEFAULT '0',
  `respdesc` varchar(255) DEFAULT '',
  `respurl` varchar(255) DEFAULT '',
  `waittext` varchar(255) DEFAULT '',
  `oktext` varchar(255) DEFAULT '',
  `subcredit` int(11) DEFAULT '0',
  `submoney` decimal(10,2) DEFAULT '0.00',
  `reccredit` int(11) DEFAULT '0',
  `recmoney` decimal(10,2) DEFAULT '0.00',
  `scantext` varchar(255) DEFAULT '',
  `subtext` varchar(255) DEFAULT '',
  `beagent` tinyint(3) DEFAULT '0',
  `bedown` tinyint(3) DEFAULT '0',
  `isopen` tinyint(3) DEFAULT '0',
  `opentext` varchar(255) DEFAULT '',
  `openurl` varchar(255) DEFAULT '',
  `paytype` tinyint(1) NOT NULL DEFAULT '0',
  `subpaycontent` text,
  `recpaycontent` varchar(255) DEFAULT '',
  `templateid` varchar(255) DEFAULT '',
  `entrytext` varchar(255) DEFAULT '',
  `reccouponid` int(11) DEFAULT '0',
  `reccouponnum` int(11) DEFAULT '0',
  `subcouponid` int(11) DEFAULT '0',
  `subcouponnum` int(11) DEFAULT '0',
  `timestart` int(11) DEFAULT '0',
  `timeend` int(11) DEFAULT '0',
  `status` tinyint(3) DEFAULT '0',
  `goodsid` int(11) DEFAULT '0',
  `starttext` varchar(255) DEFAULT '',
  `endtext` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_type` (`type`),
  KEY `idx_isdefault` (`isdefault`),
  KEY `idx_createtime` (`createtime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS  `ims_ewei_shop_postera_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '',
  `posterid` int(11) DEFAULT '0',
  `from_openid` varchar(255) DEFAULT '',
  `subcredit` int(11) DEFAULT '0',
  `submoney` decimal(10,2) DEFAULT '0.00',
  `reccredit` int(11) DEFAULT '0',
  `recmoney` decimal(10,2) DEFAULT '0.00',
  `createtime` int(11) DEFAULT '0',
  `reccouponid` int(11) DEFAULT '0',
  `reccouponnum` int(11) DEFAULT '0',
  `subcouponid` int(11) DEFAULT '0',
  `subcouponnum` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_createtime` (`createtime`),
  KEY `idx_posteraid` (`posterid`),
  FULLTEXT KEY `idx_from_openid` (`from_openid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS  `ims_ewei_shop_postera_qr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `acid` int(10) unsigned NOT NULL,
  `openid` varchar(100) NOT NULL DEFAULT '',
  `posterid` int(11) DEFAULT '0',
  `type` tinyint(3) DEFAULT '0',
  `sceneid` int(11) DEFAULT '0',
  `mediaid` varchar(255) DEFAULT '',
  `ticket` varchar(250) NOT NULL,
  `url` varchar(80) NOT NULL,
  `createtime` int(10) unsigned NOT NULL,
  `goodsid` int(11) DEFAULT '0',
  `qrimg` varchar(1000) DEFAULT '',
  `expire` int(11) DEFAULT '0',
  `endtime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_acid` (`acid`),
  KEY `idx_sceneid` (`sceneid`),
  KEY `idx_type` (`type`),
  KEY `idx_posterid` (`posterid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_system_copyright` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `copyright` text,
  `bgcolor` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_ewei_shop_exhelper_express` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '单据分类 1为快递单 2为发货单',
  `expressname` varchar(255) DEFAULT '',
  `expresscom` varchar(255) NOT NULL DEFAULT '',
  `express` varchar(255) NOT NULL DEFAULT '',
  `width` decimal(10,2) DEFAULT '0.00',
  `datas` text,
  `height` decimal(10,2) DEFAULT '0.00',
  `bg` varchar(255) DEFAULT '',
  `isdefault` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_isdefault` (`isdefault`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_ewei_shop_exhelper_senduser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `sendername` varchar(255) DEFAULT '' COMMENT '发件人',
  `sendertel` varchar(255) DEFAULT '' COMMENT '发件人联系电话',
  `sendersign` varchar(255) DEFAULT '' COMMENT '发件人签名',
  `sendercode` int(11) DEFAULT NULL COMMENT '发件地址邮编',
  `senderaddress` varchar(255) DEFAULT '' COMMENT '发件地址',
  `sendercity` varchar(255) DEFAULT NULL COMMENT '发件城市',
  `isdefault` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_isdefault` (`isdefault`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_ewei_shop_exhelper_sys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(20) NOT NULL DEFAULT 'localhost',
  `port` int(11) NOT NULL DEFAULT '8000',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

";
pdo_query($sql);

 if(!pdo_fieldexists('ewei_shop_goods', 'isnodiscount')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD   `isnodiscount` tinyint(3) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'showlevels')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD  `showlevels` text;");
}

 if(!pdo_fieldexists('ewei_shop_goods', 'buylevels')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD    `buylevels` text;");
}

 if(!pdo_fieldexists('ewei_shop_goods', 'showgroups')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD   `showgroups` text;");
}

 if(!pdo_fieldexists('ewei_shop_goods', 'buygroups')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD   `buygroups` text;");
}

 if(!pdo_fieldexists('ewei_shop_goods', 'isverify')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD  `isverify` tinyint(3) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'noticeopenid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD   `noticeopenid` text;");
}

 if(!pdo_fieldexists('ewei_shop_goods', 'storeids')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD   `storeids` text;");
}

 if(!pdo_fieldexists('ewei_shop_order_goods', 'realprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." ADD    `realprice` decimal(10,2) DEFAULT '0.00';");
}
 if(!pdo_fieldexists('ewei_shop_order', 'isverify')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD  `isverify` tinyint(3) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_order', 'verified')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD   `verified` tinyint(3) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_order', 'verifyopenid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD   `verifyopenid` varchar(255) DEFAULT '';");
}
 if(!pdo_fieldexists('ewei_shop_order', 'verifytime')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD   `verifytime` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_order', 'verifystoreid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD   `verifystoreid` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_order', 'verifycode')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD    `verifycode` varchar(255) DEFAULT '';");
}
 if(!pdo_fieldexists('ewei_shop_member', 'childtime')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." ADD   `childtime` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_member', 'inviter')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." ADD    `inviter` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_order_goods', 'realprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." ADD     `realprice` decimal(10,2) DEFAULT '0.00';");
}
//0818
 if(!pdo_fieldexists('ewei_shop_category', 'advimg')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_category')." ADD     `advimg` varchar(255) DEFAULT '';");
}
 if(!pdo_fieldexists('ewei_shop_category', 'advurl')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_category')." ADD    `advurl` varchar(500) DEFAULT '';");
}
 if(!pdo_fieldexists('ewei_shop_category', 'level')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_category')." ADD   `level` tinyint(3) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'tcate')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD    `tcate` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'noticetype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD     `noticetype` tinyint(3) DEFAULT '0';");
}

 if(!pdo_fieldexists('ewei_shop_goods_option', 'goodssn')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods_option')." ADD `goodssn` varchar(255) DEFAULT '';");
}
 if(!pdo_fieldexists('ewei_shop_goods_option', 'productsn')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods_option')." ADD `productsn` varchar(255) DEFAULT '';");
}

 if(!pdo_fieldexists('ewei_shop_member', 'agentnotupgrade')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." ADD   `agentnotupgrade` tinyint(3) DEFAULT '0';");
}

 if(pdo_fieldexists('ewei_shop_order', 'verifycode')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." CHANGE   `verifycode`  `verifycode` text;");
}

 if(!pdo_fieldexists('ewei_shop_order_goods', 'goodssn')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." ADD    `goodssn` varchar(255) DEFAULT '';");
}
 if(!pdo_fieldexists('ewei_shop_order_goods', 'productsn')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." ADD     `productsn` varchar(255) DEFAULT '';");
}

 if(!pdo_fieldexists('ewei_shop_poster', 'entrytext')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster')." ADD     `entrytext` varchar(255) DEFAULT '';");
}
//0910
 if(!pdo_fieldexists('ewei_shop_commission_shop', 'selectgoods')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_commission_shop')." ADD     `selectgoods` tinyint(3) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_commission_shop', 'selectcategory')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_commission_shop')." ADD    `selectcategory` tinyint(3) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_commission_shop', 'goodsids')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_commission_shop')." ADD   `goodsids` text;");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'needfollow')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD  `needfollow` tinyint(3) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'followtip')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD    `followtip` varchar(255) DEFAULT '';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'followurl')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD    `followurl` varchar(255) DEFAULT '';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'deduct')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD    `deduct` decimal(10,2) DEFAULT '0.00';");
}
 if(!pdo_fieldexists('ewei_shop_member', 'agentselectgoods')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." ADD    `agentselectgoods` tinyint(3) DEFAULT '0';");
}

 if(!pdo_fieldexists('ewei_shop_order', 'deductprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD     `deductprice` decimal(10,2) DEFAULT '0.00';");
}
 if(!pdo_fieldexists('ewei_shop_order', 'deductcredit')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD      `deductcredit` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_order', 'deductcredit2')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD      `deductcredit2` decimal(10,2) DEFAULT '0.00';");
}
 if(!pdo_fieldexists('ewei_shop_order', 'deductenough')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD     `deductenough` decimal(10,2) DEFAULT '0.00';");
}
 if(!pdo_fieldexists('ewei_shop_order_refund', 'refundtype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_refund')." ADD     `refundtype` tinyint(3) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_plugin', 'status')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_plugin')." ADD     `status` tinyint(3) DEFAULT '0';");
}
//1016 add
 if(pdo_fieldexists('ewei_shop_goods', 'noticetype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." CHANGE   `noticetype`   `noticetype` text;");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'virtual')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD   `virtual` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'cates')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD   `cates` text;");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'discounts')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD  `discounts` text;");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'nocommission')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD   `nocommission` tinyint(3) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'hidecommission')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD   `hidecommission` tinyint(3) DEFAULT '0';");
}

 if(!pdo_fieldexists('ewei_shop_order', 'virtual')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD   `virtual` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_order', 'virtual_info')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD     `virtual_info` text;");
}
 if(!pdo_fieldexists('ewei_shop_order', 'virtual_str')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD     `virtual_str` text;");
}

 if(!pdo_fieldexists('ewei_shop_sysset', 'sec')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_sysset')." ADD    `sec` text;");
}
 if(!pdo_fieldexists('ewei_shop_goods_option', 'virtual')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods_option')." ADD   `virtual` int(11) DEFAULT '0';");
}

 if(!pdo_fieldexists('ewei_shop_goods_spec_item', 'virtual')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods_spec_item')." ADD    `virtual` int(11) DEFAULT '0';");
}

 if(!pdo_fieldexists('ewei_shop_member', 'agentblack')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." ADD    `agentblack` tinyint(3) DEFAULT '0';");
}

 if(!pdo_fieldexists('ewei_shop_goods', 'ccates')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD `ccates` text;");
}

 if(!pdo_fieldexists('ewei_shop_goods', 'pcates')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD   `pcates` text;");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'tcates')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD    `tcates` text;");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'artid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD    `artid` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'detail_logo')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD   `detail_logo` varchar(255) DEFAULT '';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'detail_shopname')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD   `detail_shopname` varchar(255) DEFAULT '';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'detail_btntext1')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD  `detail_btntext1` varchar(255) DEFAULT '';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'detail_btnurl1')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD   `detail_btnurl1` varchar(255) DEFAULT '';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'detail_btntext2')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD   `detail_btntext2` varchar(255) DEFAULT '';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'detail_btnurl2')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD  `detail_btnurl2` varchar(255) DEFAULT '';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'detail_totaltitle')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD   `detail_totaltitle` varchar(255) DEFAULT '';");
}

 if(!pdo_fieldexists('ewei_shop_order', 'address')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD  `address` text;");
}
 if(!pdo_fieldexists('ewei_shop_order', 'sysdeleted')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD  `sysdeleted` tinyint(3) DEFAULT '0';");
}

 if(!pdo_fieldexists('ewei_shop_order_goods', 'nocommission')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." ADD   `nocommission` tinyint(3) DEFAULT '0';");
}
if(pdo_fieldexists('ewei_shop_goods', 'credit')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." CHANGE  `credit` `credit` varchar(255) DEFAULT NULL;");
}

if(!pdo_fieldexists('ewei_shop_poster_qr', 'scenestr')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster_qr')." ADD   `scenestr` varchar(255) DEFAULT '';");
}

 if(!pdo_fieldexists('ewei_shop_order_goods', 'changeprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." ADD  `changeprice` decimal(10,2) DEFAULT '0.00';");
}
 if(!pdo_fieldexists('ewei_shop_order_goods', 'oldprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." ADD   `oldprice` decimal(10,2) DEFAULT '0.00';");
}
 if(!pdo_fieldexists('ewei_shop_order_goods', 'commissions')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." ADD    `commissions` text;");
}

 if(!pdo_fieldexists('ewei_shop_order', 'ordersn2')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD  `ordersn2` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_order', 'changeprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD    `changeprice` decimal(10,2) DEFAULT '0.00';");
}
 if(!pdo_fieldexists('ewei_shop_order', 'changedispatchprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD    `changedispatchprice` decimal(10,2) DEFAULT '0.00';");
}
 if(!pdo_fieldexists('ewei_shop_order', 'oldprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD   `oldprice` decimal(10,2) DEFAULT '0.00';");
}
 if(!pdo_fieldexists('ewei_shop_order', 'olddispatchprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD    `olddispatchprice` decimal(10,2) DEFAULT '0.00';");
}
 if(!pdo_fieldexists('ewei_shop_order', 'isvirtual')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD    `isvirtual` tinyint(3) DEFAULT '0';");
}

 if(!pdo_fieldexists('ewei_shop_member_log', 'gives')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member_log')." ADD   `gives` decimal(10,2) DEFAULT NULL;");
}
 if(!pdo_fieldexists('ewei_shop_creditshop_log', 'storeid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_creditshop_log')." ADD   `storeid` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_creditshop_log', 'realname')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_creditshop_log')." ADD    `realname` varchar(255) DEFAULT '';");
}
 if(!pdo_fieldexists('ewei_shop_creditshop_log', 'mobile')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_creditshop_log')." ADD   `mobile` varchar(255) DEFAULT '';");
}
 if(pdo_fieldexists('ewei_shop_commission_apply', 'orderids')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_commission_apply')." CHANGE `orderids`  `orderids` longtext;");
}
 if(!pdo_fieldexists('ewei_shop_commission_level', 'downcount')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_commission_level')." ADD     `downcount` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_commission_level', 'ordercount')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_commission_level')." ADD     `ordercount` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_creditshop_goods', 'goodstype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_creditshop_goods')." ADD   `goodstype` tinyint(3) DEFAULT '0';");
}

 if(!pdo_fieldexists('ewei_shop_creditshop_goods', 'couponid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_creditshop_goods')." ADD    `couponid` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_creditshop_log', 'couponid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_creditshop_log')." ADD     `couponid` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_creditshop_log', 'dupdate1')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_creditshop_log')." ADD      `dupdate1` tinyint(3) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_dispatch', 'calculatetype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_dispatch')." ADD     `calculatetype` tinyint(1) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_dispatch', 'firstnum')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_dispatch')." ADD    `firstnum` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_dispatch', 'secondnum')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_dispatch')." ADD    `secondnum` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_dispatch', 'firstnumprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_dispatch')." ADD     `firstnumprice` decimal(10,2) DEFAULT '0.00';");
}
 if(!pdo_fieldexists('ewei_shop_dispatch', 'secondnumprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_dispatch')." ADD     `secondnumprice` decimal(10,2) DEFAULT '0.00';");
}
 if(!pdo_fieldexists('ewei_shop_dispatch', 'isdefault')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_dispatch')." ADD     `isdefault` tinyint(1) DEFAULT '0';");
}

 if(!pdo_fieldexists('ewei_shop_goods', 'saleupdate')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD       `saleupdate` tinyint(3) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'deduct2')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD     `deduct2` decimal(10,2) DEFAULT '0.00';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'ednum')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD      `ednum` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'edmoney')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD     `edmoney` decimal(10,2) DEFAULT '0.00';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'edareas')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD     `edareas` text;");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'diyformtype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD      `diyformtype` tinyint(3) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'diyformid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD       `diyformid` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'diymode')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD       `diymode` tinyint(3) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'dispatchtype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD      `dispatchtype` tinyint(1) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'dispatchid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD      `dispatchid` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'dispatchprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD     `dispatchprice` decimal(10,2) DEFAULT '0.00';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'manydeduct')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD     `manydeduct` tinyint(1) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_member', 'fixagentid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." ADD     `fixagentid` tinyint(3) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_member', 'diymemberid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." ADD      `diymemberid` int(11) DEFAULT '0';");
} 
if(!pdo_fieldexists('ewei_shop_member', 'diymemberfields')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." ADD       `diymemberfields` text;");
}
 if(!pdo_fieldexists('ewei_shop_member', 'diymemberdata')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." ADD      `diymemberdata` text;");
}
 if(!pdo_fieldexists('ewei_shop_member', 'diymemberdataid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." ADD     `diymemberdataid` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_member', 'diycommissionid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." ADD       `diycommissionid` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_member', 'diycommissionfields')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." ADD       `diycommissionfields` text;");
}
 if(!pdo_fieldexists('ewei_shop_member', 'diycommissiondata')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." ADD      `diycommissiondata` text;");
}
 if(!pdo_fieldexists('ewei_shop_member', 'diycommissiondataid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." ADD       `diycommissiondataid` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_member', 'isblack')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." ADD      `isblack` tinyint(3) DEFAULT '0';");
}

 if(!pdo_fieldexists('ewei_shop_member_cart', 'diyformdataid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member_cart')." ADD      `diyformdataid` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_member_cart', 'diyformdata')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member_cart')." ADD       `diyformdata` text;");
}
 if(!pdo_fieldexists('ewei_shop_member_cart', 'diyformfields')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member_cart')." ADD        `diyformfields` text;");
}
 if(!pdo_fieldexists('ewei_shop_member_cart', 'diyformid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member_cart')." ADD       `diyformid` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_member_log', 'couponid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member_log')." ADD    `couponid` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_order', 'couponid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD    `couponid` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_order', 'couponprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD      `couponprice` decimal(10,2) DEFAULT '0.00';");
}
 if(!pdo_fieldexists('ewei_shop_order', 'diyformdata')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD     `diyformdata` text;");
}
 if(!pdo_fieldexists('ewei_shop_order', 'diyformfields')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD      `diyformfields` text;");
}
 if(!pdo_fieldexists('ewei_shop_order', 'diyformid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD     `diyformid` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_order', 'storeid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD      `storeid` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_order_goods', 'diyformid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." ADD     `diyformid` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_order_goods', 'diyformdataid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." ADD       `diyformdataid` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_order_goods', 'diyformdata')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." ADD       `diyformdata` text;");
}
 if(!pdo_fieldexists('ewei_shop_order_goods', 'diyformfields')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." ADD      `diyformfields` text;");
}
 if(!pdo_fieldexists('ewei_shop_order_goods', 'openid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." ADD    `openid` varchar(255) DEFAULT NULL;");
}
 if(!pdo_fieldexists('ewei_shop_plugin', 'category')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_plugin')." ADD    `category` varchar(255) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_poster', 'reccouponid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster')." ADD    `reccouponid` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_poster', 'reccouponnum')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster')." ADD     `reccouponnum` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_poster', 'subcouponid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster')." ADD    `subcouponid` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_poster', 'subcouponnum')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster')." ADD   `subcouponnum` int(11) DEFAULT '0';");
}

 if(!pdo_fieldexists('ewei_shop_poster_log', 'reccouponid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster_log')." ADD    `reccouponid` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_poster_log', 'reccouponnum')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster_log')." ADD     `reccouponnum` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_poster_log', 'subcouponid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster_log')." ADD    `subcouponid` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_poster_log', 'subcouponnum')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster_log')." ADD   `subcouponnum` int(11) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_saler', 'salername')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_saler')." ADD   `salername` varchar(255) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_store', 'realname')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_store')." ADD    `realname` varchar(255) DEFAULT '';");
}
 if(!pdo_fieldexists('ewei_shop_store', 'mobile')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_store')." ADD    `mobile` varchar(255) DEFAULT '';");
}
 if(!pdo_fieldexists('ewei_shop_store', 'fetchtime')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_store')." ADD     `fetchtime` varchar(255) DEFAULT '';");
}
 if(!pdo_fieldexists('ewei_shop_store', 'type')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_store')." ADD    `type` tinyint(1) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_goods', 'shorttitle')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD      `shorttitle` varchar(255) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_order', 'printstate2')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD     `printstate2` tinyint(1) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_order', 'printstate')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD      `printstate` tinyint(1) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_order', 'address_send')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD      `address_send` text;");
}
 if(!pdo_fieldexists('ewei_shop_order_goods', 'printstate2')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." ADD     `printstate2` tinyint(1) DEFAULT '0';");
}
 if(!pdo_fieldexists('ewei_shop_order_goods', 'printstate')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." ADD      `printstate` tinyint(1) DEFAULT '0';");
}