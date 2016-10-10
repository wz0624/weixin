<?php

defined('IN_IA') or exit('Access Denied');
if (empty($_GPC['pid'])) {
    $this->doMobileover('参数错误');
    exit;
}
if (empty($_GPC['b']) || empty($_GPC['x'])) {
    $this->doMobileover('缺少必要参数');
    exit;
}
$item = pdo_fetch('select * from ' . tablename('hc_chuansong_list') . ' where weid=:weid AND is_default=1', array(
    ':weid' => $_W['uniacid']
));
if ($item == false) {
    $this->doMobileover('活动不存在');
    exit;
}
if ($item['status'] == 0) {
    $this->doMobileover('活动还没有开始');
    exit;
}
if ($item['starttime'] > time()) {
    $this->doMobileover('活动未开始，开始时间为:' . $date('Y-m-d H:i:s', $item['starttime']));
    exit;
}
if ($item['endtime'] < time()) {
    $this->doMobileover('活动已结束，结束时间为:' . $date('Y-m-d H:i:s', $item['endtime']));
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
if ($user != false) {
    if (((time() - $user['create_time']) < $item['part_time']) && $item['part_time'] > 0) {
        $this->doMobileover('报名客栈老板娘在生成' . $item['page_parttime'] . '秒后已自动销毁。如还未报名，请重新点击公众号右下角的“活动报名”按钮生成一个新客栈老板娘。');
        exit;
    }
}
include $this->template('adv_show');
