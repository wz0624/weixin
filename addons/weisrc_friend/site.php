<?php
/**
 * 火爆朋友圈
 *
 * 作者:迷失卍国度
 *
 * qq : 15595755
 */
defined('IN_IA') or exit('Access Denied');
include "model.php";
define('RES', '../addons/weisrc_friend/template/');

class weisrc_friendModuleSite extends WeModuleSite
{
    public $_appid = '';
    public $_appsecret = '';
    public $_accountlevel = '';
    public $_account = '';

    public $_weid = '';
    public $_fromuser = '';
    public $_nickname = '';
    public $_headimgurl = '';
    public $_activeid = 0;

    public $_auth2_openid = '';
    public $_auth2_nickname = '';
    public $_auth2_headimgurl = '';
    public $_active = '';

    public $table_reply = 'weisrc_friend_reply';
    public $table_fans = 'weisrc_friend_fans';

    function __construct()
    {
        global $_W, $_GPC;
        $this->_weid = $_W['uniacid'];
        $this->_fromuser = $_W['fans']['from_user']; //debug
        if ($_SERVER['HTTP_HOST'] == '127.0.0.1:8888' || $_SERVER['HTTP_HOST'] == '192.168.1.102:8888') {
            $this->_fromuser = 'debug';
        }

        $this->_auth2_openid = 'auth2_openid_' . $_W['uniacid'];
        $this->_auth2_nickname = 'auth2_nickname_' . $_W['uniacid'];
        $this->_auth2_headimgurl = 'auth2_headimgurl_' . $_W['uniacid'];
        $this->_active = 'active_' . $_W['uniacid'];

        $this->_appid = '';
        $this->_appsecret = '';
        $this->_accountlevel = $_W['account']['level']; //是否为高级号

        if (isset($_COOKIE[$this->_auth2_openid])) {
            $this->_fromuser = $_COOKIE[$this->_auth2_openid];
        }

        if ($this->_accountlevel < 4) {
            $setting = uni_setting($this->_weid);
            $oauth = $setting['oauth'];
            if (!empty($oauth) && !empty($oauth['account'])) {
                $this->_account = account_fetch($oauth['account']);
                $this->_appid = $this->_account['key'];
                $this->_appsecret = $this->_account['secret'];
            }
        } else {
            $this->_appid = $_W['account']['key'];
            $this->_appsecret = $_W['account']['secret'];
        }
    }

    public function doMobileindex()
    {
        global $_GPC, $_W;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $tofrom_user = trim($_GPC['tofrom_user']);

        $id = intval($_GPC['id']);

        if (empty($id)) {
            if (isset($_COOKIE[$this->_active])) {
                $id = $_COOKIE[$this->_active];
            } else {
                message('抱歉，参数错误！', '', 'error');
            }
        } else {
            setcookie($this->_active, $id, time() + 3600 * 24);
        }

        if (empty($tofrom_user)) {
            $method = 'index';
            $authurl = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array('id' => $id), true) . '&authkey=1';
            $url = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array('id' => $id), true);
//            if (isset($_COOKIE[$this->_auth2_openid])) {
//                $from_user = $_COOKIE[$this->_auth2_openid];
//                $nickname = $_COOKIE[$this->_auth2_nickname];
//                $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
//                $sex = $_COOKIE[$this->_auth2_sex];
//
//            } else {
            $headimgurl = '';
            $nickname = '';
                if (isset($_GPC['code'])) {
                    $userinfo = $this->oauth2($authurl);
                    if (!empty($userinfo)) {
                        $from_user = $userinfo["openid"];
                        $nickname = $userinfo["nickname"];
                        $headimgurl = $userinfo["headimgurl"];
                        $sex = $userinfo["sex"];
                    } else {
                        message('授权失败!');
                    }
                } else {
                    if (!empty($this->_appsecret)) {
                        $this->getCode($url);
                    }
                }
//            }

