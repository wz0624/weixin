<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
} 
/*
 * 返回以$name为名字的类对象
 * */
function m($name = '') {
	static $_modules = array();
	if (isset($_modules[$name])) {
		return $_modules[$name];
	} 
	$model = WELIAM_INDIANA_CORE."model/" . strtolower($name) . '.php';
	if (!is_file($model)) {
		die(' Model ' . $name . ' Not Found!');
	} 
	require $model;
	$class_name = 'Welian_Indiana_' . ucfirst($name);//调用该类
	$_modules[$name] = new $class_name();
	return $_modules[$name];
} 
function is_array2($array) {
	if (is_array($array)) {
		foreach ($array as $k => $v) {
			return is_array($v);
		} 
		return false;
	} 
	return false;
} 
function removeEmoji($text) {

    $clean_text = "";

    // Match Emoticons
    $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
    $clean_text = preg_replace($regexEmoticons, '', $text);

    // Match Miscellaneous Symbols and Pictographs
    $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
    $clean_text = preg_replace($regexSymbols, '', $clean_text);

    // Match Transport And Map Symbols
    $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
    $clean_text = preg_replace($regexTransport, '', $clean_text);

    // Match Miscellaneous Symbols
    $regexMisc = '/[\x{2600}-\x{26FF}]/u';
    $clean_text = preg_replace($regexMisc, '', $clean_text);

    // Match Dingbats
    $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
    $clean_text = preg_replace($regexDingbats, '', $clean_text);

    return $clean_text;
}
function wl_json($status = 1, $return = null) {
	$ret = array('status' => $status);
	if ($return) {
		$ret['result'] = $return;
	} 
	die(json_encode($ret));
}