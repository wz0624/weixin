<?php
defined('IN_IA') or exit('Access Denied');
global $_W;
$nowtime=time();
$sql="create table ims_lianhu_money_record(
		record_id int(11) auto_increment,
		uniacid int(11) default 0,
		school_id int(11) default 0,
		limit_id int(11),
		student_id int(11) comment '学生id',
		fan_id 	int(11) comment '绑定的用户id',
		uid int(11) comment '支付人的uid',
		addtime int(11),
		primary key(record_id)
	)engine=innodb charset=utf8 auto_increment=1;
	create table ims_lianhu_money_limit(
		limit_id int(11) auto_increment,
		uniacid int(11) ,
		school_id int(11) ,		
	    limit_name varchar(30) comment '限制名字',
		limit_module varchar(30) comment '限制的模块',
		limit_type tinyint(1) default 1 comment '限制类型：1=》永远；2=》每年；3=》每月',
		limit_much float(8,2) default 0.00,
		status tinyint(1) default 1 comment '状态；1=》有效；2=》失效',
		addtime int(11) default 0,
		primary key(limit_id)
	)engine=innodb charset=utf8 auto_increment=1;
";
pdo_run($sql);