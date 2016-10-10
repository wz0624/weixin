<?php

defined('IN_IA') or exit('Access Denied');
if (empty($_GPC['pid'])) {
    message('参数错误');
}
$code = md5($item['pid'] . $_W['openid'] . $_W['account']['hash']);
if ($code != $_GPC['code']) {
    message('code 错误');
}
if (empty($_GPC['b']) || empty($_GPC['x'])) {
    message('缺少必要参数');
}
$item = pdo_fetch('select * from ' . tablename('hc_chuansong_list') . ' where weid=:weid AND is_default=1', array(
    ':weid' => $_W['uniacid']
));
if ($item == false) {
    message('活动不存在');
}
if ($item['id'] != $_GPC['pid']) {
    message('参数错误');
}
if ($item['status'] == 0) {
    message('活动还没有开始');
}
if ($item['starttime'] > time()) {
    message('活动未开始，开始时间为:' . $date('Y-m-d H:i:s', $item['starttime']));
}
if ($item['endtime'] < time()) {
    message('活动已结束，结束时间为:' . $date('Y-m-d H:i:s', $item['endtime']));
}
$user = pdo_fetch('select * from ' . tablename('hc_chuansong_user') . ' where weid=:weid AND pid=:pid AND from_user=:from_user ORDER BY create_time DESC ', array(
    ':weid' => $_W['uniacid'],
    ':from_user' => $_W['openid'],
    ':pid' => $item['id']
));
if ($user != false) {
    $ret = array(
        'success' => false,
        'msg' => date('Y-m-d H:i:s', $user['create_time']),
        'ok' => 'ok'
    );
    echo json_encode($ret);
    exit();
    if (time() - $user['create_time'] < 60 * 60 * 24) {
        $ret = array(
            'success' => false,
            'msg' => '您已经领取过了',
            'ok' => 'ok'
        );
        echo json_encode($ret);
        exit();
    }
}
$cookie_name = $this->get_cookie_name();
if (!empty($_COOKIE[$cookie_name])) {
    $award_no = pdo_fetchcolumn('select award_no from ' . tablename('hc_chuansong_user') . ' where weid=:weid AND pid=:pid order by award_no desc', array(
        ':weid' => $_W['uniacid'],
        ':pid' => $item['id']
    ));
    if ($award_no == false) {
        $award_no = 0;
    } elseif ($award_no < $item['total_nums']) {
        $award_no     = $award_no + 1;
        $uid          = intval($_COOKIE[$cookie_name]);
        $member       = pdo_fetch(" SELECT * FROM " . tablename('mc_members') . " WHERE uid='" . $uid . "' ");
        $share_openid = pdo_fetchcolumn(" SELECT openid FROM " . tablename('mc_mapping_fans') . " WHERE uid='" . $uid . "' ");
        $num          = pdo_fetchcolumn(" SELECT COUNT('id') FROM " . tablename('hc_chuansong_user') . " WHERE from_user='" . $share_openid . "' AND pid='" . $item['id'] . "' ");
        if (intval($num) < $item['limit_nums']) {
            $detail = pdo_fetch(" SELECT * FROM " . tablename('hc_chuansong_user') . " WHERE from_user='" . $share_openid . "' AND pid='" . $item['id'] . "' ");
            $insert = array(
                'weid' => $_W['uniacid'],
                'pid' => $item['id'],
                'from_user' => $share_openid,
                'nickname' => $member['nickname'],
                'headimgurl' => $member['avatar'],
                'realname' => $detail['realname'],
                'mobile' => $detail['mobile'],
                'award_no' => $award_no,
                'create_time' => time(),
                'status' => 1
            );
            $temp   = pdo_insert('hc_chuansong_user', $insert);
        }
    }
}
$award_no = pdo_fetchcolumn('select award_no from ' . tablename('hc_chuansong_user') . ' where weid=:weid AND pid=:pid order by award_no desc', array(
    ':weid' => $_W['uniacid'],
    ':pid' => $item['id']
));
if ($award_no == false) {
    $award_no = 0;
} elseif ($award_no >= $item['total_nums']) {
    $ret = array(
        'success' => false,
        'msg' => '超过最大参与人数',
        'ok' => 'ok'
    );
    echo json_encode($ret);
    exit();
}
$award_no = $award_no + 1;
$member   = pdo_fetch(" SELECT * FROM " . tablename('mc_members') . " WHERE uid='" . $_W['member']['uid'] . "' ");
if (empty($member['nickname']) || empty($member['avatar'])) {
    $user_info          = $this->get_follow_info();
    $member['nickname'] = $user_info['nickname'];
    $member['avatar']   = $user_info['avatar'];
}
$insert = array(
    'weid' => $_W['uniacid'],
    'pid' => $item['id'],
    'from_user' => $_W['openid'],
    'nickname' => $member['nickname'],
    'headimgurl' => $member['avatar'],
    'realname' => $_GPC['b'],
    'mobile' => $_GPC['x'],
    'award_no' => $award_no,
    'create_time' => time(),
    'status' => 1
);
$temp   = pdo_insert('hc_chuansong_user', $insert);
if ($temp == false) {
    message('数据保存失败');
} else {
    $ret = array(
        'success' => true,
        'msg' => '超过最大参与人数',
        'ok' => 'ok'
    );
    echo json_encode($ret);
    exit();
}
