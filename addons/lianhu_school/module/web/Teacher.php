<?php 
defined('IN_IA') or exit('Access Denied');
		$this->teacher_qx();//只准管理员进入
		load()->model('user');
		$school_uniacid=  " and  ".$this->where_uniacid_school;
		$class_list= pdo_fetchall("select * from {$table_pe}lianhu_class where status=1  {$school_uniacid} order by grade_id desc ");
		$course_list=pdo_fetchall("select * from {$table_pe}lianhu_course where 1=1 {$school_uniacid} ");
        $grades=$this->grade_class();
        $power_str="leave|home_work|lianhu_school_menu_student|lianhu_school_menu_msg|lianhu_school_menu_syllabus|lianhu_school_menu_line|lianhu_school_menu_data_in|lianhu_school_menu_data_out|lianhu_school_menu_appointment|lianhu_school_menu_test|lianhu_school_menu_score_list|lianhu_school_menu_student_record";
		#列表展示
        if($ac=='list'){
			if($_GPC['teacher_realname']){
				$teacher_realname=$this->lib_replace_end_tag($_GPC['teacher_realname']);
				$where_name="and tea.teacher_realname like '%{$teacher_realname}%'";
			}
			if($_GPC['status']){
				if($_GPC['status']==2){$_GPC['status']=0;}
				$where_status="and tea.status=".intval($_GPC['status']);
			}
			$school_uniacid_table=" and tea.uniacid={$_W['uniacid']} and tea.school_id={$_SESSION['school_id']} ";
			$list=pdo_fetchall("select tea.* ,users.username,fan.nickname from {$table_pe}lianhu_teacher tea left join ".tablename('users')." users on  users.uid=tea.fanid 
								left join ".tablename('mc_members')." fan on fan.uid=tea.uid  where 1=1 {$where_name} {$where_status} {$school_uniacid_table} ");
			$num=count($list);
		}
		if($ac=='new'){
			if($_GPC['submit']){
				$class_s=$_GPC['class_s'];
				$in['teacher_other_power']=implode(',', $class_s);
				$in['teacher_realname']=$_GPC['teacher_realname'];
				$in['teacher_telphone']=$_GPC['teacher_telphone'];
				$in['teacher_email']=$_GPC['teacher_email'];
				$in['teacher_img']=$_GPC['teacher_img'];
				$in['teacher_introduce']=$_GPC['teacher_introduce'];
                if($_GPC['course_id'])	
    				$in['course_id']=implode(',',$_GPC['course_id']);
				$in['weixin_code']=$_GPC['weixin_code'];
				$in['uniacid']=$_W['uniacid'];
				$in['school_id']=$_SESSION['school_id'];
				$group_id=pdo_fetchcolumn("select id from ".tablename('users_group')." where name='教师组' ");
				$passport=$_GPC['passport'];
				if(!$_GPC['passport'] || !$_GPC['password']){
					message('系统账号信息必填','','error');
				}
				if(pdo_fetch("select * from ".tablename('users')." where username=:username ",array(':username'=>$passport))){
					message('系统里已经存在了此账号','','error');
				}
				$password=user_hash($_GPC['password'],'hjasdf01');
				$user_in['groupid']=$group_id;
				$user_in['username']=$passport;
				$user_in['password']=$password;
				$user_in['salt']='hjasdf01';
				$user_in['status']=2;
				$user_in['joindate']=TIMESTAMP;
				pdo_insert('users',$user_in);
				$user_id=pdo_insertid();
				$pre_in['uid']=$user_id;
				$pre_in['uniacid']=$_W['uniacid'];
				if($this->module['config']['version']==6){
					$pre_in['url']="c=home&a=welcome&do=ext&m=lianhu_school";
				}
				if($this->module['config']['version']==7){
					$pre_in['type']='lianhu_school';
					$pre_in['permission']=$power_str;
				}
				pdo_insert('users_permission',$pre_in);
				$accout_uses['uniacid']=$_W['uniacid'];
				$accout_uses['uid']=$user_id;
				$accout_uses['role']='operator';
				pdo_insert('uni_account_users',$accout_uses);
				$in['fanid']=$user_id;
				$in['addtime']=TIMESTAMP;
				pdo_insert('lianhu_teacher',$in);
				message('新增成功',$this->createWebUrl('teacher'),'success');
			}
		}
		if($ac=='edit'){
			$id=(int)$_GPC['id'];
			$school_uniacid_table=" and tea.uniacid={$_W['uniacid']} and tea.school_id={$_SESSION['school_id']} ";
			$result=pdo_fetch("select tea.* ,users.username,users.uid from {$table_pe}lianhu_teacher tea left join ".tablename('users')." users on  users.uid=tea.fanid where tea.teacher_id={$id} {$school_uniacid_table} ");
			$result['class_s']=explode(',',$result['teacher_other_power']);
            $result['course_ids']=explode(',',$result['course_id']);
			if(!$result){message('非法访问','','error');}
			if($_GPC['submit']){
				$class_s=$_GPC['class_s'];
				$up['status']=$_GPC['status'];
				if($up['status']==0){ pdo_update('users',array('status'=>1),array('uid'=>$_GPC['uid']));}else{
					pdo_update('users',array('status'=>2),array('uid'=>$_GPC['uid']));
				}
				if($_GPC['password']){ $password=user_hash($_GPC['password'],'hjasdf01'); pdo_update('users',array('password'=>$password),array('uid'=>$_GPC['uid']));}
				$up['teacher_other_power']=implode(',', $class_s);
				$up['teacher_realname']=$_GPC['teacher_realname'];
				$up['teacher_telphone']=$_GPC['teacher_telphone'];
				$up['teacher_email']=$_GPC['teacher_email'];
				$up['teacher_img']=$_GPC['teacher_img'];
				$up['teacher_introduce']=$_GPC['teacher_introduce'];		
                if($_GPC['course_id'])	
    				$up['course_id']=implode(',',$_GPC['course_id']);
				$up['weixin_code']=$_GPC['weixin_code'];		
				pdo_update('lianhu_teacher',$up,array('teacher_id'=>$id));
				message('修改成功',$this->createWebUrl('teacher'),'success');
			}
		}
		if($ac=='unbundling'){
			$id=(int)$_GPC['id'];
			$up['uid']=0;
			pdo_update('lianhu_teacher',$up,array('teacher_id'=>$_GPC['id']));
			message('解绑成功',$this->createWebUrl('teacher'),'success');
		}
		if($ac=='delete'){
			pdo_delete('lianhu_teacher',array('teacher_id'=>$_GPC['id']));
			message('删除成功',$this->createWebUrl('teacher'),'success');
		}