<?php
//session_start();
//$_SESSION['__:proxy:openid'] = 'oyIjYt9lQx9flMXl9F9NiAqrJd3g';
//debug
global $_W, $_GPC;


$where = '';
$params = array(':uniacid' => $_W['uniacid']);
if (isset($_GPC['state'])) { // 1 进行中 2 未开始 3 已结束
	$state=intval($_GPC['state']);
	if('1'==$state){
		$where.=' and a.start_time <= :nowtime and a.end_time >= :nowtime';
	}else if('2'==$state){
		$where.=' and a.start_time > :nowtime';
	}else if('3'==$state){
		$where.=' and a.end_time < :nowtime';
	}

	$params[':nowtime'] = TIMESTAMP;
}

$total = pdo_fetchcolumn("select count(a.id) from " . tablename('gandl_wall') . " a where a.uniacid=:uniacid " . $where . "", $params);
$pindex = max(1, intval($_GPC['page']));
$psize = 12;
$pager = pagination($total, $pindex, $psize);
$start = ($pindex - 1) * $psize;
$limit .= " LIMIT {$start},{$psize}";
$list = pdo_fetchall("select a.* from " . tablename('gandl_wall') . " a where a.uniacid=:uniacid  " . $where . " order by a.id desc " . $limit, $params);
for($i=0;$i<count($list);$i++){
	// 处理活动状态
	if($list[$i]['start_time'] <= TIMESTAMP && $list[$i]['end_time'] >= TIMESTAMP){
		$list[$i]['state']=1;
	}else if($list[$i]['start_time'] > TIMESTAMP){
		$list[$i]['state']=2;
	}else if($list[$i]['end_time'] < TIMESTAMP){
		$list[$i]['state']=3;
	}
	// 处理活动入口
	$url = $this->createMobileUrl('index', array('pid' => pencode($list[$i]['id'])));
	$list[$i]['surl'] = $url;
	$url = substr($url, 2);
	$url = $_W['siteroot'] . 'app/' . $url;
	$list[$i]['url'] = $url;
}

include $this->template('web/list');
?>