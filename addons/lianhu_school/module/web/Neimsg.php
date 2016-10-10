<?php 
    defined('IN_IA') or exit('Access Denied');
		$this->teacher_qx();#管理员可进入
		$school_uniacid=" and ".$this->where_uniacid_school;	
		$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if($op=='display'){
			if($_GPC['status']){
				if($_GPC['status']==2){
					$where_status=" and status =0";
				}else{
					$where_status=" and status=1";
				}
			}
			$list=pdo_fetchall("select * from  {$table_pe}lianhu_msg where 1=1 {$where_status} {$school_uniacid} order by msg_id desc ");
			$num=count($list);
		}	
		if($op=='edit'){
			$id=intval($_GPC['id']);
		 	$result=pdo_fetch("select * from {$table_pe}lianhu_msg where msg_id=:id",array(':id'=>$id));
		 if($_GPC['submit']){
			$in['msg_title']=$_GPC['msg_title'];
			$in['msg_content']=$_GPC['msg_content'];
			$in['status']=$_GPC['status'];
			pdo_update('lianhu_msg',$in,array('msg_id'=>$id));
			message('修改成功',$this->createWebUrl('neimsg'),'success');
		 }
		}
		if($op=='new'){
		 if($_GPC['submit']){
			$in['msg_title']=$_GPC['msg_title'];
			$in['msg_content']=$_GPC['msg_content'];
			$in['addtime']=TIMESTAMP;
			$in['uniacid']=$_W['uniacid'];
			$in['school_id']=$_SESSION['school_id'];
			pdo_insert('lianhu_msg',$in);
			message('新增成功',$this->createWebUrl('neimsg'),'success');		
		 }	
		}
		if($op=='delete'){
			$id=intval($_GPC['id']);
			pdo_delete('lianhu_msg',array('msg_id'=>$id));
			message('删除成功',$this->createWebUrl('neimsg'),'success');		
		}