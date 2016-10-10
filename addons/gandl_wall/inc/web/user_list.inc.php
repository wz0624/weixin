<?php
//session_start();
//$_SESSION['__:proxy:openid'] = 'oyIjYt9lQx9flMXl9F9NiAqrJd3g';
//debug
global $_W, $_GPC;



$wall_id=$_GPC['wall_id'];
if(empty($wall_id)){
	returnError('请选择圈子');
}
$wall = pdo_fetch("select * from " . tablename('gandl_wall') . "  where uniacid=".$_W['uniacid']." and id=".$wall_id." ");
if(empty($wall)){
	returnError('圈子不存在');
}

$search=$_GPC['search'];
$uids=array();
if(!empty($search)){
	$us = pdo_fetchall("select uid from " . tablename('mc_members') . " where uniacid=:uniacid and  nickname LIKE :nickname ", array(':uniacid' => $_W['uniacid'],':nickname' => '%'.$search.'%'));
	if(empty($us) || count($us)==0){
		returnError('查无此人');
	}
	foreach($us as $u){
		$uids[]=$u['uid'];
	}
}



$where = ' and wall_id=:wall_id ';
$params = array(':uniacid' => $_W['uniacid'],':wall_id' => $wall_id);


if (isset($_GPC['status'])) { // 0 全部 2 黑名单
	$status=intval($_GPC['status']);
	if('2'==$status){
		$where.=' and black=1 ';
	}
}

$order = ' order by id desc ';
if (isset($_GPC['sort'])) { // 0 全部 2 黑名单
	$order = ' order by '.$_GPC['sort'].' DESC ';
}


if(count($uids)>0){
	$where.=' and user_id IN('.implode(',',$uids).') ';
}


$total = pdo_fetchcolumn("select count(id) from " . tablename('gandl_wall_user') . "  where uniacid=:uniacid " . $where . "", $params);
$pindex = max(1, intval($_GPC['page']));
$psize = 12;
$pager = pagination($total, $pindex, $psize);
$start = ($pindex - 1) * $psize;
$limit .= " LIMIT {$start},{$psize}";
$list = pdo_fetchall("select * from " . tablename('gandl_wall_user') . "  where uniacid=:uniacid  " . $where . $order . $limit, $params);

load()->model('mc');

for($i=0;$i<count($list);$i++){
	$list[$i]['_fan']=mc_fansinfo($list[$i]['user_id']);
	$list[$i]['_user']= mc_fetch($list[$i]['user_id'], array('nickname', 'avatar'));
}


include $this->template('web/user_list');
?>