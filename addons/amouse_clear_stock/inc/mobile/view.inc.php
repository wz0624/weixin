<?php
global $_W, $_GPC;
$weid   = $_W['uniacid'];
$set    = $this->getSysset($weid);
$openid = $_GPC['amouse_board_openid' . $weid];
if (empty($openid)) {
    $openid = $_W['fans']['from_user'];
}
$index_url       = murl('entry', array(
    'do' => 'board',
    'm' => 'amouse_hufen',
    'op' => 'b'
), true, true);
$vip_url         = murl('entry', array(
    'do' => 'vip',
    'm' => 'amouse_hufen',
    'op' => 'step1'
), true, true);
$e_url           = murl('entry', array(
    'do' => 'exchage',
    'm' => 'amouse_hufen'
), true, true);
$me_url          = murl('entry', array(
    'do' => 'me',
    'm' => 'amouse_hufen',
    'op' => 'me'
), true, true);
$view_url        = murl('entry', array(
    'do' => 'log',
    'm' => 'amouse_hufen',
    'show_type' => 'person'
), true, true);
$cset            = pdo_fetch("SELECT * FROM " . tablename('amouse_board_clear_sysset') . "where uniacid=$weid limit 1 ");
$clear_stock_url = murl('entry', array(
    'do' => 'product',
    'm' => 'amouse_clear_stock'
), true, true);
$pk              = $_GPC['pk'];
$sql             = "SELECT sg.*,c.qrcode,c.location_p,c.location_c,c.nickname,c.wechatno,c.id as cid FROM " . tablename('amouse_board_clear_stock_goods') . " as sg left join" . tablename('amouse_board_card') . " as c on c.openid = sg.openid WHERE sg.id=$pk ";
$sg              = pdo_fetch($sql);
$myrecord        = pdo_fetch("SELECT * FROM " . tablename('amouse_board_clear_log') . " WHERE openid=:openid and goodsid=:goodsid and uniacid=:uniacid limit 1 ", array(
    ':openid' => $openid,
    ':goodsid' => $pk,
    ':uniacid' => $weid
));
if (empty($myrecord['id'])) {
    $insert = array(
        'goodsid' => $pk,
        'read' => 1,
        'uniacid' => $weid,
        'openid' => $openid
    );
    pdo_insert('amouse_board_clear_log', $insert);
    pdo_update('amouse_board_clear_stock_goods', array(
        'viewcount' => $sg['viewcount'] + 1
    ), array(
        'id' => $sg['id']
    ));
}
include $this->template('view_product');