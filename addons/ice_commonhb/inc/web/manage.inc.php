<?php
defined('IN_IA') or exit('Access Denied');
global  $_W,$_GPC;
load()->func("logging");
$foo = empty($_GPC['foo']) ? 'sendlist' : $_GPC['foo'];
$op = empty($_GPC['op']) ? 'sendlist' : $_GPC['op'];

	$hbid = 0;

if($op == 'sendlist'){
	

	//这个操作被定义用来呈现 发放流水

	
	$res = groupsendList($hbid);
	
	$list = $res['list'];
	
	
	$acid = intval($_W['account']['uniacid']);
	$acc = WeAccount::create($acid);
	if(empty($acc)){
	$name = $_W['account']['name'];
	$acid = pdo_fetchcolumn("select acid from ".tablename("account_wechats")." where uniacid = :uniacid and name = :name",array(":uniacid"=>$acid,":name"=>$name));
	$acc = WeAccount::create($acid);
	}
	foreach ($list as $k => $v){ 
		$openid2 = $v['openid'];
		
		//$acc = WeAccount::create($acid);
		$fan = $acc->fansQueryInfo($openid2, true);
		//$fan = $acc->fansQueryInfo($openid2, true);
		
		
		$pici = pdo_fetchcolumn("select piciid from ".tablename("ice_yzmhb_code")." where uniacid = :uniacid and  id = :codeid",array(":uniacid"=>$_W['uniacid'],":codeid"=>$v['codeid']));
		$list[$k]['pici'] = $pici;
		
		if(empty($v['nickname'])){
			 $nickname = $fan['nickname'];
			$list[$k]['nickname'] = $nickname;

		}
		if($fan['subscribe'] != 1){
			$list[$k]['issub'] = 2;
		}else{
			$list[$k]['issub'] = 1;
		}
	}
	
	
	
	$pager	= $res['pager'];
	
	
	
	include $this->template('sendlist');
	
	
	
}


function  groupsendList($hbid){
	
	
	global  $_W,$_GPC;
	$content = "";
	
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$param = array();
	$param[':uniacid'] = $_W['uniacid'];
	
	$content = ' ';
	
	
	$content .= " and yzmhbid = ".$hbid;
	
	$content .= " and type =  '1'";
	
	$listSql = "select s.*,u.nickname as nickname from ".tablename("ice_yzmhb_sendlist")." s  left join ".tablename("ice_yzmhb_user")." u on s.openid = u.openid where s.uniacid = :uniacid   ".$content ." order by time desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize ;
	
	$list = pdo_fetchall($listSql,$param);
	$sql = "select count(*) from ".tablename("ice_yzmhb_sendlist")."  where uniacid = :uniacid  ".$content ;
	$total = pdo_fetchcolumn($sql,$param);
	$pager = pagination($total, $pindex, $psize);
	
	$result = array();
	$result['list'] = $list;
	$result['pager'] = $pager;
	return $result;
	
	
	}