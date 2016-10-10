<?php 
defined('IN_IA') or exit('Access Denied');
#验证是否是管理员
$admin=$this->teacher_qx('no');
$class_list=$this->teacher_main();
if($admin=='teacher'){
    foreach ($class_list as $key => $value) {
        $teacher_class_arr[$key]=$value['class_id'];
    }
}
            $grades=$this->grade_class();
            $school_uniacid=" and ".$this->where_uniacid_school;
			$ac=$_GPC['ac']?$_GPC['ac']:'list';
			$grade_list=pdo_fetchall("select * from {$table_pe}lianhu_grade where 1=1 {$school_uniacid}");
            if($admin!='teacher'){
    			$class_list=pdo_fetchall("select * from {$table_pe}lianhu_class where status=1 {$school_uniacid} ");
            }
			if($ac=='list'){
                $where =" 1=:a ";
                $params[':a']=1;
				if($_GPC['grade_id']){
					$where .=" and student.grade_id=:gid";
                    $params[':gid']=$_GPC['grade_id'];
				}
                if(!$_GPC['class_id'] && $admin=='teacher'){
                    foreach ($teacher_class_arr as $key => $value) {
                        $teacher_class_arr[$key]=intval($value);
                    }
                    $teacher_class_str=implode(',',$teacher_class_arr);
 					$where .=" and student.class_id in ({$teacher_class_str})";
                }
				if($_GPC['class_id']){
					$where .=" and student.class_id=:cid";
                    $params[':cid']=$_GPC['class_id'];
				}
				if($_GPC['student_name']){
					$where .=" and student.student_name like :student_name ";
                    $params[':student_name']='%'.$_GPC['student_name'].'%';
                }
				if($_GPC['status']){
					if($_GPC['status']==2){$_GPC['status']=0;}
					$where .="and student.status=:status";
                    $params[':status']=$_GPC['status'];
				}
				$total=	pdo_fetchcolumn("select count(*) from {$table_pe}lianhu_student student where {$where} {$school_uniacid} ",$params);
                $num=$total;
                $student_school_uniacid="and  student.school_id={$_SESSION['school_id']} and student.uniacid={$_W['uniacid']}";
                if($_GPC['print_code']==1){
 				   $list=	pdo_fetchall("select student.*,grade.grade_name,class.class_name from {$table_pe}lianhu_student student left join {$table_pe}lianhu_grade grade on
				                    grade.grade_id=student.grade_id left join {$table_pe}lianhu_class class on class.class_id=student.class_id 
                                     where {$where} {$student_school_uniacid}  ",$params);
                }else{
                    $list=	pdo_fetchall("select student.*,grade.grade_name,class.class_name from {$table_pe}lianhu_student student left join {$table_pe}lianhu_grade grade on
                                        grade.grade_id=student.grade_id left join {$table_pe}lianhu_class class on class.class_id=student.class_id 
                                        where {$where} {$student_school_uniacid} {$sql_limit} ",$params);
                }
			}
			if($ac=='new'){
				if($_GPC['submit']){
                    if($admin=='teacher'){
                        if(!in_array($_GPC['class_id'],$teacher_class_arr))message("您只能编辑您班级下的学生",'','error');
                    }
					$in['class_id']=intval($_GPC['class_id']);
                    $in['grade_id']=pdo_fetchcolumn("select grade_id from {$table_pe}lianhu_class where class_id=:class_id",array(':class_id'=>$in['class_id']));
					$in['student_name']=$_GPC['student_name'];
					$in['student_img']=$_GPC['student_img'];
					$in['parent_name']=$_GPC['parent_name'];
					$in['parent_phone']=$_GPC['parent_phone'];
					$in['xuehao']=$_GPC['xuehao'];
					$in['address']=$_GPC['address'];
					$in['student_link']=$_GPC['student_link'];
					$in['addtime']=TIMESTAMP;
                    $in['school_id']=$_SESSION['school_id'];
                    $in['uniacid']=$_W['uniacid'];
					pdo_insert('lianhu_student',$in);
					$id=pdo_insertid();
					$up['student_passport']=$in['class_id'].$id;
					pdo_update('lianhu_student',$up,array('student_id'=>$id));
					message('新增成功',$this->createWebUrl('student', array('op' => 'student')),'success');
				}
			}
			if($ac=='edit'){
				$id=intval($_GPC['id']);
				$result=pdo_fetch("select * from {$table_pe}lianhu_student where student_id=:id",array(':id'=>$id));
				if(!$result){message('没有找到相应学生','','error');}
				if($_GPC['submit']){
                    if($admin=='teacher'){
                        if(!in_array($_GPC['class_id'],$teacher_class_arr))message("您只能编辑您班级下的学生",'','error');
                    }                    
					$in['class_id']=intval($_GPC['class_id']);
					$in['grade_id']=pdo_fetchcolumn("select grade_id from {$table_pe}lianhu_class where class_id=:class_id",array(':class_id'=>$in['class_id']));
					$in['student_name']=$_GPC['student_name'];
					$in['student_img']=$_GPC['student_img'];
					$in['parent_name']=$_GPC['parent_name'];
					$in['xuehao']=$_GPC['xuehao'];
					$in['address']=$_GPC['address'];
					$in['student_link']=$_GPC['student_link'];					
					$in['parent_phone']=$_GPC['parent_phone'];
					$in['fanid']=$_GPC['fanid'];
					$in['fanid1']=$_GPC['fanid1'];
					$in['fanid2']=$_GPC['fanid2'];
					$in['status']=$_GPC['status'];				
					pdo_update('lianhu_student',$in,array('student_id'=>$id));					
					message('修改成功',$this->createWebUrl('student', array('op' => 'student')),'success');
				}
			}	
			if($ac=='delete'){
				$id=intval($_GPC['id']);
				$result1=pdo_fetch("select * from {$table_pe}lianhu_test where student_id=:id",array(':id'=>$id));
				$result2=pdo_fetch("select * from {$table_pe}lianhu_weak where student_id=:id",array(':id'=>$id));
				$result3=pdo_fetch("select * from {$table_pe}lianhu_work where student_id=:id",array(':id'=>$id));
				if(!$result1 && !$result2 && !$result3){
					pdo_delete('lianhu_student',array('student_id'=>$id));
					message('删除成功',$this->createWebUrl('student', array('op' => 'student')),'success');
				}else{
					message('已有数据，无法删除',$this->createWebUrl('student', array('op' => 'student')),'error');
				}
			}		