<?php
defined('IN_IA') or exit('Access Denied');
require(IA_ROOT.'/framework/library/phpexcel/PHPExcel.php');
$teacher=$this->teacher_qx('no');
$ac= $ac=='list' ? 'grade': $ac;

if( $teacher== 'teacher' && $ac=='grade'){
    $ac='score';
}
if($_GPC['op']=='new'){
	$begin_in=intval($_GPC['begin_in']) ? intval($_GPC['begin_in']):2;
	$success=0;
	$imgtype=pathinfo($_FILES['file']['name']); 
	if($imgtype['extension'] !='xls'){message('上传的文件格式不对，请检查','','error'); }

	$file_name=$this->file_upload($_FILES['file'],'application/vnd.ms-excel');
	if($file_name['success']!=true){
		var_dump($file_name);
		exit("上传失败");
	}else{
		$upload_file_name=ATTACHMENT_ROOT.'/'.$file_name['path'];
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
		$objPHPExcel = $objReader->load($upload_file_name);
		$currentSheet = $objPHPExcel->getSheet(0);
		/**取得最大的列号*/
		$allColumn = $currentSheet->getHighestColumn(); 			
		/**取得一共有多少行*/
		$allRow = $currentSheet->getHighestRow(); 		
	}
}
$school_uniacid=" and ".$this->where_uniacid_school;
if($ac=='grade'){
    #导入年级
	$this->teacher_qx();//管理员权限
	if($op=='new'){
		$grade_name=strtoupper($_GPC['grade_name']);
		if(empty($grade_name)){message('没有设置列','','error');}
			for($currentRow = $begin_in;$currentRow <= $allRow;$currentRow++){
				/**从第A列开始输出*/
				for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
				   $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();/**ord()将字符转为十进制数*/
				   if($currentColumn ==$grade_name){
				   		if(empty($val)){continue;}
				   		$grade_name_from_excel=(string)$val;
				   		$have_or_not=pdo_fetch("select grade_id from  {$table_pe}lianhu_grade where grade_name=:name {$school_uniacid} ",array(':name'=>$grade_name_from_excel));
				   		if(!$have_or_not){pdo_insert('lianhu_grade',array('grade_name'=>$grade_name_from_excel,'uniacid'=>$_W['uniacid'],'school_id'=>$_SESSION['school_id'] ));$success++;}else{$error_in .="{$grade_name_from_excel}插入失败,";} 
				   }
				}
		#end excel
		}
	}
}
if($ac=='class'){
    #导入班级
	$this->teacher_qx();//管理员权限
	if($op=='new'){
		$class_name=strtoupper($_GPC['class_name']);
		$grade_name=strtoupper($_GPC['grade_name']);
		$in_arr=array();
		if(empty($grade_name) || empty($grade_name) ){message('没有设置列','','error');}
			for($currentRow = $begin_in;$currentRow <= $allRow;$currentRow++){
				/**从第A列开始输出*/
				for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
				   $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();/**ord()将字符转为十进制数*/
				   if($currentColumn ==$grade_name){
				   		if(empty($val)){continue;}
				   		$grade_name_from_excel=(string)$val;
				   		$grade_id=pdo_fetchcolumn("select grade_id from {$table_pe}lianhu_grade where grade_name=:name {$school_uniacid} ",array(':name'=>$grade_name_from_excel));
				   		if(!$grade_id){$error_in .="{$grade_name_from_excel}插入失败,";continue;}else{$in_arr[$currentRow]['grade_id']=$grade_id;}
				   }
				   if($currentColumn==$class_name){
				   		if(empty($val)){continue;}
				   		$class_name_from_excel=(string)$val;
				   		$have_or_not=pdo_fetch("select class_id from {$table_pe}lianhu_class where class_name=:name {$school_uniacid} ",array(':name'=>$class_name_from_excel));
				   		if(!$have_or_not){$in_arr[$currentRow]['class_name']=$class_name_from_excel;}else{$error_in .="{$class_name_from_excel}插入失败,";} 
				   }
				}
		#end excel
		}
		foreach ($in_arr as $key => $value) {
			if($value['grade_id']&&$value['class_name']){
				$success++;
				$value['uniacid']=$_W['uniacid'];
				$value['school_id']=$_SESSION['school_id'];
				pdo_insert('lianhu_class',$value);
			}
		}
	}	
	
}
if($ac=='student'){
    #导入学生
    $class_list=$this->teacher_main();
    $grade_list=pdo_fetchall("select * from {$table_pe}lianhu_grade where status=1 {$school_uniacid} order by grade_id desc ");
	if($op=='new'){
		$class_name=strtoupper($_GPC['class_name']);
		$student_name=strtoupper($_GPC['student_name']);
		$student_code=strtoupper($_GPC['student_code']);
		$in_arr=array();
		if(empty($class_name) || empty($student_name)||empty($student_code) ){message('没有设置列','','error');}
			for($currentRow = $begin_in;$currentRow <= $allRow;$currentRow++){
				/**从第A列开始输出*/
				for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
				   $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();/**ord()将字符转为十进制数*/
				   if($currentColumn ==$class_name){
				   		if(empty($val)){continue;}
				   		$class_name_from_excel=(string)$val;
				   		$class_re=pdo_fetch("select grade_id,class_id from {$table_pe}lianhu_class where class_name=:name and grade_id=:gid {$school_uniacid} ",array(':name'=>$class_name_from_excel,':gid'=>$_GPC['grade_id']));
				   		if(!$class_re){$error_in .="{$class_name_from_excel}插入失败,";continue;}else{$in_arr[$currentRow]['class_id']=$class_re['class_id'];$in_arr[$currentRow]['grade_id']=$class_re['grade_id'];}
				   }
				   if($currentColumn==$student_name){
				   		if(empty($val)){continue;}
				   		$student_name_from_excel=(string)$val;
						$in_arr[$currentRow]['student_name']=$student_name_from_excel;				   		
				   }
				   if($currentColumn==$student_code){
				   		if(empty($val)){continue;}
				   		$student_code_from_excel=$val;
				   		$have_or_not=pdo_fetch("select student_id from {$table_pe}lianhu_student where xuehao=:xuehao {$school_uniacid} ",array(':xuehao'=>$student_code));
				   		if($have_or_not){$error_in .="{$student_code_from_excel}插入失败,";continue;}else{$in_arr[$currentRow]['xuehao']=$student_code_from_excel;}
				   }
				}
		#end excel
		}
		foreach ($in_arr as $key => $value) {
			if($value['xuehao'] && $value['class_id']){
				$success++;
				$value['addtime']=TIMESTAMP;
				$value['uniacid']=$_W['uniacid'];
				$value['school_id']=$_SESSION['school_id'];				
				pdo_insert('lianhu_student',$value);
				$insert_id=pdo_insertid();
				pdo_update('lianhu_student',array('student_passport'=>$value['class_id'].$insert_id),array('student_id'=>$insert_id));				
			}
		}
	}	
}

