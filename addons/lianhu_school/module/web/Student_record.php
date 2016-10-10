<?php 
defined('IN_IA') or exit('Access Denied');
#ac=work,jinbu,weak
$model=$_GPC['model'] ? $_GPC['model'] :"grade";
$result=$this->student_standard();
$uid=$_W['uid'];
$this->teacher_qx('no');
$school_uniacid=" and  ".$this->where_uniacid_school;
#作业管理
if($ac=='work'|| $ac=='list'){
		if($model=='someone'){
			if($_GPC['submit']){
				$in['teacher_id']=$_W['uid'];
				$in['student_id']=$result['student_id'];
				$in['class_id']=$result['class_id'];
				$in['grade_id']=$result['grade_id'];
				$in['word']=$_GPC['word'];
				$in['img']=$_GPC['img'];
				$in['content']=$_GPC['content'];
				$in['addtime']=TIMESTAMP;
				$in['course_name']=$this->find_teacher_by_uid($_W['uid'],'',true);
				$in['uniacid']=$_W['uniacid'];
				$in['school_id']=$_SESSION['school_id'];
                #调用七牛处理
                $img=$this->upToQiniu($_GPC['img']);
                if($img)
                     $in['img']= $img;
				pdo_insert('lianhu_work',$in);
				$this->send_record_msg($result['student_id'],'作业记录');
				message('新增作业记录成功','refresh','success');
			}
			$list=pdo_fetchall("select work.*,tea.teacher_realname from {$table_pe}lianhu_work work left join  {$table_pe}lianhu_teacher tea on tea.teacher_id=work.teacher_id where work.student_id=:id order by work_id desc ",array(':id'=>$result['student_id']));
		}
}
#进步管理
if($ac=='jinbu'){
		$t_id=pdo_fetchcolumn("select teacher_id from {$table_pe}lianhu_teacher where fanid={$uid} {$school_uniacid} ");	
		$model=$_GPC['model'] ? $_GPC['model'] :"grade";
		$result=$this->student_standard();		
		if($model=='someone'){
			if($_GPC['submit']){
				$in['teacher_id']=$t_id;
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
}
#错题管理
if($ac=='weak'){
		$t_id=pdo_fetchcolumn("select teacher_id from {$table_pe}lianhu_teacher where fanid={$uid} {$school_uniacid} ");	
		$model=$_GPC['model'] ? $_GPC['model'] :"grade";
		$result=$this->student_standard();	
		if($model=='someone'){
			if($_GPC['submit']){
				$in['teacher_id']=$t_id;
				$in['student_id']=$result['student_id'];
				$in['class_id']=$result['class_id'];
				$in['grade_id']=$result['grade_id'];				
				$in['content']=$_GPC['content'];
				$in['content1']=$_GPC['content1'];
				$in['addtime']=TIMESTAMP;
				$in['course_name']=$this->find_teacher_by_uid($_W['uid'],'',true);
				$in['uniacid']=$_W['uniacid'];
				$in['school_id']=$_SESSION['school_id'];
				pdo_insert('lianhu_weak',$in);
				$this->send_record_msg($result['student_id'],'弱项记录');
				message('新增弱项记录成功','refresh','success');
			}
			$list=pdo_fetchall("select weak.*,tea.teacher_realname from {$table_pe}lianhu_weak weak left join  {$table_pe}lianhu_teacher tea on tea.teacher_id=weak.teacher_id where weak.student_id=:id  order by weak_id desc ",array(':id'=>$result['student_id']));
		}
}
