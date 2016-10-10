<?php
/**
 * 微外卖模块微站定义
 * @author strday
 * @url http://bbs.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'verify';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'first_order';
$sid = intval($_GPC['sid']);
if($op == 'first_order') {
	$mobile = trim($_GPC['mobile']);
	if($mobile == ''){
		exit('请输入手机号');
	}

	if(!preg_match(REGULAR_MOBILE, $mobile)) {
		exit('手机号格式错误');
	}
	$code = trim($_GPC['code']);
	if($code == ''){
		exit('验证码不能为空');
	}
	$isexist = pdo_fetch('select * from ' . tablename('uni_verifycode') . ' where uniacid = :uniacid and receiver = :receiver and verifycode = :verifycode and createtime >= :createtime', array(':uniacid' => $_W['uniacid'], ':receiver' => $mobile, ':verifycode' => $code, ':createtime' => time()-1800));
	if(empty($isexist)) {
		exit('验证码错误');
	}
	mc_update($_W['member']['uid'], array('mobile' => $mobile));
	exit('success');
}
