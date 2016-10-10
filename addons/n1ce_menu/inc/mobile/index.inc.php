<?php
/**
 * 个性化菜单模块处理程序
 *
 * @author n1ce   QQ：541535641
 * @url http://www.012wz.com/
 */
global $_W,$_GPC;
load()->func('tpl');
load()->func('communication');
 if(checksubmit('menu')){
	$data['appid'] = $_GPC['appid'];
	$data['appsecret'] = $_GPC['appsecret'];
	$data['name1'] = $_GPC['name1'];
	$data['url1'] = $_GPC['url1'];
	$data['name2'] = $_GPC['name2'];
	$data['url2'] = $_GPC['url2'];
	$data['name3'] = $_GPC['name3'];
	$data['url3'] = $_GPC['url3'];
	$data['sex'] = $_GPC['sex'];
	
	if($data){
	$appid = $data['appid'];
$appsecret = $data['appsecret'];
$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";

$output = https_request($url);
$jsoninfo = json_decode($output, true);

$access_token = $jsoninfo["access_token"];
/**测试**/

$jsonmenu = '{
 	"button":[
 	{	
    	"type":"view",
    	"name":"'.$data['name1'].'",
     	"url":"'.$data['url1'].'" 
	},
	{	
    	"type":"view",
    	"name":"'.$data['name2'].'",
     	"url":"'.$data['url2'].'" 
	},
	{	
    	"type":"view",
    	"name":"'.$data['name3'].'",
     	"url":"'.$data['url3'].'" 
	}],
"matchrule":{
  "sex":"'.$data['sex'].'"
  }
}';


$url = "https://api.weixin.qq.com/cgi-bin/menu/addconditional?access_token=".$access_token;
$result = https_request($url, $jsonmenu);
	
		message("$result",'refresh',success);
	}else{
		message('保存失败','referer',error);
	}
 }
 	function https_request($url,$data = null){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}
 include $this->template('index');