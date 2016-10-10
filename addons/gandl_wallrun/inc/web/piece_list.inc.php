<?php
//session_start();
//$_SESSION['__:proxy:openid'] = 'oyIjYt9lQx9flMXl9F9NiAqrJd3g';
//debug
global $_W, $_GPC;


$wall_id=$_GPC['wall_id'];
if(empty($wall_id)){
	returnError("请选择圈子");
}

$where = ' where uniacid=:uniacid and wall_id=:wall_id ';
$params = array(':uniacid' => $_W['uniacid'],':wall_id'=> $wall_id);

if (isset($_GPC['state'])) { // 1 未开始的 2 进行中的 3 已结束的
	$state=intval($_GPC['state']);
	if('1'==$state){
		$where.=' and status=1 and rob_start_time > :nowtime ';
		$params[':nowtime'] = TIMESTAMP;
	}else if('2'==$state){
		$where.=' and status=1 and rob_start_time <= :nowtime ';
		$params[':nowtime'] = TIMESTAMP;
	}else if('3'==$state){
		$where.=' and status=2 ';
	}
}else{
	$where.=' and status>0 ';
}

$total = pdo_fetchcolumn("select count(id) from " . tablename('gandl_wall_piece') . " " . $where . "", $params);
$pindex = max(1, intval($_GPC['page']));
$psize = 20;
$pager = pagination($total, $pindex, $psize);
$start = ($pindex - 1) * $psize;
$limit .= " LIMIT {$start},{$psize}";
$list = pdo_fetchall("select id,user_id,total_amount,total_num,content,images,publish_time,hot_time,top_level,rob_start_time,rob_users,views,status from " . tablename('gandl_wall_piece') . " " . $where . " order by create_time desc " . $limit, $params);


include $this->template('web/piece_list');
?>