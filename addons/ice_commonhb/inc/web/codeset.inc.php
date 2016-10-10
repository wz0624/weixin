<?php
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
if (checksubmit()) {
    $count = $_GPC['count'];
    $type  = $_GPC['type'];
    $hbid  = 0;
    $param = array(
        'uniacid' => $_W['uniacid'],
        'hbid' => $hbid,
        'count' => $_GPC['count'],
        'type' => $type,
        'time' => time('Ymd')
    );
    if (pdo_insert('ice_yzmhb_codenum', $param)) {
        $pcid = pdo_insertid();
        getcode($pcid, $_GPC['count'], $type, $hbid);
        message('验证码生成成功', '', 'success');
    }
}
$sql   = 'select * from ' . tablename('ice_yzmhb_codenum') . 'where uniacid = :uniacid and hbid = 0 and type = "1"';
$prarm = array(
    ':uniacid' => $_W['uniacid']
);
$list  = pdo_fetchall($sql, $prarm);
include $this->template('codesettings');
function getcode($pcid, $count, $type, $hbid)
{
    global $_W;
    if (intval($count) > 0) {
        for ($i = 0; $i < $count; $i++) {
            do {
                $code1 = genkeyword(6);
            } while (pdo_fetchcolumn('select id from ' . tablename('ice_yzmhb_code') . ' where code = :code limit 1', array(
                    ':code' => $code1
                )));
            $code = array(
                'uniacid' => $_W['uniacid'],
                'piciid' => $pcid,
                'yzmhbid' => $hbid,
                'code' => $code1,
                'type' => $type,
                'time' => time('Ymd')
            );
            if (!pdo_insert('ice_yzmhb_code', $code))
                return false;
        }
    }
}
function genkeyword($length)
{
    $chars    = array(
        'a',
        'b',
        'c',
        'd',
        'e',
        'f',
        'g',
        'h',
        'i',
        'j',
        'k',
        'l',
        'm',
        'n',
        'o',
        'p',
        'q',
        'r',
        's',
        't',
        'u',
        'v',
        'w',
        'x',
        'y',
        'z',
        '0',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9'
    );
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $keys = array_rand($chars, 1);
        $password .= $chars[$keys];
    }
    return $password;
}
