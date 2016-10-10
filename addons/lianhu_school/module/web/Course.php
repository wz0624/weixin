	<?php
    defined('IN_IA') or exit('Access Denied');
		global $_W,$_GPC;
		$this->teacher_qx();
		$school_uniacid=" and uniacid={$_W['uniacid']} and school_id={$_SESSION['school_id']} ";
		if($_GPC['ac']=='new'){
			if($_GPC['submit']=='提交'){
				if($_GPC['course_name']){
					$result=pdo_fetch("select * from {$table_pe}lianhu_course where course_name=:course_name {$school_uniacid} ",array(':course_name'=>$_GPC['course_name']));
					if($result){
						message('已经存在这个课程啦！',$this->createWebUrl('course'),'error');
					}
					$in['course_name']=$_GPC['course_name'];
					$in['addtime']=TIMESTAMP;
					$in['school_id']=$_SESSION['school_id'];
					$in['uniacid']=$_W['uniacid'];
					pdo_insert('lianhu_course',$in);
					message('新增成功',$this->createWebUrl('course'),'success');
				}else{
					message('请输入课程名',$this->createWebUrl('course'),'error');
				}				
			}
		}elseif($_GPC['ac']=='edit'){
			$result=pdo_fetch("select * from {$table_pe}lianhu_course where course_id=:course_id  {$school_uniacid}",array(':course_id'=>$_GPC['cid']));
			if($_GPC['submit']=='提交'){
				if($_GPC['course_name']){
					$result=pdo_fetch("select * from {$table_pe}lianhu_course where course_name=:course_name  and course_id !=:cid  {$school_uniacid}",array(':course_name'=>$_GPC['course_name'],':cid'=>$_GPC['cid']));
					if($result){
						message('已经存在这个课程啦！',$this->createWebUrl('course'),'error');
					}
					$in['course_name']=$_GPC['course_name'];
					pdo_update('lianhu_course',$in,array('course_id'=>$_GPC['cid']));
					message('更新成功',$this->createWebUrl('course'),'success');
				}else{
					message('请输入课程名',$this->createWebUrl('course'),'error');
				}				
			}
		}elseif($_GPC['ac']=='delete'){
			if($_GPC['cid']){
				pdo_delete('lianhu_course',array('course_id'=>$_GPC['cid']));
				$this->delete_course_class($_GPC['cid'],'all');
				$this->delete_course_teacher($_GPC['cid'],'all');
				message('删除成功',$this->createWebUrl('course'),'success');
			}
		}elseif($_GPC['ac']=='update'){
            if($_GPC['delete']==1){
                 if($_GPC['cid']){
                    pdo_update('lianhu_course',array('course_basic'=>0),array('course_id'=>$_GPC['cid']));
                    $this->add_course_class($_GPC['cid'],'all');
                    message('降为普通课程成功',$this->createWebUrl('course'),'success');
                }               
            }else{
                if($_GPC['cid']){
                    pdo_update('lianhu_course',array('course_basic'=>1),array('course_id'=>$_GPC['cid']));
                    $this->add_course_class($_GPC['cid'],'all');
                    message('设置为基础课程成功',$this->createWebUrl('course'),'success');
                }               
            }
		}else{
			$list=pdo_fetchall("select * from  {$table_pe}lianhu_course where 1=1 {$school_uniacid} ");
		}
		$ac=$_GPC['ac'];