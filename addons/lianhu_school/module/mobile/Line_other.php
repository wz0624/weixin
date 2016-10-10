<?php 
defined('IN_IA') or exit('Access Denied');
$uid=$_W['member']['uid'];
$signPackage=$this->getSignPackage();
if($this->module['config']['line_type'][$_SESSION['school_id']]){
	$line_type_cfg=explode("||", $this->module['config']['line_type'][$_SESSION['school_id']]);
	foreach ($line_type_cfg as $key => $value) {
		if($value){
			$line_type[]=$value;
		}
	}
	$_W['line_type']=$line_type;
}
$student_info=$this->mobile_from_find_student();
$class_name=$student_info['class_name'];
#作业
    if($op=='home_work'){
        $news_list=pdo_fetchall("select * from {$table_pe}lianhu_homework where class_id={$student_info['class_id']} and status=1 order by add_time desc limit 0,20");
        
    }else{
        $type=array_search($op, $_W['line_type']);
        if(!$type){$type=0;}
        $total=pdo_fetchcolumn("select count(*) num from {$table_pe}lianhu_line where class_id=:cid and status=1 and  line_type={$type}",array(':cid'=>$student_info['class_id']));
        $news_list=pdo_fetchall("select line.*,tea.teacher_realname from {$table_pe}lianhu_line line left join {$table_pe}lianhu_teacher tea on tea.teacher_id=line.teacher_id  where line.class_id=:cid and line.status=1 and  line_type={$type} order by line.addtime desc {$sql_limit}",array(':cid'=>$student_info['class_id']));  
    }



