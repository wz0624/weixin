<?php 
defined('IN_IA') or exit('Access Denied');
$this->teacher_qx();
$school_uniacid=" and ".$this->where_uniacid_school;
$grade_list=pdo_fetchall("select * from {$table_pe}lianhu_grade where 1=1 {$school_uniacid} ");
$course_list=pdo_fetchall("select * from {$table_pe}lianhu_course where 1=1 {$school_uniacid} ");
$video_list=pdo_fetchall("select * from {$table_pe}lianhu_video where status=1");
			if($ac=='list'){
                $params[':a']=1;
				if($_GPC['grade_id']){
					$where =' and class.grade_id=:grade_id';
				    $params[':grade_id']=$_GPC['grade_id'];
                }
				if($_GPC['status']){
					if($_GPC['status']==2){$_GPC['status']=0;}
					$where.=" and class.status=:status";
                    $params[':status']=$_GPC['status'];
				}
				$school_uniacid_class=" and class.uniacid={$_W['uniacid']} and class.school_id={$_SESSION['school_id']} ";
				$list=pdo_fetchall("select class.*,grade.grade_name,tea.teacher_realname from {$table_pe}lianhu_class class left join {$table_pe}lianhu_grade grade on 
								grade.grade_id=class.grade_id   left join  {$table_pe}lianhu_teacher tea on class.teacher_id=tea.teacher_id where 1=:a  
                                {$where} 
                                {$school_uniacid_class} 
                                order by class.class_id",$params);
				$num=count($list);
			}
			if($ac=='new'){
                #获取基本课程
                $base_class=pdo_fetchall("select course_id from {$table_pe}lianhu_course where course_basic=1 ");
                foreach ($base_class as $key => $value) {
                    $course_ids[$key]=$value['course_id'];
                }
				if($_GPC['submit']){
                    $where[':class_name']=$_GPC['class_name'];
                    $where[':gid']       =$_GPC['grade_id'];
                    $result=pdo_fetch("select * from {$table_pe}lianhu_class where class_name=:class_name and grade_id=:gid {$school_uniacid}",$where);
                    if($result){
                        message('此班级名已经存在',$this->createWebUrl('class'),'error');
                    }
					$in['class_name']=$_GPC['class_name'];
					$in['grade_id']=$_GPC['grade_id'];
					$in['course_ids']=serialize($_GPC['course_s']);
					$in['video_ids']=serialize($_GPC['video_s']);
					$in['uniacid']=$_W['uniacid'];
					$in['school_id']=$_SESSION['school_id'];
					pdo_insert('lianhu_class',$in);
					message('新增成功',$this->createWebUrl('class'),'success');
				}
			}
			if($ac=='edit'){
				$id=(int)$_GPC['id'];
				if(empty($id)){message('非法传输','','error');}
				$result=pdo_fetch("select * from {$table_pe}lianhu_class where class_id={$id}");					
				$list_teacher=pdo_fetchall("select * from {$table_pe}lianhu_teacher where teacher_other_power like '%{$id}%' and status=1 {$school_uniacid} ");
				if($_GPC['submit']){                  
					$in['class_name']=$_GPC['class_name'];
					$in['grade_id']=$_GPC['grade_id'];
					$in['teacher_id']=$_GPC['teacher_id'];
					$in['status']=$_GPC['status'];
					$in['course_ids']=serialize($_GPC['course_s']);
                    $in['video_ids']=serialize($_GPC['video_s']);
					pdo_update('lianhu_class',$in,array('class_id'=>$id));
					message('修改成功',$this->createWebUrl('class'),'success');					
				}
            }
			if($ac=='delete'){
				$id=(int)$_GPC['id'];
				if(pdo_fetch("select * from {$table_pe}lianhu_student where class_id=:class_id",array(':class_id'=>$id) )){
					message('无法删除,该班级下已有学生','','error');
				}else{
					pdo_delete('lianhu_class',array('class_id'=>$id));
					message('删除成功',$this->createWebUrl('class'),'success');		
				}
			}	