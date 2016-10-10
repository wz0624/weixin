<?php
defined('IN_IA') or exit('Access Denied');
#教师不能访问
$limit_type_arr=array(1=>'一次缴费，永远免费',2=>'按年',3=>'按月');
$this->teacher_qx();
$school_uniacid=" and uniacid={$_W['uniacid']} and school_id={$_SESSION['school_id']} ";
if($ac=='edit'){
	if(empty($_GPC['limit_id'])){ message('非法访问','','error');}
	$result=pdo_fetch("select * from  {$table_pe}lianhu_money_limit where limit_id=:lid ",array(':lid'=>$_GPC['limit_id']) );
	if($_GPC['submit']){
		$up['limit_name']=$_GPC['limit_name'];
		$up['limit_module']=$_GPC['limit_module'];
		$up['limit_type']=$_GPC['limit_type'];
		$up['limit_much']=$_GPC['limit_much'];
		$up['status']=$_GPC['status'];
		pdo_update('lianhu_money_limit',$up,array('limit_id'=>$_GPC['limit_id']));
		message('修改成功，请注意对于已经缴费过的用户本有效期内，这次修改不会起效，是否缴费是已ID值鉴别的；',$this->createWebUrl('money'),'success');
	}	
}
if($ac=='new'){
	if($_GPC['submit']){
		$in['uniacid']=$_W['uniacid'];
		$in['school_id']=$_SESSION['school_id'];
		$in['addtime']=TIMESTAMP;
		$in['limit_name']=$_GPC['limit_name'];
		$in['limit_module']=$_GPC['limit_module'];
		$in['limit_type']=$_GPC['limit_type'];
		$in['limit_much']=$_GPC['limit_much'];
		$in['status']=$_GPC['status'];
		pdo_insert('lianhu_money_limit',$in);
		message('新增成功','','success');
	}
}
if($ac=='list'){
	$list=pdo_fetchall("select * from {$table_pe}lianhu_money_limit where 1=1 {$school_uniacid} ");
}

