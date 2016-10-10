<?php 
defined('IN_IA') or exit('Access Denied');

$this->teacher_qx();//只有管理员能进入
$grades=$this->grade_class();
$school_uniacid=" and ".$this->where_uniacid_school;
if($ac=='list'){
	$list=pdo_fetchall("select * from {$table_pe}lianhu_grade where 1=1 {$school_uniacid} order by grade_id desc ");
}
if($ac=='delete'){
	$id=(int)$_GPC['id'];
	if(empty($id)){message('非法传输','','error');}
	$result=pdo_fetch("select * from {$table_pe}lianhu_class where grade_id={$id}");
	if($result){message('下面已经绑定班级，无法删除','','error');}
	$de_re=pdo_delete('lianhu_grade',array('grade_id'=>$id));
	if($de_re){message("删除成功","referer",'success');}
}

if($ac=='new'){
	if($_GPC['submit']){
         $where[':grade_name']=$_GPC['grade_name'];
         $result=pdo_fetch("select * from {$table_pe}lianhu_grade where grade_name=:grade_name {$school_uniacid}",$where);
         if($result)
               message('此年级名已经存在',$this->createWebUrl('grade'),'error');
	$in['grade_name']=$_GPC['grade_name'];	
	$in['uniacid']=$_W['uniacid'];
	$in['school_id']=$_SESSION['school_id'];				
	pdo_insert('lianhu_grade',$in);
	message('新增成功',$this->createWebUrl('grade'),'success');
 }
}
if($ac=='edit'){
	$id=(int)$_GPC['id'];
	if(empty($id)){message('非法传输','','error');}
	$result=pdo_fetch("select * from {$table_pe}lianhu_grade where grade_id={$id}");				
	if($_GPC['submit']){        
	 $in['grade_name']=$_GPC['grade_name'];
	 pdo_update('lianhu_grade',$in,array('grade_id'=>$id));
	message('修改成功',$this->createWebUrl('grade'),'success');					
	}
}