<?php
defined('IN_IA') or exit('Access Denied');
if(!$_GPC['limit_id']){message('没有传入id值','','error');}
$total=pdo_fetchcolumn("select count(*) num  from {$table_pe}lianhu_money_record where limit_id=:limit_id ",array(':limit_id'=>$_GPC['limit_id']));
$list=pdo_fetchall("select li.limit_name,stu.student_name,me.nickname,re.* from {$table_pe}lianhu_money_record  re 
					left join ".tablename('mc_members')." me on me.uid=re.uid 
					left join {$table_pe}lianhu_student stu on stu.student_id=re.student_id
					left join {$table_pe}lianhu_money_limit li on li.limit_id=re.limit_id
					where li.limit_id=:limit_id order by addtime desc {$sql_limit}",array(':limit_id'=>$_GPC['limit_id']));

