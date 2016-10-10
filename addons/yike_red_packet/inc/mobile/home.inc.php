<?php
global $_W, $_GPC;
$op = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
if ($op == 'index') {
    $mid         = $_GPC['mid'];
    $user        = $this->getInfo();
    $_user       = pdo_get('yike_red_packet_user', array(
        'uniacid' => $_W['uniacid'],
        'uid' => $user['uid']
    ));
    $result      = pdo_getall('yike_red_packet_level', array(
        'uniacid' => $_W['uniacid']
    ));
    $level_list  = array();
    $level_price = array();
    foreach ($result as $item) {
        $level_list[$item['level']]  = $item['name'];
        $level_price[$item['level']] = $item['money'];
    }
    unset($item);
    $level_list        = json_encode($level_list);
    $level_price       = json_encode($level_price);
    $_user['avatar']   = $user['avatar'];
    $_user['nickname'] = $user['nickname'];
    $me                = json_encode($_user);
    $url               = $this->createMobileUrl('pay');
    include $this->template($this->tplname . '/wx');
} else if ($op == 'banners') {
    $setdata = pdo_fetch("select * from " . tablename('yike_red_packet_setting') . ' where uniacid=:uniacid limit 1', array(
        ':uniacid' => $_W['uniacid']
    ));
    $set     = unserialize($setdata['sets']);
    $banner  = tomedia($set['banner']);
    show_json(1, array(
        'banners' => $banner
    ));
} else if ($op == 'get_qrcode') {
    global $_W, $_GPC;
    $user = $this->getInfo();
    $uid  = $user['uid'];
    $path = IA_ROOT . "/addons/yike_red_packet/data/qrcode/" . $_W['uniacid'];
    if (!is_dir($path)) {
        load()->func('file');
        mkdirs($path);
    }
    $url     = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=yike_red_packet&do=home&mid=' . $uid;
    $account = m('common')->getAccount();
    if (!$account) {
        message('生成失败');
    }
    $result      = $account->barCodeCreateFixed(array(
        'action_name' => 'QR_LIMIT_SCENE',
        'action_info' => array(
            'scene' => array(
                'scene_str' => 'heheh',
                'scene_id' => 1000
            )
        )
    ));
    $url         = $result['url'];
    $file        = 'qrcode_' . $uid . '.png';
    $qrcode_file = $path . '/' . $file;
    if (!is_file($qrcode_file)) {
        require IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
        QRcode::png($url, $qrcode_file, QR_ECLEVEL_H, 4);
    }
    $setdata = pdo_fetch("select * from " . tablename('yike_red_packet_setting') . ' where uniacid=:uniacid limit 1', array(
        ':uniacid' => $_W['uniacid']
    ));
    $set     = unserialize($setdata['sets']);
    if (empty($set)) {
        $set = array(
            'poster' => '/addons/yike_red_packet/static/img/poster.png'
        );
    }
    $img    = $_W['siteroot'] . 'addons/yike_red_packet/data/qrcode/' . $_W['uniacid'] . '/' . $file;
    $poster = array(
        'data' => '[{"left":"15px","top":"15px","type":"qr","width":"84px","height":"84px","size":""},{"left":"16px","top":"108px","type":"nickname","width":"80px","height":"40px","size":"16px","color":"#fff"},{"left":"16px","top":"148px","type":"word","width":"80px","height":"40px","size":"16px","color":"#fff"}]',
        'bg' => $set['poster']
    );
    $qr     = array();
    $media  = $this->createPoster($poster, $user, $img, false);
    $this->show_json(1, $media);
} else if ($op == 'level_list') {
    $result = pdo_getall('yike_red_packet_level', array(
        'uniacid' => $_W['uniacid']
    ));
    $list   = array();
    foreach ($result as $item) {
        $list[$item['level']] = $item['money'];
    }
    unset($item);
    $this->show_json(1, $list);
} else if ($op == 'children') {
    $user              = $this->getInfo();
    $uid               = $user['uid'];
    $uniacid           = $_W['uniacid'];
    $level1_count      = pdo_fetchcolumn('select count(*) from ' . tablename('yike_red_packet_user') . ' where uniacid=:uniacid and level1=:uid', array(
        ':uniacid' => $uniacid,
        ':uid' => $uid
    ));
    $level2_count      = pdo_fetchcolumn('select count(*) from ' . tablename('yike_red_packet_user') . ' where uniacid=:uniacid and level2=:uid', array(
        ':uniacid' => $uniacid,
        ':uid' => $uid
    ));
    $level3_count      = pdo_fetchcolumn('select count(*) from ' . tablename('yike_red_packet_user') . ' where uniacid=:uniacid and level3=:uid', array(
        ':uniacid' => $uniacid,
        ':uid' => $uid
    ));
    $_user             = pdo_get('yike_red_packet_user', array(
        'uniacid' => $uniacid,
        'uid' => $uid
    ));
    $_user['avatar']   = $user['avatar'];
    $_user['nickname'] = $user['nickname'];
    $this->show_json(1, array(
        'count1' => $level1_count,
        'count2' => $level2_count,
        'count3' => $level3_count,
        'user' => $_user
    ));
} else if ($op == 'rebates') {
    $user      = $this->getInfo();
    $uid       = $user['uid'];
    $status    = $_GPC['status'];
    $uniacid   = $_W['uniacid'];
    $page      = max(1, intval($_GPC['page']));
    $size      = 20;
    $condition = ' and rebates.uniacid=:uniacid and rebates.uid = :uid';
    $params    = array(
        ':uniacid' => $_W['uniacid'],
        ':uid' => $uid
    );
    if (!empty($status)) {
        $condition .= ' and rebates.status= :status';
        $params[':status'] = $status;
    }
    $sql = "select rebates.*, f.nickname from " . tablename('yike_red_packet_rebates') . " as rebates" . " left join " . tablename('mc_members') . " as f on f.uid=rebates.uid and f.uniacid={$_W['uniacid']}" . " where 1 {$condition}  ORDER BY rebates.id DESC";
    $sql .= " limit " . ($page - 1) * $size . ',' . $size;
    $users = pdo_fetchall($sql, $params);
    $total = pdo_fetchcolumn("select count(*) from" . tablename('yike_red_packet_rebates') . " as rebates " . " left join " . tablename('mc_members') . " as f on f.uid =rebates.uid and f.uniacid={$_W['uniacid']}" . " where 1 {$condition} ", $params);
    $this->show_json(1, array(
        'users' => $users,
        'count' => $total
    ));
} else if ($op == 'child') {
    $user     = $this->getInfo();
    $uid      = $user['uid'];
    $id       = $_GPC['id'];
    $uniacid  = $_W['uniacid'];
    $children = pdo_fetchall('select user.*, member.nickname, member.avatar from ' . tablename('yike_red_packet_user') . ' as user left join ' . tablename('mc_members') . ' as member on user.uid = member.uid where user.uniacid=:uniacid and user.level' . $id . '=:uid', array(
        ':uniacid' => $uniacid,
        ':uid' => $uid
    ));
    $this->show_json(1, $children);
} else if ($op == 'send') {
    $user    = $this->getInfo();
    $uid     = $user['uid'];
    $uniacid = $_W['uniacid'];
    $id      = $_GPC['id'];
    $rebate  = pdo_get('yike_red_packet_rebates', array(
        'uniacid' => $uniacid,
        'id' => $id,
        'status' => 1
    ));
    if (empty($rebate)) {
        $this->show_json(500, '查无此单');
        return false;
    }
    $sec = pdo_fetch("select sec from " . tablename('yike_red_packet_setting') . ' where uniacid=:uniacid limit 1', array(
        ':uniacid' => $uniacid
    ));
    if (empty($sec)) {
        $sec = array();
    }
    $sec    = iunserializer($sec['sec']);
    $rootca = $sec['root'];
    $cert   = $sec['cert'];
    $key    = $sec['key'];
    $result = $this->send($rootca, $key, $cert, $id, $user['openid'], floatval($rebate['money']));
    if ($result) {
        $rebate_result = pdo_update('yike_red_packet_rebates', array(
            'status' => 2
        ), array(
            'uniacid' => $uniacid,
            'id' => $id
        ));
        $user_result   = pdo_query('update ' . tablename('yike_red_packet_user') . ' set withdraw = withdraw + :money where uniacid=:uniacid and uid = :uid', array(
            ':money' => $rebate['money'],
            ':uniacid' => $uniacid,
            ':uid' => $uid
        ));
        $user          = pdo_get('yike_red_packet_user', array(
            'uid' => $uid,
            'uniacid' => $uniacid
        ));
        $openid        = $user['openid'];
        $msg           = '您的提现已成功,请注意查收';
        m('notice')->sendCustomNotice($openid, $msg);
        $this->show_json(1, '发放成功');
    } else {
        $this->show_json(500, '发放失败');
    }
}