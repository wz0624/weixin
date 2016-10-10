<?php
defined('IN_IA') or exit('Access Denied');
$teacher_info=$this->teacher_mobile_qx();
$_W['uid']=$teacher_info['fanid'];

$model=$_GPC['model'] ? $_GPC['model'] :"grade";
$result=$this->student_standard();		
if($model=='someone'){
		if($_GPC['submit']){
			$in['teacher_id']=$teacher_info['teacher_id'];
			$in['student_id']=$result['student_id'];
			$in['class_id']=$result['class_id'];
			$in['grade_id']=$result['grade_id'];				
			$in['content']=$_GPC['content'];
			$in['content1']=$_GPC['content1'];
			$in['addtime']=TIMESTAMP;
			$in['course_name']=$this->find_teacher_by_uid($_W['uid'],'',true);
			$in['uniacid']=$_W['uniacid'];
			$in['school_id']=$_SESSION['school_id'];
			pdo_insert('lianhu_jinbu',$in);
			$this->send_record_msg($result['student_id'],'进步记录');
			message('新增进步记录成功','refresh','success');
		}
	$list=pdo_fetchall("select jinbu.*,tea.teacher_realname from {$table_pe}lianhu_jinbu jinbu left join  {$table_pe}lianhu_teacher tea on tea.teacher_id=jinbu.teacher_id where jinbu.student_id=:id order by jinbu_id desc ",array(':id'=>$result['student_id']));
}	
