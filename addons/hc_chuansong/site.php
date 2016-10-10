<?php
defined('IN_IA') or exit('Access Denied');
class hc_chuansongModuleSite extends WeModuleSite
{
    public function __Web($f_name)
    {
        global $_W, $_GPC;
        $op = $_GPC['op'] ? $_GPC['op'] : 'display';
        include_once 'web/adv_' . strtolower(substr($f_name, 5)) . '.php';
    }
    public function doWebList()
    {
        $this->__Web(__FUNCTION__);
    }
	public function doWebHelp()
    {
        include $this->template('help');
    }
    public function __Mobile($f_name)
    {
        global $_W, $_GPC;
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($user_agent, 'MicroMessenger') === false) {
        }
        $op = $_GPC['op'] ? $_GPC['op'] : 'display';
        include_once 'mobile/adv_' . strtolower(substr($f_name, 8)) . '.php';
    }
    public function doMobileresutle()
    {
        $this->__Mobile(__FUNCTION__);
    }
    public function doMobileshare_detail()
    {
        $this->__Mobile(__FUNCTION__);
    }
    public function doMobileajax()
    {
        $this->__Mobile(__FUNCTION__);
    }
    public function doMobileIndex()
    {
        $this->__Mobile(__FUNCTION__);
    }
    public function doMobileshow()
    {
        $this->__Mobile(__FUNCTION__);
    }
    public function doMobileuserlist()
    {
        $this->__Mobile(__FUNCTION__);
    }
    public function doMobileover($str = '')
    {
        global $_W, $_GPC;
        include $this->template('adv_over');
    }
    public function getuserinfo($_from_user)
    {
        global $_W;
        if (empty($_from_user)) {
            return false;
        }
        load()->classs('weixin.account');
        $accObj       = WeixinAccount::create($_W['uniacid']);
        $access_token = $accObj->fetch_token();
        $ACCESS_TOKEN = $access_token;
        $OPENID       = $_W['openid'];
        $url          = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$ACCESS_TOKEN}&openid={$OPENID}&lang=zh_CN";
        $json         = ihttp_get($url);
        $userInfo     = @json_decode($json['content'], true);
        $uid          = pdo_fetchcolumn(" SELECT uid FROM " . tablename('mc_mapping_fans') . " WHERE openid='" . $_W['openid'] . "' AND uniacid='" . $_W['uniacid'] . "' ");
        $member       = pdo_fetch(" SELECT * FROM " . tablename('mc_members') . " WHERE uid='" . $uid . "' ");
        if (empty($member)) {
            $member = array();
        }
        $member['nickname'] = $userInfo['nickname'];
        $member['avatar']   = $userInfo['headimgurl'];
        if (empty($member)) {
            $member['uniacid'] = $_W['uniacid'];
            $member['uid']     = $uid;
            pdo_insert('mc_members', $member);
        } else {
            pdo_update('mc_members', $member, array(
                'uid' => $uid
            ));
        }
    }
    public function get_follow_info()
    {
        global $_W, $_GPC;
        load()->model('account');
        $account = uni_fetch();
        load()->classs('weixin.account');
        $token        = WeAccount::token();
        $ACCESS_TOKEN = $token;
        $OPENID       = $_W['openid'];
        $url          = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$ACCESS_TOKEN}&openid={$OPENID}&lang=zh_CN";
        $json         = ihttp_get($url);
        $userInfo     = @json_decode($json['content'], true);
        $uid          = pdo_fetchcolumn(" SELECT uid FROM " . tablename('mc_mapping_fans') . " WHERE openid='" . $_W['openid'] . "' AND uniacid='" . $_W['uniacid'] . "' ");
        $member       = pdo_fetch(" SELECT * FROM " . tablename('mc_members') . " WHERE uid='" . $uid . "' ");
        if (empty($member)) {
            $member = array();
        }
        $member['nickname'] = $userInfo['nickname'];
        $member['avatar']   = $userInfo['headimgurl'];
        if (empty($member)) {
            $member['uniacid'] = $_W['uniacid'];
            $member['uid']     = $uid;
            pdo_insert('mc_members', $member);
        } else {
            pdo_update('mc_members', $member, array(
                'uid' => $uid
            ));
        }
        return $userInfo;
    }
    public function add_member()
    {
        global $_W, $_GPC;
        $openid = $_W['openid'];
        $uid    = $_W['member']['uid'];
        if (!empty($openid) && empty($uid)) {
            $default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' . tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(
                ':uniacid' => $_W['uniacid']
            ));
            $row             = array(
                'uniacid' => $_W['uniacid'],
                'nickname' => $info['nickname'],
                'avatar' => $info['headimgurl'],
                'realname' => $info['nickname'],
                'groupid' => $default_groupid,
                'email' => random(32) . '@012wz.com',
                'salt' => random(8),
                'createtime' => time()
            );
            pdo_insert('mc_members', $row);
            $user['uid'] = pdo_insertid();
            $fan         = mc_fansinfo($_W['openid']);
            pdo_update('mc_mapping_fans', array(
                'uid' => $user['uid']
            ), array(
                'fanid' => $fan['fanid']
            ));
            _mc_login($user);
        }
    }
    public function account_weixin_token($account)
    {
        if (is_array($account['access_token']) && !empty($account['access_token']['token']) && !empty($account['access_token']['expire']) && $account['access_token']['expire'] > TIMESTAMP) {
            return $account['access_token']['token'];
        } else {
            if (empty($account['weid'])) {
                message('参数错误.');
            }
            if (empty($account['key']) || empty($account['secret'])) {
                message('请填写公众号的appid及appsecret, (需要你的号码为微信服务号)！', create_url('account/post', array(
                    'id' => $account['weid']
                )), 'error');
            }
            $url     = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$account['key']}&secret={$account['secret']}";
            $content = ihttp_get($url);
            if (empty($content)) {
                message('获取微信公众号授权失败, 请稍后重试！');
            }
            $token = @json_decode($content['content'], true);
            if (empty($token) || !is_array($token)) {
                message('获取微信公众号授权失败, 请稍后重试！ 公众平台返回原始数据为: <br />' . $token);
            }
            if (empty($token['access_token']) || empty($token['expires_in'])) {
                message('解析微信公众号授权失败, 请稍后重试！');
            }
            $record              = array();
            $record['token']     = $token['access_token'];
            $record['expire']    = TIMESTAMP + $token['expires_in'];
            $row                 = array();
            $row['access_token'] = iserializer($record);
            pdo_update('wechats', $row, array(
                'weid' => $account['weid']
            ));
            return $record['token'];
        }
    }
    public function getSignPackage($_weid = 0)
    {
        if ($_weid == 0) {
            global $_W, $_GPC;
            $account = $_W['account'];
        } else {
            $account                 = pdo_fetch('select weid,`key`,`secret`,access_token,jsapi_ticket from ' . tablename('wechats') . ' where weid=1');
            $account['access_token'] = iunserializer($account['access_token']);
            $account['jsapi_ticket'] = iunserializer($account['jsapi_ticket']);
        }
        if (empty($account['key']) || empty($account['secret'])) {
            message('请填写公众号的appid及appsecret, (需要你的号码为微信服务号)！', create_url('account/post', array(
                'id' => $account['weid']
            )), 'error');
        }
        $jsapiTicket = $this->account_weixin_jsapi_ticket($account);
        $url         = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $timestamp   = TIMESTAMP;
        $nonceStr    = strtolower(random(16));
        $string      = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature   = sha1($string);
        $signPackage = array(
            "appId" => $account['key'],
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }
    public function account_weixin_jsapi_ticket($account)
    {
        if (is_array($account['jsapi_ticket']) && !empty($account['jsapi_ticket']['ticket']) && !empty($account['jsapi_ticket']['expire']) && $account['jsapi_ticket']['expire'] > TIMESTAMP) {
            return $account['jsapi_ticket']['ticket'];
        } else {
            if (empty($account['weid'])) {
                message('参数错误.');
            }
            if (empty($account['key']) || empty($account['secret'])) {
                message('请填写公众号的appid及appsecret, (需要你的号码为微信服务号)！', create_url('account/post', array(
                    'id' => $account['weid']
                )), 'error');
            }
            $accessToken = $this->account_weixin_token($account);
            $url         = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $content     = ihttp_get($url);
            if (empty($content)) {
                message('获取微信公众号授权失败, 请稍后重试！');
            }
            $jsapi_ticket = @json_decode($content['content'], true);
            if (empty($jsapi_ticket) || !is_array($jsapi_ticket)) {
                message('获取微信公众号授权失败, 请稍后重试！ 公众平台返回原始数据为: <br />' . $token);
            }
            if (empty($jsapi_ticket['ticket']) || empty($jsapi_ticket['expires_in'])) {
                message('解析微信公众号授权失败, 请稍后重试！');
            }
            $record              = array();
            $record['ticket']    = $jsapi_ticket['ticket'];
            $record['expire']    = TIMESTAMP + $jsapi_ticket['expires_in'];
            $row                 = array();
            $row['jsapi_ticket'] = iserializer($record);
            pdo_update('wechats', $row, array(
                'weid' => $account['weid']
            ));
            return $record['ticket'];
        }
    }
    public function get_cookie_name()
    {
        global $_W;
        $item        = pdo_fetch('select * from ' . tablename('hc_chuansong_list') . ' where weid=:weid AND is_default=1', array(
            ':weid' => $_W['uniacid']
        ));
        $cookie_name = '6hc_chuansongmen' . $_W['weid'] . $item['id'];
        return $cookie_name;
    }
    public function check_follow()
    {
        global $_W, $_GPC;
        $openid = $_W['openid'];
        if (empty($openid)) {
            $url = $this->createMobileUrl('share_detail');
            header("location:$url");
        }
        return;
    }
}