<?php 
defined('IN_IA') or exit('Access Denied');
		$uid=$_W['uid'];
		$this->teacher_qx('no');
		$school_uniacid=" and ".$this->where_uniacid_school;
		$t_id=pdo_fetchcolumn("select teacher_id from {$table_pe}lianhu_teacher where fanid={$uid} {$school_uniacid} ");			
		$model=$_GPC['model'] ? $_GPC['model'] :"grade";
		$result=$this->student_standard();	
		if($model=='someone'){
			if($_GPC['submit']){
				$in['teacher_id']=$t_id;
				$in['student_id']=$result['student_id'];
				$in['class_id']=$result['class_id'];
				$in['grade_id']=$result['grade_id'];				
				$in['word']=$_GPC['word'];
				$in['img']=$_GPC['img'];
				$in['score']=$_GPC['score'];
				$in['content']=$_GPC['content'];
				$in['addtime']=TIMESTAMP;
				$in['course_name']=$this->find_teacher_by_uid($_W['uid'],'',true);
				$in['uniacid']=$_W['uniacid'];
				$in['school_id']=$_SESSION['school_id'];
                $img=$this->upToQiniu($_GPC['img']);
                if($img)
                $in['img']= $img;                
				pdo_insert('lianhu_test',$in);
				$this->send_record_msg($result['student_id'],'考试记录');
				message('新增考试记录成功','refresh','success');
			}
			$school_uniacid_test=" and test.uniacid={$_W['uniacid']} and test.school_id={$_SESSION['school_id']} ";
			$list=pdo_fetchall("select test.*,tea.teacher_realname from {$table_pe}lianhu_test test left join  {$table_pe}lianhu_teacher tea on tea.teacher_id=test.teacher_id where test.student_id=:id {$school_uniacid_test} order by test_id desc ",array(':id'=>$result['student_id']));
		}				