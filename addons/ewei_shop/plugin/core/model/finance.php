<?php
if (!defined("IN_IA")) {
    exit("Access Denied");
}
class Ewei_DShop_Finance
{
    public function pay($val0 = '', $val1 = 0, $val2 = 0, $val3 = '', $val4 = '')
    {
        global $_W, $_GPC;
        if (empty($val0)) {
            return error(-1, "openid不能为空");
        }
        $val8 = m("member")->getMember($val0);
        if (empty($val8)) {
            return error(-1, "未找到用户");
        }
        if (empty($val1)) {
            m("member")->setCredit($val0, "credit2", $val2, array(
                0,
                $val4
            ));
            return true;
        } else {
            $val15 = uni_setting($_W["uniacid"], array(
                "payment"
            ));
            if (!is_array($val15["payment"])) {
                return error(1, "没有设定支付参数");
            }
            $val18                     = m("common")->getSysset("pay");
            $val19                     = $val15["payment"]["wechat"];
            $val21                     = "SELECT `key`,`secret` FROM " . tablename("account_wechats") . " WHERE `uniacid`=:uniacid limit 1";
            $val22                     = pdo_fetch($val21, array(
                ":uniacid" => $_W["uniacid"]
            ));
            $val25                     = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
            $val26                     = array();
            $val26["mch_appid"]        = $val22["key"];
            $val26["mchid"]            = $val19["mchid"];
            $val26["nonce_str"]        = random(32);
            $val26["partner_trade_no"] = empty($val3) ? time() . random(4, true) : $val3;
            $val26["openid"]           = $val0;
            $val26["check_name"]       = "NO_CHECK";
            $val26["amount"]           = $val2;
            $val26["desc"]             = empty($val4) ? "佣金提现" : $val4;
            $val26["spbill_create_ip"] = gethostbyname($_SERVER["HTTP_HOST"]);
            ksort($val26, SORT_STRING);
            $val46 = '';
            foreach ($val26 as $val48 => $val49) {
                $val46 .= "{$val48}={$val49}&";
            }
            $val46 .= "key=" . $val19["apikey"];
            $val26["sign"] = strtoupper(md5($val46));
            $val57         = array2xml($val26);
            $val59         = array();
            $val60         = m("common")->getSec();
            $val61         = iunserializer($val60["sec"]);
            if (is_array($val61)) {
                if (empty($val61["cert"]) || empty($val61["key"]) || empty($val61["root"])) {
                    message("未上传完整的微信支付证书，请到【系统设置】->【支付方式】中上传!", '', "error");
                }
                $val67 = IA_ROOT . "/addons/ewei_shop/cert/" . random(128);
                file_put_contents($val67, $val61["cert"]);
                $val70 = IA_ROOT . "/addons/ewei_shop/cert/" . random(128);
                file_put_contents($val70, $val61["key"]);
                $val73 = IA_ROOT . "/addons/ewei_shop/cert/" . random(128);
                file_put_contents($val73, $val61["root"]);
                $val59["CURLOPT_SSLCERT"] = $val67;
                $val59["CURLOPT_SSLKEY"]  = $val70;
                $val59["CURLOPT_CAINFO"]  = $val73;
            } else {
                message("未上传完整的微信支付证书，请到【系统设置】->【支付方式】中上传!", '', "error");
            }
            load()->func("communication");
            $val82 = ihttp_request($val25, $val57, $val59);
            @unlink($val67);
            @unlink($val70);
            @unlink($val73);
            if (is_error($val82)) {
                return error(-2, $val82["message"]);
            }
            if (empty($val82["content"])) {
                return error(-2, "网络错误");
            } else {
                $val92 = json_decode(json_encode((array) simplexml_load_string($val82["content"])), true);
                $val57 = "<?xml version='1.0' encoding='utf-8'?>" . $val82["content"];
                $val96 = new \DOMDocument();
                if ($val96->loadXML($val57)) {
                    $val98  = new \DOMXPath($val96);
                    $val100 = $val98->evaluate("string(//xml/return_code)");
                    $val102 = $val98->evaluate("string(//xml/result_code)");
                    if (strtolower($val100) == "success" && strtolower($val102) == "success") {
                        return true;
                    } else {
                        if ($val98->evaluate("string(//xml/return_msg)") == $val98->evaluate("string(//xml/err_code_des)")) {
                            $val108 = $val98->evaluate("string(//xml/return_msg)");
                        } else {
                            $val108 = $val98->evaluate("string(//xml/return_msg)") . "<br/>" . $val98->evaluate("string(//xml/err_code_des)");
                        }
                        return error(-2, $val108);
                    }
                } else {
                    return error(-1, "未知错误");
                }
            }
        }
    }
    public function refund($val0, $val115, $val116, $val117, $val118 = 0)
    {
        global $_W, $_GPC;
        if (empty($val0)) {
            return error(-1, "openid不能为空");
        }
        $val8 = m("member")->getMember($val0);
        if (empty($val8)) {
            return error(-1, "未找到用户");
        }
        $val15 = uni_setting($_W["uniacid"], array(
            "payment"
        ));
        if (!is_array($val15["payment"])) {
            return error(1, "没有设定支付参数");
        }
        $val18                  = m("common")->getSysset("pay");
        $val19                  = $val15["payment"]["wechat"];
        $val21                  = "SELECT `key`,`secret` FROM " . tablename("account_wechats") . " WHERE `uniacid`=:uniacid limit 1";
        $val22                  = pdo_fetch($val21, array(
            ":uniacid" => $_W["uniacid"]
        ));
        $val25                  = "https://api.mch.weixin.qq.com/secapi/pay/refund";
        $val26                  = array();
        $val26["appid"]         = $val22["key"];
        $val26["mch_id"]        = $val19["mchid"];
        $val26["nonce_str"]     = random(8);
        $val26["out_trade_no"]  = $val115;
        $val26["out_refund_no"] = $val116;
        $val26["total_fee"]     = $val117;
        $val26["refund_fee"]    = $val118;
        $val26["op_user_id"]    = $val19["mchid"];
        ksort($val26, SORT_STRING);
        $val46 = '';
        foreach ($val26 as $val48 => $val49) {
            $val46 .= "{$val48}={$val49}&";
        }
        $val46 .= "key=" . $val19["apikey"];
        $val26["sign"] = strtoupper(md5($val46));
        $val57         = array2xml($val26);
        $val59         = array();
        $val60         = m("common")->getSec();
        $val61         = iunserializer($val60["sec"]);
        if (is_array($val61)) {
            if (empty($val61["cert"]) || empty($val61["key"]) || empty($val61["root"])) {
                message("未上传完整的微信支付证书，请到【系统设置】->【支付方式】中上传!", '', "error");
            }
            $val67 = IA_ROOT . "/addons/ewei_shop/cert/" . random(128);
            file_put_contents($val67, $val61["cert"]);
            $val70 = IA_ROOT . "/addons/ewei_shop/cert/" . random(128);
            file_put_contents($val70, $val61["key"]);
            $val73 = IA_ROOT . "/addons/ewei_shop/cert/" . random(128);
            file_put_contents($val73, $val61["root"]);
            $val59["CURLOPT_SSLCERT"] = $val67;
            $val59["CURLOPT_SSLKEY"]  = $val70;
            $val59["CURLOPT_CAINFO"]  = $val73;
        } else {
            message("未上传完整的微信支付证书，请到【系统设置】->【支付方式】中上传!", '', "error");
        }
        load()->func("communication");
        $val82 = ihttp_request($val25, $val57, $val59);
        @unlink($val67);
        @unlink($val70);
        @unlink($val73);
        if (is_error($val82)) {
            return error(-2, $val82["message"]);
        }
        if (empty($val82["content"])) {
            return error(-2, "网络错误");
        } else {
            $val92 = json_decode(json_encode((array) simplexml_load_string($val82["content"])), true);
            $val57 = "<?xml version='1.0' encoding='utf-8'?>" . $val82["content"];
            $val96 = new \DOMDocument();
            if ($val96->loadXML($val57)) {
                $val98  = new \DOMXPath($val96);
                $val100 = $val98->evaluate("string(//xml/return_code)");
                $val102 = $val98->evaluate("string(//xml/result_code)");
                if (strtolower($val100) == "success" && strtolower($val102) == "success") {
                    return true;
                } else {
                    if ($val98->evaluate("string(//xml/return_msg)") == $val98->evaluate("string(//xml/err_code_des)")) {
                        $val108 = $val98->evaluate("string(//xml/return_msg)");
                    } else {
                        $val108 = $val98->evaluate("string(//xml/return_msg)") . "<br/>" . $val98->evaluate("string(//xml/err_code_des)");
                    }
                    return error(-2, $val108);
                }
            } else {
                return error(-1, "未知错误");
            }
        }
    }
    public function downloadbill($val221, $val222, $val223 = 'ALL')
    {
        global $_W, $_GPC;
        $val226 = array();
        $val227 = date("Ymd", $val221);
        $val229 = date("Ymd", $val222);
        if ($val227 == $val229) {
            $val226 = array(
                $val227
            );
        } else {
            $val235 = (float) ($val222 - $val221) / 86400;
            for ($val238 = 0; $val238 < $val235; $val238++) {
                $val226[] = date("Ymd", strtotime($val227 . "+{$val238} day"));
            }
        }
        if (empty($val226)) {
            message("对账单日期选择错误!", '', "error");
        }
        $val15 = uni_setting($_W["uniacid"], array(
            "payment"
        ));
        if (!is_array($val15["payment"])) {
            return error(1, "没有设定支付参数");
        }
        $val19  = $val15["payment"]["wechat"];
        $val21  = "SELECT `key`,`secret` FROM " . tablename("account_wechats") . " WHERE `uniacid`=:uniacid limit 1";
        $val22  = pdo_fetch($val21, array(
            ":uniacid" => $_W["uniacid"]
        ));
        $val255 = "";
        foreach ($val226 as $val257) {
            $val258 = $this->downloadday($val257, $val22, $val19, $val223);
            if (is_error($val258) || strexists($val258, "CDATA[FAIL]")) {
                continue;
            }
            $val255 .= $val257 . " 账单

";
            $val255 .= $val258 . "

";
        }
        $val255 = "﻿" . $val255;
        $val271 = time() . ".csv";
        header("Content-type: application/octet-stream ");
        header("Accept-Ranges: bytes ");
        header("Content-Disposition: attachment; filename={$val271}");
        header("Expires: 0 ");
        header("Content-Encoding: UTF8");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0 ");
        header("Pragma: public ");
        die($val255);
    }
    private function downloadday($val257, $val22, $val19, $val223)
    {
        $val25                = "https://api.mch.weixin.qq.com/pay/downloadbill";
        $val26                = array();
        $val26["appid"]       = $val22["key"];
        $val26["mch_id"]      = $val19["mchid"];
        $val26["nonce_str"]   = random(8);
        $val26["device_info"] = "ewei_shop";
        $val26["bill_date"]   = $val257;
        $val26["bill_type"]   = $val223;
        ksort($val26, SORT_STRING);
        $val46 = '';
        foreach ($val26 as $val48 => $val49) {
            $val46 .= "{$val48}={$val49}&";
        }
        $val46 .= "key=" . $val19["apikey"];
        $val26["sign"] = strtoupper(md5($val46));
        $val57         = array2xml($val26);
        $val59         = array();
        load()->func("communication");
        $val82 = ihttp_request($val25, $val57, $val59);
        if (strexists($val82["content"], "No Bill Exist")) {
            return error(-2, "未搜索到任何账单");
        }
        if (is_error($val82)) {
            return error(-2, $val82["message"]);
        }
        if (empty($val82["content"])) {
            return error(-2, "网络错误");
        } else {
            return $val82["content"];
        }
    }
    public function closeOrder($val115 = '')
    {
        global $_W, $_GPC;
        $val15 = uni_setting($_W["uniacid"], array(
            "payment"
        ));
        if (!is_array($val15["payment"])) {
            return error(1, "没有设定支付参数");
        }
        $val19                 = $val15["payment"]["wechat"];
        $val21                 = "SELECT `key`,`secret` FROM " . tablename("account_wechats") . " WHERE `uniacid`=:uniacid limit 1";
        $val22                 = pdo_fetch($val21, array(
            ":uniacid" => $_W["uniacid"]
        ));
        $val25                 = "https://api.mch.weixin.qq.com/pay/closeorder";
        $val26                 = array();
        $val26["appid"]        = $val22["key"];
        $val26["mch_id"]       = $val19["mchid"];
        $val26["nonce_str"]    = random(8);
        $val26["out_trade_no"] = $val115;
        ksort($val26, SORT_STRING);
        $val46 = '';
        foreach ($val26 as $val48 => $val49) {
            $val46 .= "{$val48}={$val49}&";
        }
        $val46 .= "key=" . $val19["apikey"];
        $val26["sign"] = strtoupper(md5($val46));
        $val57         = array2xml($val26);
        load()->func("communication");
        $val82 = ihttp_post($val25, $val57);
        if (is_error($val82)) {
            return error(-2, $val82["message"]);
        }
        if (empty($val82["content"])) {
            return error(-2, "网络错误");
        } else {
            $val92 = json_decode(json_encode((array) simplexml_load_string($val82["content"])), true);
            $val57 = "<?xml version='1.0' encoding='utf-8'?>" . $val82["content"];
            $val96 = new \DOMDocument();
            if ($val96->loadXML($val57)) {
                $val98  = new \DOMXPath($val96);
                $val100 = $val98->evaluate("string(//xml/return_code)");
                $val102 = $val98->evaluate("string(//xml/result_code)");
                $val367 = $val98->evaluate("string(//xml/trade_state)");
                if (strtolower($val100) == "success" && strtolower($val102) == "success" && strtolower($val367) == "success") {
                    return true;
                } else {
                    if ($val98->evaluate("string(//xml/return_msg)") == $val98->evaluate("string(//xml/err_code_des)")) {
                        $val108 = $val98->evaluate("string(//xml/return_msg)");
                    } else {
                        $val108 = $val98->evaluate("string(//xml/return_msg)") . "<br/>" . $val98->evaluate("string(//xml/err_code_des)");
                    }
                    return error(-2, $val108);
                }
            } else {
                return error(-1, "未知错误");
            }
        }
    }
    public function isWeixinPay($val115, $val2 = 0)
    {
        global $_W, $_GPC;
        $val15 = uni_setting($_W["uniacid"], array(
            "payment"
        ));
        if (!is_array($val15["payment"])) {
            return error(1, "没有设定支付参数");
        }
        $val19                 = $val15["payment"]["wechat"];
        $val21                 = "SELECT `key`,`secret` FROM " . tablename("account_wechats") . " WHERE `uniacid`=:uniacid limit 1";
        $val22                 = pdo_fetch($val21, array(
            ":uniacid" => $_W["uniacid"]
        ));
        $val25                 = "https://api.mch.weixin.qq.com/pay/orderquery";
        $val26                 = array();
        $val26["appid"]        = $val22["key"];
        $val26["mch_id"]       = $val19["mchid"];
        $val26["nonce_str"]    = random(8);
        $val26["out_trade_no"] = $val115;
        ksort($val26, SORT_STRING);
        $val46 = '';
        foreach ($val26 as $val48 => $val49) {
            $val46 .= "{$val48}={$val49}&";
        }
        $val46 .= "key=" . $val19["apikey"];
        $val26["sign"] = strtoupper(md5($val46));
        $val57         = array2xml($val26);
        load()->func("communication");
        $val82 = ihttp_post($val25, $val57);
        if (is_error($val82)) {
            return error(-2, $val82["message"]);
        }
        if (empty($val82["content"])) {
            return error(-2, "网络错误");
        } else {
            $val92 = json_decode(json_encode((array) simplexml_load_string($val82["content"])), true);
            $val57 = "<?xml version='1.0' encoding='utf-8'?>" . $val82["content"];
            $val96 = new \DOMDocument();
            if ($val96->loadXML($val57)) {
                $val98  = new \DOMXPath($val96);
                $val100 = $val98->evaluate("string(//xml/return_code)");
                $val102 = $val98->evaluate("string(//xml/result_code)");
                $val367 = $val98->evaluate("string(//xml/trade_state)");
                if (strtolower($val100) == "success" && strtolower($val102) == "success" && strtolower($val367) == "success") {
                    $val439 = intval($val98->evaluate("string(//xml/total_fee)")) / 100;
                    if ($val439 != $val2) {
                        return error(-1, "金额出错");
                    }
                    return true;
                } else {
                    if ($val98->evaluate("string(//xml/return_msg)") == $val98->evaluate("string(//xml/err_code_des)")) {
                        $val108 = $val98->evaluate("string(//xml/return_msg)");
                    } else {
                        $val108 = $val98->evaluate("string(//xml/return_msg)") . "<br/>" . $val98->evaluate("string(//xml/err_code_des)");
                    }
                    return error(-2, $val108);
                }
            } else {
                return error(-1, "未知错误");
            }
        }
    }
    function isAlipayNotify($val451)
    {
        global $_W;
        $val453 = trim($val451["notify_id"]);
        $val455 = trim($val451["sign"]);
        if (empty($val453) || empty($val455)) {
            return false;
        }
        $val15 = uni_setting($_W["uniacid"], array(
            "payment"
        ));
        if (!is_array($val15["payment"])) {
            return false;
        }
        $val462 = $val15["payment"]["alipay"];
        $val464 = array();
        foreach ($val451 as $val466 => $val467) {
            if (in_array($val466, array(
                "sign",
                "sign_type",
                "i",
                "m",
                "openid",
                "c",
                "do",
                "p",
                "op"
            )) || empty($val467)) {
                continue;
            }
            $val464[$val466] = $val467;
        }
        ksort($val464, SORT_STRING);
        $val46 = '';
        foreach ($val464 as $val48 => $val49) {
            $val46 .= "{$val48}={$val49}&";
        }
        $val46  = rtrim($val46, "&") . $val462["secret"];
        $val484 = strtolower(md5($val46));
        if ($val455 != $val484) {
            return false;
        }
        $val25 = "https://mapi.alipay.com/gateway.do?service=notify_verify&partner={$val462['partner']}&notify_id={$val453}";
        $val82 = @file_get_contents($val25);
        return preg_match("/true$/i", $val82);
    }
}
?>