            $fans = pdo_fetch("SELECT * FROM " . tablename($this->table_fans) . " WHERE from_user=:from_user AND rid=:rid LIMIT 1", array(':from_user' => $from_user, ':rid' => $id));
            if (empty($fans)) {
                $data = array(
                    'rid' => $id,
                    'weid' => $weid,
                    'from_user' => $from_user,
                    'nickname' => $nickname,
                    'headimgurl' => $headimgurl,
                    'sex' => $sex == 1 ? 1 : 2,
                    'number' => rand(1,4),
                    'dateline' => TIMESTAMP
                );
                if (!empty($from_user)) {
                    pdo_insert($this->table_fans, $data);
                }
            } else {
                pdo_update($this->table_fans, array('nickname' => $nickname, 'headimgurl' => $headimgurl, 'sex' => $sex), array('id' => $fans['id']));
            }
            $fans = pdo_fetch("SELECT * FROM " . tablename($this->table_fans) . " WHERE from_user=:from_user AND rid=:rid LIMIT 1", array(':from_user' => $from_user, ':rid' => $id));
        } else {
            $fans = pdo_fetch("SELECT * FROM " . tablename($this->table_fans) . " WHERE from_user=:from_user AND rid=:rid LIMIT 1", array(':from_user' => $tofrom_user, ':rid' => $id));
        }

        $number = $fans['number'];
        $openid = $fans['from_user'];
        $sex = $fans['sex'] == 1? '男' : '女';
        $headimgurl = tomedia($fans['headimgurl']);
        $nickname = $fans['nickname'];

        $reply = pdo_fetch("SELECT * FROM " . tablename($this->table_reply) . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $id));
        if ($reply == false) {
            message('抱歉，活动不存在！', '', 'error');
        } else {
            if ($reply['starttime'] > TIMESTAMP) {
                message('活动未开始，请等待...', $this->createMobileUrl('rank', array('id' => $id), true), 'error');
            }
            if ($reply['endtime'] < TIMESTAMP) {
                message('抱歉，活动已经结束，下次再来吧！', $this->createMobileUrl('rank', array('id' => $id), true), 'error');
            }
            if ($reply['status'] == 0) {
                message('活动暂停，请稍后...', $this->createMobileUrl('rank', array('id' => $id), true), 'error');
            }
            pdo_update($this->table_reply, array('view' => $reply['view'] + 1), array('id' => $reply['id']));
        }

        $follow_url = $reply['follow_url'];
        $sub = 0;
        if ($this->_accountlevel == 4) {
            $userinfo = $this->getUserInfo($from_user);
            if ($userinfo['subscribe'] == 1) {
                $sub = 1;
            }
        } else {
            if ($_W['fans']['follow'] == 1) {
                $sub = 1;
            }
        }
        if ($sub == 0) {
            if (empty($follow_url)) {
                $follow_url = $this->createMobileUrl('index', array('id' => $id), true);
            }
        } else {
            $follow_url = $this->createMobileUrl('index', array('id' => $id), true);
        }

        $bg = tomedia($reply['bg']);
        $logo = tomedia($reply['logo']);
        $qrcode = tomedia($reply['qrcode']);
        $ad_url = $reply['ad_url'];
        $ad_nickname = $reply['nickname'];
        $ad_desc = $reply['desc'];

        //分享信息
        $share_url = $_W['siteroot'] . 'app/' . $this->createMobileUrl('index', array('id' => $id, 'tofrom_user' => $openid), true);
        $share_title = $nickname.'的朋友圈，您怕了吗？';
        $share_desc = '快来膜拜火爆朋友圈！';
        $share_image = $headimgurl;
        include $this->template('index');
    }

    public function getItemTiles()
    {
        global $_W;
        $articles = pdo_fetchall("SELECT * FROM " . tablename('weisrc_friend_reply') . " WHERE weid = '{$_W['uniacid']}'");
        if (!empty($articles)) {
            foreach ($articles as $row) {
                $urls[] = array('title' => $row['title'], 'url' => $this->createMobileUrl('index', array('id' => $row['rid']), true));
            }
            return $urls;
        }
    }

    public function doMobileShare()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $id = intval($_GPC['id']);

        $reply = pdo_fetch("SELECT * FROM " . tablename($this->table_reply) . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $id));
        if (!empty($reply)) {
            pdo_update($this->table_reply, array('sharecount' => $reply['sharecount'] + 1), array('id' => $reply['id']));
        }
    }

    public function doWebManage() {
        global $_GPC, $_W;
        load()->model('reply');
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $sql = "uniacid = :weid AND `module` = :module";
        $params = array();
        $params[':weid'] = $_W['uniacid'];
        $params[':module'] = 'weisrc_friend';

        if (isset($_GPC['keywords'])) {
            $sql .= ' AND `name` LIKE :keywords';
            $params[':keywords'] = "%{$_GPC['keywords']}%";
        }

        $list = reply_search($sql, $params, $pindex, $psize, $total);
        $pager = pagination($total, $pindex, $psize);

        if (!empty($list)) {
            foreach ($list as &$item) {
                $condition = "`rid`={$item['id']}";
                $item['keywords'] = reply_keywords_search($condition);
                $weisrc_friend = pdo_fetch("SELECT * FROM " . tablename($this->table_reply) . " WHERE rid = :rid ", array(':rid' => $item['id']));
                $item['viewnum'] = $item['viewnum`'];
                $item['starttime'] = date('Y-m-d H:i', $weisrc_friend['starttime']);
                $endtime = $weisrc_friend['endtime'];
                $item['endtime'] = date('Y-m-d H:i', $endtime);
                $nowtime = time();
                if ($weisrc_friend['starttime'] > $nowtime) {
                    $item['show'] = '<span class="label label-warning">未开始</span>';
                } elseif ($endtime < $nowtime) {
                    $item['show'] = '<span class="label label-default">已结束</span>';
                } else {
                    if ($weisrc_friend['status'] == 1) {
                        $item['show'] = '<span class="label label-success">已开始</span>';
                    } else {
                        $item['show'] = '<span class="label label-default">已暂停</span>';
                    }
                }
                $item['status'] = $weisrc_friend['status'];
                $item['weid'] = $weisrc_friend['weid'];
                $item['view'] = $weisrc_friend['view'];
                $item['sharecount'] = $weisrc_friend['sharecount'];
            }
        }
        include $this->template('manage');
    }

    public function doWebdelete() {
        global $_GPC, $_W;
        $rid = intval($_GPC['rid']);
        $rule = pdo_fetch("SELECT id, module FROM " . tablename('rule') . " WHERE id = :id and uniacid=:weid", array(':id' => $rid, ':weid' => $_W['uniacid']));
        if (empty($rule)) {
            message('抱歉，要修改的规则不存在或是已经被删除！');
        }
        if (pdo_delete('rule', array('id' => $rid))) {
            pdo_delete('rule_keyword', array('rid' => $rid));
            //删除统计相关数据
            pdo_delete('stat_rule', array('rid' => $rid));
            pdo_delete('stat_keyword', array('rid' => $rid));
        }
        message('规则操作成功！', $this->createWebUrl('manage', array('op' => 'display')), 'success');
    }

    public function doWebdeleteAll() {
        global $_GPC, $_W;

        foreach ($_GPC['idArr'] as $k => $rid) {
            $rid = intval($rid);
            if ($rid == 0)
                continue;
            $rule = pdo_fetch("SELECT id, module FROM " . tablename('rule') . " WHERE id = :id and weid=:weid", array(':id' => $rid, ':weid' => $_W['uniacid']));
            if (empty($rule)) {
                $this->message('抱歉，要修改的规则不存在或是已经被删除！');
            }
            if (pdo_delete('rule', array('id' => $rid))) {
                pdo_delete('rule_keyword', array('rid' => $rid));
                //删除统计相关数据
                pdo_delete('stat_rule', array('rid' => $rid));
                pdo_delete('stat_keyword', array('rid' => $rid));
                //调用模块中的删除
                $module = WeUtility::createModule($rule['module']);
                if (method_exists($module, 'ruleDeleted')) {
                    $module->ruleDeleted($rid);
                }
            }
        }
        $this->message('规则操作成功！', '', 0);
    }

    public function doWebfanslist() {
        global $_GPC, $_W;
        load()->func('tpl');
        $weid = $this->_weid;
        $rid = intval($_GPC['rid']);

        if (empty($rid)) {
            message('抱歉，传递的参数错误！', '', 'error');
        }

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        $url = $this->createWebUrl('fanslist', array('op' => 'display', 'rid' => $rid));

        if ($operation == 'display') {

            $reply = pdo_fetch("SELECT * FROM " . tablename($this->table_reply) . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
            $condition = ' ';
            if ($reply == false) {
                $this->showMsg('抱歉，活动不存在！');
            }
            $pindex = max(1, intval($_GPC['page']));
            $psize = 12;

            $start = ($pindex - 1) * $psize;
            $limit = "";
            $limit .= " LIMIT {$start},{$psize}";
            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_fans) . " WHERE rid = :rid ORDER BY id DESC " . $limit, array(':rid' => $rid));

            $total = pdo_fetchcolumn("SELECT count(1) FROM " . tablename($this->table_fans) . " WHERE rid = :rid  ", array(':rid' => $rid));
            $mancount = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename($this->table_fans) . " WHERE rid = :rid AND sex=1", array(':rid' => $rid));
            $womancount = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename($this->table_fans) . " WHERE rid = :rid AND sex=2", array(':rid' => $rid));
            $pager = pagination($total, $pindex, $psize);
        } else if ($operation == 'post') {
            $id = intval($_GPC['id']);
            $item = pdo_fetch("SELECT * FROM " . tablename($this->table_fans) . " WHERE id = :id", array(':id' => $id));

            if (checksubmit()) {
                $data = array(
                    'weid' => $weid,
                    'rid' => $rid,
                    'nickname' => trim($_GPC['nickname']),
                    'number' => intval($_GPC['number']),
                    'sex' => intval($_GPC['sex']),
                    'dateline' => TIMESTAMP
                );
                if (!empty($_GPC['headimgurl'])) {
                    $data['headimgurl'] = $_GPC['headimgurl'];
                }

                if (empty($item)) {
                    pdo_insert($this->table_fans, $data);
                } else {
                    unset($data['dateline']);
                    pdo_update($this->table_fans, $data, array('id' => $id, 'weid' => $weid));
                }
                message('操作成功！', $url, 'success');
            }
        } else if ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $item = pdo_fetch("SELECT id FROM " . tablename($this->table_fans) . " WHERE id = :id AND weid=:weid", array(':id' => $id, ':weid' => $weid));
            if (empty($item)) {
                message('抱歉，不存在或是已经被删除！', $url, 'error');
            }
            pdo_delete($this->table_fans, array('id' => $id, 'weid' => $weid));
            message('删除成功！', $url, 'success');
        }
        include $this->template('fanslist');
    }

    protected function exportexcel($data = array(), $title = array(), $filename = 'report')
    {
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=" . $filename . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        //导出xls 开始
        if (!empty($title)) {
            foreach ($title as $k => $v) {
                $title[$k] = iconv("UTF-8", "GB2312", $v);
            }
            $title = implode("\t", $title);
            echo "$title\n";
        }
        if (!empty($data)) {
            foreach ($data as $key => $val) {
                foreach ($val as $ck => $cv) {
                    $data[$key][$ck] = iconv("UTF-8", "GB2312", $cv);
                }
                $data[$key] = implode("\t", $data[$key]);

            }
            echo implode("\n", $data);
        }
    }

    public function oauth2($url)
    {
        global $_GPC, $_W;
        load()->func('communication');
        $code = $_GPC['code'];
        if (empty($code)) {
            message('code获取失败.');
        }
        $token = $this->getAuthorizationCode($code);
        $from_user = $token['openid'];
        $userinfo = $this->getUserInfo($from_user);
        $sub = 1;
        if ($userinfo['subscribe'] == 0) {
            //未关注用户通过网页授权access_token
            $sub = 0;
            $authkey = intval($_GPC['authkey']);
            if ($authkey == 0) {
                $oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->_appid . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";
                header("location:$oauth2_code");
            }
            $userinfo = $this->getUserInfo($from_user, $token['access_token']);
        }

        if (empty($userinfo) || !is_array($userinfo) || empty($userinfo['openid']) || empty($userinfo['nickname'])) {
            echo '<h1>获取微信公众号授权失败[无法取得userinfo], 请稍后重试！ 公众平台返回原始数据为: <br />' . $sub . $userinfo['meta'] . '<h1>';
            exit;
        }

        //设置cookie信息
        setcookie($this->_auth2_headimgurl, $userinfo['headimgurl'], time() + 3600 * 24);
        setcookie($this->_auth2_nickname, $userinfo['nickname'], time() + 3600 * 24);
        setcookie($this->_auth2_openid, $from_user, time() + 3600 * 24);
        setcookie($this->_auth2_sex, $userinfo['sex'], time() + 3600 * 24);
        return $userinfo;
    }

    public function getUserInfo($from_user, $ACCESS_TOKEN = '')
    {
        if ($ACCESS_TOKEN == '') {
            $ACCESS_TOKEN = $this->getAccessToken();
            $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$ACCESS_TOKEN}&openid={$from_user}&lang=zh_CN";
        } else {
            $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$ACCESS_TOKEN}&openid={$from_user}&lang=zh_CN";
        }

        $json = ihttp_get($url);
        $userInfo = @json_decode($json['content'], true);
        return $userInfo;
    }

    public function getAuthorizationCode($code)
    {
        $oauth2_code = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->_appid}&secret={$this->_appsecret}&code={$code}&grant_type=authorization_code";
        $content = ihttp_get($oauth2_code);
        $token = @json_decode($content['content'], true);
        if (empty($token) || !is_array($token) || empty($token['access_token']) || empty($token['openid'])) {
            $id = $this->_activeid;
            $oauth2_code = $this->createMobileUrl('index', array('id' => $id), true);
            header("location:$oauth2_code");
//            echo '微信授权失败, 请稍后重试! 公众平台返回原始数据为: <br />' . $content['meta'] . '<h1>';
            exit;
        }
        return $token;
    }

    public function getAccessToken()
    {
        global $_W;
        $account = $_W['account'];
        if($this->_accountlevel < 4){
            if (!empty($this->_account)) {
                $account = $this->_account;
            }
        }
        load()->classs('weixin.account');
        $accObj= WeixinAccount::create($account['acid']);
        $access_token = $accObj->fetch_token();
        return $access_token;
    }

    public function getCode($url)
    {
        global $_W;
        $url = urlencode($url);
        $oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->_appid}&redirect_uri={$url}&response_type=code&scope=snsapi_base&state=0#wechat_redirect";
        header("location:$oauth2_code");
    }
}
