<?php
require '../../framework/bootstrap.inc.php';
$rtime = date("Y-m-d H:i",time()); 
$real_time = strtotime($rtime);
$online_list = pdo_fetchall("SELECT `id`,`weid` FROM ".tablename('meepo_online_list')." WHERE start_time=:start_time",array(':start_time'=>$real_time));
if(!empty($online_list) && is_array($online_list)){
	foreach($online_list as $row){
						$site = WeUtility::createModuleSite('meepo_online');
						if(!is_error($site)) {
							$method = 'sendMess';
							if (method_exists($site, $method)) {
								$ret = array();
								$ret['uniacid'] = $row['weid'];
								$ret['listid'] = $row['id'];
								
								$site->$method($ret);
							}
						}
	}
}
?>