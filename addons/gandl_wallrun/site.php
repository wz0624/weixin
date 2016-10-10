<?php
 defined('IN_IA')or exit('Access Denied');
define('MD_ROOT', IA_ROOT . '/addons/gandl_wallrun');
require MD_ROOT . '/source/common/common.func.php';
class Gandl_wallrunModuleSite extends WeModuleSite{
    public function doWebQr(){
        global $_GPC;
        $raw = @base64_decode($_GPC['raw']);
        if (!empty($raw)){
            include MD_ROOT . '/source/common/phpqrcode.php';
            QRcode :: png($raw, false, QR_ECLEVEL_Q, 4);
        }
    }
    protected function returnMessage($msg, $redirect = '', $type = ''){
        global $_W, $_GPC;
        if($redirect == 'refresh'){
            $redirect = $_W['script_name'] . '?' . $_SERVER['QUERY_STRING'];
        }
        if($redirect == 'referer'){
            $redirect = referer();
        }
        if($redirect == ''){
            $type = in_array($type, array('success', 'error', 'info', 'warn'))? $type : 'info';
        }else{
            $type = in_array($type, array('success', 'error', 'info', 'warn'))? $type : 'success';
        }
        if (empty($msg) && !empty($redirect)){
            header('location: ' . $redirect);
        }
        $label = $type;
        if($type == 'error'){
            $label = 'warn';
        }
        include $this -> template('inc/message');
        exit();
    }
    protected function returnError($message, $data = '', $status = 0, $type = ''){
        global $_W;
        if($_W['isajax'] || $type == 'ajax'){
            header('Content-Type:application/json; charset=utf-8');
            $ret = array('status' => $status, 'info' => $message, 'data' => $data);
            exit(json_encode($ret));
        }else{
            return $this -> returnMessage($message, $data, 'error');
        }
    }
    protected function returnSuccess($message, $data = '', $status = 1, $type = ''){
        global $_W;
        if($_W['isajax'] || $type == 'ajax'){
            header('Content-Type:application/json; charset=utf-8');
            $ret = array('status' => $status, 'info' => $message, 'data' => $data);
            exit(json_encode($ret));
        }else{
            return $this -> returnMessage($message, $data, 'success');
        }
    }
}
