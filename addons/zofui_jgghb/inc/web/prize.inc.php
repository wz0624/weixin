<?php
	global $_W, $_GPC;
	load() -> func('tpl');
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'prize';

if($operation == 'prize'){
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$total = pdo_fetchcolumn(" SELECT COUNT(id) FROM " . tablename('zofui_jgghb_log') . " WHERE uniacid ={$_W['uniacid']}");

	$prizeinfo = pdo_fetchall(" SELECT * FROM " . tablename('zofui_jgghb_log') . " WHERE uniacid ='{$_W['uniacid']}' ORDER BY id DESC " . " LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
	$pager = pagination($total, $pindex, $psize);
}
	
	
	
	
if($operation == 'set'){	
	$prizeinfo1 = pdo_fetch("SELECT * FROM " . tablename('zofui_jgghb_prize') . " WHERE  uniacid = '{$_W['uniacid']}' AND pid = 1");
	$prizeinfo2 = pdo_fetch("SELECT * FROM " . tablename('zofui_jgghb_prize') . " WHERE  uniacid = '{$_W['uniacid']}' AND pid = 2");
	$prizeinfo3 = pdo_fetch("SELECT * FROM " . tablename('zofui_jgghb_prize') . " WHERE  uniacid = '{$_W['uniacid']}' AND pid = 3");
	$prizeinfo4 = pdo_fetch("SELECT * FROM " . tablename('zofui_jgghb_prize') . " WHERE  uniacid = '{$_W['uniacid']}' AND pid = 4");
	$prizeinfo5 = pdo_fetch("SELECT * FROM " . tablename('zofui_jgghb_prize') . " WHERE  uniacid = '{$_W['uniacid']}' AND pid = 5");
	$prizeinfo6 = pdo_fetch("SELECT * FROM " . tablename('zofui_jgghb_prize') . " WHERE  uniacid = '{$_W['uniacid']}' AND pid = 6");
	$prizeinfo7 = pdo_fetch("SELECT * FROM " . tablename('zofui_jgghb_prize') . " WHERE  uniacid = '{$_W['uniacid']}' AND pid = 7");
	
	
	if(checksubmit()){
		$info1 = array();
		$info1['uniacid'] = $_W['uniacid'];
		$info1['pid'] = 1;
		$info1['maxmoney'] = $_GPC['maxmoney1'];
		$info1['money'] = $_GPC['money1'];
		$info1['chance'] = $_GPC['chace1'];
		$info1['pic'] = $_GPC['thumb1'];
		if(!$prizeinfo1){
			pdo_insert('zofui_jgghb_prize', $info1);
		}
		pdo_update('zofui_jgghb_prize', $info1, array('pid' => 1,'uniacid' => $_W['uniacid']));
		
		$info2 = array();
		$info2['uniacid'] = $_W['uniacid'];
		$info2['pid'] = 2;
		$info2['maxmoney'] = $_GPC['maxmoney2'];
		$info2['money'] = $_GPC['money2'];
		$info2['chance'] = $_GPC['chace2'];
		$info2['pic'] = $_GPC['thumb2'];
		if(!$prizeinfo2){
			pdo_insert('zofui_jgghb_prize', $info2);
		}
		pdo_update('zofui_jgghb_prize', $info2, array('pid' => 2,'uniacid' => $_W['uniacid']));
			
		$info3 = array();
		$info3['uniacid'] = $_W['uniacid'];
		$info3['pid'] = 3;
		$info3['maxmoney'] = $_GPC['maxmoney3'];
		$info3['money'] = $_GPC['money3'];
		$info3['chance'] = $_GPC['chace3'];
		$info3['pic'] = $_GPC['thumb3'];
		if(!$prizeinfo3){
			pdo_insert('zofui_jgghb_prize', $info3);
		}
		pdo_update('zofui_jgghb_prize', $info3, array('pid' => 3,'uniacid' => $_W['uniacid']));
		
		$info4 = array();
		$info4['uniacid'] = $_W['uniacid'];
		$info4['pid'] = 4;
		$info4['maxmoney'] = $_GPC['maxmoney4'];
		$info4['money'] = $_GPC['money4'];
		$info4['chance'] = $_GPC['chace4'];
		$info4['pic'] = $_GPC['thumb4'];
		if(!$prizeinfo4){
			pdo_insert('zofui_jgghb_prize', $info4);
		}
		pdo_update('zofui_jgghb_prize', $info4, array('pid' => 4,'uniacid' => $_W['uniacid']));
		
		$info5 = array();
		$info5['uniacid'] = $_W['uniacid'];
		$info5['pid'] = 5;
		$info5['maxmoney'] = $_GPC['maxmoney5'];
		$info5['money'] = $_GPC['money5'];
		$info5['chance'] = $_GPC['chace5'];
		$info5['pic'] = $_GPC['thumb5'];
		if(!$prizeinfo5){
			pdo_insert('zofui_jgghb_prize', $info5);
		}
		pdo_update('zofui_jgghb_prize', $info5, array('pid' => 5,'uniacid' => $_W['uniacid']));
		
		$info6 = array();
		$info6['uniacid'] = $_W['uniacid'];
		$info6['pid'] = 6;
		$info6['maxmoney'] = $_GPC['maxmoney6'];
		$info6['money'] = $_GPC['money6'];
		$info6['chance'] = $_GPC['chace6'];
		$info6['pic'] = $_GPC['thumb6'];
		if(!$prizeinfo6){
			pdo_insert('zofui_jgghb_prize', $info6);
		}
		pdo_update('zofui_jgghb_prize', $info6, array('pid' => 6,'uniacid' => $_W['uniacid']));
		
		$info7 = array();
		$info7['uniacid'] = $_W['uniacid'];
		$info7['pid'] = 7;
		$info7['maxmoney'] = $_GPC['maxmoney7'];
		$info7['money'] = $_GPC['money7'];
		$info7['chance'] = $_GPC['chace7'];
		$info7['pic'] = $_GPC['thumb7'];
		if(!$prizeinfo7){
			pdo_insert('zofui_jgghb_prize', $info7);
		}
		pdo_update('zofui_jgghb_prize', $info7, array('pid' => 7,'uniacid' => $_W['uniacid']));
		message('更新成功', $this -> createWebUrl('prize', array('op' => 'set')), 'success');
	}
}
	
	include $this -> template('web/prize');
