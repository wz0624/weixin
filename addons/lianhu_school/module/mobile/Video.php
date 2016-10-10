<?php 
defined('IN_IA') or exit('Access Denied');
$result=$this->mobile_from_find_student();
$class_re=pdo_fetch("select * from {$table_pe}lianhu_class where class_id =:cid",array(':cid'=>$result['class_id'])); 
$video_ids=unserialize($class_re['video_ids']);
if(!empty($video_ids)){
            $video_ids_str=implode(',',$video_ids);
            $now_time=date("H:i:s",time());
            $video_list=pdo_fetchall("select * from {$table_pe}lianhu_video where status=1 and begin_time<='{$now_time}'
            and end_time >='{$now_time}' and video_id in ({$video_ids_str}) ");     
            foreach ($video_list as $key => $value) {
                if(stristr($value['video_url'],"rtmp"))
                          $video_list[$key]['rmtp']=1;
           }
}
         