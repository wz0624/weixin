<?php
defined('IN_IA') or exit('Access Denied');
if($ac=='send_msg_line'){
    $teacher_info  =$this->teacher_mobile_qx();
    $queue_id      =$_GPC['queue_id'];
    if(!$queue_id) return false;
    $this->sendAllMsg($queue_id);
    $end_time      =date("H:i:s",time());
    echo  json_encode(array("end_time"=>$end_time,'status'=>2));
    exit();
}
if($op=='line_like'){
    $in_type            =$this->judePortType();
    $send_id            =$_GPC['send_id'];   
    if(!$this->zanLine($send_id)){
       $in['send_id']   =$send_id;
       $in['uid']       =$uid;
       $in['add_time']  =time();    
       pdo_insert("lianhu_send_like",$in);
       $send_re         =pdo_fetch("select * from {$table_pe}lianhu_send where send_id=:id",array(":id"=>$send_id));
       $like_num        =$send_re['send_like']+1;
       pdo_update('lianhu_send',array('send_like'=>$like_num),array('send_id'=>$send_id));
     }
       $lan_name=$this->getLineZanName($send_id);
       exit($lan_name);         
}
if($op=='huifu'){
    $in_type           =$this->judePortType();
    if($in_type['type']=='teacher')  $class_id=$_GPC['class_id'];
    else                             $class_id=$in_type['info']['class_id'];  
     $send_id          =$_GPC['send_id'];   
     $content          =$_GPC['content'];   
     $in['send_id']          =$send_id;
     $in['comment_uid']      =$uid;
     $in['comment_text']     =$content;
     $in['add_time']         =time();
     pdo_insert('lianhu_send_comment',$in);  
     $list             =$this->getLineComplete($send_id);
     foreach ($list as $key => $value) {
         $html .="<span>{$value['nickname']}:</span>{$value['comment_text']}<br>";
     }
     exit($html);
}
if($op=='line_change'){
    $in_type    =$this->judePortType();
     $send_id   =$_GPC['send_id'];
     $ac        =$_GPC['ac'];
     if($ac=='like'){
         if(!$this->zanLine($send_id)){
             $in['send_id']   =$send_id;
             $in['uid']       =$uid;
             $in['add_time']  =time();    
             pdo_insert("lianhu_send_like",$in);
             $send_re         =pdo_fetch("select * from {$table_pe}lianhu_send where send_id=:id",array(":id"=>$send_id));
             $like_num        =$send_re['send_like']+1;
             pdo_update('lianhu_send',array('send_like'=>$like_num),array('send_id'=>$send_id));
             echo json_encode(array('errcode'=>0,msg=>''));        
         }
     }
     if($ac=='delete'){
         if($in_type['type']=='student')
             $where['send_uid'] =$uid;
         $where['send_id']      =$send_id;
         $result                =pdo_update("lianhu_send",array('send_status'=>3),$where);
         if(!$result)  echo json_encode(array('errcode'=>1,msg=>'删除失败'));          
         else          echo json_encode(array('errcode'=>0,msg=>''));              
     }
     exit();
}
if($op=='line_all'){
    $in_type      =$this->judePortType();
    if($in_type['type']=='teacher')   $class_id =$_GPC['class_id'];
    else                              $class_id=$in_type['info']['class_id'];   
    $pager        =$_GPC['page'];
    $list         =$this->getLineList($pager,10,$class_id);
    if(empty($list))
     {
         echo json_encode(array('errcode'=>1,'msg'=>'已经全部查看！'));
         exit();
     }
     foreach ($list as $key => $value) {
         if($uid==$value['send_uid'] || $in_type['type']=='teacher' )
             $add ='<a href="javascript:;" class="close delete" data-send="'.$value['send_id'].'"><img src="'.MODULE_URL.'/style/images/close.png"></a>';
         else 
            $add  ='';
         if($this->zanLine($value['send_id']) )
             $color='color:#07E';
         else 
            $color='color:#333';
     $comment_list=$this->getLineComplete($value['send_id']);
     $html_content='';
     foreach ($comment_list as $k => $val) {
         $html_content .="<span>{$val['nickname']}:</span>{$val['comment_text']}<br>";
     }           
         $html .='<UL  id="list_id_'.$value['send_id'].'" >
            <LI class="box" >
            		<div class="author">
                    		<a href="#"><img src="'.$value['avatar'].'"></a>
                            <p class="author_name">'.$value['nickname'].'</p>
                            '.$add.'
                    </div>
                        <div class="topic">
                            <p>'.$value['send_content'].'</p>
                            <div onclick="displayImage(this)">
                               '.$this->decodeLineImgs($value['send_image'],true).'
                                <div class="clear"></div>
                             </div>
                            <p class="author_time">'. date("m-d H:i",$value['add_time']).'</p>
                        </div>
                    <div class="click_hf">
                        <a class="zan" id="zan_'.$value['send_id'].'"  style="'.$color.'"  
                        data-send="'.$value['send_id'].'" href="javascript:;">
                            <i class="fa fa-heart-o"></i>
                       </a>
                        <span id="like_num_'.$value['send_id'].'"  class="like_name">'.$this->getLineZanName($value['send_id']).'</span>
                         <div  class="comment huifu" data-id="'.$value['send_id'].'"><i class="fa fa-comment-o"></i>
                        </div>
                    <div class="comment_list_line" id="comment_list_line'.$value['send_id'].'">
                    '.$html_content.'
                    </div>
                    </div>
            </LI>           
            </UL>';
            
     }
        $html .="<script>
            $('.zan').on('click',function(){
                send_id=$(this).attr('data-send');
                ajaxComment(send_id,1,'line_like','like_num_')
                $('#zan_'+send_id).css('color','#07E');    
            });
            $('.huifu').click(function(){
                send_id=$(this).attr('data-id');
                $('#comment_area').show();
            });            
            $('.delete').on('click',function(){
                send_id=$(this).attr('data-send');
                line_ajax(send_id,'delete');
            });      
        </script>";
        echo json_encode(array('html'=>$html));
        exit();
}
if($op=='line' && $_GPC['home_work']=='home_work'){
    $student_info =$this->mobile_from_find_student();
     $pager       =$_GPC['page'];
     $start       =($pager-1)*20;
     $news_list   =pdo_fetchall("select * from {$table_pe}lianhu_homework where class_id={$student_info['class_id']} and status=1 order by add_time desc limit {$start},20");
     $count      =count($news_list);
     if($count < $pagesize)		$arr['done']='yes';
     if($count>0){
		$arr['list'] ='yes';
        foreach ($news_list as $key => $value) {
         if($value['teacher_id'])  $add=$_W['attachurl'].$this->getTeacherImg($value['teacher_id']);
         else                      $add=MODULE_URL.'icon.jpg'; 
          $value['teacher_realname']   =$value['teacher_realname']?$value['teacher_realname']:"管理员";
            $html.='
       		<ul>
            <li class="box" >
            		<div class="author">
                    		<a href="#"><img src="'.$add.'"></a>
                            <p class="author_name">'.$value['teacher_realname'].$this->courseName($row['course_id']).'</p>
                            <p class="author_time">时间：'.date("Y-m-d H:i:s",$row['add_time'] ).'</p>
                    </div>
                            <div class="topic" style="margin-top:-10px;">
                                    <a href="'.$this->createMobileUrl('line_article',array('hid'=>$row['homework_id'] )).'">
                                            <p>'.$this->clear_html_short($row['content']).'......</p>
                                    </a>
                                <div onclick="displayImage(this)">
                                    '.$this->decodeLineImgs($row['img']).'
                                    <div class="clear"></div>
                                </div>                                    
                            </div>
                       </li> </ul>';
        }
        $arr        ='';
        $arr['html']=$arr;
     }
     echo json_encode($arr);
     exit();
}
if($op=='line' && $_GPC['home_work']!='home_work'){
    $student_info  =$this->mobile_from_find_student();
	$typeid        =$_GPC['type_id'];
	$news_list     =pdo_fetchall("select line.*,tea.teacher_realname from {$table_pe}lianhu_line line left join {$table_pe}lianhu_teacher tea on tea.teacher_id=line.teacher_id  where line.class_id=:cid and line.status=1 and  line_type={$typeid} order by line.addtime desc {$sql_limit}",array(':cid'=>$student_info['class_id']));	
	$count=count($news_list);
	if($count < $pagesize)
		$arr['done']='yes';
	if($count>0){
		$arr['list']='yes';
		foreach ($news_list as $key => $value) {
			$arr['list_con'][$key]['url']=$this->createMobileUrl('line_article',array('op'=>$value['line_id']));
			$arr['list_con'][$key]['title']=$value['line_title'];
			$arr['list_con'][$key]['content']=$this->clear_html_short($value['line_content']);
			$arr['list_con'][$key]['teacher_realname']=$value['teacher_realname']?$value['teacher_realname']:"管理员";
			$arr['list_con'][$key]['time']=date("Y-m-d H:i:s",$value['addtime']);
			$arr['list_con'][$key]['num']=$value['line_look'];
            if($value['teacher_id'])
             $add=$_W['attachurl'].$this->getTeacherImg($value['teacher_id']);
         else 
            $add =MODULE_URL.'icon.jpg';         
         $html  .='<UL >
            <LI class="box" >
            		<div class="author">
                    		<a href="#"><img src="'.$add.'"></a>
                            <p class="author_name">'.$arr['list_con'][$key]['teacher_realname'].'</p>
                            <p class="author_time">时间：'.$arr['list_con'][$key]['time'].'</p>
                    </div>
                        <div class="topic">
                            <p>'.$arr['list_con'][$key]['content'].'</p>
                        </div>
                    <div class="click_hf">
                        <a class="zan" style="color:#ff0033";href="javascript:;">
                            <i class="fa fa-heart"></i>
                       </a>
                        <span  >'.$arr['list_con'][$key]['num'].'</span>
                    </div>
            </LI>           
            </UL>';
		}
        $arr        ='';
        $arr['html']=$arr;
	}
	echo json_encode($arr);
    exit();
}
if($op=='appointment'){
    $student_info =$this->mobile_from_find_student();
	$where=" appointment_type_limit=0 || (appointment_type_limit=1 && appointment_grade_class like '%{$student_info['grade_id']}%' ) || (appointment_type_limit=2 && appointment_grade_class like '%{$student_info['class_id']}%' ) ";
	$app_result   =pdo_fetch("select * from {$table_pe}lianhu_appointment where ($where) and  appointment_id=:id",array(':id'=>$_GPC['appointment_id']));
	if(!$app_result){ echo json_encode(array('msg'=>'无此预约'));exit();}
	if(empty($app_result['appointment_mutex'])){
		#无限制
		$join_num    =pdo_fetchcolumn("select count(*) num  from {$table_pe}lianhu_applist where appointment_id={$app_result['appointment_id']} and status !=2");	
		if($join_num>=$app_result['appointment_max_num']){echo json_encode(array('msg'=>'已经满员啦'));exit();}
	}else{
		#有限制
			$arr        =unserialize($app_result['appointment_mutex']);
			$active_arr =explode('||', $arr['content']);
			$join_num   =pdo_fetchcolumn("select count(*) num  from {$table_pe}lianhu_applist where appointment_id={$app_result['appointment_id']} and status !=2 ");
			if($join_num>=$arr['num']){echo json_encode(array('msg'=>'您加入的项目达到限制了！'));exit();}
			$join_num   =pdo_fetchcolumn("select count(*) num  from {$table_pe}lianhu_applist where appointment_id={$app_result['appointment_id']} and status !=2 and content like :con",array(':con'=>"%{$_GPC['active']}%"));	
			foreach ($active_arr as $key => $value) {
				list($active_list[$key]['name'],$active_list[$key]['max'])=explode('--', $value);
				if($active_list[$key]['name']==$_GPC['active']){
			      if($join_num>=$active_list[$key]['max']){echo json_encode(array('msg'=>'此项目已经满员啦！'));exit();}
				}
			}
	}
	#all pass
	pdo_update('lianhu_appointment',array('appointment_join_num'=>$app_result['appointment_join_num']+1),array('appointment_id'=>$app_result['appointment_id']));
	pdo_insert('lianhu_applist',array('appointment_id'=>$app_result['appointment_id'],'student_id'=>$student_info['student_id'],'addtime'=>TIMESTAMP,'content'=>$_GPC['active'],'uniacid'=>$_W['uniacid'],'school_id'=>$_SESSION['school_id']));
	echo json_encode(array('status'=>'yes'));
     exit();
}
if($ac=='student_score_list'){
    $student_info  =$this->mobile_from_find_student();
	$class_id      =$_GPC['cid'];
	$course_id     =$_GPC['course_id'];
	$scorejilv_id  =$_GPC['scorejilv_id'];
	$list          =pdo_fetchall("select * from {$table_pe}lianhu_scorelist where course_id=:course_id and ji_lv_id=:ji_lv_id and class_id=:class_id",array(':course_id'=>$course_id,':ji_lv_id'=>$scorejilv_id,':class_id'=>$class_id));
	echo json_encode(array('status'=>"yes",'student_score_list'=>$list));	
    exit();
}
exit();