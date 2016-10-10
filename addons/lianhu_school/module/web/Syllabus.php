<?php 
defined('IN_IA') or exit('Access Denied');
$teacher=$this->teacher_qx('no');
		if($teacher=='teacher'){
			$uid=$_W['uid'];
			$t_id=pdo_fetchcolumn("select teacher_id from {$table_pe}lianhu_teacher where fanid={$uid}");
			$list=pdo_fetchall("select class.*, sylla.syllabus_id from {$table_pe}lianhu_class class 
                left join {$table_pe}lianhu_syllabus sylla on sylla.class_id=class.class_id 
                left join {$table_pe}lianhu_grade grade on class.grade_id =grade.grade_id
                where class.status=1 and class.teacher_id={$t_id} order by grade_name desc , grade_id desc ");
		}else{
			$school_uniacid_class=" and class.uniacid={$_W['uniacid']} and class.school_id={$_SESSION['school_id']} ";
			$list=pdo_fetchall("select class.*, sylla.syllabus_id from {$table_pe}lianhu_class class 
            left join {$table_pe}lianhu_syllabus sylla on sylla.class_id=class.class_id 
              left join {$table_pe}lianhu_grade grade on class.grade_id =grade.grade_id
             where class.status=1 {$school_uniacid_class}  order by grade_name desc, grade_id desc ");
		}
		$quanxian_class_id_arr=array();
		if($list){
			foreach ($list as $key => $value) {
				if(in_array($value['class_id'], $quanxian_class_id_arr)){
					unset($list[$key]);
					continue;
				}
				$quanxian_class_id_arr[]=$value['class_id'];
			}
		}else{
			message("只有班主任和管理员能够访问，或者暂未设置年级班级");
		}
		for ($i=0; $i <100 ; $i++) { 
			$loop[$i]=1;
		}
        
		$on_school    =$this->module['config']['on_school'][$_SESSION['school_id']];
		$begin_course =$this->module['config']['begin_course'][$_SESSION['school_id']];
		$am_much      =$this->module['config']['am_much'][$_SESSION['school_id']];
		$pm_much      =$this->module['config']['pm_much'][$_SESSION['school_id']];
		$ye_much      =$this->module['config']['ye_much'][$_SESSION['school_id']];
        
		if($ac=='new'){
			if(in_array($_GPC['cid'],$quanxian_class_id_arr)){
				$class_result=pdo_fetch("select * from {$table_pe}lianhu_class where class_id=:cid",array(':cid'=>$_GPC['cid']));
				$course_ids=unserialize($class_result['course_ids']);
				if($course_ids){
					foreach ($course_ids as $key => $value) {
						$courses[$key]['id']=$value;
						$courses[$key]['name']=pdo_fetchcolumn("select course_name from {$table_pe}lianhu_course where course_id={$value}" );
					}
				}
				$old_result=pdo_fetch("select * from {$table_pe}lianhu_syllabus where class_id=:cid order by addtime desc ",array(':cid'=>$_GPC['cid']));
				if($old_result){
					$data=unserialize($old_result['content']);
				}
			}else{
				message("非法访问1");
			}
		}
		if($ac=='save'){
			if(in_array($_GPC['cid'],$quanxian_class_id_arr)){
				$class_result=pdo_fetch("select * from {$table_pe}lianhu_class where class_id=:cid",array(':cid'=>$_GPC['cid']));
				$in['addtime']=TIMESTAMP;
				$in['teacher_uid']=$_W['uid'];
				$in['class_id']=$_GPC['cid'];
				$in['grade_id']=$class_result['grade_id'];
				$in['on_school']=$on_school;
				$in['am_much']=$am_much;
				$in['pm_much']=$pm_much;
				$in['ye_much']=$ye_much;
				$in['uniacid']=$_W['uniacid'];
				$in['school_id']=$_SESSION['school_id'];
				$content['am']=$_GPC['am'];
				$content['pm']=$_GPC['pm'];
				$content['ye']=$_GPC['ye'];
                $content['teacher_am']=$_GPC['teacher_am'];
                $content['teacher_pm']=$_GPC['teacher_pm'];
                $content['teacher_ye']=$_GPC['teacher_ye'];
				$in['content']=serialize($content);
				pdo_insert('lianhu_syllabus',$in);
				message("编辑成功",$this->createWebUrl('syllabus',array('ac'=>'new','cid'=>$_GPC['cid'])),'success');
			}else{
				message("非法访问2");
			}
		}
        if($ac=='edit_course_time'){
            if($_GPC['submit']=='提交'){
 				$in['uniacid']=$_W['uniacid'];
				$in['school_id']=$_SESSION['school_id'];
                $in['keyword']  ='course_time';
                $content['begin_time']=$_GPC['begin_time'];
                $content['end_time']=$_GPC['end_time'];   
                $in['content']=serialize($content);
                pdo_insert('lianhu_set',$in);               
				message("新增成功",$this->createWebUrl('syllabus',array('ac'=>'edit_course_time')),'success');
            }
            $result=pdo_fetch("select * from {$table_pe}lianhu_set where keyword='course_time' order by set_id  desc ");
            $result['content']=unserialize($result['content']);
            $result['begin_time']=$result['content']['begin_time'];
            $result['end_time']=$result['content']['end_time'];
        }