if($ac=='score'){
    #导入成绩
    $class_list=$this->teacher_main(1);//获取老师，已经授课老师的班级列表
	if($op=='new'){
		$class_id=$_GPC['class_id'];
		$course_id=$_GPC['course_id'];
		$grade_id=pdo_fetchcolumn("select grade_id from {$table_pe}lianhu_class where class_id=:cid",array(':cid'=>$class_id));
		$course_name=pdo_fetchcolumn("select course_name from {$table_pe}lianhu_course where course_id=:cid ",array(':cid'=>$course_id));
		$teahcer_re=pdo_fetch("select * from {$table_pe}lianhu_teacher where 
        (course_id ={$course_id} or course_id like '{$course_id},%' or course_id like '%,{$course_id},%' or course_id like '%,{$course_id}'   ) 
        and teacher_other_power like :power {$school_uniacid} ",array(":power"=>"%{$class_id}%"));
		if(!$course_name || !$teahcer_re){message('没有找到课程或者没有找到负责次班级此课程的老师无法导入！','','error');}
		$student_code=strtoupper($_GPC['student_code']);
		$score=strtoupper($_GPC['score']);
		$in_arr=array();
		if(empty($score) || empty($student_code)){message('没有设置列','','error');}
			for($currentRow = $begin_in;$currentRow <= $allRow;$currentRow++){
				/**从第A列开始输出*/
				for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
				   $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();
                   /**ord()将字符转为十进制数*/
				   if($currentColumn ==$student_code){
				   		if(empty($val)){continue;}
				   		$student_code_from_excel=$val;
				   		$student_re=pdo_fetch("select student_id,student_name from {$table_pe}lianhu_student where xuehao=:code  and class_id=:cid  ",array(':code'=>$student_code_from_excel,':cid'=>$class_id));
				   		if(!$student_re){$error_in .="{$student_code_from_excel}插入失败,";continue;}
                           else{ $in_arr[$currentRow]['student_id']=$student_re['student_id'];}
				   }
				   if($currentColumn==$score){
				   		$score_from_excel=intval($val);
						$in_arr[$currentRow]['score']=$score_from_excel;				   		
				   }
				}
		#end excel
		}
		foreach ($in_arr as $key => $value) {
			if($value['score'] && $value['student_id']){
					$success++;
					$value['addtime']=TIMESTAMP;
					$value['uid']=$_W['uid'];
					$value['course_id']=$course_id;
					$value['class_id']=$class_id;
					$value['grade_id']=$grade_id;
					$value['ji_lv_id']=$_GPC['jilv_id'];
					$value['teacher_id']=$teahcer_re['teacher_id'];
					$value['uniacid']=$_W['uniacid'];
					$value['school_id']=$_SESSION['school_id'];						
					pdo_insert('lianhu_scorelist',$value);
			}
		}
	}	
}
if($op=='new'){
	$message="成功插入 ".$success." 个;".$error_in;
}
if($ac=='score_list_jilv'){
    $this->teacher_qx();//管理员权限
	if($op=='list'){
		$total=pdo_fetchcolumn("select count(*) num from {$table_pe}lianhu_scorejilv where 1=1 {$school_uniacid} ");
		$school_uniacid_jilv=" and jilv.uniacid={$_W['uniacid']} and jilv.school_id={$_SESSION['school_id']}";
		$list=pdo_fetchall("select jilv.*,grade.grade_name from {$table_pe}lianhu_scorejilv jilv left join {$table_pe}lianhu_grade grade on grade.grade_id=jilv.grade_id where 1=1 {$school_uniacid_jilv} order by addtime  {$sql_limit}");
	}elseif($op=='edit'){
		if($_GPC['submit']){
			$in['status']=$_GPC['status'];
			$in['scorejilv_name']=$_GPC['scorejilv_name'];
			// $in['grade_id']=$_GPC['grade_id'];
			pdo_update('lianhu_scorejilv',$in,array('scorejilv_id'=>$_GPC['jilv_id']));
			message('更新成功',$this->createWebUrl('data_in', array('ac' => 'score_list_jilv','op'=>'list')),'success');
		}
		$result=pdo_fetch("select * from {$table_pe}lianhu_scorejilv where scorejilv_id=:id ",array(':id'=>$_GPC['jilv_id']));
		$grade_list=pdo_fetchall("select * from {$table_pe}lianhu_grade where status=1 {$school_uniacid} ");
	}elseif($op=='create'){
		$grade_list=pdo_fetchall("select * from {$table_pe}lianhu_grade where status=1 {$school_uniacid}");
		if($_GPC['submit']){
			$in['status']=$_GPC['status'];
			$in['scorejilv_name']=$_GPC['scorejilv_name'];
			$in['grade_id']=$_GPC['grade_id'];
			$in['addtime']=TIMESTAMP;
			$in['uniacid']=$_W['uniacid'];
			$in['school_id']=$_SESSION['school_id'];				
			pdo_insert('lianhu_scorejilv',$in);
			message('新增成功',$this->createWebUrl('data_in', array('ac' => 'score_list_jilv','op'=>'list')),'success');
		}

	}

}