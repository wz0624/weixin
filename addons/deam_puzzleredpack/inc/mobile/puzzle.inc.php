<?php
global $_W, $_GPC;
$this->Checkeduseragent();
load()->model('mc');
$userinfo = mc_oauth_userinfo($_W['uniacid']);
load()->classs('weixin.account');
$accObj       = WeixinAccount::create($acid);
$access_token = $accObj->fetch_token();
$uniacid      = $_W['uniacid'];
$act_id       = intval($_GPC['id']);
$openid       = $_W['openid'];
$arr          = pdo_fetch("SELECT * FROM " . tablename('deam_puzzleredpack_packetset') . " WHERE id = :id AND uniacid = :uniacid", array(
    ':id' => $act_id,
    ':uniacid' => $uniacid
));
if (empty($arr)) {
    exit('活动不存在！');
    die();
}
$isprize = pdo_fetch("SELECT * FROM " . tablename('deam_puzzleredpack_record') . " WHERE actid = :actid AND uniacid = :uniacid AND openid = :openid ORDER BY `id` DESC", array(
    ':actid' => $act_id,
    ':uniacid' => $uniacid,
    ':openid' => $openid
));
if ($isprize) {
    $myShareTitle = $arr['share_title'];
    $myShareTitle = str_replace("{USERNAME}", $userinfo['nickname'], $myShareTitle);
    $myShareTitle = str_replace("{MONEY}", number_format($isprize['total_amount'] / 100, 2), $myShareTitle);
} else {
    $myShareTitle = $arr['noshare_title'];
    $myShareTitle = str_replace("{USERNAME}", $userinfo['nickname'], $myShareTitle);
}
$share_title = $myShareTitle;
if (!$_COOKIE['deam_openid']) {
    if ($_GPC['openid']) {
        $openid = addslashes($_GPC['openid']);
        setcookie('deam_openid', $openid, time() + 31536000);
        header('Location: ' . $this->createMobileUrl('puzzle', array(
            'id' => $act_id
        )));
        exit;
    }
} else {
    if ($_GPC['openid']) {
        if ($_GPC['openid'] != $_COOKIE['deam_openid']) {
            $openid = addslashes($_GPC['openid']);
            setcookie('deam_openid', $openid, time() + 31536000);
        }
        header('Location: ' . $this->createMobileUrl('puzzle', array(
            'id' => $act_id
        )));
        exit;
    }
}
$package = $_W['account']['jssdkconfig'];
if (strpos($arr['puzzleimage'], '://')) {
    $puzzle_image = $arr['puzzleimage'];
} else {
    $puzzle_image = $_W['attachurl'] . $arr['puzzleimage'];
}
if (strpos($arr['share_img'], '://')) {
    $share_img = $arr['share_img'];
} else {
    $share_img = $_W['attachurl'] . $arr['share_img'];
}
if (empty($arr['share_img'])) {
    $share_img = $puzzle_image;
}
if (strpos($arr['qrcodeimage'], '://')) {
    $qrcode_image = $arr['qrcodeimage'];
} else {
    $qrcode_image = $_W['attachurl'] . $arr['qrcodeimage'];
}
$is_subscribe = intval($arr['is_subscribe']);
if ($is_subscribe) {
    if ($_W['account']['level'] == '4') {
        $subscribe_model = '2';
    } else {
        $subscribe_model = '1';
    }
    $isGuanzhu = $this->checkIsSubscribe($subscribe_model);
}
$title       = $arr['pagetitle'];
$puzzlelevel = $arr['puzzlelevel'];
$status      = $arr['status'];
$starttime   = $arr['starttime'];
$endtime     = $arr['endtime'];
$nowtime     = TIMESTAMP;
if (empty($status)) {
    $act_status = 1;
} else {
    if ($nowtime < $starttime) {
        $act_status = 2;
    } elseif ($nowtime >= $starttime && $nowtime <= $endtime) {
        $act_status = 3;
    } elseif ($nowtime > $endtime) {
        $act_status = 4;
    }
}
include $this->template('puzzle');
?>