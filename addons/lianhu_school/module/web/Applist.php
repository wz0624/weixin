<?php 
defined('IN_IA') or exit('Access Denied');
require(IA_ROOT.'/framework/library/phpexcel/PHPExcel.php');
		$this->teacher_qx();
		$school_uniacid=" and uniacid={$_W['uniacid']} and school_id={$_SESSION['school_id']} ";
		$ac=$_GPC['ac']?$_GPC['ac']:'list';
		$aid=intval($_GPC['aid']);
		if($ac=='list'){
			if($aid){
				$where_aid="and list.appointment_id={$aid} ";
				$aname=pdo_fetchcolumn("select appointment_name from {$table_pe}lianhu_appointment where appointment_id=:aid",array(':aid'=>$aid));
			}			
			$list=pdo_fetchall("select list.*,stu.student_name,stu.class_id,app.appointment_name from {$table_pe}lianhu_applist list left join {$table_pe}lianhu_student stu on stu.student_id=list.student_id left join {$table_pe}lianhu_appointment app on app.appointment_id=list.appointment_id where 1=1 {$where_aid} order by status desc,addtime desc ");
            foreach ($list as $key => $value) {
                if($value['content']){
                    $course_id=intval($value['content']);    
                    preg_match('/\w$/',$value['content'],$matchs);
                    $course_name=pdo_fetchcolumn("select course_name from {$table_pe}lianhu_appointment_course where course_id={$course_id}");
                    $list[$key]['my_course']=$course_name.':'.$matchs[0].'课时';
                }
            }
		}
        if($_GPC['excel_out']=='yes'){
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("家校通")->setLastModifiedBy("家校通")->setTitle("Office 2007 XLSX Test Document")->setSubject("Office 2007 XLSX Test Document")->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")->setKeywords("office 2007 openxml php")->setCategory("report file");
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '学生')->setCellValue('B1', '班级')
                                                ->setCellValue('C1', '时间')->setCellValue('D1', '项目')
                                                ->setCellValue('E1', '状态');
		    foreach ($list as $k=> $v) {
                $i=$k+2;
                if($v['status']==0)
                    $status='默认通过';
                else 
                    $status="不通过";
		        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $i, $v['student_name'])
                                                    ->setCellValue('B' . $i, $this->className($v['class_id']))
                                                    ->setCellValue('C' . $i, date('Y-m-d H:i:s',$v['addtime']))
                                                    ->setCellValue('D' . $i, $v['my_course'])
                                                    ->setCellValue('E' . $i, $status);
		    }
		    $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);
		    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
		    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
		    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
		    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
		    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
		    $objPHPExcel->getActiveSheet()->setTitle($aname);		
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="report_' . time() . '.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;	
        }
		if($_GPC['submit']){
			$list_id=$_GPC['list_id'];
			$uid=$_W['uid'];
			pdo_update('lianhu_applist',array('teacher_id'=>$uid,'reason'=>$_GPC['content'],'status'=>2),array('list_id'=>$list_id));
			message('操作成功','','success');
		}