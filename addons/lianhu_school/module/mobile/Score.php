<?php
defined('IN_IA') or exit('Access Denied');
$student_info=$this->mobile_from_find_student();
$class_name=$student_info['class_name'];

if($ac=='list'){
	$list=pdo_fetchall("select * from {$table_pe}lianhu_scorejilv where grade_id=:id and status=1 order by addtime desc ",array(':id'=>$student_info['grade_id']));
}
if($ac=='listall'){
	$scorejilv_name=pdo_fetchcolumn("select scorejilv_name from {$table_pe}lianhu_scorejilv where scorejilv_id=:id ",array(':id'=>$op));
	$all_score=pdo_fetchcolumn("select sum(score) num from {$table_pe}lianhu_scorelist where ji_lv_id=:id and student_id=:sid ",array(':id'=>$op,':sid'=>$student_info['student_id']));
	$paiming=pdo_fetchall("select * ,sum(score) num from {$table_pe}lianhu_scorelist where ji_lv_id=:id group by student_id order by num desc ",array(':id'=>$op));
	foreach ($paiming as $key => $value) {
		if($value['student_id']==$student_info['student_id']){$sort=$key+1;break;}
	}
	$score_list=pdo_fetchall("select score.*,course.course_name from {$table_pe}lianhu_scorelist score left join {$table_pe}lianhu_course course 
								on course.course_id=score.course_id  where score.ji_lv_id=:id and score.student_id=:sid ",array(':id'=>$op,':sid'=>$student_info['student_id']));
}
