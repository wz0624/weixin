<?php

defined('IN_IA') or exit('Access Denied');
if (empty($_W['openid'])) {
    $this->doMobileover('请从微信端进入！');
    exit;
}
$item = pdo_fetch('select * from ' . tablename('hc_chuansong_list') . ' where weid=:weid AND is_default=1', array(
    ':weid' => $_W['uniacid']
));
if ($item == false) {
    $this->doMobileover('活动不存在');
    exit;
}
$pid = intval($_GPC['pid']);
if ($pid != $item['id']) {
    $this->doMobileover();
    exit;
}
if ($item['join_nums'] >= $item['total_nums']) {
    $this->doMobileover('参加人次已经达到' . $item['total_nums'] . ',请等待下次机会');
    exit;
}
$user = pdo_fetch('select * from' . tablename('hc_chuansong_user') . ' where weid=:weid AND pid=:pid AND from_user=:from_user', array(
    ':weid' => $_W['uniacid'],
    ':from_user' => $_W['openid'],
    ':pid' => $item['id']
));
if (((time() - $_GPC['t']) > $item['page_parttime']) && $item['page_parttime'] > 0) {
    $this->doMobileover('报名客栈老板娘在生成' . $item['page_parttime'] . '秒后已自动销毁。如还未报名，请重新点击公众号右下角的“活动报名”按钮生成一个新客栈老板娘。');
    exit;
}
if ($user != false) {
    if (((time() - $user['create_time']) < $item['part_time']) && $item['part_time'] > 0) {
        $this->doMobileover();
        exit;
    }
}
$uid = $_W['member']['uid'];
if (empty($uid)) {
    $this->add_member();
    $uid = $_W['member']['uid'];
}
$member = pdo_fetch(" SELECT * FROM " . tablename('mc_members') . " WHERE uid='" . $uid . "' ");
if (empty($member['nickname']) || empty($member['avatar'])) {
    $this->getuserinfo($_W['openid']);
}
if ($item['status'] == 0) {
    $this->doMobileover('活动还没有开始');
    exit;
}
if ($item['starttime'] > time()) {
    $this->doMobileover('活动未开始，开始时间为:' . date('Y-m-d H:i:s', $item['starttime']));
    exit;
}
if ($item['endtime'] < time()) {
    $this->doMobileover('活动已结束，结束时间为:' . date('Y-m-d H:i:s', $item['endtime']));
    exit;
}
$item['code'] = md5($item['pid'] . $_W['openid'] . $_W['account']['hash']);
if (!empty($user)) {
    $list = pdo_fetchall(" SELECT * FROM " . tablename('hc_chuansong_user') . " WHERE from_user='" . $_W['openid'] . "' AND pid='" . $item['id'] . "' ");
    include $this->template('adv_has');
    exit();
}
include $this->template('adv_index');
