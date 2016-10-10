<?php
 defined('IN_IA')or exit('Access Denied');
function returnError($message, $data = '', $status = 0, $type = ''){
    global $_W;
    if($_W['isajax'] || $type == 'ajax'){
        header('Content-Type:application/json; charset=utf-8');
        $ret = array('status' => $status, 'info' => $message, 'data' => $data);
        exit(json_encode($ret));
    }else{
        return message($message, $data, 'error');
    }
}
function returnSuccess($message, $data = '', $status = 1, $type = ''){
    global $_W;
    if($_W['isajax'] || $type == 'ajax'){
        header('Content-Type:application/json; charset=utf-8');
        $ret = array('status' => $status, 'info' => $message, 'data' => $data);
        exit(json_encode($ret));
    }else{
        return message($message, $data, 'success');
    }
}
function time_to_text($s){
    $t = '';
    if($s > 86400){
        $t .= intval($s / 86400) . "天";
        $s = $s % 86400;
    }
    if($s > 3600){
        $t .= intval($s / 3600) . "小时";
        $s = $s % 3600;
    }
    if($s > 60){
        $t .= intval($s / 60) . "分钟";
        $s = $s % 60;
    }
    if($s > 0){
        $t .= intval($s) . "秒";
    }
    return $t;
}
function rand_words($src, $len){
    $randStr = str_shuffle($src);
    return substr($randStr, 0, $len);
}
function url_base64_encode($str){
    $str = base64_encode($str);
    $code = url_encode($str);
    return $code;
}
function url_encode($code){
    $code = str_replace('+', "!", $code);
    $code = str_replace('/', "*", $code);
    $code = str_replace('=', "", $code);
    return $code;
}
function url_base64_decode($code){
    $code = url_decode($code);
    $str = base64_decode($code);
    return $str;
}
function url_decode($code){
    $code = str_replace("!", '+', $code);
    $code = str_replace("*", '/', $code);
    return $code;
}
function pencode($code, $seed = 'gengli9876543210'){
    $c = url_base64_encode($code);
    $pre = substr(md5($seed . $code), 0, 3);
    return $pre . $c;
}
function pdecode($code, $seed = 'gengli9876543210'){
    if(empty($code) || strlen($code) <= 3){
        return "";
    }
    $pre = substr($code, 0, 3);
    $c = substr($code, 3);
    $str = url_base64_decode($c);
    $spre = substr(md5($seed . $str), 0, 3);
    if($spre == $pre){
        return $str;
    }else{
        return "";
    }
}
function text_len($text){
    preg_match_all('/./us', $text, $match);
    return count($match[0]);
}
function VP_IMAGE_SAVE($path, $dir = ''){
    global $_W;
    $filePath = ATTACHMENT_ROOT . '/' . $path;
    $key = $path;
    $accessKey = $_W['module_setting']['qn_ak'];
    $secretKey = $_W['module_setting']['qn_sk'];
    $auth = new Qiniu\Auth($accessKey, $secretKey);
    $bucket = empty($dir)?$_W['module_setting']['qn_bucket']:$dir;
    $token = $auth -> uploadToken($bucket);
    $uploadMgr = new Qiniu\Storage\UploadManager();
    list($ret, $err) = $uploadMgr -> putFile($token, $key, $filePath);
    return array('error' => $err, 'image' => empty($ret)?'':$ret['key']);
}
function VP_IMAGE_URL($path, $style = 'm', $dir = '', $driver = ''){
    global $_W;
    if('local' == $driver){
        return $_W['attachurl'] . $path;
    }else{
        return 'http://' . $_W['module_setting']['qn_api'] . '/' . $path . '-' . $style;
    }
}
function VP_AVATAR($src, $size = 's'){
    if(empty($src) || empty($size)){
        return $src;
    }else{
        $parts = parse_url($src);
        if($parts['host'] == 'wx.qlogo.cn'){
            if($size == 's'){
                $size = '64';
            }else if($size == 'm'){
                $size = '132';
            }
            $src = substr($src, 0, strrpos($src, '/')) . '/' . $size;
        }else{
            $src = tomedia($src);
        }
        return $src;
    }
}
function VP_THUMB($src, $size = 120){
    $ppos = strrpos($src, ".");
    return substr($src, 0, $ppos) . '_' . $size . substr($src, $ppos);
}
function roll_weight($datas = array()){
    $roll = rand (1, array_sum($datas));
    $_tmpW = 0;
    $rollnum = 0;
    foreach ($datas as $k => $v){
        $min = $_tmpW;
        $_tmpW += $v;
        $max = $_tmpW;
        if ($roll > $min && $roll <= $max){
            $rollnum = $k;
            break;
        }
    }
    return $rollnum;
}
function redpack_plan($total, $num, $min){
    $packs = array();
    for ($i = 1;$i < $num;$i++){
        $safe_total = ($total - ($num - $i) * $min) / ($num - $i);
        $money = mt_rand($min, $safe_total);
        $total = $total - $money;
        $packs[] = $money;
    }
    $packs[] = $total;
    return $packs;
}
function explode_map($txt){
    $result = array();
    $arr = array();
    $txt = str_replace("\r\n", '%e2%80%a1', $txt);
    $txt = str_replace("\n", '%e2%80%a1', $txt);
    $arr = explode('%e2%80%a1', $txt);
    foreach($arr as $kv){
        if(empty($kv)){
            continue;
        }
        $kv = explode(':', $kv);
        if(count($kv) != 2){
            continue;
        }
        $result[$kv[0]] = $kv[1];
    }
    return $result;
}
