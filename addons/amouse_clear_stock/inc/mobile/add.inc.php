<?php
global $_W, $_GPC;
$weid = $_W['uniacid'];
$set  = pdo_fetch("SELECT thumb,appid,followurl,isopen,shkey FROM " . tablename('amouse_board_sysset') . " WHERE weid=:weid limit 1", array(
    ':weid' => $weid
));
require_once IA_ROOT . '/addons/amouse_hufen/jssdk.php';
$pk = $_GPC['pk'];
if (empty($_GPC['amouse_board_openid' . $weid])) {
    if (empty($_W['openid'])) {
        $redirect_uri = urlencode($_W['siteroot'] . "app/index.php?i=$weid&c=entry&m=amouse_hufen&do=oauth&au=board");
        $authurl      = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $set['appid'] . "&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
        header('location:' . $authurl);
        exit();
    } else {
        $openid = $_W['openid'];
    }
} else {
    $openid = $_GPC['amouse_board_openid' . $weid];
}
$fans      = pdo_fetch('select * from ' . tablename('amouse_board_member') . ' where weid=:weid AND openid=:openid limit 1', array(
    ':weid' => $weid,
    ':openid' => $openid
));
$from_user = !empty($_W['fans']['from_user']) ? $_W['fans']['from_user'] : $fans['from_user'];
$follow    = pdo_fetchcolumn('select follow from ' . tablename('mc_mapping_fans') . ' where openid=:openid LIMIT 1', array(
    ':openid' => $from_user
));
$status    = 1;
if (empty($from_user) || $follow <> 1) {
    $status = 0;
}
if ($status == 0) {
    if ($set && $set['followurl']) {
        $followurl = $set['followurl'];
        header("location:$followurl");
        exit;
    }
}
$card = pdo_fetch('select * from ' . tablename('amouse_board_card') . ' where weid=:weid AND openid=:openid ORDER BY createtime DESC LIMIT 1', array(
    ':weid' => $weid,
    ':openid' => $openid
));
if (empty($card)) {
    $pub_url = murl('entry', array(
        'do' => 'release',
        'm' => 'amouse_hufen',
        'ptype' => 'person'
    ), true, true);
    message('您还没有上传您的二维码，不能使用此功能。！', $pub_url, 'error');
}
$cset            = pdo_fetch("SELECT * FROM " . tablename('amouse_board_clear_sysset') . "where uniacid=$weid limit 1 ");
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
$upload_url      = murl('entry', array(
    'do' => 'imgupload',
    'm' => 'amouse_hufen'
), true, true);
$clear_stock_url = murl('entry', array(
    'do' => 'product',
    'm' => 'amouse_clear_stock'
), true, true);
$categorys       = pdo_fetchall("SELECT * FROM " . tablename('amouse_board_product_category') . " WHERE uniacid = $weid ORDER BY displayorder DESC ");
if (!empty($pk)) {
    $product = pdo_fetch("SELECT * FROM " . tablename('amouse_board_clear_stock_goods') . " WHERE uniacid = $weid AND id=$pk ");
}
include $this->template('add_product');