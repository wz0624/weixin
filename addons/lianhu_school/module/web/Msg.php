<?php 	
    defined('IN_IA') or exit('Access Denied');
	$teacher=$this->teacher_qx('no');
    $admin_name=$this->getWebAdminName();
	$school_uniacid=" and ".$this->where_uniacid_school;
		if($teacher=='teacher'){
			$model=$_GPC['model'] ? $_GPC['model'] :"class";
			if($model=='class'){
				$result=$this->teacher_standard('no');
			}else{
				$result=$this->student_standard();		
			}
            
        }else{
			$model=$_GPC['model'] ? $_GPC['model'] :"grade";
			$result=$this->student_standard();		
		}
        
		if($_GPC['submit_weixin'] || $_GPC['submit_sms'] || $_GPC['submit_kf']){
			if(!$_GPC['content'] && $_GPC['submit_weixin']=='提交' ){message('请填写内容','','error');}
			$have=$_GPC['have'];
			if(!$have){message('请选择用户或者群组','','error');}
            foreach ($have as $key => $value) {
                $value=intval($value);
                if(!$value)
                    unset($have[$key]);
                else 
                  $have[$key]=$value;
            }
			if($model=='grade'){
				$grade_id_str=implode(',', $have);
				$student_list=pdo_fetchall("select * from {$table_pe}lianhu_student where grade_id in({$grade_id_str}) and status=1 and (fanid !='' or fanid1 !='' or fanid2 !='') {$school_uniacid} ");
			}			
			if($model=='class'){
				$class_id_str=implode(',', $have);
				$student_list=pdo_fetchall("select * from {$table_pe}lianhu_student where class_id in({$class_id_str}) and status=1 and (fanid !='' or fanid1 !='' or fanid2 !='') {$school_uniacid}");				
			}
			if($model=='student'){
				$student_id_str=implode(',', $have);
				$student_list=pdo_fetchAll("select * from {$table_pe}lianhu_student where student_id in({$student_id_str}) and status=1 and (fanid !='' or fanid1 !='' or fanid2 !='') {$school_uniacid} ");	
			}
			$mu_id=$_GPC['mu_id'];
			$acid=pdo_fetchcolumn("select acid from ".tablename('account')." where uniacid={$_W['uniacid']}");
			load()->classs('weixin.account');
			$accObj= WeixinAccount::create($acid);
			$i=0;
            $que_num=false;
            #微信发送
            if($_GPC['submit_weixin']=='提交'){
                $url=$_GPC['url'];
                foreach ($student_list as $key => $value) {
                    #遍历and发送
                    $openids=$this->returnEfficeOpenid($value,3);
                    $data=array(
                        'first'   =>array('value'=>$value['student_name'].'的家长您好，您有一个学校通知，请查看'),
                        'keyword1'=>array('value'=>$_SESSION['school_name']),
                        'keyword2'=>array('value'=>$admin_name),
                        'keyword3'=>array('value'=>date("Y-m-d H:i:s",TIMESTAMP)),
                        'keyword4'=>array('value'=>$_GPC['content']),
                        'remark'  =>array('value'=>$_GPC['remark']),
                        );
                    foreach ($openids as $key => $v) {
                        if($v){
                           $que_num=$this->insertMsgQueueMu($v,$data,$mu_id,$_GPC['url'],$que_num);
                        }
                    }
                }                
            }
            #客服消息
            if($_GPC['submit_kf']=='客服消息'){
                $url_kf      =$_GPC['url_kf'];
                $content_kf  =$_GPC['content_kf'];
                $title_kf    =$_GPC['title_kf'];
                foreach ($student_list as $key => $value) {
                    #遍历and发送
                    $openids=$this->returnEfficeOpenid($value,3);
                     foreach ($openids as $key => $v) {
                        if($v){
                            $data=array('title'=>$title_kf,'url'=>$url_kf,'content'=>$content_kf);
                            $que_num=$this->insertMsgQueueKe($v,$data,$que_num);
                        }
                    }                   
                }
            }
            if($_GPC['submit_sms']=='发送短信'){
                foreach ($student_list as $key => $value) {
                    $data=array('head'=>$_GPC['sms_head'],'content'=>$_GPC['sms_content']);
                    #遍历and发送
                    if($value['fanid']){
                        $phone=$this->sendSms($value['fanid']);
                        if($phone)
                            $que_num=$this->insertMsgQueueSms($phone,$data,$que_num);
                    }
                    if($value['fanid1']){
                        $phone=$this->sendSms($value['fanid1']);
                        if($phone)                     
                            $que_num=$this->insertMsgQueueSms($phone,$data,$que_num);
                    }
                    if($value['fanid2']){
                        $phone=$this->sendSms($value['fanid2']);
                        if($phone)                       
                            $que_num=$this->insertMsgQueueSms($phone,$data,$que_num);
                    }    
                }                
            }            
            message('添加成功，跳转往发送页面，请勿关闭',$this->createWebUrl('sendToMsg',array('que_num'=>$que_num)),'success');
 }		