<?php
class ShowApiSdk
{
    static $showapi_appid = '9958';
    static $showapi_sign = '9fc7866fc5f14196859b38002f42b24f';
    static function createSign($paramArr)
    {
        $sign = "";
        ksort($paramArr);
        foreach ($paramArr as $key => $val) {
            if ($key != '' && $val != '') {
                $sign .= $key . $val;
            }
        }
        $sign .= ShowApiSdk::$showapi_sign;
        $sign = strtoupper(md5($sign));
        return $sign;
    }
    static function createStrParam($paramArr)
    {
        $strParam = '';
        foreach ($paramArr as $key => $val) {
            if ($key != '' && $val != '') {
                $strParam .= $key . '=' . urlencode($val) . '&';
            }
        }
        return $strParam;
    }
    static function getContent()
    {
        $paramArr = array(
            'showapi_appid' => ShowApiSdk::$showapi_appid,
            'time' => date('Y-m-d'),
            'page' => '',
            'maxResult' => '50',
            'showapi_timestamp' => date('YmdHis')
        );
        $sign     = ShowApiSdk::createSign($paramArr);
        $strParam = ShowApiSdk::createStrParam($paramArr);
        $strParam .= 'showapi_sign=' . $sign;
        $url    = 'http://route.showapi.com/341-1?' . $strParam;
        $result = file_get_contents($url);
        $result = json_decode($result);
        if (intval($result->showapi_res_code) == 0) {
            var_dump(count($result->showapi_res_body->contentlist));
            echo $result->showapi_res_body->contentlist[0]->text;
        } else {
            var_dump('失败了');
        }
    }
    static function getIDInfo($id)
    {
        $paramArr = array(
            'showapi_appid' => ShowApiSdk::$showapi_appid,
            'id' => $id,
            'showapi_timestamp' => date('YmdHis')
        );
        $sign     = ShowApiSdk::createSign($paramArr);
        $strParam = ShowApiSdk::createStrParam($paramArr);
        $strParam .= 'showapi_sign=' . $sign;
        $url    = 'http://route.showapi.com/25-3?' . $strParam;
        $result = file_get_contents($url);
        $result = json_decode($result);
        if (intval($result->showapi_res_code) == 0) {
            if ($result->showapi_res_body->retMsg == "success") {
                return $result->showapi_res_body->retData;
            }
        } else {
            return '';
        }
    }
    static function getDomainInfo($domain)
    {
        $paramArr = array(
            'showapi_appid' => ShowApiSdk::$showapi_appid,
            'domain' => $domain,
            'showapi_timestamp' => date('YmdHis')
        );
        $sign     = ShowApiSdk::createSign($paramArr);
        $strParam = ShowApiSdk::createStrParam($paramArr);
        $strParam .= 'showapi_sign=' . $sign;
        $url    = 'http://route.showapi.com/846-1?' . $strParam;
        $result = file_get_contents($url);
        $result = json_decode($result);
        if (intval($result->showapi_res_code) == 0) {
            if ($result->showapi_res_error == "") {
                return $result->showapi_res_body->obj;
            }
        } else {
            return '';
        }
    }
    static function endWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }
        return (substr($haystack, -$length) === $needle);
    }
}
