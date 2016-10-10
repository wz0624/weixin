<?php

defined('IN_IA') or exit('Access Denied');
$item = pdo_fetch('select id,title,houxuan_thumb,status,endtime,starttime from ' . tablename('hc_chuansong_list') . ' where weid=:weid AND is_default=1', array(
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
    $this->doMobileover('活动未开始，开始时间为:' . date('Y-m-d H:i:s', $item['starttime']));
    exit;
}
if ($item['endtime'] < time()) {
    $this->doMobileover('活动已结束，结束时间为:' . date('Y-m-d H:i:s', $item['endtime']));
    exit;
}
$where  = "WHERE weid=" . $_W['uniacid'] . " AND  pid=" . $item['id'] . " ";
$pindex = max(1, intval($_GPC['page']));
$psize  = 20;
$total  = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('hc_chuansong_user') . $where);
$start  = ($pindex - 1) * $psize;
$where .= "  order by `award_no` asc   LIMIT {$start},{$psize}";
$list  = pdo_fetchall("SELECT * FROM " . tablename('hc_chuansong_user') . " " . $where);
$pager = pagination($total, $pindex, $psize, '', array(
    'before' => 1,
    'after' => 1,
    'ajaxcallback' => ''
));
include $this->template('adv_userlist');
