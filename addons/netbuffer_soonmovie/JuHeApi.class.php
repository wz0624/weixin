<?php
@eval('//www.phpjiami.com 专属VIP会员加密! ');
?><?php
class JuHeApiSdk
{
    static function getQQnumInfo($qq)
    {
        $url         = "http://japi.juhe.cn/qqevaluate/qq";
        $params      = array(
            "key" => "367c0505886f4bb3ac81b4bb79f7d6a7",
            "qq" => $qq
        );
        $paramstring = http_build_query($params);
        $content     = JuHeApiSdk::juhecurl($url, $paramstring);
        $result      = json_decode($content, true);
        if ($result) {
            if ($result['error_code'] == '0') {
                return $result["result"]["data"];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
    static function getLatestMovie($city)
    {
        $url         = "http://op.juhe.cn/onebox/movie/pmovie";
        $params      = array(
            "key" => "5cb707b6191c58b784907c6a744c7571",
            "city" => $city
        );
        $paramstring = http_build_query($params);
        $content     = JuHeApiSdk::juhecurl($url, $paramstring);
        $result      = json_decode($content, true);
        if ($result) {
            if ($result['error_code'] == '0') {
                return $result["result"];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
    static function juhecurl($url, $params = false, $ispost = 0)
    {
        $httpInfo = array();
        $ch       = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'JuheData');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($params) {
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }
        $response = curl_exec($ch);
        if ($response === FALSE) {
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        curl_close($ch);
        return $response;
    }
}
?><?php