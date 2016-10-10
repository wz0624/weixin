<?php
global $_GPC, $_W;
if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
    $date['status'] = 40001;
    $date['msg']    = 'error';
    echo json_encode($date);
    exit();
}
$uniacid      = $_W['uniacid'];
$nowtime      = TIMESTAMP;
$openid       = $_W['openid'];
$actid        = intval($_GPC['actid']);
$actInfo      = pdo_fetch("SELECT * FROM " . tablename('deam_puzzleredpack_packetset') . " WHERE id = :id AND uniacid = :uniacid", array(
    ':id' => $actid,
    ':uniacid' => $uniacid
));
$starttime    = $actInfo['starttime'];
$endtime      = $actInfo['endtime'];
$is_subscribe = intval($actInfo['is_subscribe']);
if ($is_subscribe) {
    if ($_W['account']['level'] == '4') {
        $subscribe_model = '2';
    } else {
        $subscribe_model = '1';
    }
    $isGuanzhu = $this->checkIsSubscribe($subscribe_model);
}
if ($is_subscribe && !$isGuanzhu) {
    $date['status'] = 40007;
    $date['msg']    = '未关注';
    echo json_encode($date);
    exit();
}
if ($nowtime < $starttime) {
    $date['status'] = 40002;
    $date['msg']    = '活动还未开始';
} elseif ($nowtime >= $starttime && $nowtime <= $endtime) {
    $date['status'] = 1002;
    $date['msg']    = '活动进行中';
} elseif ($nowtime > $endtime) {
    $date['status'] = 40003;
    $date['msg']    = '活动已结束';
}
echo json_encode($date);
exit();
$config = $this->module['config'];
$thisip = !empty($config['getip']) ? $config['getip'] : $_SERVER['SERVER_ADDR'];
?>