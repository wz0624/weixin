<?php
include_once('CommonUtil.php');
include_once('SDKRuntimeException.class.php');
include_once('MD5SignUtil.php');
include_once('FlashHongBaoException.php');
class FlashHongBaoHelper
{
    var $parameters;
    var $apiclient_cert;
    var $apiclient_key;
    var $rootca;
    var $passkey;
    function __construct($cert, $key, $ca, $passkey = "")
    {
        $this->apiclient_cert = $cert;
        $this->apiclient_key  = $key;
        $this->rootca         = $ca;
        $this->passkey        = $passkey;
    }
    function setParameter($parameter, $parameterValue)
    {
        $this->parameters[CommonUtil::trimString($parameter)] = CommonUtil::trimString($parameterValue);
    }
    function getParameter($parameter)
    {
        return $this->parameters[$parameter];
    }
    protected function create_noncestr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str   = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    function check_sign_parameters()
    {
        if ($this->parameters["nonce_str"] == null || $this->parameters["mch_billno"] == null || $this->parameters["mch_id"] == null || $this->parameters["wxappid"] == null || $this->parameters["nick_name"] == null || $this->parameters["send_name"] == null || $this->parameters["re_openid"] == null || $this->parameters["total_amount"] == null || $this->parameters["total_num"] == null || $this->parameters["wishing"] == null || $this->parameters["client_ip"] == null || $this->parameters["act_name"] == null || $this->parameters["remark"] == null) {
            return false;
        }
        return true;
    }
    protected function get_sign()
    {
        try {
            if (null == $this->passkey || "" == $this->passkey) {
                throw new SDKRuntimeException("密钥不能为空!");
            }
            if ($this->check_sign_parameters() == false) {
                throw new SDKRuntimeException("生成签名参数缺失!");
            }
            $commonUtil = new CommonUtil();
            ksort($this->parameters);
            $unSignParaString = $commonUtil->formatQueryParaMap($this->parameters, false);
            $md5SignUtil      = new MD5SignUtil();
            $sign             = $md5SignUtil->sign($unSignParaString, $commonUtil->trimString($this->passkey));
            return $sign;
        }
        catch (SDKRuntimeException $e) {
            throw new FlashHongBaoException($e->errorMessage(), 10302);
        }
    }
    function create_hongbao_xml($retcode = 0, $reterrmsg = "ok")
    {
        try {
            $this->setParameter('sign', $this->get_sign());
            $xml = CommonUtil::arrayToXml($this->parameters);
            return $xml;
        }
        catch (SDKRuntimeException $e) {
            throw new FlashHongBaoException($e->errorMessage(), 10301);
        }
    }
    function curl_post_ssl($url, $vars, $second = 30, $aHeader = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSLCERT, ATTACHMENT_ROOT . $this->apiclient_cert);
        curl_setopt($ch, CURLOPT_SSLKEY, ATTACHMENT_ROOT . $this->apiclient_key);
        curl_setopt($ch, CURLOPT_CAINFO, ATTACHMENT_ROOT . $this->rootca);
        if (count($aHeader) >= 1) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        }
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
        $data = curl_exec($ch);
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            return false;
        }
    }
}
