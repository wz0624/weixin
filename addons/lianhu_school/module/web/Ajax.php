<?php
defined('IN_IA') or exit('Access Denied');
if($ac=='send_msg_line'){
    $queue_id=$_GPC['queue_id'];
    if(!$queue_id) return false;
    $this->sendAllMsg($queue_id);
    $end_time=date("H:i:s",time());
    echo  json_encode(array("end_time"=>$end_time,'status'=>2));
    exit();
}
if($ac=='course_list'){
	if($_GPC['cid']){
		$course_list=$this->get_class_course($_GPC['cid']);
		if($course_list){
			echo json_encode(array('success'=>'yes','list'=>$course_list));
		}else{
			echo '1';
		}
	}
}
if($ac=='class_list'){
	if($_GPC['gid']){
		$class_list=$this->grade_class_num($_GPC['gid'],false);
		if($class_list){
			echo json_encode(array('success'=>'yes','list'=>$class_list));
		}
	}
}	
if($ac=='jilv_list'){
	if($_GPC['cid']){
		$gid=pdo_fetchcolumn("select grade_id from {$table_pe}lianhu_class where class_id=:id",array(':id'=>$_GPC['cid']));
		$jilv_list=$this->get_grade_sroce_jilv($gid,TIMESTAMP-3600*24*30*2);
		if($jilv_list){
			echo json_encode(array('success'=>'yes','list'=>$jilv_list));
		}else{
			echo '1';
		}
	}
	if($_GPC['gid']){
			$jilv_list=$this->get_grade_sroce_jilv($_GPC['gid'],TIMESTAMP-TIMESTAMP+1);
				if($jilv_list){
					echo json_encode(array('success'=>'yes','list'=>$jilv_list));
				}else{
					echo '1';
				}			
	}
}
if($ac=='student_score_list'){
	$class_id=$_GPC['cid'];
	$course_id=$_GPC['course_id'];
	$scorejilv_id=$_GPC['scorejilv_id'];
	$list=pdo_fetchall("select * from {$table_pe}lianhu_scorelist where course_id=:course_id and ji_lv_id=:ji_lv_id and class_id=:class_id",array(':course_id'=>$course_id,':ji_lv_id'=>$scorejilv_id,':class_id'=>$class_id));
    echo json_encode(array('status'=>"yes",'student_score_list'=>$list));
}
if($ac=='teacher_class_change'){
    $class_id_str=trim($_GPC['class_str'],',');
    $arr=explode(',',$class_id_str);
    foreach ($arr as $key => $value) {
        $arr[$key]=intval($value);
    }
    $class_id_str=implode(',',$arr);
    $list=pdo_fetchall("select course_ids from {$table_pe}lianhu_class where class_id in ({$class_id_str})");
    $course_arr=array();
   foreach($list as $key=>$value){
       $course_arr=array_merge($course_arr,unserialize($value['course_ids']));
   } 
   $course_arr=array_unique($course_arr);
   $course_id_str=implode(',',$course_arr);
   $out_list=pdo_fetchall("select * from {$table_pe}lianhu_course where course_id in ({$course_id_str})");
   echo json_encode(array('status'=>"success",'list'=> $out_list));
}
exit();