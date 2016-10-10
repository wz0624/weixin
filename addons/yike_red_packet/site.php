<?php
defined('IN_IA') or exit('Access Denied');
require IA_ROOT . '/addons/yike_red_packet/yike/functions.php';
class Yike_red_packetModuleSite extends WeModuleSite
{
    public function __construct()
    {
        $this->tplname = 'yike';
    }
    public function getInfo($base64 = false, $debug = true, $debug_id = '1')
    {
        global $_W, $_GPC;
        if ($debug) {
            load()->model('mc');
            $userinfo = mc_fetch($debug_id);
        } else {
            load()->model('mc');
            $userinfo    = mc_oauth_userinfo();
            $need_openid = true;
            if ($_W['container'] != 'wechat') {
                if ($_GPC['do'] == 'order' && $_GPC['p'] == 'pay') {
                    $need_openid = false;
                }
                if ($_GPC['do'] == 'member' && $_GPC['p'] == 'recharge') {
                    $need_openid = false;
                }
            }
            if (empty($userinfo['openid']) && $need_openid) {
                die("<!DOCTYPE html>
                <html>
                    <head>
                        <meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'>
                        <title>抱歉，出错了</title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'><link rel='stylesheet' type='text/css' href='https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css'>
                    </head>
                    <body>
                    <div class='page_msg'><div class='inner'><span class='msg_icon_wrp'><i class='icon80_smile'></i></span><div class='msg_content'><h4>请在微信客户端打开链接</h4></div></div></div>
                    </body>
                </html>");
            }
            $userinfo['uid'] = $_W['member']['uid'];
        }
        if ($base64) {
            return urlencode(base64_encode(json_encode($userinfo)));
        }
        return $userinfo;
    }
    public function doMobilePay()
    {
        global $_W, $_GPC;
        $info     = $this->getInfo();
        $fee      = floatval($_GPC['money']);
        $level    = intval($_GPC['level']);
        $realname = $_GPC['realname'];
        $wx       = $_GPC['wx'];
        $mobile   = $_GPC['mobile'];
        $agent_id = $_GPC['mid'];
        if ($fee <= 0) {
            message('支付错误, 金额小于0');
        }
        $old_user = pdo_get('yike_red_packet_user', array(
            'uniacid' => $_W['uniacid'],
            'uid' => $info['uid']
        ));
        if (!$old_user) {
            $agent       = pdo_get('yike_red_packet_user', array(
                'uniacid' => $_W['uniacid'],
                'uid' => $agent_id
            ));
            $user        = array(
                'uniacid' => $_W['uniacid'],
                'uid' => $info['uid'],
                'level1' => $agent_id,
                'level2' => $agent['level1'],
                'level3' => $agent['level2'],
                'wx' => $wx,
                'openid' => $_W['openid'],
                'realname' => $realname,
                'mobile' => $mobile,
                'created_time' => time(),
                'status' => 1,
                'is_inviter' => 0,
                'click_count' => 0,
                'money' => 0,
                'point' => 0
            );
            $user_result = pdo_insert('yike_red_packet_user', $user);
            if (empty($user_result)) {
                message('出错了, 用户数据创建失败');
            }
        } else {
            if ($old_user['inviter_level'] != '0') {
                $result     = pdo_getall('yike_red_packet_level', array(
                    'uniacid' => $_W['uniacid']
                ));
                $level_list = array();
                foreach ($result as $item) {
                    $level_list[$item['level']] = $item['money'];
                }
                unset($item);
                $now_level    = $old_user['inviter_level'];
                $now_price    = $level_list[$now_level];
                $target_level = $level;
                $target_price = $level_list[$target_level];
                if (floatval($target_price) - floatval($now_price) != $fee) {
                    message('出错了,请检查升级所需金额');
                }
            }
        }
        $data   = array(
            'uniacid' => $_W['uniacid'],
            'uid' => $info['uid'],
            'money' => $fee,
            'status' => 0,
            'created_time' => time(),
            'remark' => '',
            'type' => $level
        );
        $result = pdo_insert('yike_red_packet_recharge', $data);
        if (!empty($result)) {
            $id     = pdo_insertid();
            $params = array(
                'tid' => $id,
                'ordersn' => $id,
                'title' => '升级VIP',
                'fee' => $fee,
                'user' => $info['uid']
            );
            $this->pay($params);
        } else {
            message('出错了');
        }
    }
    public function payResult($params)
    {
        if ($params['result'] == 'success' && $params['from'] == 'notify') {
            $uniontid = $params['uniontid'];
            $order    = pdo_get('core_paylog', array(
                'uniontid' => $uniontid
            ));
            $id       = $order['tid'];
            if (floatval($order['fee']) != floatval($params['fee'])) {
                message('请检查支付金额');
                return false;
            }
            $result  = pdo_get('yike_red_packet_recharge', array(
                'id' => $id
            ));
            $uid     = $result['uid'];
            $uniacid = $result['uniacid'];
            $level   = $result['type'];
            $result1 = pdo_update('yike_red_packet_recharge', array(
                'status' => 1
            ), array(
                'id' => $id
            ));
            $result2 = pdo_update('yike_red_packet_user', array(
                'inviter_level' => $level
            ), array(
                'uid' => $uid,
                'uniacid' => $uniacid
            ));
            $user    = pdo_get('yike_red_packet_user', array(
                'uid' => $uid,
                'uniacid' => $uniacid
            ));
            $this->payback($user);
            if ($this->tplname == 'huiyigou') {
                $isagent = pdo_update('ewei_shop_member', array(
                    'isagent' => 1,
                    'status' => 1
                ), array(
                    'uid' => $uid,
                    'uniacid' => $uniacid
                ));
            } else {
                $level1 = pdo_get('yike_red_packet_user', array(
                    'uid' => $user['level1'],
                    'uniacid' => $uniacid
                ));
                $openid = $level1['openid'];
                $msg    = '您的粉丝:[' . $user['id'] . ':' . $user['realname'] . '],已成功成为您的下级!';
                m('notice')->sendCustomNotice($openid, $msg);
            }
            message('升级成功');
        }
        if (empty($params['result']) || $params['result'] != 'success') {
            load()->func('logging');
            logging_run($params);
            message('升级失败');
        }
        if ($params['from'] == 'return') {
            $url      = $this->createMobileUrl('home', array());
            $uniontid = $params['uniontid'];
            $order    = pdo_get('core_paylog', array(
                'uniontid' => $uniontid
            ));
            if (floatval($order['fee']) != floatval($params['fee'])) {
                message('请检查支付金额', $url, 'error');
                return false;
            }
            if ($params['result'] == 'success') {
                message('支付成功！', $url, 'success');
            } else {
                message('支付失败！', $url, 'error');
            }
        }
    }
    public function show_json($status = 1, $return = null)
    {
        $ret = array(
            'status' => $status
        );
        if ($return) {
            $ret['result'] = $return;
        }
        die(json_encode($ret));
    }
    public function oauth_info()
    {
        global $_W, $_GPC;
        if ($_W['container'] != 'wechat') {
            if ($_GPC['do'] == 'order' && $_GPC['p'] == 'pay') {
                return array();
            }
            if ($_GPC['do'] == 'member' && $_GPC['p'] == 'recharge') {
                return array();
            }
        }
        $lifeTime = 24 * 3600 * 3;
        session_set_cookie_params($lifeTime);
        @session_start();
        $sessionid = "__cookie_yike_201506080000_{$_W['uniacid']}";
        $session   = json_decode(base64_decode($_SESSION[$sessionid]), true);
        $openid    = is_array($session) ? $session['openid'] : '';
        $nickname  = is_array($session) ? $session['openid'] : '';
        if (!empty($openid)) {
            return $session;
        }
        load()->func('communication');
        $appId        = $_W['account']['key'];
        $appSecret    = $_W['account']['secret'];
        $access_token = "";
        $code         = $_GPC['code'];
        $url          = $_W['siteroot'] . 'app/index.php?' . $_SERVER['QUERY_STRING'];
        if (empty($code)) {
            $authurl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appId . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";
            header('location: ' . $authurl);
            exit();
        } else {
            $tokenurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appId . "&secret=" . $appSecret . "&code=" . $code . "&grant_type=authorization_code";
            $resp     = ihttp_get($tokenurl);
            $token    = @json_decode($resp['content'], true);
            if (!empty($token) && is_array($token) && $token['errmsg'] == 'invalid code') {
                $authurl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appId . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";
                header('location: ' . $authurl);
                exit();
            }
            if (empty($token) || !is_array($token) || empty($token['access_token']) || empty($token['openid'])) {
                die('获取token失败,请重新进入!');
            } else {
                $access_token = $token['access_token'];
                $openid       = $token['openid'];
            }
        }
        $infourl  = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid . "&lang=zh_CN";
        $resp     = ihttp_get($infourl);
        $userinfo = @json_decode($resp['content'], true);
        if (isset($userinfo['nickname'])) {
            $_SESSION[$sessionid] = base64_encode(json_encode($userinfo));
            return $userinfo;
        } else {
            die('获取用户信息失败，请重新进入!');
        }
    }
    public function followed($openid = '')
    {
        global $_W;
        $followed = !empty($openid);
        if ($followed) {
            $mf       = pdo_fetch("select follow from " . tablename('mc_mapping_fans') . " where openid=:openid and uniacid=:uniacid limit 1", array(
                ":openid" => $openid,
                ':uniacid' => $_W['uniacid']
            ));
            $followed = $mf['follow'] == 1;
        }
        return $followed;
    }
    public function getRealData($data)
    {
        $data['left']   = intval(str_replace('px', '', $data['left'])) * 2;
        $data['top']    = intval(str_replace('px', '', $data['top'])) * 2;
        $data['width']  = intval(str_replace('px', '', $data['width'])) * 2;
        $data['height'] = intval(str_replace('px', '', $data['height'])) * 2;
        $data['size']   = intval(str_replace('px', '', $data['size'])) * 2;
        $data['src']    = tomedia($data['src']);
        return $data;
    }
    public function createImage($imgurl)
    {
        load()->func('communication');
        $resp = ihttp_request($imgurl);
        return imagecreatefromstring($resp['content']);
    }
    public function mergeImage($target, $data, $imgurl)
    {
        $img = $this->createImage($imgurl);
        $w   = imagesx($img);
        $h   = imagesy($img);
        imagecopyresized($target, $img, $data['left'], $data['top'], 0, 0, $data['width'], $data['height'], $w, $h);
        imagedestroy($img);
        return $target;
    }
    public function mergeText($target, $data, $text)
    {
        $font   = IA_ROOT . "/addons/yike_red_packet/static/fonts/msyh.ttf";
        $colors = $this->hex2rgb($data['color']);
        $color  = imagecolorallocate($target, $colors['red'], $colors['green'], $colors['blue']);
        imagettftext($target, $data['size'], 0, $data['left'], $data['top'] + $data['size'], $color, $font, $text);
        return $target;
    }
    function hex2rgb($colour)
    {
        if ($colour[0] == '#') {
            $colour = substr($colour, 1);
        }
        if (strlen($colour) == 6) {
            list($r, $g, $b) = array(
                $colour[0] . $colour[1],
                $colour[2] . $colour[3],
                $colour[4] . $colour[5]
            );
        } elseif (strlen($colour) == 3) {
            list($r, $g, $b) = array(
                $colour[0] . $colour[0],
                $colour[1] . $colour[1],
                $colour[2] . $colour[2]
            );
        } else {
            return false;
        }
        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);
        return array(
            'red' => $r,
            'green' => $g,
            'blue' => $b
        );
    }
    public function createPoster($poster, $member, $qr, $upload = true)
    {
        global $_W;
        $path = IA_ROOT . "/addons/yike_red_packet/data/poster/" . $_W['uniacid'] . "/";
        if (!is_dir($path)) {
            load()->func('file');
            mkdirs($path);
        }
        $md5  = md5(json_encode(array(
            'openid' => $member['openid'],
            'data' => $poster['data'],
            'version' => 1
        )));
        $file = $md5 . '.jpg';
        if (!is_file($path . $file)) {
            set_time_limit(0);
            @ini_set('memory_limit', '256M');
            $target = imagecreatetruecolor(640, 1008);
            $bg     = $this->createImage(tomedia($poster['bg']));
            imagecopy($target, $bg, 0, 0, 0, 0, 640, 1008);
            imagedestroy($bg);
            $data = json_decode(str_replace('&quot;', "'", $poster['data']), true);
            foreach ($data as $d) {
                $d = $this->getRealData($d);
                if ($d['type'] == 'head') {
                    $avatar = preg_replace('/\/0$/i', '/96', $member['avatar']);
                    $target = $this->mergeImage($target, $d, $avatar);
                } else if ($d['type'] == 'img') {
                    $target = $this->mergeImage($target, $d, $d['src']);
                } else if ($d['type'] == 'qr') {
                    $target = $this->mergeImage($target, $d, $qr);
                } else if ($d['type'] == 'nickname') {
                    $target = $this->mergeText($target, $d, $member['nickname']);
                } else if ($d['type'] == 'word') {
                    $target = $this->mergeText($target, $d, '我为世界代言');
                }
            }
            imagejpeg($target, $path . $file);
            imagedestroy($target);
        }
        $img = $_W['siteroot'] . "addons/yike_red_packet/data/poster/" . $_W['uniacid'] . "/" . $file;
        if (!$upload) {
            return $img;
        }
        if ($qr['qrimg'] != $qr['current_qrimg'] || empty($qr['mediaid']) || empty($qr['createtime']) || $qr['createtime'] + 3600 * 24 * 3 - 7200 < time()) {
            $mediaid       = $this->uploadImage($path . $file);
            $qr['mediaid'] = $mediaid;
            pdo_update('ewei_shop_poster_qr', array(
                'mediaid' => $mediaid,
                'createtime' => time()
            ), array(
                'id' => $qr['id']
            ));
        }
        return array(
            'img' => $img,
            'mediaid' => $qr['mediaid']
        );
    }
    function payback($user)
    {
        global $_W;
        $settings   = pdo_getall('yike_red_packet_level', array(
            'uniacid' => $_W['uniacid']
        ));
        $user_level = $user['inviter_level'];
        $level1     = $user['level1'];
        if ($level1 != '0') {
            $user1          = pdo_get('yike_red_packet_user', array(
                'uniacid' => $_W['uniacid'],
                'uid' => $user['level1']
            ));
            $count1         = pdo_fetchcolumn('select count(*) from ' . tablename('yike_red_packet_user') . ' where uniacid=:uniacid and level1=:uid', array(
                ':uniacid' => $_W['uniacid'],
                ':uid' => $user['level1']
            ));
            $index1         = intval($user1['inviter_level']) - 1;
            $level1_setting = $settings[$index1];
            if ($user_level > 0 && ($count1 < $level1_setting['level1_count'] || $level1_setting['level1_count'] == -1) && $user1['inviter_level'] > 0) {
                $money1   = $level1_setting['level1_money'];
                $result1  = pdo_query('update ' . tablename('yike_red_packet_user') . ' set money=money + :money where uniacid=:uniacid and id = :id', array(
                    ':uniacid' => $_W['uniacid'],
                    ':id' => $user1['id'],
                    ':money' => floatval($money1)
                ));
                $_result1 = pdo_insert('yike_red_packet_rebates', array(
                    'uniacid' => $_W['uniacid'],
                    'uid' => $user1['uid'],
                    'money' => floatval($money1),
                    'status' => 1,
                    'created_time' => time(),
                    'remark' => '',
                    'level' => 1
                ));
            }
        }
        $level2 = $user['level2'];
        if ($level2 != '0') {
            $user2          = pdo_get('yike_red_packet_user', array(
                'uniacid' => $_W['uniacid'],
                'uid' => $user['level2']
            ));
            $count2         = pdo_fetchcolumn('select count(*) from ' . tablename('yike_red_packet_user') . ' where uniacid=:uniacid and level2=:uid', array(
                ':uniacid' => $_W['uniacid'],
                ':uid' => $user['level1']
            ));
            $index2         = intval($user2['inviter_level']) - 1;
            $level2_setting = $settings[$index2];
            if ($user_level > 1 && ($count2 < $level2_setting['level2_count'] || $level2_setting['level2_count'] == -1) && $user2['inviter_level'] > 0) {
                $money2   = $level2_setting['level2_money'];
                $result2  = pdo_query('update ' . tablename('yike_red_packet_user') . ' set money=money + :money where uniacid=:uniacid and id = :id', array(
                    ':uniacid' => $_W['uniacid'],
                    ':id' => $user2['id'],
                    ':money' => floatval($money2)
                ));
                $_result2 = pdo_insert('yike_red_packet_rebates', array(
                    'uniacid' => $_W['uniacid'],
                    'uid' => $user2['uid'],
                    'money' => floatval($money2),
                    'status' => 1,
                    'created_time' => time(),
                    'remark' => '',
                    'level' => 2
                ));
            }
        }
        $level3 = $user['level3'];
        if ($level3 != '0') {
            $user3          = pdo_get('yike_red_packet_user', array(
                'uniacid' => $_W['uniacid'],
                'uid' => $user['level3']
            ));
            $count3         = pdo_fetchcolumn('select count(*) from ' . tablename('yike_red_packet_user') . ' where uniacid=:uniacid and level3=:uid', array(
                ':uniacid' => $_W['uniacid'],
                ':uid' => $user['level1']
            ));
            $index3         = intval($user3['inviter_level']) - 1;
            $level3_setting = $settings[$index3];
            if ($user_level > 2 && ($count3 < $level3_setting['level3_count'] || $level3_setting['level3_count'] == -1) && $user3['inviter_level'] > 0) {
                $money3   = $level3_setting['level3_money'];
                $result3  = pdo_query('update ' . tablename('yike_red_packet_user') . ' set money=money + :money where uniacid=:uniacid and id = :id', array(
                    ':uniacid' => $_W['uniacid'],
                    ':id' => $user3['id'],
                    ':money' => floatval($money3)
                ));
                $_result3 = pdo_insert('yike_red_packet_rebates', array(
                    'uniacid' => $_W['uniacid'],
                    'uid' => $user3['uid'],
                    'money' => floatval($money3),
                    'status' => 1,
                    'created_time' => time(),
                    'remark' => '',
                    'level' => 3
                ));
            }
        }
        return true;
    }
    function send($rootca, $key, $cert, $id = 10, $openid = 'oL5Tft0KFMTADBLnBUAL3xOmLCaA', $money = 1.1, $nickname = 'steve', $wishing = '恭喜发财', $remark = '中奖咯')
    {
        global $_W;
        $setting               = uni_setting($_W['uniacid'], array(
            'payment'
        ));
        $wechat                = $setting['payment']['wechat'];
        $fee                   = floatval($money) * 100;
        $url                   = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        $pars                  = array();
        $pars['nonce_str']     = random(32);
        $pars['mch_billno']    = date('Ymd') . sprintf('%010d', $id);
        $pars['mch_id']        = $wechat['mchid'];
        $pars['wxappid']       = $_W['account']['key'];
        $pars['nick_name']     = $nickname;
        $pars['send_name']     = $nickname;
        $pars['re_openid']     = $openid;
        $pars['total_amount']  = $fee;
        $pars['min_value']     = $pars['total_amount'];
        $pars['max_value']     = $pars['total_amount'];
        $pars['total_num']     = 1;
        $pars['wishing']       = $wishing;
        $pars['client_ip']     = gethostbyname($_SERVER["HTTP_HOST"]);
        $pars['act_name']      = '现金红包';
        $pars['remark']        = $remark;
        $pars['logo_imgurl']   = 'http://weixin.yike1908.com/attachment/headimg_3.jpg?acid=3';
        $pars['share_content'] = '谢谢分享';
        $pars['share_imgurl']  = 'http://weixin.yike1908.com/attachment/headimg_3.jpg?acid=3';
        $pars['share_url']     = 'http://baidu.com';
        ksort($pars, SORT_STRING);
        $string1 = '';
        foreach ($pars as $k => $v) {
            $string1 .= "{$k}={$v}&";
        }
        $string1 .= "key={$wechat['apikey']}";
        $pars['sign'] = strtoupper(md5($string1));
        $xml          = array2xml($pars);
        $extras       = array();
        $certfile     = IA_ROOT . "/addons/yike_red_packet/cert/" . random(128);
        file_put_contents($certfile, $cert);
        $keyfile = IA_ROOT . "/addons/yike_red_packet/cert/" . random(128);
        file_put_contents($keyfile, $key);
        $rootfile = IA_ROOT . "/addons/yike_red_packet/cert/" . random(128);
        file_put_contents($rootfile, $rootca);
        $extras['CURLOPT_SSLCERT'] = $certfile;
        $extras['CURLOPT_SSLKEY']  = $keyfile;
        $extras['CURLOPT_CAINFO']  = $rootfile;
        load()->func('communication');
        $procResult = null;
        $resp       = ihttp_request($url, $xml, $extras);
        if (is_error($resp)) {
            $procResult = $resp;
        } else {
            $xml = '<?xml version="1.0" encoding="utf-8"?>' . $resp['content'];
            $dom = new \DOMDocument();
            if ($dom->loadXML($xml)) {
                $xpath = new \DOMXPath($dom);
                $code  = $xpath->evaluate('string(//xml/return_code)');
                $ret   = $xpath->evaluate('string(//xml/result_code)');
                if (strtolower($code) == 'success' && strtolower($ret) == 'success') {
                    $procResult = true;
                } else {
                    $error      = $xpath->evaluate('string(//xml/err_code_des)');
                    $procResult = error(-2, $error);
                }
            } else {
                $procResult = error(-1, 'error response');
            }
        }
        return $procResult;
    }
}