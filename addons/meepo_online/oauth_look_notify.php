<?php
define('IN_MOBILE', true);
require '../../framework/bootstrap.inc.php';
$input = file_get_contents('php://input');
$isxml = true;
if (!empty($input) && empty($_GET['out_trade_no'])) {
    $obj  = isimplexml_load_string($input, 'SimpleXMLElement', LIBXML_NOCDATA);
    $data = json_decode(json_encode($obj), true);
    if (empty($data)) {
        exit('fail');
        exit;
    }
    if ($data['result_code'] != 'SUCCESS' || $data['return_code'] != 'SUCCESS') {
        exit('fail');
        exit;
    }
    $get = $data;
} else {
    $isxml = false;
    $get   = $_GET;
}
$_W['uniacid'] = $_W['weid'] = $get['attach'];
$setting       = uni_setting($_W['uniacid'], array(
    'payment'
));
if (is_array($setting['payment'])) {
    $wechat = $setting['payment']['wechat'];
    WeUtility::logging('pay', var_export($get, true));
    if (!empty($wechat)) {
        ksort($get);
        $string1 = '';
        foreach ($get as $k => $v) {
            if ($v != '' && $k != 'sign') {
                $string1 .= "{$k}={$v}&";
            }
        }
        $wechat['signkey'] = ($wechat['version'] == 1) ? $wechat['key'] : $wechat['signkey'];
        $sign              = strtoupper(md5($string1 . "key={$wechat['signkey']}"));
        if ($sign == $get['sign']) {
            $sql           = 'SELECT * FROM ' . tablename('meepo_online_list_lookpay') . ' WHERE `id`=:id';
            $params        = array();
            $pay_id        = substr($get['out_trade_no'], 10);
            $params[':id'] = $pay_id;
            $log           = pdo_fetch($sql, $params);
            if (!empty($log) && $log['status'] == '0') {
                pdo_update('meepo_online_list_lookpay', array(
                    'status' => '1'
                ), array(
                    'id' => $log['id']
                ));
            }
        }
    }
}
if ($isxml) {
    exit('fail');
    exit;
} else {
    exit('fail');
}