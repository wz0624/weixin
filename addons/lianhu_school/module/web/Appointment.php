<?php 
defined('IN_IA') or exit('Access Denied');
		if($this->module['config']['appointment'][$_SESSION['school_id']]){
			$appointment_cfg=explode("||", $this->module['config']['appointment'][$_SESSION['school_id']]);
			foreach ($appointment_cfg as $key => $value) {
				if($value){
					$appointment[]=$value;
				}
			}
		}else{
			$appointment=$_W['appointment'];
		}
		$teacher=$this->teacher_qx('no');
		$school_uniacid=" and uniacid={$_W['uniacid']} and school_id={$_SESSION['school_id']} ";
		$ac=$_GPC['ac'] ? $_GPC['ac']:"list";
		$aid=intval($_GPC['aid']);
		$appointment_limit=$_W['appointment_limit'];
		$school_uniacid_class=" and class.uniacid={$_W['uniacid']} and class.school_id={$_SESSION['school_id']} ";
		if($teacher=='teacher'){
			$uid=$_W['uid'];
			$t_id=pdo_fetchcolumn("select teacher_id from {$table_pe}lianhu_teacher where fanid={$uid}");
			$class_list=pdo_fetchall("select class.*, sylla.syllabus_id from {$table_pe}lianhu_class class left join {$table_pe}lianhu_syllabus sylla on sylla.class_id=class.class_id  where class.status=1 and class.teacher_id={$t_id} {$school_uniacid_class} ");
		}else{
			$grade_list=pdo_fetchall("select * from {$table_pe}lianhu_grade where status=1 {$school_uniacid}");
			$class_list=pdo_fetchall("select class.*, sylla.syllabus_id from {$table_pe}lianhu_class class left join {$table_pe}lianhu_syllabus sylla on sylla.class_id=class.class_id  where class.status=1 {$school_uniacid_class} ");
			$t_id=0;
		}
		if(!$class_list){message('您不是管理员或者班主任，或者没有设置年级和班级','','error');}		
		
		if($ac=='list'){
			$total=pdo_fetchcolumn("select count(*) num from {$table_pe}lianhu_appointment where 1=1 {$school_uniacid}");			
			$list=pdo_fetchall("select * from {$table_pe}lianhu_appointment  where 1=1 {$school_uniacid} order by appointment_id desc {$sql_limit}");
    }
            $list_max=pdo_fetchall("select * from {$table_pe}lianhu_appointment_course where course_type=1   {$school_uniacid} and status=1");
		    $list_min=pdo_fetchall("select * from {$table_pe}lianhu_appointment_course where course_type=2   {$school_uniacid} and status=1");
		if($ac=='new'){
			if($_GPC['submit']){
				if($_GPC['aname']){
					$limit_list='';
					if($_GPC['limit_type']==0){
					}elseif($_GPC['limit_type']==1){
						$limit_list=$_GPC['grades'];
					}elseif($_GPC['limit_type']==2){
						$limit_list=$_GPC['class'];
					}
                    $in['appointment_mutex']=serialize($_POST['appointment_mutex']);
					$in['appointment_limit']=serialize(array('type'=>$_GPC['limit_type'],'list'=>$limit_list));
					$in['appointment_type_limit']=$_GPC['limit_type'];
					$in['appointment_grade_class']=implode(',', $limit_list);
					$in['appointment_name']=$_GPC['aname'];
					$in['appointment_intro']=$_GPC['aintro'];
					$in['appointment_content']=$_GPC['acontent'];
					$in['appointment_start']=strtotime($_GPC['atime']['start']);
					$in['appointment_end']=strtotime($_GPC['atime']['end']);
					$in['appointment_addtime']=TIMESTAMP;
					$in['appointment_type']=$_GPC['atype'];
					$in['appointment_max_num']=$_GPC['amax_num'];
					$in['teacher_id']=$t_id;
					$in['uniacid']=$_W['uniacid'];
					$in['school_id']=$_SESSION['school_id'];
					pdo_insert('lianhu_appointment',$in);
					message('新增成功',$this->createWebUrl('appointment'),'success');
				}else{message('请输入预约名称',$this->createWebUrl('appointment'),'error');}
			}
		}
		if($ac=='edit'){
			if($_GPC['submit']){
				if($_GPC['aname']){
					$limit_list='';
					if($_GPC['limit_type']==0){

					}elseif($_GPC['limit_type']==1){
						$limit_list=$_GPC['grades'];
					}elseif($_GPC['limit_type']==2){
						$limit_list=$_GPC['class'];
					}
                    $in['appointment_mutex']=serialize($_POST['appointment_mutex']);
					$in['appointment_limit']=serialize(array('type'=>$_GPC['limit_type'],'list'=>$limit_list));
					$in['appointment_type_limit']=$_GPC['limit_type'];
					$in['appointment_grade_class']=implode(',', $limit_list);
					$in['appointment_name']=$_GPC['aname'];
					$in['appointment_intro']=$_GPC['aintro'];
					$in['appointment_content']=$_GPC['acontent'];
					$in['appointment_start']=strtotime($_GPC['atime']['start']);
					$in['appointment_end']=strtotime($_GPC['atime']['end']);
					$in['appointment_type']=$_GPC['atype'];
					$in['appointment_max_num']=$_GPC['amax_num'];
					$in['teacher_id']=$t_id;
					pdo_update('lianhu_appointment',$in,array('appointment_id'=>$aid));
                    
					message('更新成功',$this->createWebUrl('appointment'),'success');
				}else{message('请输入预约名称',$this->createWebUrl('appointment'),'error');}				
			}
			$result=pdo_fetch("select * from {$table_pe}lianhu_appointment where appointment_id=:aid",array(':aid'=>$aid));
			$limit=unserialize($result['appointment_limit']);
			$limit_type=$limit['type'];
			$limit_arr=$limit['list'];
            $result['appointment_mutex']=unserialize($result['appointment_mutex']);
		}
        #可预约的课程
        if($ac=='ac_new'){
            if($_GPC['submit']){
                $in['school_id']=$_SESSION['school_id'];
                $in['uniacid']=$_W['uniacid'];   
                 
                $in['course_name']=$_GPC['course_name'];
                $in['course_type']=$_GPC['course_type'];
                $in['course_num']=$_GPC['course_num'];
                $in['course_content']=$_GPC['course_content'];
                $in['status']=$_GPC['status'];
                $time['a']=$_POST['timea'];
                $time['b']=$_POST['timeb'];
                if($time){
                    $in['course_time']=serialize($time);
                }
               pdo_insert("lianhu_appointment_course",$in);
                message("新增成功",$this->createWebUrl('appointment',array('ac'=>'ac_list')),'success');
            }
        }
        if($ac=='ac_edit'){
            $aid=$_GPC['aid'];
            $result=pdo_fetch("select * from {$table_pe}lianhu_appointment_course where course_id=:aid",array(':aid'=>$aid));
            $time=unserialize($result['course_time']);
            if($_GPC['submit']){
                $in['course_name']=$_GPC['course_name'];
                $in['course_type']=$_GPC['course_type'];
                $in['course_num']=$_GPC['course_num'];
                $in['course_content']=$_GPC['course_content'];
                $in['status']=$_GPC['status'];
                $time['a']=$_POST['timea'];
                $time['b']=$_POST['timeb'];
                if($time){
                    $in['course_time']=serialize($time);
                }     
                pdo_update('lianhu_appointment_course',$in,array('course_id'=>$aid));
                message("更新成功",$this->createWebUrl('appointment',array('ac'=>'ac_list')),'success');
            }
       }
       if($ac=='ac_list'){
           $where=$this->where_uniacid_school;
           $list=pdo_fetchall("select * from {$table_pe}lianhu_appointment_course where {$where} order by course_id desc");
       }
       
        