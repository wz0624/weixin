<?php

//微赞科技 by QQ:800083075 http://www.012wz.com/
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid    = m('user')->getOpenid();
$uniacid   = $_W['uniacid'];
$orderid   = intval($_GPC['id']);
$order     = pdo_fetch("select id,status,isverify,verified,verifycode from " . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(
    ':id' => $orderid,
    ':uniacid' => $uniacid,
    ':openid' => $openid
));
if (empty($order)) {
    show_json(0, '订单未找到!');
}
$qrcode = $this->model->createQrcode($orderid);
show_json(1, array(
    'qrcode' => $qrcode,
    'verifycode' => $order['verifycode']
));