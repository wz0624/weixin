<?php 
function jetsum_fetch_token() {
	global $_GPC, $_W;
	load()->func('communication');
	if(!$_W['openid']) return;
	if($_W['account']['level']<3)return 0;
	$Jetsumtoken="";
	if(is_array($_W['account']['access_token']) && !empty($_W['account']['access_token']['token']) && !empty($_W['account']['access_token']['expire']) && $_W['account']['access_token']['expire'] > TIMESTAMP) {
		$Jetsumtoken=$_W['account']['access_token']['token'];
	} else {
		if (empty($_W['account']['key']) || empty($_W['account']['secret'])) {
			return 0;
		}
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$_W['account']['key']}&secret={$_W['account']['secret']}";
		$content = ihttp_get($url);
		if(is_error($content)) {
			return 0;
		}
		$token = @json_decode($content['content'], true);
		if(empty($token) || !is_array($token) || empty($token['access_token']) || empty($token['expires_in'])) {
			$errorinfo = substr($content['meta'], strpos($content['meta'], '{'));
			$errorinfo = @json_decode($errorinfo, true);
			return 0;
		}
		$record = array();
		$record['token'] = $token['access_token'];
		$record['expire'] = TIMESTAMP + $token['expires_in'];
		$row = array();
		$row['access_token'] = iserializer($record);
		pdo_update('account_wechats', $row, array('acid' => $_W['account']['acid']));
		$Jetsumtoken= $record['token'];
	}
	$oauth3_code = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$Jetsumtoken."&openid=".$_W['openid'];
	$content = ihttp_get ( $oauth3_code );
	$token = @json_decode($content['content'], true);
	//print_r($token);
	return $token;
}
function j_member_fetch(){
	global $_GPC, $_W;
	if($_W['member']['uid'])return mc_fetch($_W['member']['uid']);
	$profile=pdo_fetch("SELECT * FROM ".tablename('mc_mapping_fans')." WHERE openid = :openid",array(":openid"=>$_W['openid']));
	if($profile['uid'])return pdo_fetch("SELECT * FROM ".tablename('mc_mapping_fans')." WHERE uid = :id",array(":id"=>$profile['uid']));
	//没有uid
	$p=jetsum_fetch_token();
	$avatar=$p['headimgurl'];
	$nickname=$p['nickname'];
	$gender=$p['gender'];
	$data=array(
		'uniacid'=>$_W['uniacid'],
		'createtime'=>TIMESTAMP,
		'nickname'=>$nickname,
		'avatar'=>$avatar,
		'gender'=>$gender,
		'salt'=>$profile['salt'],
		'lookingfor'=>$_W['openid'],
	);
	pdo_insert('mc_members',$data);
	$uid = pdo_insertid();
	pdo_update('mc_mapping_fans',array('uid'=>$uid),array('uniacid'=>$_W['uniacid'],'openid'=>$_W['openid']));
	return pdo_fetch("SELECT * FROM ".tablename('mc_members')." WHERE uid = :uid",array(":uid"=>$uid));
}
function j_member_update($param=array()){
	global $_W;
	if($_W['member']['uid'])mc_update($_W['member']['uid'],$param);
	j_member_fetch();
	pdo_update('mc_members',$param,array('lookingfor'=>$_W['openid']));
}

//***加密函数***//
/*
$str = 'abc'; 
$key = 'www.helloweba.com'; 
echo '加密:'.encrypt($str, 'E', $key); 
echo '解密：'.encrypt($str, 'D', $key);
 */
function encrypt($string,$operation,$key=''){ 
    $key=md5($key); 
    $key_length=strlen($key); 
      $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string; 
    $string_length=strlen($string); 
    $rndkey=$box=array(); 
    $result=''; 
    for($i=0;$i<=255;$i++){ 
           $rndkey[$i]=ord($key[$i%$key_length]); 
        $box[$i]=$i; 
    } 
    for($j=$i=0;$i<256;$i++){ 
        $j=($j+$box[$i]+$rndkey[$i])%256; 
        $tmp=$box[$i]; 
        $box[$i]=$box[$j]; 
        $box[$j]=$tmp; 
    } 
    for($a=$j=$i=0;$i<$string_length;$i++){ 
        $a=($a+1)%256; 
        $j=($j+$box[$a])%256; 
        $tmp=$box[$a]; 
        $box[$a]=$box[$j]; 
        $box[$j]=$tmp; 
        $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256])); 
    } 
    if($operation=='D'){ 
        if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8)){ 
            return substr($result,8); 
        }else{ 
            return''; 
        } 
    }else{ 
        return str_replace('=','',base64_encode($result)); 
    } 
}
function checkcode(){
	global $_W;
	$urls=urlencode($_SERVER['HTTP_HOST']);
}
?>