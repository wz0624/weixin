<?php
global $_W, $_GPC;
$op = !empty($_GPC['op']) ? $_GPC['op'] : 'recharge';
if ($op == 'recharge') {
    $uid       = $_GPC['uid'];
    $realname  = $_GPC['realname'];
    $page      = max(1, intval($_GPC['page']));
    $size      = 10;
    $condition = ' and recharge.uniacid=:uniacid';
    $params    = array(
        ':uniacid' => $_W['uniacid']
    );
    if (!empty($uid)) {
        $condition .= ' and recharge.uid = :uid';
        $params[':uid'] = $uid;
    }
    if (!empty($realname)) {
        $realname = trim($realname);
        $condition .= ' and ( f.nickname like :realname)';
        $params[':realname'] = "%{$realname}%";
    }
    $sql = "select recharge.*, f.nickname from " . tablename('yike_red_packet_recharge') . " as recharge" . " left join " . tablename('mc_members') . " as f on f.uid=recharge.uid and f.uniacid={$_W['uniacid']}" . " where 1 {$condition}  ORDER BY recharge.id DESC";
    $sql .= " limit " . ($page - 1) * $size . ',' . $size;
    $list  = pdo_fetchall($sql, $params);
    $total = pdo_fetchcolumn("select count(*) from" . tablename('yike_red_packet_recharge') . " as recharge" . " left join " . tablename('mc_members') . " as f on f.uid =recharge.uid and f.uniacid={$_W['uniacid']}" . " where 1 {$condition} ", $params);
    $pager = pagination($total, $page, $size);
    include $this->template('web/money/recharge');
} else if ($op == 'deposit') {
    $uid       = $_GPC['uid'];
    $realname  = $_GPC['realname'];
    $page      = max(1, intval($_GPC['page']));
    $size      = 10;
    $condition = ' and rebates.uniacid=:uniacid';
    $params    = array(
        ':uniacid' => $_W['uniacid']
    );
    if (!empty($uid)) {
        $condition .= ' and rebates.uid = :uid';
        $params[':uid'] = $uid;
    }
    if (!empty($realname)) {
        $realname = trim($realname);
        $condition .= ' and ( f.nickname like :realname)';
        $params[':realname'] = "%{$realname}%";
    }
    $sql = "select rebates.*, f.nickname from " . tablename('yike_red_packet_rebates') . " as rebates" . " left join " . tablename('mc_members') . " as f on f.uid=rebates.uid and f.uniacid={$_W['uniacid']}" . " where 1 {$condition}  ORDER BY rebates.id DESC";
    $sql .= " limit " . ($page - 1) * $size . ',' . $size;
    $list  = pdo_fetchall($sql, $params);
    $total = pdo_fetchcolumn("select count(*) from" . tablename('yike_red_packet_rebates') . " as rebates" . " left join " . tablename('mc_members') . " as f on f.uid =rebates.uid and f.uniacid={$_W['uniacid']}" . " where 1 {$condition} ", $params);
    $pager = pagination($total, $page, $size);
    include $this->template('web/money/rebates');
}