<?php
		include_once(MODULE_ROOT.'/func.php');
		global $_W,$_GPC;
		$weid = $_W['uniacid'];
		$useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
		if(strpos($useragent, 'MicroMessenger') === false && strpos($useragent, 'Windows Phone') === false ){
			 $url=$this->createMobileUrl('Errorjoin');			
				header("location:$url");
				exit;
		}
		if(strpos($useragent, 'WindowsWechat')){
		    $url=$this->createMobileUrl('Errorjoin');			
				header("location:$url");
				exit;
		}
		//幻灯片
		$slide = pdo_fetchall("SELECT * FROM " . tablename('meepoweixiangqin_slide') . " WHERE weid = :weid AND status=1 ORDER BY displayorder DESC,id DESC LIMIT 6", array(':weid' => $weid));
		$sujinum = rand();
		$openid = $_W['openid'];
		$cfg = $this->module['config'];		
		$flower_jifen = intval($cfg['flower_jifen']) ? intval($cfg['flower_jifen']):1;
		$flower_time = intval($cfg['flower_time']) ? intval($cfg['flower_time'])*1000:8000;
		$settings = pdo_fetch("SELECT * FROM ".tablename('meepo_hongniangset')." WHERE weid=:weid",array(':weid'=>$_W['weid']));
		$res = $this->getusers($weid,$openid);
		if (!empty($openid)) {    
			    if($_W['fans']['follow'] != '1'){
					   $url =  empty($settings['url']) ? 'http://baidu.com' : $settings['url'];
				       header("location:$url");
				       exit;
					}else{
						if($cfg['telephoneconfirm'] == '1'){
								if($res['telephoneconfirm'] == '0' || empty($res['telephone'])){
									$smsurl=$this->createMobileUrl('sms');			
									header("location:$smsurl");
									exit;
								} 
						}
						if(empty($res['nickname'])){
							 $this->insertit();//录入
						}
						$tablename = tablename("hnfans");
						$gender = pdo_fetchcolumn("SELECT `gender` FROM ".  $tablename ." WHERE  weid=:weid AND from_user = :from_user",array(':weid'=>$weid,':from_user'=>$openid));
						$isshow =1;
						$tuijiannum = empty($cfg['tuijiannum']) ? 10 : intval($cfg['tuijiannum']);
						if($gender=='2'){
								$tuijian = pdo_fetchcolumn("SELECT count(*)  FROM " . $tablename . " WHERE  weid=:weid AND nickname!=:nickname AND isshow=:isshow  AND yingcang=:yingcang AND gender=:gender AND tuijian=:tuijian AND tj_over_time>=:tj_over_time",array(':weid'=>$weid,':nickname'=>'',':isshow'=>1,':yingcang'=>1,':gender'=>1,':tuijian'=>2,':tj_over_time'=>time())); 
								if($tuijian < $tuijiannum){
									$list1 = pdo_fetchall("SELECT * FROM " . $tablename . " WHERE  weid=:weid AND nickname!=:nickname AND isshow=:isshow  AND yingcang=:yingcang AND gender=:gender AND tuijian=:tuijian AND tj_over_time>=:tj_over_time ORDER BY love DESC,time DESC",array(':weid'=>$weid,':nickname'=>'',':isshow'=>1,':yingcang'=>1,':gender'=>1,':tuijian'=>2,':tj_over_time'=>time())); 
									$NUM = $tuijiannum - $tuijian;
									$list2 = pdo_fetchall("SELECT * FROM " . $tablename . " WHERE  weid=:weid AND nickname!=:nickname AND isshow=:isshow  AND yingcang=:yingcang AND gender=:gender AND tj_over_time<:tj_over_time ORDER BY rand() LIMIT 0,".$NUM,array(':weid'=>$weid,':nickname'=>'',':isshow'=>1,':yingcang'=>1,':gender'=>1,':tj_over_time'=>time())); 
									$list = array_merge_recursive($list1,$list2);
									
									
								}else{
										$list = pdo_fetchall("SELECT * FROM " . $tablename . " WHERE  weid=:weid AND nickname!=:nickname AND isshow=:isshow  AND yingcang=:yingcang AND gender=:gender AND tuijian=:tuijian  AND tj_over_time>=:tj_over_time ORDER BY love DESC,time DESC",array(':weid'=>$weid,':nickname'=>'',':isshow'=>1,':yingcang'=>1,':gender'=>1,':tuijian'=>2,':tj_over_time'=>time()));
								}
						}else{
						    $tuijian = pdo_fetchcolumn("SELECT count(*)  FROM " . $tablename . " WHERE  weid=:weid AND nickname!=:nickname AND isshow=:isshow  AND yingcang=:yingcang AND gender=:gender AND tuijian=:tuijian AND tj_over_time>=:tj_over_time",array(':weid'=>$weid,':nickname'=>'',':isshow'=>1,':yingcang'=>1,':gender'=>2,':tuijian'=>2,':tj_over_time'=>time())); 
								if($tuijian < $tuijiannum){
									$list1 = pdo_fetchall("SELECT * FROM " . $tablename . " WHERE  weid=:weid AND nickname!=:nickname AND isshow=:isshow  AND yingcang=:yingcang AND gender=:gender AND tuijian=:tuijian AND tj_over_time>=:tj_over_time ORDER BY love DESC,time DESC",array(':weid'=>$weid,':nickname'=>'',':isshow'=>1,':yingcang'=>1,':gender'=>2,':tuijian'=>2,':tj_over_time'=>time())); 
									$NUM = $tuijiannum - $tuijian;
									$list2 = pdo_fetchall("SELECT * FROM " . $tablename . " WHERE  weid=:weid AND nickname!=:nickname AND isshow=:isshow  AND yingcang=:yingcang AND gender=:gender AND tj_over_time<:tj_over_time ORDER BY rand() LIMIT 0,".$NUM,array(':weid'=>$weid,':nickname'=>'',':isshow'=>1,':yingcang'=>1,':gender'=>2,':tj_over_time'=>time())); 
									$list = array_merge_recursive($list1,$list2);
									
									
								}else{
										$list = pdo_fetchall("SELECT * FROM " . $tablename . " WHERE  weid=:weid AND nickname!=:nickname AND isshow=:isshow  AND yingcang=:yingcang AND gender=:gender AND tuijian=:tuijian  AND tj_over_time>=:tj_over_time ORDER BY love DESC,time DESC",array(':weid'=>$weid,':nickname'=>'',':isshow'=>1,':yingcang'=>1,':gender'=>2,':tuijian'=>2,':tj_over_time'=>time()));
								}
						}
						if(!empty($list) && is_array($list)){
								foreach($list as $row){
									if(!empty($row['lat']) && !empty($row['lng'])){
										if(!empty($res['lat']) && !empty($res['lng'])){
										   $juli[$row['id']]= "相距: ".getDistance($res['lat'],$res['lng'],$row['lat'],$row['lng'])."km";
										}else{
											$juli[$row['id']]= ""; 
										}
									}else{
										 $juli[$row['id']]= ""; 
									}
								}
							
						}
					}
		}else{
		   $url =  empty($settings['url']) ? 'http://baidu.com' : $settings['url'];
		   header("location:$url");
		   exit;
		}
		if($cfg['telephoneconfirm'] == '1'){
		    if($res['telephoneconfirm'] == '0' || empty($res['telephone'])){
			   $smsurl=$this->createMobileUrl('sms');			
				header("location:$smsurl");
				exit;
			} 
		}
		
		$all_num = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('hnfans')." WHERE weid = :weid",array(':weid'=>$_W['uniacid']));
		include $this->template('alllist');
?>