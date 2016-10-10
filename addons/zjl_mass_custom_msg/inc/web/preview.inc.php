<?php

global $_W, $_GPC;
$openid = $_GPC['openid'];
$optionId = $_GPC['oid'];
$op = $_GPC['op'] == '' ? 'show' : $_GPC['op'];
if ($openid != '' && $op == 'ajax') {
    $this->sendCustomMsg($openid, $optionId);
    if ($status['errcode'] == 0) {
        echo "1";
    } else {
        echo "0";
    }
    exit();
}
if ($op == 'qrcode') {
    $scanUrl = murl("entry", array('m' => strtolower($this->modulename), 'do' => 'preview', 'oid' => $optionId), true, true);
    require_once('../framework/library/qrcode/phpqrcode.php');
    QRcode::png(urldecode($scanUrl), false, 0, 6, 2);
    die();
}
include $this->template('preview');
