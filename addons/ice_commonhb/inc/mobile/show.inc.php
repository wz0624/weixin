<?php
global $_W, $_GPC;
$openid = $_W['openid'];
if (empty($openid)) {
    echo "<script>";
    echo "alert('请使用微信访问')";
    echo '</script>';
    exit();
}
$codeid = $_GPC['codeid'];
load()->func('tpl');
load()->func('logging');
$modulelist = uni_modules(false);
$name       = 'ice_commonhb';
$module     = $modulelist[$name];
if (empty($module)) {
    message('抱歉，你操作的模块不能被访问！');
}
define('CRUMBS_NAV', 1);
$ptr_title    = '参数设置';
$module_types = module_types();
define('ACTIVE_FRAME_URL', url('home/welcome/ext', array(
    'm' => $name
)));
$settings = $module['config'];
if (substr($settings['logoImg'], 0, 5) != "http:") {
    $settings['logoImg'] = "../attachment/" . $settings['logoImg'];
}
$money = pdo_fetchcolumn("select money from " . tablename("ice_yzmhb_sendlist") . " where codeid = :codeid and openid = :openid", array(
    ":codeid" => $codeid,
    ":openid" => $openid
));
include $this->template("show");