<?php
/**
 * 易客优惠券模块微站定义
 *
 * @author stevezheng
 * @url http://www.yike1908.com/
 */
defined('IN_IA') or exit('Access Denied');

class Yike_activity_couponModuleSite extends WeModuleSite {
    function show_json($status = 1, $return = null)
    {
        $ret = array(
            'status' => $status
        );
        if ($return) {
            $ret['data'] = $return;
        }
        die(json_encode($ret));
    }

    function send() {

        global $_W,$_GPC;

        load()->classs('weixin.account');

        $accObj = WeixinAccount::create($_W ['uniacid']);

        $access_token = $accObj->fetch_token();

        $data = array('touser' => $_GPC['openid'], 'template_id' =>$_GPC['template_id'] ,'topcolor' => '#FF0000','data'=>$_GPC['data']);

        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $access_token;

        return ihttp_post($url, json_encode($data));
    }
}