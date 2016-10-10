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
$configArr    = $this->module['config'];
$thisip       = !empty($configArr['getip']) ? $configArr['getip'] : $_SERVER['SERVER_ADDR'];
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
$isprize = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('deam_puzzleredpack_record') . " WHERE actid = :actid AND uniacid = :uniacid AND openid = :openid", array(
    ':actid' => $actid,
    ':uniacid' => $uniacid,
    ':openid' => $openid
));
if ($isprize) {
    $date['status'] = 40004;
    $date['msg']    = '已中奖';
    echo json_encode($date);
    exit();
}
if ($nowtime < $starttime) {
    $date['status'] = 40002;
    $date['msg']    = '活动还未开始';
} elseif ($nowtime >= $starttime && $nowtime <= $endtime) {
    $total_money = $actInfo['total_prize'] * 100;
    $alreadyPost = pdo_fetchcolumn("SELECT SUM(total_amount) FROM " . tablename('deam_puzzleredpack_record') . " WHERE actid =" . $actid . " AND uniacid = " . $uniacid);
    $price_num   = mt_rand($actInfo['minprize'] * 100, $actInfo['maxprize'] * 100);
    if ($alreadyPost < $total_money) {
        $is_packet = '1';
    } else {
        $is_packet = '0';
    }
    if ($is_packet == '1') {
        $mch_billno                   = $configArr['mch_id'] . date("YmdHis") . mt_rand(1111, 9999);
        $mch_id                       = $configArr['mch_id'];
        $wxappid                      = $configArr['appid'];
        $packetInsert['mch_billno']   = $mch_billno;
        $packetInsert['uniacid']      = $uniacid;
        $packetInsert['mch_id']       = $mch_id;
        $packetInsert['wxappid']      = $wxappid;
        $packetInsert['openid']       = $openid;
        $packetInsert['total_amount'] = $price_num;
        $packetInsert['actid']        = $actid;
        $packetInsert['mytime']       = TIMESTAMP;
        pdo_insert('deam_puzzleredpack_record', $packetInsert);
        $insertid = pdo_insertid();
        if ($insertid) {
            $postdate['nonce_str']    = Deam_puzzleredpackModuleSite::random_str(32);
            $postdate['mch_billno']   = $mch_billno;
            $postdate['mch_id']       = $mch_id;
            $postdate['wxappid']      = $wxappid;
            $postdate['send_name']    = $actInfo['send_name'];
            $postdate['re_openid']    = $openid;
            $postdate['total_amount'] = $price_num;
            $postdate['wishing']      = $actInfo['wishing'];
            $postdate['client_ip']    = $thisip;
            $postdate['act_name']     = $actInfo['act_name'];
            $postdate['total_num']    = $actInfo['total_num'];
            $postdate['remark']       = $actInfo['remark'];
            $unSignParaStr            = $this->formatQueryParaMap($postdate, false);
            $signStr                  = $unSignParaStr . "&key=" . $configArr['partnerkey'];
            $postdate['sign']         = strtoupper(md5($signStr));
            $hborderinfo              = simplexml_load_string($this->curl_post_ssl("https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack", Deam_puzzleredpackModuleSite::arrayToXml($postdate)), 'SimpleXMLElement', LIBXML_NOCDATA);
            $hborderinfo              = json_decode(json_encode($hborderinfo), true);
            if ($hborderinfo['return_code'] == 'SUCCESS') {
                $packetUpdate['send_time']   = $hborderinfo['send_time'];
                $packetUpdate['send_listid'] = $hborderinfo['send_listid'];
                pdo_update('deam_puzzleredpack_record', $packetUpdate, array(
                    'id' => $insertid
                ));
                $getmoney       = number_format($price_num / 100, 2);
                $date['status'] = 101;
                $date['money']  = $getmoney;
            } else {
                pdo_delete('deam_puzzleredpack_record', array(
                    'id' => $insertid
                ));
                file_put_contents(DM_ROOT . '/tmpdata/log/' . date('YmdHis') . rand(1111, 9999) . '.log', $this->json_encode_ex($hborderinfo));
                $date['status'] = 109;
            }
        } else {
            $date['status'] = 109;
        }
    } else {
        $date['status'] = 109;
    }
} elseif ($nowtime > $endtime) {
    $date['status'] = 40003;
    $date['msg']    = '活动已结束';
}
echo json_encode($date);
exit();
?>