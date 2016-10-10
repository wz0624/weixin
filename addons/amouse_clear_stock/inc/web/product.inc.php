<?php
global $_W, $_GPC;
$weid = $_W['uniacid'];
$op   = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
load()->func('tpl');
$category = pdo_fetchall("SELECT id,name FROM " . tablename('amouse_board_product_category') . " WHERE uniacid = $weid ORDER BY displayorder DESC ");
if ($op == 'display') {
    $pindex    = max(1, intval($_GPC['page']));
    $psize     = 15;
    $status    = $_GPC['status'];
    $params    = array();
    $condition = " where uniacid= $weid ";
    if (!empty($_GPC['keyword'])) {
        $_GPC['keyword'] = trim($_GPC['keyword']);
        $condition .= ' AND ( title LIKE :keyword ) ';
        $params[':keyword'] = '%' . trim($_GPC['keyword']) . '%';
    }
    if (!empty($_GPC['pcateid'])) {
        $cid = intval($_GPC['pcateid']);
        $condition .= " AND pcateid = '{$cid}' ";
    }
    $list   = pdo_fetchall("SELECT * FROM " . tablename('amouse_board_clear_stock_goods') . " {$condition} ORDER BY status DESC,createtime DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',
' . $psize, $params);
    $orders = array();
    foreach ($list as &$item) {
        $mtask              = pdo_fetch("SELECT headimgurl,nickname FROM " . tablename('amouse_board_member') . " WHERE openid=:openid limit 1", array(
            ':openid' => $item['openid']
        ));
        $item['headimgurl'] = $mtask['headimgurl'];
        $item['nickname']   = $mtask['nickname'];
        $pc                 = pdo_fetch("SELECT id,name FROM " . tablename('amouse_board_product_category') . " WHERE uniacid = $weid AND id=:id ", array(
            ':id' => $item['pcateid']
        ));
        $item['cname']      = $pc['name'];
        $item['cid']        = $pc['id'];
        $orders[]           = $item;
    }
    unset($item);
    $total = pdo_fetchcolumn('SELECT count(*)  FROM ' . tablename('amouse_board_clear_stock_goods') . $condition, $params);
    $pager = pagination($total, $pindex, $psize);
} elseif ($op == 'post') {
    $id = intval($_GPC['id']);
    load()->func('tpl');
    if ($id > 0) {
        $item = pdo_fetch("SELECT * FROM " . tablename('amouse_board_clear_stock_goods') . " WHERE id = :id", array(
            ':id' => $id
        ));
        if (empty($item)) {
            message('抱歉，记录不存在或是已经删除！', '', 'error');
        }
    } else {
        $item['createtime'] = TIMESTAMP;
    }
    $pindex = max(1, intval($_GPC['page']));
    if (checksubmit('submit')) {
        $data = array(
            'uniacid' => $weid,
            'title' => $_GPC['title'],
            'price' => $_GPC['price'],
            'clear_price' => $_GPC['clear_price'],
            'pcateid' => $_GPC['pcateid'],
            'logo' => tomedia($_GPC['logo']),
            'thumb1' => tomedia($_GPC['thumb1']),
            'thumb2' => tomedia($_GPC['thumb2']),
            'thumb3' => tomedia($_GPC['thumb3']),
            'detail' => $_GPC['detail'],
            'status' => 1,
            'createtime' => TIMESTAMP,
            'viewcount' => $_GPC['viewcount'],
            'uptime' => TIMESTAMP
        );
        if (empty($id)) {
            pdo_insert('amouse_board_clear_stock_goods', $data);
        } else {
            pdo_update('amouse_board_clear_stock_goods', $data, array(
                'id' => $id
            ));
        }
        message('更新成功！', $this->createWebUrl('product', array(
            'op' => 'display',
            'page' => $pindex
        )), 'success');
    }
} elseif ($op == 'delete') {
    $id  = intval($_GPC['id']);
    $row = pdo_fetch("SELECT id FROM " . tablename('amouse_board_clear_stock_goods') . " WHERE id = :id", array(
        ':id' => $id
    ));
    if (empty($row)) {
        message('抱歉，货源记录不存在或是已经被删除！');
    }
    pdo_delete('amouse_board_clear_stock_goods', array(
        'id' => $id
    ));
    message('删除成功！', referer(), 'success');
} elseif ($op == 'setstatus') {
    $id   = intval($_GPC['id']);
    $data = intval($_GPC['data']);
    $type = $_GPC['type'];
    $data = ($data == 1 ? '0' : '1');
    pdo_update('amouse_board_clear_stock_goods', array(
        $type => $data
    ), array(
        "id" => $id,
        "uniacid" => $_W['uniacid']
    ));
    die(json_encode(array(
        'result' => 1,
        'data' => $data
    )));
}
include $this->template('web/product');