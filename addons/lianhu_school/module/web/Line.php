<?php 
    defined('IN_IA') or exit('Access Denied');
    if($this->module['config']['line_type'][$_SESSION['school_id']]){
			$line_type_cfg=explode("||", $this->module['config']['line_type'][$_SESSION['school_id']]);
			foreach ($line_type_cfg as $key => $value) {
				if($value){
					$line_type[]=$value;
				}
			}
		}else{
			$line_type=$_W['line_type'];
		}
		$teacher=$this->teacher_qx('no');
		$school_uniacid=" and ".$this->where_uniacid_school;
		$model=$_GPC['model'] ? $_GPC['model'] :"class";
		$cid=$_GPC['cid'];
		if($teacher=='teacher'){
			$uid=$_W['uid'];
			$t_id  =   pdo_fetchcolumn("select teacher_id from {$table_pe}lianhu_teacher where fanid={$uid} {$school_uniacid}");
			$t_name= pdo_fetchcolumn("select teacher_realname from {$table_pe}lianhu_teacher where fanid={$uid} {$school_uniacid}");
			$list  =pdo_fetchall("select class.* from {$table_pe}lianhu_class class  where class.status=1 and class.teacher_id={$t_id} {$school_uniacid}");
		}else{
			$list=pdo_fetchall("select class.* from {$table_pe}lianhu_class class  where class.status=1  {$school_uniacid}");
			$t_name="管理员";
		}
        
		if($_GPC['cid']){
			$class=pdo_fetch("select * from {$table_pe}lianhu_class where class_id=:cid {$school_uniacid} ",array(':cid'=>$_GPC['cid']));
			if(!$class){
				message('没有找到此班级',$this->createWebUrl('line'),'error');
			}
		}
		
        if($ac=='new'){
			if($_GPC['submit']){
				$in['class_id']=$_GPC['cid'];
				$in['line_title']=$_GPC['line_title'];
				$in['line_content']=$_GPC['line_content'];
				$in['line_type']=$_GPC['line_type'];
				$in['addtime']=TIMESTAMP;
				$in['teacher_id']=$t_id;
				$in['teacher_intro']=$t_name."添加";
				$in['school_id']=$_SESSION['school_id'];
				$in['uniacid']=$_W['uniacid'];
				pdo_insert('lianhu_line',$in);
				message('添加成功',$this->createWebUrl('line',array('ac'=>'old','cid'=>$_GPC['cid'])),'success');
			}
		}
		if($ac=='edit'){
			$lid=$_GPC['lid'];
			if($_GPC['submit']){
				$result=pdo_fetch("select * from {$table_pe}lianhu_line where line_id=:lid",array(':lid'=>$lid));
				$in['line_title']=$_GPC['line_title'];
				$in['line_content']=$_GPC['line_content'];
				$in['line_type']=$_GPC['line_type'];
				$in['teacher_id']=$t_id;
				$in['status']=$_GPC['status'];
				$in['teacher_intro']=$t_name."编辑";
				pdo_update('lianhu_line',$in,array('line_id'=>$lid));
				message('编辑成功',$this->createWebUrl('line',array('ac'=>'old','cid'=>$result['class_id'])),'success');				
			}
			$result=pdo_fetch("select * from {$table_pe}lianhu_line where line_id=:lid",array(':lid'=>$lid));
		}
		if($ac=='old'){
			$total=pdo_fetchcolumn("select count(*) num from  {$table_pe}lianhu_line where class_id=:cid {$school_uniacid} ",array(':cid'=>$cid));
			$school_uniacid_line=" and line.uniacid={$_W['uniacid']} and line.school_id={$_SESSION['school_id']} ";
			$list=pdo_fetchall("select line.*,class.class_name,tea.teacher_realname from {$table_pe}lianhu_line line left join {$table_pe}lianhu_class class on class.class_id=line.class_id
			 left join {$table_pe}lianhu_teacher tea on line.teacher_id=tea.teacher_id where line.class_id=:cid {$school_uniacid_line} order by addtime desc {$sql_limit}",array(':cid'=>$cid));
		}