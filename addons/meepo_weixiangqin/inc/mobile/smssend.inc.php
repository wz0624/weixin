<?php
global $_W,$_GPC;
$weid = $_W['uniacid'];
$openid = $_W['openid'];
if(empty($openid)){
   die('error');
}
if($_W['isajax']){
   $cfg = $this->module['config'];
   $Mobile = $_GPC['mobile'];
	 $check_mobile = pdo_fetchcolumn("SELECT `telephone` FROM".tablename('hnfans')." WHERE telephone=:telephone AND weid=:weid AND telephoneconfirm=:telephoneconfirm",array(':telephone'=>$Mobile,':weid'=>$weid,':telephoneconfirm'=>'1'));
	 if(!empty($check_mobile)){
			die('over');
	 }
   $num =  random(6, true); 
	 if(empty($cfg['ali_appkey'])){
		 $url='http://utf8.sms.webchinese.cn/?Uid='.$cfg['smsuid'].'&Key='.$cfg['smskey'].'&smsMob='.$Mobile.'&smsText=验证码：'.$num;
		 $result = Get($url);
		 if($result=='1'){
					$status = 1;
		 }else{
					$status = 2;
			}
	 }else{
			include "TopSdk.php";
			$c = new TopClient();
			$c->appkey = $cfg['ali_appkey'];
			$c->secretKey = $cfg['ali_appsecret'];
			$req = new AlibabaAliqinFcSmsNumSendRequest;
			$req->setExtend("123456");
			$req->setSmsType("normal");
			$req->setSmsFreeSignName($cfg['ali_signname']);
			$json = json_encode(array("code"=>$num,'product'=>$_W['account']['name'].'平台的'));
			$req->setSmsParam($json);
			$req->setRecNum($Mobile);
			$req->setSmsTemplateCode($cfg['ali_moban_num']);//  SMS_585014  SMS_6290144
			$result = $c->execute($req);
			
			if($result->result->err_code=='0'){
					$status = 1;
			}else{
					$status = 2;
			}
	 }
   if($status == '1'){
	   //pdo_update('hnfans',array('telephone'=>$Mobile),array('from_user'=>$openid,'weid'=>$weid));
		 $check = pdo_fetchcolumn("SELECT `id` FROM".tablename('meepo_sms_news')." WHERE openid=:openid AND weid=:weid ORDER BY createtime DESC",array(':openid'=>$openid,':weid'=>$weid));
	  if(empty($check)){
				pdo_insert('meepo_sms_news',array('weid'=>$weid,'openid'=>$openid,'createtime'=>time(),'news'=>$num));
	  }else{
				pdo_update('meepo_sms_news',array('news'=>$num),array('id'=>$check,'weid'=>$weid));
	  }
   }
   echo $status;
  
}
function Get($url){
	if(function_exists('file_get_contents')){
		  $file_contents = file_get_contents($url);
	}else{
		   $ch = curl_init();
		$timeout = 5;
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$file_contents = curl_exec($ch);
		curl_close($ch);
	}
	return $file_contents;
}