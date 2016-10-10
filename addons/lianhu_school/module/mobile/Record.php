<?php 
defined('IN_IA') or exit('Access Denied');
		$uid=$_W['member']['uid'];
		if(empty($uid)){
			$uid=$this->register_member();
		}
		$student_info=$this->mobile_from_find_student();
		$class_name=$student_info['class_name'];
		$category=array(
			array('keyword'=>'test_record' , 'name'=>'考试记录'),
			array('keyword'=>'work_record' , 'name'=>'作业记录'),
			array('keyword'=>'weak_record' , 'name'=>'弱项记录'),
			array('keyword'=>'error_record','name'=>'错题记录'),
			array('keyword'=>'jinbu_record', 'name'=>'进步记录'),
			);
		$content=$_GPC['op'];
		$fanid=pdo_fetchcolumn("select fanid from ".tablename('mc_mapping_fans')." where uid={$uid} ");
		$this->mobile_from_find_student();
		$result=pdo_fetch("select * from {$table_pe}lianhu_student where fanid={$fanid} or fanid1={$fanid} or fanid2={$fanid} ");
		switch ($content) {
					case 'work_record':
							$list=$this->get_info('lianhu_work',$result['student_id']);		
							$id_zd="work_id";
						break;
					case 'test_record':
							$list=$this->get_info('lianhu_test',$result['student_id']);	
							$id_zd="test_id";
						break;	
					case 'error_record':
							$list=$this->get_info('lianhu_weak',$result['student_id']);
							$id_zd="weak_id";
						break;
					case 'weak_record':
							$list=$this->get_info('lianhu_weak',$result['student_id']);
							$id_zd="weak_id";
						break;
					case 'jinbu_record':
							$list=$this->get_info('lianhu_jinbu',$result['student_id']);	
							$id_zd="jinbu_id";
						break;					
			}	