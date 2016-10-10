<?php
global $_W,$_GPC;
$w = $_W;
// session_start();
// var_dump($_SESSION);exit;
$handlers = array('ajax'=>'ajax请求');
$operation = isset($_GPC['op'])&&array_key_exists($_GPC['op'], $handlers) ? $_GPC['op'] : '';
$acidarr = uni_accounts($_W['uniacid']);
if($operation == 'ajax'){
	if($_GPC['handler'] == 'getaccesstoken'){
		load()->classs('weixin.account');
		$accObj= WeixinAccount::create($_GPC['acid']);
		$access_token = $accObj->fetch_token();
		echo $access_token;
	}
	exit;
}

include $this->template('setting');