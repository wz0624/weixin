<?php 
defined('IN_IA') or exit('Access Denied');
		$uid=$_W['member']['uid'];
		if(empty($uid)){
			$uid=$this->register_member();
		}
		$uid=$_W['member']['uid'];
		$fanid=pdo_fetchcolumn("select fanid from ".tablename('mc_mapping_fans')." where uid={$uid} ");
		$this->mobile_from_find_student();
		$result=pdo_fetch("select stu.*, class.class_name ,grade.grade_name, tea.teacher_realname from {$table_pe}lianhu_student stu 
			left join {$table_pe}lianhu_class class on class.class_id=stu.class_id left join {$table_pe}lianhu_grade grade on grade.grade_id=class.grade_id
			left join  {$table_pe}lianhu_teacher tea on tea.teacher_id=class.teacher_id
			where stu.fanid={$fanid} or stu.fanid1={$fanid} or stu.fanid2={$fanid} ");		
		$msg_count=count($this->web_msg());
		$work_num=$this->get_info('lianhu_work',$result['student_id']);
		$test_num=$this->get_info('lianhu_test',$result['student_id']);
		$weak_num=$this->get_info('lianhu_weak',$result['student_id']);
		$jinbu_num=$this->get_info('lianhu_jinbu',$result['student_id']);
		$work_num=count($work_num);
		$test_num=count($test_num);
		$weak_num=count($weak_num);
		$jinbu_num=count($jinbu_num);
		for ($i=0; $i <100 ; $i++) { 
			$loop[$i]=1;
		}
		$old_result=pdo_fetch("select * from {$table_pe}lianhu_syllabus where class_id=:cid order by addtime desc ",array(':cid'=>$result['class_id']));
		$data=unserialize($old_result['content']);
        #
        $time_result=pdo_fetch("select * from {$table_pe}lianhu_set where keyword='course_time' order by set_id  desc ");
        $time_result['content']=unserialize($time_result['content']);
        $time_result['begin_time']=$time_result['content']['begin_time'];
        $time_result['end_time']=$time_result['content']['end_time'];
        $begin_course =$this->module['config']['begin_course'][$_SESSION['school_id']];
                   
        
        