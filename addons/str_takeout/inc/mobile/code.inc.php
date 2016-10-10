<?php
/**
 * 微外卖模块微站定义
 * @author strday
 * @url http://bbs.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$do = 'code';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

$sid = intval($_GPC['sid']);
$mobile = trim($_GPC['mobile']);
if($mobile == ''){
	exit('请输入邮箱或手机号');
}

if(!preg_match(REGULAR_MOBILE, $mobile)) {
	exit('手机号格式错误');
}

$sql = 'DELETE FROM ' . tablename('uni_verifycode') . ' WHERE `createtime`<' . (TIMESTAMP - 1800);
pdo_query($sql);

$sql = 'SELECT * FROM ' . tablename('uni_verifycode') . ' WHERE `receiver`=:receiver AND `uniacid`=:uniacid';
$pars = array();
$pars[':receiver'] = $mobile;
$pars[':uniacid'] = $_W['uniacid'];
$row = pdo_fetch($sql, $pars);
$record = array();
if(!empty($row)) {
	if($row['total'] >= 5) {
		exit('您的操作过于频繁,请稍后再试');
	}
	$code = $row['verifycode'];
	$record['total'] = $row['total'] + 1;
} else {
	$code = random(6, true); 
	$record['uniacid'] = $_W['uniacid'];
	$record['receiver'] = $mobile;
	$record['verifycode'] = $code;
	$record['total'] = 1;
	$record['createtime'] = TIMESTAMP;
}
if(!empty($row)) {
	pdo_update('uni_verifycode', $record, array('id' => $row['id']));
} else {
	pdo_insert('uni_verifycode', $record);
}
$content = "您的手机号：{$mobile}，修改手机号码验证码：{$code}，请不要把验证码泄露给其他人。如非本人操作，可不用理会！";

$result = sms_send($sid, $mobile, $content);
if(is_error($result)) {
	exit($result['message']);
}
exit('success');