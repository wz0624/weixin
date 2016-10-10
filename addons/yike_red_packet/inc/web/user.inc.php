<?php
global $_W, $_GPC;
$op = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
if ($op == 'list') {
    $uid       = $_GPC['uid'];
    $realname  = $_GPC['realname'];
    $page      = max(1, intval($_GPC['page']));
    $size      = 20;
    $condition = ' and user.uniacid=:uniacid';
    $params    = array(
        ':uniacid' => $_W['uniacid']
    );
    if (!empty($uid)) {
        $condition .= ' and user.uid = :uid';
        $params[':uid'] = $uid;
    }
    if (!empty($realname)) {
        $realname = trim($realname);
        $condition .= ' and ( user.realname like :realname or f.nickname like :realname or user.mobile like :realname)';
        $params[':realname'] = "%{$realname}%";
    }
    $sql = "select user.*, f.nickname from " . tablename('yike_red_packet_user') . " as user" . " left join " . tablename('mc_members') . " as f on f.uid=user.uid and f.uniacid={$_W['uniacid']}" . " where 1 {$condition}  ORDER BY user.id DESC";
    $sql .= " limit " . ($page - 1) * $size . ',' . $size;
    $users = pdo_fetchall($sql, $params);
    $total = pdo_fetchcolumn("select count(*) from" . tablename('yike_red_packet_user') . " as user " . " left join " . tablename('mc_members') . " as f on f.uid =user.uid and f.uniacid={$_W['uniacid']}" . " where 1 {$condition} ", $params);
    $pager = pagination($total, $page, $size);
    include $this->template('web/user/list');
} else if ($op == 'level') {
    $levels = pdo_getall('yike_red_packet_level', array(
        'uniacid' => $_W['uniacid']
    ));
    if (count($levels) > 0) {
    } else {
        $result  = pdo_insert('yike_red_packet_level', array(
            'uniacid' => $_W['uniacid'],
            'level' => 1,
            'name' => '等级一',
            'money' => 100,
            'level1_count' => 3,
            'level1_money' => 10,
            'level2_count' => 0,
            'level2_money' => 20,
            'level3_count' => 0,
            'level3_money' => 30,
            'other_count' => 8,
            'other_money' => 1
        ));
        $result1 = pdo_insert('yike_red_packet_level', array(
            'uniacid' => $_W['uniacid'],
            'level' => 2,
            'name' => '等级二',
            'money' => 300,
            'level1_count' => -1,
            'level1_money' => 10,
            'level2_count' => 3,
            'level2_money' => 20,
            'level3_count' => 0,
            'level3_money' => 30,
            'other_count' => 8,
            'other_money' => 1
        ));
        $result2 = pdo_insert('yike_red_packet_level', array(
            'uniacid' => $_W['uniacid'],
            'level' => 3,
            'name' => '等级三',
            'money' => 600,
            'level1_count' => -1,
            'level1_money' => 10,
            'level2_count' => -1,
            'level2_money' => 20,
            'level3_count' => -1,
            'level3_money' => 30,
            'other_count' => 8,
            'other_money' => 1
        ));
        $levels  = pdo_getall('yike_red_packet_level', array(
            'uniacid' => $_W['uniacid']
        ));
    }
    include $this->template('web/user/level');
} else if ($op == 'edit_level') {
    if (checksubmit('submit')) {
        $data      = array(
            'name' => $_GPC['name'],
            'money' => $_GPC['money'],
            'level1_money' => $_GPC['level1_money'],
            'level1_count' => $_GPC['level1_count'],
            'level2_money' => $_GPC['level2_money'],
            'level2_count' => $_GPC['level2_count'],
            'level3_money' => $_GPC['level3_money'],
            'level3_count' => $_GPC['level3_count'],
            'other_money' => $_GPC['other_money'],
            'other_count' => $_GPC['other_count']
        );
        $condition = array(
            'id' => $_GPC['id']
        );
        $result    = pdo_update('yike_red_packet_level', $data, $condition);
        if (!empty($result)) {
            message('更新成功');
        } else {
            message('更新出错');
        }
    } else {
        $item = pdo_get('yike_red_packet_level', array(
            'uniacid' => $_W['uniacid'],
            'id' => $_GPC['id']
        ));
        if (!empty($item)) {
            include $this->template('web/user/level_edit');
        } else {
            message('查无此记录');
        }
    }
} else if ($op == 'notice') {
    $result = m('notice')->sendTplNotice('oL5Tft0KFMTADBLnBUAL3xOmLCaA', 'MgUz2D35Mpoqf7vTemycymCYrv87RFZw-VmodnDrfyo', array(
        'first' => array(
            'value' => "您的退款申请被商家驳回，可与商家协商沟通！",
            "color" => "#4a5077"
        ),
        'orderMoneySum' => array(
            'title' => '退款金额',
            'value' => '100',
            "color" => "#4a5077"
        ),
        'orderProductName' => array(
            'title' => '商品详情',
            'value' => '产品详情',
            "color" => "#4a5077"
        ),
        'orderName' => array(
            'title' => '订单编号',
            'value' => '1908',
            "color" => "#4a5077"
        ),
        'remark' => array(
            'value' => '',
            "color" => "#4a5077"
        )
    ), '');
    var_dump($result);
} else if ($op == 'msg') {
    $uid     = '16112';
    $uniacid = $_W['uniacid'];
    $user    = pdo_get('yike_red_packet_user', array(
        'uid' => $uid,
        'uniacid' => $uniacid
    ));
    $level1  = pdo_get('yike_red_packet_user', array(
        'uid' => $user['level1'],
        'uniacid' => $uniacid
    ));
    $openid  = $level1['openid'];
    $msg     = '您的粉丝:[' . $user['id'] . ':' . $user['realname'] . '],已成功关注!';
    m('notice')->sendCustomNotice($openid, $msg);
} else if ($op == 'qrcode') {
    $account = m('common')->getAccount();
    if (!$account) {
        message('生成失败');
    }
    $result = $account->barCodeCreateFixed(array(
        'action_name' => 'QR_LIMIT_SCENE',
        'action_info' => array(
            'scene' => array(
                'scene_str' => 'heheh',
                'scene_id' => 1000
            )
        )
    ));
    var_dump($result);
}