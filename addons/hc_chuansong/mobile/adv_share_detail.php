<?php

$uid = $_GPC['uid'];
if (!empty($uid)) {
    $cookie_name = $this->get_cookie_name();
    setcookie($cookie_name, $uid, time() + 60 * 60 * 24 * 3, '/', $_SERVER['HTTP_HOST']);
}
$item = pdo_fetch('select * from ' . tablename('hc_chuansong_list') . ' where weid=:weid AND is_default=1', array(
    ':weid' => $_W['uniacid']
));
include $this->template('share_detail');
