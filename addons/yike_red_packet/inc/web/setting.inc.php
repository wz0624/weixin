<?php
global $_W, $_GPC;
$op      = !empty($_GPC['op']) ? $_GPC['op'] : 'pay';
$setdata = pdo_fetch("select * from " . tablename('yike_red_packet_setting') . ' where uniacid=:uniacid limit 1', array(
    ':uniacid' => $_W['uniacid']
));
$set     = unserialize($setdata['sets']);
if (empty($set)) {
    $set = array();
}
if (empty($setdata)) {
    pdo_insert('yike_red_packet_setting', array(
        'uniacid' => $_W['uniacid'],
        'deposit_limit' => 5000,
        'is_level_limit' => 0,
        'sec' => '',
        'sets' => serialize(array(
            'deposit_limit' => 5000,
            'is_upgrade' => 1,
            'is_withdraw' => 1
        ))
    ));
}
$sec = pdo_fetch("select sec from " . tablename('yike_red_packet_setting') . ' where uniacid=:uniacid limit 1', array(
    ':uniacid' => $_W['uniacid']
));
if (empty($sec)) {
    $sec = array();
}
$sec     = iunserializer($sec['sec']);
$uniacid = $_W['uniacid'];
if ($op == 'pay') {
    if (checksubmit()) {
        $set['pay']  = is_array($_GPC['pay']) ? $_GPC['pay'] : array();
        $sec['cert'] = upload_cert('weixin_cert_file');
        $sec['key']  = upload_cert('weixin_key_file');
        $sec['root'] = upload_cert('weixin_root_file');
        if (empty($sec['cert']) || empty($sec['key']) || empty($sec['root'])) {
        }
        $result = pdo_update('yike_red_packet_setting', array(
            'sec' => iserializer($sec)
        ), array(
            'uniacid' => $_W['uniacid']
        ));
    }
    include $this->template('web/setting/pay');
} else if ($op == 'limit') {
    if (checksubmit()) {
        $set['limit']       = $_GPC['limit'];
        $set['is_upgrade']  = $_GPC['is_upgrade'];
        $set['is_withdraw'] = $_GPC['is_withdraw'];
        $result             = pdo_update('yike_red_packet_setting', array(
            'sets' => serialize($set)
        ), array(
            'uniacid' => $_W['uniacid']
        ));
        message('更新成功', 'refresh');
    }
    include $this->template('web/setting/limit');
} else if ($op == 'poster') {
    if (checksubmit()) {
        $set['poster'] = $_GPC['thumb'];
        $data          = array(
            'sets' => serialize($set)
        );
        pdo_update('yike_red_packet_setting', $data, array(
            'uniacid' => $uniacid
        ));
        message('更新海报成功！', $this->createWebUrl('setting', array(
            'op' => 'poster'
        )), 'success');
    }
    load()->func('tpl');
    include $this->template('web/setting/poster');
} else if ($op == 'banner') {
    if (checksubmit()) {
        $set['banner'] = $_GPC['thumb'];
        $data          = array(
            'sets' => serialize($set)
        );
        pdo_update('yike_red_packet_setting', $data, array(
            'uniacid' => $uniacid
        ));
        message('更新广告图成功！', $this->createWebUrl('setting', array(
            'op' => 'banner'
        )), 'success');
    }
    load()->func('tpl');
    include $this->template('web/setting/banner');
}
function upload_cert($fileinput)
{
    global $_W;
    $path = IA_ROOT . "/addons/yike_red_packet/cert";
    load()->func('file');
    mkdirs($path, '0777');
    $f           = $fileinput . '_' . $_W['uniacid'] . '.pem';
    $outfilename = $path . "/" . $f;
    $filename    = $_FILES[$fileinput]['name'];
    $tmp_name    = $_FILES[$fileinput]['tmp_name'];
    if (!empty($filename) && !empty($tmp_name)) {
        $ext = strtolower(substr($filename, strrpos($filename, '.')));
        if ($ext != '.pem') {
            message('证书文件格式错误: ' . $fileinput . "!", '', 'error');
        }
        return file_get_contents($tmp_name);
    }
    return "";
}