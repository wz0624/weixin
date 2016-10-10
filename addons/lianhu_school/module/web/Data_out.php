<?php
defined('IN_IA') or exit('Access Denied');
$class_list=$this->teacher_main();                                                      //班主任权限
require(IA_ROOT.'/framework/library/phpexcel/PHPExcel.php');
$school_uniacid=" and uniacid={$_W['uniacid']} and school_id={$_SESSION['school_id']}";
if(($ac=='list' && $op=='list' )|| $ac=='sroce' ) {
	$grade=pdo_fetchall("select * from {$table_pe}lianhu_grade where 1=1 {$school_uniacid}");
}
if($ac=='list'&& $op=='class'){
	$gid=$_GPC['gid'];
	$grade_name=pdo_fetchcolumn("select grade_name from {$table_pe}lianhu_grade where grade_id=:gid",array(':gid'=>$gid));
	$class=$this->grade_class_num($gid,false);
}
if($ac=='list' && $op=='student'){
	$cid=$_GPC['cid'];
	$class_name=pdo_fetchcolumn("select class_name from {$table_pe}lianhu_class where class_id=:cid",array(':cid'=>$cid));
	$student=$this->class_student_num($cid,false);
}
if($ac=='sroce'){
	$gid=$_GPC['gid'] ? $_GPC['gid'] :$grade[0]['grade_id'];
	$where_grade=" grade_id={$gid}";
	if($_GPC['class_id']){
		$where_class=" and class_id={$_GPC['class_id']}";
	}
	if($_GPC['jilv_id']){
		$where_jilv=" and ji_lv_id={$_GPC['jilv_id']}";
	}
	$list_jilv=pdo_fetchall("select  ji_lv_id  from {$table_pe}lianhu_scorelist where {$where_grade} {$where_class} {$where_jilv}  {$school_uniacid} group by ji_lv_id order by addtime desc {$sql_limit} ");
	$g=0;
	foreach ($list_jilv as $key => $value) {
			$list_student[$value['ji_lv_id']]=pdo_fetchall("select  student_id  from {$table_pe}lianhu_scorelist where {$where_grade} {$where_class} and ji_lv_id={$value['ji_lv_id']}  
														   group by student_id order by addtime desc");
			foreach ($list_student[$value['ji_lv_id']] as $k => $v) {
				$list_student[$value['ji_lv_id']][$v['student_id']]=pdo_fetchall("select * from {$table_pe}lianhu_scorelist where 
                                                                                   student_id={$v['student_id']} and ji_lv_id={$value['ji_lv_id']} ");
				foreach ($list_student[$value['ji_lv_id']][$v['student_id']] as $kv => $va) {
					$list_student[$value['ji_lv_id']][$v['student_id']]['all_score'] += $va['score'];
					$course_ids[$va['course_id']]=$va['score'];
                    $course_id_student[$v['student_id']][$va['course_id']]=$va['score'];
				}
                
				if($count_max < count($course_ids)){
					$max_course_arr=$course_ids;
				}
				$out_list[$g]['student_id']=$v['student_id'];
				$out_list[$g]['course_ids']= $course_id_student[$v['student_id']];	
				$out_list[$g]['class_id']  =$list_student[$value['ji_lv_id']][$v['student_id']][0]['class_id'];	
				$out_list[$g]['all_score']=$list_student[$value['ji_lv_id']][$v['student_id']]['all_score'];
				$g++;
			}
	}
	$out_list=$this->sort_arr($out_list,'all_score');
	$total=count($out_list);
	$kk=0;
	foreach ($max_course_arr as $key => $value) {
		$out_course_arr[$kk]['course_name']=pdo_fetchcolumn("select course_name from {$table_pe}lianhu_course where course_id=:cid ",array(':cid'=>$key));
		$out_course_arr[$kk]['course_id']=$key;
		$kk++;
	}
	foreach ($out_list as $key => $value) {
		$out_list[$key]['class_name']=$this->class_name_by_id($value['class_id']);
		$out_list[$key]['student_name']=pdo_fetchcolumn("select student_name from {$table_pe}lianhu_student where student_id=:sid",array(':sid'=>$value['student_id']));
	}

	if($_GPC['excel']){
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("家校通")->setLastModifiedBy("家校通")->setTitle("Office 2007 XLSX Test Document")->setSubject("Office 2007 XLSX Test Document")->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")->setKeywords("office 2007 openxml php")->setCategory("report file");
		    
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '学生')->setCellValue('B1', '班级');
		    foreach ($out_course_arr as $key => $value) {
		    	$local=67+$key;
		    	$local=chr($local);
		    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($local."1",$value['course_name']);
		    }
		    $local=67+$key+1;
		    $nlocal=67+$key+2;
		         $local=chr($local);
		         $nlocal=chr($nlocal);		    
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($local.'1', '总分')->setCellValue($nlocal.'1', '排名');
		    $i = 2;
		    foreach ($out_list as $kv=> $v) {
		        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $i, $v['student_name'])->setCellValue('B' . $i, $v['class_name']);
		        foreach ($out_course_arr as $key => $value) {
		        	$local=67+$key;
		        	$local=chr($local);
		        	if(!$v['course_ids'][$value['course_id']] ){
		        		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($local.$i,0);
		        	}else{
		        		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($local.$i,$v['course_ids'][$value['course_id']]);
		        	}
		        }
		   		 $local=67+$key+1;
		    	 $nlocal=67+$key+2;
		         $local=chr($local);
		         $nlocal=chr($nlocal);
		         $objPHPExcel->setActiveSheetIndex(0)->setCellValue($local.$i, $v['all_score'])->setCellValue($nlocal.$i, $kv+1);
		        $i++;
		    }
		    $objPHPExcel->getActiveSheet()->getStyle('A1:C1')->getFont()->setBold(true);
		    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
		    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
		    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
		    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
		    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
		    $objPHPExcel->getActiveSheet()->setTitle('成绩报告');		
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="report_' . time() . '.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;			
	}
}