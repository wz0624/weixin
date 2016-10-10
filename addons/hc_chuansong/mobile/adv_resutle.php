<?php
defined('IN_IA') or exit('Access Denied');
$item = pdo_fetch('select * from ' . tablename('hc_chuansong_list') . ' where weid=:weid AND is_default=1', array(
    ':weid' => $_W['uniacid']
));
if ($item == false) {
    message('活动不存在');
}
$user = pdo_fetch('select * from' . tablename('hc_chuansong_user') . ' where weid=:weid AND pid=:pid AND from_user=:from_user', array(
    ':weid' => $_W['uniacid'],
    ':from_user' => $_W['openid'],
    ':pid' => $item['id']
));
if (!empty($user)) {
    $list = pdo_fetchall(" SELECT * FROM " . tablename('hc_chuansong_user') . " WHERE from_user='" . $_W['openid'] . "' AND pid='" . $item['id'] . "' ");
    include $this->template('adv_has');
    exit();
}
include $this->template('adv_resutle');
