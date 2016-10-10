<?php
global $_W, $_GPC;
$weid = $_W['uniacid'];
$op   = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($op == 'display') {
    $condition = "WHERE uniacid = $weid  ";
    $params    = array();
    if (!empty($_GPC['keyword'])) {
        $condition .= " AND name LIKE :keyword";
        $params[':keyword'] = "%{$_GPC['keyword']}%";
    }
    $cates = pdo_fetchall("SELECT * FROM " . tablename('amouse_board_product_category') . " $condition ORDER BY displayorder DESC, createtime DESC ", $params);
    if (checksubmit('submit')) {
        for ($i = 0; $i < count($_GPC['titles']); $i++) {
            $ids    = $_GPC['ids'];
            $id     = trim(implode(',', $ids), ',');
            $insert = array(
                'name' => $_GPC['titles'][$i],
                'displayorder' => $_GPC['displayorders'][$i],
                'uniacid' => $weid,
                'createtime' => time()
            );
            if ($ids[$i] != NULL) {
                pdo_update("amouse_board_product_category", $insert, array(
                    'id' => $ids[$i]
                ));
            } else {
                pdo_insert('amouse_board_product_category', $insert);
            }
        }
        message('更新信息成功', referer(), 'success');
    }
} elseif ($op == 'delete') {
    load()->func('file');
    $id = intval($_GPC['id']);
    pdo_delete('amouse_board_product_category', array(
        'id' => $id
    ));
    message('分类删除成功！', $this->createWebUrl('category', array(
        'do' => 'display'
    )), 'success');
}
include $this->template('web/category');