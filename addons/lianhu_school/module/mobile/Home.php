<?php 
defined('IN_IA') or exit('Access Denied');
$result=$this->mobile_from_find_student();
        $msg_count=count($this->web_msg(true));
		$work_num=$this->get_info('lianhu_work',$result['student_id']);
		$test_num=$this->get_info('lianhu_test',$result['student_id']);
		$weak_num=$this->get_info('lianhu_weak',$result['student_id']);
		$jinbu_num=$this->get_info('lianhu_jinbu',$result['student_id']);
		$work_num=count($work_num);
		$test_num=count($test_num);
		$weak_num=count($weak_num);
		$jinbu_num=count($jinbu_num);
        $need_money=$this->MoneyGive();