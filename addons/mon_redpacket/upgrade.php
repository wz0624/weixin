<?php







$sql="CREATE TABLE IF NOT EXISTS ".tablename('redpacket_award')." (
  `id` int(11)  AUTO_INCREMENT,
  `pid` int(11) DEFAULT 0 ,
  `point` decimal(10,2) default 0,
  `name` varchar(255) default '',
  `num` int(11) default 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8; ";
pdo_query($sql);






if(!pdo_fieldexists('redpacket_user', 'name')) {
	pdo_query("ALTER TABLE ".tablename('redpacket_user')." ADD `name` varchar(100) NOT NULL COMMENT '姓名' ;");
}

if(!pdo_fieldexists('redpacket_user', 'name')) {
	pdo_query("ALTER TABLE ".tablename('redpacket_user')." ADD `name` varchar(100) NOT NULL COMMENT '姓名' ;");
}


if(!pdo_fieldexists('redpacket_user', 'status')) {
    pdo_query("ALTER TABLE ".tablename('redpacket_user')." ADD `status` int(10) DEFAULT '0' ;");
}


if(!pdo_fieldexists('redpacket_user', 'awardid')) {
    pdo_query("ALTER TABLE ".tablename('redpacket_user')." ADD `awardid` int(10) DEFAULT '0' ;");
}


if(!pdo_fieldexists('redpacket_user', 'awardtime')) {
    pdo_query("ALTER TABLE ".tablename('redpacket_user')." ADD `awardtime` int(10) DEFAULT '0' ;");
}



if(!pdo_fieldexists('redpacket_user', 'virtual')) {
    pdo_query("ALTER TABLE ".tablename('redpacket_user')." ADD `virtual` int (1) DEFAULT '0' ;");
}







if(!pdo_fieldexists('redpacket', 'shareImg')) {
	pdo_query("ALTER TABLE ".tablename('redpacket')." ADD shareImg VARCHAR(200) NOT NULL ;");
}

if(!pdo_fieldexists('redpacket', 'cardbg')) {
	pdo_query("ALTER TABLE ".tablename('redpacket')." ADD `cardbg` VARCHAR(200) NOT NULL ;");
}


if(!pdo_fieldexists('redpacket', 'sortcount')) {
	pdo_query("ALTER TABLE ".tablename('redpacket')." ADD `sortcount` INT(5) DEFAULT 10 ;");
}

if(!pdo_fieldexists('redpacket', 'sharebtn')) {
	pdo_query("ALTER TABLE ".tablename('redpacket')." ADD `sharebtn`  VARCHAR(10) NOT NULL default '邀请好友帮我攒钱';");
}

if(!pdo_fieldexists('redpacket', 'fsharebtn')) {
	pdo_query("ALTER TABLE ".tablename('redpacket')." ADD `fsharebtn` VARCHAR(10) NOT NULL default '邀请好友帮他攒钱' ;");
}







if(!pdo_fieldexists('redpacket', 'shareTitle')) {
	pdo_query("ALTER TABLE ".tablename('redpacket')." ADD shareTitle VARCHAR(200) NOT NULL ;");
}


if(!pdo_fieldexists('redpacket', 'shareContent')) {
	pdo_query("ALTER TABLE ".tablename('redpacket')." ADD shareContent VARCHAR(200) NOT NULL ;");
}

if(!pdo_fieldexists('redpacket', 'daylimit')) {
    pdo_query("ALTER TABLE ".tablename('redpacket')." ADD daylimit INT(5) DEFAULT 0 ;");
}
if(!pdo_fieldexists('redpacket', 'totallimit')) {
    pdo_query("ALTER TABLE ".tablename('redpacket')." ADD totallimit INT(5) DEFAULT 0 ;");
}

if(!pdo_fieldexists('redpacket', 'limitType')) {
    pdo_query("ALTER TABLE ".tablename('redpacket')." ADD limitType INT(1) DEFAULT 0 ;");
}



if(!pdo_fieldexists('redpacket', 'start')) {
    pdo_query("ALTER TABLE ".tablename('redpacket')." ADD `start` float(10,2) unsigned NOT NULL DEFAULT '0' COMMENT '初始分值' ;");
}


if(!pdo_fieldexists('redpacket', 'step')) {
    pdo_query("ALTER TABLE ".tablename('redpacket')." ADD `step` float(10,2) unsigned NOT NULL DEFAULT '0' COMMENT '固定金额' ;");
}


if(!pdo_fieldexists('redpacket', 'steprandom')) {
    pdo_query("ALTER TABLE ".tablename('redpacket')." ADD `steprandom` float(10,2) unsigned NOT NULL DEFAULT '0' COMMENT '随机金额' ;");
}

if(!pdo_fieldexists('redpacket', 'steptype')) {
    pdo_query("ALTER TABLE ".tablename('redpacket')." ADD `steptype` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '随机金额' ;");
}

if(!pdo_fieldexists('redpacket', 'addp')) {
    pdo_query("ALTER TABLE ".tablename('redpacket')." ADD `addp` int (5) NOT NULL DEFAULT 100 ;");
}

if(!pdo_fieldexists('redpacket', 'packetsummary')) {
    pdo_query("ALTER TABLE ".tablename('redpacket')." ADD `packetsummary` VARCHAR(100) NOT NULL COMMENT '活动摘要' ;");
}

if(!pdo_fieldexists('redpacket', 'sharetip')) {
    pdo_query("ALTER TABLE ".tablename('redpacket')." ADD `sharetip` VARCHAR(100) NOT NULL ;");
}

if(!pdo_fieldexists('redpacket', 'fanpaitip')) {
    pdo_query("ALTER TABLE ".tablename('redpacket')." ADD `fanpaitip` VARCHAR(100) NOT NULL ;");
}




if(!pdo_fieldexists('redpacket', 'carebtn')) {
    pdo_query("ALTER TABLE ".tablename('redpacket')." ADD `carebtn` VARCHAR(100) NOT NULL ;");
}
if(!pdo_fieldexists('redpacket', 'awardtip')) {
    pdo_query("ALTER TABLE ".tablename('redpacket')." ADD `awardtip`	VARCHAR(200) NOT NULL ;");
}


if(!pdo_fieldexists('redpacket_firend', 'income')) {
	pdo_query("ALTER TABLE ".tablename('redpacket_firend')." ADD `income` float(10,2) unsigned NOT NULL DEFAULT '0' ;");
}





///1.5 版本

if(!pdo_fieldexists('redpacket', 'fanpaiurl')) {
    pdo_query("ALTER TABLE ".tablename('redpacket')." ADD fanpaiurl VARCHAR(500)  ;");
}


if(!pdo_fieldexists('redpacket', 'fanpaimustfollow')) {
    pdo_query("ALTER TABLE ".tablename('redpacket')." ADD fanpaimustfollow int (1)  ;");
}


pdo_query("ALTER TABLE ".tablename('redpacket')."  modify column packetsummary varchar(1000);");

pdo_query("ALTER TABLE ".tablename('redpacket')."  modify column sharetip varchar(1000);");

pdo_query("ALTER TABLE ".tablename('redpacket')." modify column  sharetip varchar(1000);");


