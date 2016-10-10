<?php
    include_once(MODULE_ROOT.'/func.php');
    global $_W,$_GPC;
		if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
	    }else{
		    $url=$this->createMobileUrl('Errorjoin');			
			header("location:$url");
			exit;
		}	
        $weid = $_W['uniacid'];
		$cfg = $this->module['config'];	
		$flower_jifen = intval($cfg['flower_jifen']) ? intval($cfg['flower_jifen']):1;
		$suijinum = rand();
		$settings = pdo_fetch("SELECT * FROM ".tablename('meepo_hongniangset')." WHERE weid=:weid",array(':weid'=>$_W['weid']));
		$tablename = tablename("hnfans");
		$endToday=mktime(0,0,0,date('m'),date('d')-7,date('Y'))-1;
      $sql = "SELECT  toopenid,count(*) AS count,sum(flower_num) AS flower FROM ".tablename('meepo_hongnianglikes')." WHERE weid=:weid AND createtime>=:createtime GROUP BY toopenid ORDER BY flower DESC,count DESC";
				$list = pdo_fetchall($sql,array(':weid'=>$weid,':createtime'=>$endToday));
				if(!empty($list) && is_array($list)){
					foreach($list as $val){
						$temp = pdo_fetch("SELECT *  FROM " . $tablename . " WHERE yingcang=1 AND weid='{$weid}' AND isshow=1 AND from_user='{$val['toopenid']}'"); 
						if($temp['gender']=='2'){
							$list2[] = $temp;
							if(count($list2)==20){
								 break;
							}
						}
					}
					
				}
        if(!empty($list2) && is_array($list2)){
			
					foreach($list2 as $row){
						$photoss = $this->getphotos($row['from_user']);
						$num = count($photoss);
						if($num > 3){
						  $photos[$row['id']] = array($photoss[0],$photoss[1],$photoss[2]);
						}else{
						  $photos[$row['id']] =$photoss;
						}
						
					}
        }
        include $this->template('bangdan');