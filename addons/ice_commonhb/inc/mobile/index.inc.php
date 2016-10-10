<?php
global $_W, $_GPC;
$openid = $_W['openid'];
include(IA_ROOT . '/addons/ice_commonhb/util/emoji.php');
if (empty($openid))
    exit();
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
if (!empty($settings['othersource'])) {
    header("location:" . $settings['othersource']);
}
if (substr($settings['logoImg'], 0, 5) != "http:") {
    $settings['logoImg'] = "../attachment/" . $settings['logoImg'];
}
$count = pdo_fetchcolumn("select count(*) from " . tablename("ice_yzmhb_user") . " where openid = :openid", array(
    ":openid" => $openid
));
load()->model('mc');
$userinfo = mc_oauth_userinfo();
if ($count == 0) {
    $nickname    = $userinfo['nickname'];
    $first_name  = emoji_docomo_to_unified($nickname);
    $second_name = emoji_unified_to_html($first_name);
    $third_name  = strip_tags($second_name);
    $dat         = array(
        'uniacid' => $_W['uniacid'],
        'openid' => $openid,
        'nickname' => $third_name,
        'headimgurl' => $userinfo['avatar']
    );
    pdo_insert('ice_yzmhb_user', $dat);
} else {
    $nickname    = $userinfo['nickname'];
    $first_name  = emoji_docomo_to_unified($nickname);
    $second_name = emoji_unified_to_html($first_name);
    $third_name  = strip_tags($second_name);
    $dat         = array(
        'nickname' => $third_name,
        'headimgurl' => $userinfo['avatar']
    );
    pdo_update('ice_yzmhb_user', $dat, array(
        "openid" => $openid
    ));
}
$settings['hbrule'] = htmlspecialchars_decode($settings['hbrule']);
include $this->template("index");
