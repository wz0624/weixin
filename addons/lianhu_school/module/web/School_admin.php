<?php 
    defined('IN_IA') or exit('Access Denied');
    $this->teacher_qx();//只准管理员进入
    load()->model('user');
    $power="leave|home_work|lianhu_school_menu_teacher|lianhu_school_menu_course|lianhu_school_menu_student|lianhu_school_menu_msg|lianhu_school_menu_syllabus|lianhu_school_menu_neimsg|lianhu_school_menu_line|lianhu_school_menu_data_in|lianhu_school_menu_data_out|lianhu_school_menu_appointment|lianhu_school_menu_test|lianhu_school_menu_score_list|lianhu_school_menu_money|lianhu_school_menu_grade|lianhu_school_menu_class|lianhu_school_menu_student_record|lianhu_school_menu_video";
     $group_id_school=pdo_fetchcolumn("select id from ".tablename('users_group')." where name='学校组' ");
    if(!$group_id_school)
			message('请先设置一个学校用户组哦(组名：学校组)','','error');
	if($ac=='list'){
        $list=pdo_fetchall("select {$table_pe}lianhu_school_admin.*,  ".tablename('users').".username,{$table_pe}lianhu_school.school_name from {$table_pe}lianhu_school_admin
         left join ".tablename('users')." on ".tablename('users').".uid={$table_pe}lianhu_school_admin.uid
         left join {$table_pe}lianhu_school on {$table_pe}lianhu_school.school_id={$table_pe}lianhu_school_admin.school_id
         where {$table_pe}lianhu_school_admin.uniacid=:uniacid",array(':uniacid'=>$_W['uniacid']));
    }
    $school_list=pdo_fetchall("select * from {$table_pe}lianhu_school where uniacid=:uniacid",array(':uniacid'=>$_W['uniacid']));
    if($ac=='new'){
        if($_GPC['submit']){
            $passport=$_GPC['passport'];
         	if(!$_GPC['passport'] || !$_GPC['password'])
					message('系统账号信息必填','','error');
 			if(pdo_fetch("select * from ".tablename('users')." where username=:username ",array(':username'=>$passport)))
					message('系统里已经存在了此账号','','error');
			$password=user_hash($_GPC['password'],'hjasdf01');           
            $user_in['groupid']=$group_id_school;
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
					$pre_in['permission']=$power;
			}
			pdo_insert('users_permission',$pre_in);
   		    $accout_uses['uniacid']=$_W['uniacid'];
			$accout_uses['uid']=$user_id;
			$accout_uses['role']='operator';
			pdo_insert('uni_account_users',$accout_uses);         
            $in['uniacid']=$_W['uniacid'];            
            $in['school_id']=$_GPC['school_id'];            
            $in['uid']=$user_id;
            $in['status']=$_GPC['status'];
           pdo_insert('lianhu_school_admin',$in);
            message('新增成功',$this->createWebUrl('school_admin'),'success');
        }
    }
        if($ac=='edit'){
            $result=pdo_fetch("select {$table_pe}lianhu_school_admin.*,".tablename('users').".username,{$table_pe}lianhu_school.school_name from {$table_pe}lianhu_school_admin
            left join ".tablename('users')." on ".tablename('users').".uid={$table_pe}lianhu_school_admin.uid
            left join {$table_pe}lianhu_school on {$table_pe}lianhu_school.school_id={$table_pe}lianhu_school_admin.school_id
             where {$table_pe}lianhu_school_admin.admin_id=:admin_id",array(':admin_id'=>$_GPC['admin_id']));
            if(!$result){message('非法访问','','error');}
  			if($_GPC['submit']){
				$up['status']=$_GPC['status'];
                $uid=$result['uid'];
				if($up['status']==0)
                    pdo_update('users',array('status'=>1),array('uid'=>$uid));
                else
					pdo_update('users',array('status'=>2),array('uid'=>$uid));
                    
				if($_GPC['password']){ $password=user_hash($_GPC['password'],'hjasdf01'); pdo_update('users',array('password'=>$password),array('uid'=>$uid ));}
                 
                 $up['school_id']=$_GPC['school_id'];            
                 $up['status']  =$_GPC['status'];
                 
				pdo_update('lianhu_school_admin',$up,array('admin_id'=>$_GPC['admin_id']));
				message('修改成功',$this->createWebUrl('school_admin'),'success');
			}
		}      
        