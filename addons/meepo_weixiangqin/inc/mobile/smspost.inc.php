<?php
global $_W,$_GPC;
$openid = $_W['openid'];
$weid = $_W['uniacid'];
$cfg = $this->module['config'];
if(empty($openid)){
   $data =array(
	 'error'=>1,
	   'message'=>'参数错误',
   );
   die(json_encode($data));
}
if($_W['isajax']) {
	$user = $this->getusers($weid,$openid);
	$gender = intval($_GPC['gender']);
	$wechat = $_GPC['wechat'];
	$mobile = $_GPC['mobile'];
	if(!empty($_GPC['mobile']) && !empty($_GPC['yzm'])){
		$check = pdo_fetchcolumn("SELECT news FROM".tablename('meepo_sms_news')." WHERE openid=:openid AND weid=:weid",array(':openid'=>$openid,':weid'=>$weid));
		if($_GPC['yzm'] == $check){
				if(!empty($user)){
						pdo_update('hnfans',array('telephoneconfirm'=>1,'gender'=>$gender,'wechat'=>$wechat,'telephone'=>$mobile),array('from_user'=>$openid,'weid'=>$weid));
				}else{
						
						 load()->classs('weixin.account');
						 $accObj= WeixinAccount::create($_W['account']['acid']);
						 $access_token = $accObj->fetch_token();
						 if(empty($access_token)){
							 $data =array('error'=>1,'message'=>'管理员配置的参数有误');
							 die(json_encode($data));
						 }else{
								load()->func('communication');
								$url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
								$content2 = ihttp_request($url);
								$info = @json_decode($content2['content'], true);
								if(empty($info['nickname'])){
									 $data =array('error'=>1,'message'=>'管理员配置的参数有误');
									 die(json_encode($data));
								}else{
									 $row = array();
									 $onoff = pdo_fetchcolumn('SELECT `status` FROM ' .tablename('meepo_hongniangonoff') . ' WHERE weid=:weid',array(':weid' => $weid));
									 $row = array(
										'nickname'=> $info["nickname"],
										'realname'=> $info["nickname"],
										'avatar' => $info["headimgurl"],
										'gender'  => $gender,
										'time'=>time()
									);
									if($onoff!='0'){
											 $row['isshow'] =0;
									}else{
											 $row['isshow'] = 1;
									}  
									if($cfg['yingcang'] == '2'){
											 $row['yingcang'] =2;
									}else{
											 $row['yingcang'] =1;
									} 
									if(!empty($info["country"])){
										$row['nationality']=$info["country"];
									}
									if(!empty($info["province"])){
										$row['resideprovincecity']=$info["province"].$info["city"];
									}
									$row['telephoneconfirm'] = 1;
									$row['wechat'] = $wechat;
									$row['telephone'] = $mobile;
									$row['weid'] = $weid;
									$row['from_user'] = $openid;
									pdo_insert('hnfans',$row);	
								}
						} 
				}
				$data =array('error'=>0,'message'=>'success');
		}else{
			$data =array('error'=>1,'message'=>'验证码不正确');
		}
	}else{
			$data =array('error'=>1,'message'=>'提交的数据不正确、请重试！');
	}
	die(json_encode($data));	 
}
