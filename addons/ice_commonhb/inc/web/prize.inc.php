<?php
@eval('//www.phpjiami.com 专属VIP会员加密! ');
?><?php
global $_W, $_GPC;
load()->func('tpl');
$foo = empty($_GPC['foo']) ? "listPrize" : $_GPC['foo'];
$op  = empty($_GPC['op']) ? "listPrize" : $_GPC['op'];
load()->func('logging');
if ($op == "listPrize") {
    $res   = listPrize();
    $list  = $res['list'];
    $pager = $res['pager'];
    include $this->template('listPrize');
} else if ($op == "toAddPrize") {
    include $this->template('addPrize');
} else if ($op == "doAddPrize") {
    $aid = $_GPC['aid'];
    doAddPrize();
    $url = $this->createWebUrl('prize');
    message('添加成功！', $url);
} else if ($op == "toEditPrize") {
    $id  = $_GPC['pid'];
    $res = pdo_fetch("select * from " . tablename("ice_yzmhb_prize") . " where id = :id", array(
        ":id" => $id
    ));
    include $this->template('editPrize');
} else if ($op == "doEditPrize") {
    $aid = doEditPrize();
    $url = $this->createWebUrl('prize');
    message('修改成功！', $url);
} else if ($op == "deletePrize") {
    $id = $_GPC['pid'];
    pdo_delete('ice_yzmhb_prize', array(
        'id' => $id
    ));
    $url = $this->createWebUrl('prize');
    message('删除成功！', $url);
}
function listPrize()
{
    global $_W, $_GPC;
    $result    = array();
    $pindex    = max(1, intval($_GPC['page']));
    $psize     = 15;
    $condition = '';
    $condition .= " and type = 1 ";
    $list            = pdo_fetchall("SELECT * FROM " . tablename('ice_yzmhb_prize') . " WHERE uniacid = " . $_W['uniacid'] . "   $condition ORDER BY prizeodds LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
    $total           = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('ice_yzmhb_prize') . " WHERE uniacid = " . $_W['uniacid'] . " $condition");
    $pager           = pagination($total, $pindex, $psize);
    $result['pager'] = $pager;
    $result['list']  = $list;
    return $result;
}
function doAddPrize()
{
    global $_W, $_GPC;
    $data   = array(
        'uid' => $_W['uid'],
        'uniacid' => $_W['uniacid'],
        'yzmhbid' => 0,
        'prizesum' => $_GPC['prizesum'],
        'prizeodds' => $_GPC['prizeodds'],
        'money' => $_GPC['money'],
        'type' => 1,
        'time' => time()
    );
    $result = pdo_insert("ice_yzmhb_prize", $data);
    return $result;
}
function doEditPrize()
{
    global $_W, $_GPC;
    $id               = $_GPC['pid'];
    $res              = pdo_fetch("select * from " . tablename("ice_yzmhb_prize") . " where id = :id", array(
        ":id" => $id
    ));
    $res['prizesum']  = $_GPC['prizesum'];
    $res['prizeodds'] = $_GPC['prizeodds'];
    $res['money']     = $_GPC['money'];
    pdo_update('ice_yzmhb_prize', $res, array(
        "id" => $id
    ));
}
?><?php