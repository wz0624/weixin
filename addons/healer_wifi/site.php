<?php


defined('IN_IA') or exit('Access Denied');
class Healer_wifiModuleSite extends WeModuleSite
{
    public function doWebOpen()
    {
        global $_W, $_GPC;
        if ($_W["uniaccount"]["type"] != 3) {
            message("很抱歉，仅允许通过接入微信第三方平台使用该功能。", "", "error");
        }
        load()->func('cache');
        load()->func('cache.mysql');
        load()->func('communication');
        load()->classs('weixin.platform');
        $account_platform      = new WeiXinPlatform($_W["account"]);
        $component_accesstoken = $account_platform->getComponentAccesstoken();
        $authorizer            = cache_load("account:auth:accesstoken:" . $_W["account"]["key"]);
        $authorizer            = json_decode($authorizer, true);
        if (empty($authorizer) || $authorizer["expires_in"] < TIMESTAMP) {
            $response = ihttp_request("https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=" . $component_accesstoken, json_encode(array(
                "component_appid" => $_W["setting"]["platform"]["appid"],
                "authorizer_appid" => $_W["account"]["key"],
                "authorizer_refresh_token" => $_W["account"]["auth_refresh_token"]
            )));
            if (is_error($response)) {
                message("缺少接入平台关键数据，等待微信开放平台推送数据，请十分钟后再试或是检查“授权事件接收URL”是否写错（index.php?c=account&amp;a=auth&amp;do=ticket地址中的&amp;符号容易被替换成&amp;amp;）", "", "error");
            }
            $authorizer = json_decode($response["content"], true);
            if ($authorizer["authorizer_refresh_token"] != $_W["account"]["auth_refresh_token"]) {
                $account_platform->setAuthRefreshToken($authorizer['authorizer_refresh_token']);
            }
            $authorizer["expires_in"] = TIMESTAMP + $authorizer["expires_in"] - 200;
            cache_write('account:auth:accesstoken:' . $_W["account"]["key"], $authorizer);
        }
        if (checksubmit()) {
            $data     = array(
                "callback_url" => trim($_GPC["callback_url"])
            );
            $response = ihttp_request("https://api.weixin.qq.com/bizwifi/openplugin/token?access_token=" . $authorizer["authorizer_access_token"], json_encode($data));
            $response = json_decode($response["content"], ture);
            if (empty($response)) {
                message("查询失败，请稍后再试试！", "", "error");
            } else {
                if ($response["data"]["is_open"]) {
                    message("恭喜您，您已开通微信连WIFI功能！", referer(), "success");
                } else {
                    message("抱歉您还未开通微信连WIFI功能，<a href='https://wifi.weixin.qq.com/biz/mp/thirdProviderPlugin.xhtml?token=" . $response["data"]["wifi_token"] . "'><b> 请点击这里开通 </b></a>。", "", "error");
                }
            }
        }
        include $this->template("open");
    }
    public function doWebShop()
    {
        message("该功能还在开发中！", "", "error");
    }
    public function doWebDevice()
    {
        message("该功能还在开发中！", "", "error");
    }
    public function doWebBizwifi()
    {
        message("该功能还在开发中！", "", "error");
    }
    public function doWebHomepage()
    {
        message("该功能还在开发中！", "", "error");
    }
    public function doWebStatistics()
    {
        message("该功能还在开发中！", "", "error");
    }
}