<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
class Ewei_DShop_User{
    private $sessionid;
    public function __construct(){
        global $_W;
        $this -> sessionid = "__cookie_ewei_shop_201507200000_{$_W['uniacid']}";
    }
    function getOpenid(){
        $weizan_0 = $this -> getInfo(false, true);
        return $weizan_0['openid'];
    }
    function getPerOpenid(){
        global $_W, $_GPC;
        $weizan_1 = 24 * 3600 * 3;
        session_set_cookie_params($weizan_1);
        @session_start();
        $weizan_2 = "__cookie_ewei_shop_openid_{$_W['uniacid']}";
        $weizan_3 = base64_decode($_COOKIE[$weizan_2]);
        if (!empty($weizan_3)){
            return $weizan_3;
        }
        load() -> func('communication');
        $weizan_4 = $_W['account']['key'];
        $weizan_5 = $_W['account']['secret'];
        $weizan_6 = "";
        $weizan_7 = $_GPC['code'];
        $weizan_8 = $_W['siteroot'] . 'app/index.php?' . $_SERVER['QUERY_STRING'];
        if (empty($weizan_7)){
            $weizan_9 = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $weizan_4 . '&redirect_uri=' . urlencode($weizan_8) . '&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
            header('location: ' . $weizan_9);
            exit();
        }else{
            $weizan_10 = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $weizan_4 . '&secret=' . $weizan_5 . '&code=' . $weizan_7 . '&grant_type=authorization_code';
            $weizan_11 = ihttp_get($weizan_10);
            $weizan_12 = @json_decode($weizan_11['content'], true);
            if (!empty($weizan_12) && is_array($weizan_12) && $weizan_12['errmsg'] == 'invalid code'){
                $weizan_9 = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $weizan_4 . '&redirect_uri=' . urlencode($weizan_8) . '&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
                header('location: ' . $weizan_9);
                exit();
            }
            if (is_array($weizan_12) && !empty($weizan_12['openid'])){
                $weizan_6 = $weizan_12['access_token'];
                $weizan_3 = $weizan_12['openid'];
                setcookie($weizan_2, base64_encode($weizan_3));
            }else{
                $weizan_13 = explode('&', $_SERVER['QUERY_STRING']);
                $weizan_14 = array();
                foreach($weizan_13 as $weizan_15){
                    if(!strexists($weizan_15, 'code=') && !strexists($weizan_15, 'state=') && !strexists($weizan_15, 'from=') && !strexists($weizan_15, 'isappinstalled=')){
                        $weizan_14[] = $weizan_15;
                    }
                }
                $weizan_16 = $_W['siteroot'] . 'app/index.php?' . implode('&', $weizan_14);
                $weizan_9 = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $weizan_4 . '&redirect_uri=' . urlencode($weizan_16) . '&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
                header('location: ' . $weizan_9);
                exit;
            }
        }
        return $weizan_3;
    }
    function getInfo($weizan_17 = false, $weizan_18 = false){
        global $_W, $_GPC;
        $weizan_0 = array();
        if (EWEI_SHOP_DEBUG){
			$weizan_0 = array('openid' => 'o9NSQt-C6M33iau0Cz-cBpsGlBJI', 'nickname' => '微赞科技', 'headimgurl' => 'http://bbs.012wz.com/static/image/common/logo.png', 'province' => '广东', 'city' => '深圳');
        }else{
            load() -> model('mc');
            if (empty($_GPC['directopenid'])){
                $weizan_0 = mc_oauth_userinfo();
            }else{
                $weizan_0 = array('openid' => $this -> getPerOpenid());
            }
            $weizan_19 = true;
            if ($_W['container'] != 'wechat'){
                if($_GPC['do'] == 'order' && $_GPC['p'] == 'pay'){
                    $weizan_19 = false;
                }
                if($_GPC['do'] == 'member' && $_GPC['p'] == 'recharge'){
                    $weizan_19 = false;
                }
                if($_GPC['do'] == 'plugin' && $_GPC['p'] == 'article' && $_GPC['preview'] == '1'){
                    $weizan_19 = false;
                }
            }
            if(empty($weizan_0['openid']) && $weizan_19){
                die('<!DOCTYPE html>
                <html>
                    <head>
                        <meta name=\'viewport\' content=\'width=device-width, initial-scale=1, user-scalable=0\'>
                        <title>抱歉，出错了</title><meta charset=\'utf-8\'><meta name=\'viewport\' content=\'width=device-width, initial-scale=1, user-scalable=0\'><link rel=\'stylesheet\' type=\'text/css\' href=\'https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css\'>
                    </head>
                    <body>
                    <div class=\'page_msg\'><div class=\'inner\'><span class=\'msg_icon_wrp\'><i class=\'icon80_smile\'></i></span><div class=\'msg_content\'><h4>请在微信客户端打开链接</h4></div></div></div>
                    </body>
                </html>');
            }
        }
        if ($weizan_17){
            return urlencode(base64_encode(json_encode($weizan_0)));
        }
        return $weizan_0;
    }
    function oauth_info(){
        global $_W, $_GPC;
        if ($_W['container'] != 'wechat'){
            if($_GPC['do'] == 'order' && $_GPC['p'] == 'pay'){
                return array();
            }
            if($_GPC['do'] == 'member' && $_GPC['p'] == 'recharge'){
                return array();
            }
        }
        $weizan_1 = 24 * 3600 * 3;
        session_set_cookie_params($weizan_1);
        @session_start();
        $weizan_20 = "__cookie_ewei_shop_201507100000_{$_W['uniacid']}";
        $weizan_21 = json_decode(base64_decode($_SESSION[$weizan_20]), true);
        $weizan_3 = is_array($weizan_21) ? $weizan_21['openid'] : '';
        $weizan_22 = is_array($weizan_21) ? $weizan_21['openid'] : '';
        if (!empty($weizan_3)){
            return $weizan_21;
        }
        load() -> func('communication');
        $weizan_4 = $_W['account']['key'];
        $weizan_5 = $_W['account']['secret'];
        $weizan_6 = "";
        $weizan_7 = $_GPC['code'];
        $weizan_8 = $_W['siteroot'] . 'app/index.php?' . $_SERVER['QUERY_STRING'];
        if (empty($weizan_7)){
            $weizan_9 = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $weizan_4 . '&redirect_uri=' . urlencode($weizan_8) . '&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect';
            header('location: ' . $weizan_9);
            exit();
        }else{
            $weizan_10 = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $weizan_4 . '&secret=' . $weizan_5 . '&code=' . $weizan_7 . '&grant_type=authorization_code';
            $weizan_11 = ihttp_get($weizan_10);
            $weizan_12 = @json_decode($weizan_11['content'], true);
            if (!empty($weizan_12) && is_array($weizan_12) && $weizan_12['errmsg'] == 'invalid code'){
                $weizan_9 = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $weizan_4 . '&redirect_uri=' . urlencode($weizan_8) . '&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect';
                header('location: ' . $weizan_9);
                exit();
            }
            if (empty($weizan_12) || !is_array($weizan_12) || empty($weizan_12['access_token']) || empty($weizan_12['openid'])){
                die('获取token失败,请重新进入!');
            }else{
                $weizan_6 = $weizan_12['access_token'];
                $weizan_3 = $weizan_12['openid'];
            }
        }
        $weizan_23 = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $weizan_6 . '&openid=' . $weizan_3 . '&lang=zh_CN';
        $weizan_11 = ihttp_get($weizan_23);
        $weizan_0 = @json_decode($weizan_11['content'], true);
        if (isset($weizan_0['nickname'])){
            $_SESSION[$weizan_20] = base64_encode(json_encode($weizan_0));
            return $weizan_0;
        }else{
            die('获取用户信息失败，请重新进入!');
        }
    }
    function followed($weizan_3 = ''){
        global $_W;
        $weizan_24 = !empty($weizan_3);
        if ($weizan_24){
            $weizan_25 = pdo_fetch('select follow from ' . tablename('mc_mapping_fans') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':openid' => $weizan_3, ':uniacid' => $_W['uniacid']));
            $weizan_24 = $weizan_25['follow'] == 1;
        }
        return $weizan_24;
    }
}
