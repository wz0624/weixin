<?php 
defined('IN_IA') or exit('Access Denied');
set_time_limit(0);
if($_GPC['que_num']){
    $list=pdo_fetchall("select * from {$table_pe}lianhu_msg_queue
      where queue_num=:num order by queue_status desc ,end_time desc ",array(":num"=>$_GPC['que_num']));
    $not_send_list=pdo_fetchall("select * from {$table_pe}lianhu_msg_queue
      where queue_num=:num and queue_status=1   order by queue_status desc ,end_time desc ",array(":num"=>$_GPC['que_num']));      
    $count=count($not_send_list);
}