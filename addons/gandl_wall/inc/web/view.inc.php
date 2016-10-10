<?php
//session_start();
//$_SESSION['__:proxy:openid'] = 'oyIjYt9lQx9flMXl9F9NiAqrJd3g';
//debug
global $_W, $_GPC;



$wall_id=$_GPC['wall_id'];
if(empty($wall_id)){
	returnError("请选择圈子");
}

$wall = pdo_fetch("select * from " . tablename('gandl_wall') . " where uniacid=:uniacid and id=:id ", array(':uniacid' => $_W['uniacid'],':id' => $wall_id));
if(empty($wall)) {
	message('抱歉，没有相关数据！', '', 'error');
}

// 此处数据统计全是实际数据


/**
撒钱人数 u
撒钱次数 i
撒钱总额 t
收入总额 p
**/
$static_piece = pdo_fetch("select COUNT(id) as i, COUNT(DISTINCT(user_id)) AS u, SUM(total_amount) AS t, SUM(pay) AS p from " . tablename('gandl_wall_piece') . " where uniacid=:uniacid AND wall_id=:wall_id AND status>0", array(':uniacid' => $_W['uniacid'],':wall_id' => $wall['id']));

/**
已抢人数 u
已抢次数 i
抢钱总额 m
**/
$static_rob = pdo_fetch("select COUNT(id) as i, COUNT(DISTINCT(user_id)) AS u, SUM(money) AS m from " . tablename('gandl_wall_rob') . " where uniacid=:uniacid AND wall_id=:wall_id", array(':uniacid' => $_W['uniacid'],':wall_id' => $wall['id']));

/**
提现人数 u
提现次数 i
提现总额 m
**/
$static_transfer = pdo_fetch("select COUNT(id) as i, COUNT(DISTINCT(user_id)) AS u, SUM(money) AS m from " . tablename('gandl_wall_user_transfer') . " where uniacid=:uniacid AND wall_id=:wall_id AND status=1", array(':uniacid' => $_W['uniacid'],':wall_id' => $wall['id']));

/**
总人数		static_user
未提现人数  i
未提现总额  m
**/
$static_user = pdo_fetchcolumn("select COUNT(id) from " . tablename('gandl_wall_user') . " where uniacid=:uniacid AND wall_id=:wall_id", array(':uniacid' => $_W['uniacid'],':wall_id' => $wall['id']));
$static_user2 = pdo_fetch("select id AS i, SUM(money) AS m from " . tablename('gandl_wall_user') . " where uniacid=:uniacid AND wall_id=:wall_id AND money>0", array(':uniacid' => $_W['uniacid'],':wall_id' => $wall['id']));

/**
支出总额 = 提现总额+未提现总额
净利润 = 收入总额-支出总额
**/


load()->func('tpl');
include $this->template('web/view');




?>