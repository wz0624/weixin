<?php

defined('IN_IA') or die('Access Denied');
function returnError($message, $data = '', $status = 0, $type = '')
{
	global $_W;
	if ($_W['isajax'] || $type == 'ajax') {
		header('Content-Type:application/json; charset=utf-8');
		$ret = array('status' => $status, 'info' => $message, 'data' => $data);
		die(json_encode($ret));
	} else {
		return message($message, $data, 'error');
	}
}
function returnSuccess($message, $data = '', $status = 1, $type = '')
{
	global $_W;
	if ($_W['isajax'] || $type == 'ajax') {
		header('Content-Type:application/json; charset=utf-8');
		$ret = array('status' => $status, 'info' => $message, 'data' => $data);
		die(json_encode($ret));
	} else {
		return message($message, $data, 'success');
	}
}
function time_to_text($s)
{
	$t = '';
	if ($s > 86400) {
		$t .= intval($s / 86400) . "天";
		$s = $s % 86400;
	}
	if ($s > 3600) {
		$t .= intval($s / 3600) . "小时";
		$s = $s % 3600;
	}
	if ($s > 60) {
		$t .= intval($s / 60) . "分钟";
		$s = $s % 60;
	}
	if ($s > 0) {
		$t .= intval($s) . "秒";
	}
	return $t;
}
function rand_words($src, $len)
{
	$randStr = str_shuffle($src);
	return substr($randStr, 0, $len);
}
function url_base64_encode($str)
{
	$str = base64_encode($str);
	$code = url_encode($str);
	return $code;
}
function url_encode($code)
{
	$code = str_replace('+', "!", $code);
	$code = str_replace('/', "*", $code);
	$code = str_replace('=', "", $code);
	return $code;
}
function url_base64_decode($code)
{
	$code = url_decode($code);
	$str = base64_decode($code);
	return $str;
}
function url_decode($code)
{
	$code = str_replace("!", '+', $code);
	$code = str_replace("*", '/', $code);
	return $code;
}
function pencode($code, $seed = 'gengli9876543210')
{
	$c = url_base64_encode($code);
	$pre = substr(md5($seed . $code), 0, 3);
	return $pre . $c;
}
function pdecode($code, $seed = 'gengli9876543210')
{
	if (empty($code) || strlen($code) <= 3) {
		return "";
	}
	$pre = substr($code, 0, 3);
	$c = substr($code, 3);
	$str = url_base64_decode($c);
	$spre = substr(md5($seed . $str), 0, 3);
	if ($spre == $pre) {
		return $str;
	} else {
		return "";
	}
}
function text_len($text)
{
	preg_match_all('/./us', $text, $match);
	return count($match[0]);
}
function VP_IMAGE_SAVE($path, $dir = '')
{
	global $_W;
	$filePath = ATTACHMENT_ROOT . '/' . $path;
	$key = $path;
	$accessKey = $_W['module_setting']['qn_ak'];
	$secretKey = $_W['module_setting']['qn_sk'];
	$auth = new Qiniu\Auth($accessKey, $secretKey);
	$bucket = empty($dir) ? $_W['module_setting']['qn_bucket'] : $dir;
	$token = $auth->uploadToken($bucket);
	$uploadMgr = new Qiniu\Storage\UploadManager();
	list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
	return array('error' => $err, 'image' => empty($ret) ? '' : $ret['key']);
}
function VP_IMAGE_URL($path, $dir = '', $driver = '')
{
	global $_W;
	if ('local' == $driver) {
		return $_W['attachurl'] . $path;
	} else {
		return 'http://' . $_W['module_setting']['qn_api'] . '/' . $path;
	}
}
// function VP_IMAGE_URL($path, $style = 'm', $dir = '', $driver = '')
// {
// 	global $_W;
// 	if ('local' == $driver) {
// 		return $_W['attachurl'] . $path;
// 	} else {
// 		return 'http://' . $_W['module_setting']['qn_api'] . '/' . $path . '-' . $style;
// 	}
// }
function VP_AVATAR($src, $size = 's')
{
	if (empty($src) || empty($size)) {
		return $src;
	} else {
		$parts = parse_url($src);
		if ($parts['host'] == 'wx.qlogo.cn') {
			if ($size == 's') {
				$size = '64';
			} else {
				if ($size == 'm') {
					$size = '132';
				}
			}
			$src = substr($src, 0, strrpos($src, '/')) . '/' . $size;
		} else {
			$src = tomedia($src);
		}
		return $src;
	}
}
function VP_THUMB($src, $size = 120)
{
	$ppos = strrpos($src, ".");
	return substr($src, 0, $ppos) . '_' . $size . substr($src, $ppos);
}
function WX_CARD_TYPE($type = null)
{
	$map = array('GROUPON' => '团购券', 'DISCOUNT' => '折扣券', 'GIFT' => '礼品券', 'CASH' => '代金券', 'GENERAL_COUPON' => '通用券', 'MEMBER_CARD' => '会员卡', 'SCENIC_TICKET' => '景点门票', 'MOVIE_TICKET' => '电影票', 'BOARDING_PASS' => '飞机票', 'MEETING_TICKET' => '会议门票', 'BUS_TICKET' => '汽车票');
	if ($type == null) {
		return $map;
	} else {
		return $map[$type];
	}
}
function WX_CARD_STATUS($status = null)
{
	$map = array('CARD_STATUS_NOT_VERIFY' => '待审核', 'CARD_STATUS_VERIFY_FAIL' => '审核失败', 'CARD_STATUS_VERIFY_OK' => '通过审核', 'CARD_STATUS_USER_DELETE' => '卡券被商户删除', 'CARD_STATUS_DISPATCH' => '在公众平台投放过的卡券');
	if ($status == null) {
		return $map;
	} else {
		return $map[$status];
	}
}
function roll_weight($datas = array())
{
	$roll = rand(1, array_sum($datas));
	$_tmpW = 0;
	$rollnum = 0;
	foreach ($datas as $k => $v) {
		$min = $_tmpW;
		$_tmpW += $v;
		$max = $_tmpW;
		if ($roll > $min && $roll <= $max) {
			$rollnum = $k;
			break;
		}
	}
	return $rollnum;
}
function vp_sqr($n)
{
	return $n * $n;
}
function vp_random($bonus_min, $bonus_max)
{
	$sqr = intval(vp_sqr($bonus_max - $bonus_min));
	$rand_num = rand(0, $sqr - 1);
	return intval(sqrt($rand_num));
}
function redpack_plan($bonus_total, $bonus_count, $bonus_max, $bonus_min)
{
	$result = array();
	$average = $bonus_total / $bonus_count;
	$a = $average - $bonus_min;
	$b = $bonus_max - $bonus_min;
	$range1 = vp_sqr($average - $bonus_min);
	$range2 = vp_sqr($bonus_max - $average);
	for ($i = 0; $i < $bonus_count; $i++) {
		if (rand($bonus_min, $bonus_max) > $average) {
			$temp = $bonus_min + vp_random($bonus_min, $average);
			$result[$i] = $temp;
			$bonus_total -= $temp;
		} else {
			$temp = $bonus_max - vp_random($average, $bonus_max);
			$result[$i] = $temp;
			$bonus_total -= $temp;
		}
	}
	while ($bonus_total > 0) {
		for ($i = 0; $i < $bonus_count; $i++) {
			if ($bonus_total > 0 && $result[$i] < $bonus_max) {
				$result[$i]++;
				$bonus_total--;
			}
		}
	}
	while ($bonus_total < 0) {
		for ($i = 0; $i < $bonus_count; $i++) {
			if ($bonus_total < 0 && $result[$i] > $bonus_min) {
				$result[$i]--;
				$bonus_total++;
			}
		}
	}
	return $result;
}
function explode_map($txt)
{
	$result = array();
	$arr = array();
	$txt = str_replace("\r\n", '%e2%80%a1', $txt);
	$txt = str_replace("\n", '%e2%80%a1', $txt);
	$arr = explode('%e2%80%a1', $txt);
	foreach ($arr as $kv) {
		if (empty($kv)) {
			continue;
		}
		$kv = explode(':', $kv);
		if (count($kv) != 2) {
			continue;
		}
		$result[$kv[0]] = $kv[1];
	}
	return $result;
}
function wall_lang($map, $key)
{
	$lang = array('rob_text' => '撒钱发动态');
	if (!empty($map[$key])) {
		return $map[$key];
	} else {
		return $lang[$key];
	}
}