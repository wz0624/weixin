<?php
/**
 * Created by 蓝森林.
 * 这不是一个开源版本的软件,程序版权归 蓝森林 所有
 * QQ:40221015
 * Date: 2015-05-19
 * Time: 10:44
 */
defined('IN_IA') or exit('Access Denied');

class Aidybnt_testapiModuleProcessor extends WeModuleProcessor {
    public $tablename2 = 'mc_mapping_fans';
    public $tablename = 'aaidybnt_testapi';
    public $tablename3 = 'account';

    public function respond() {
        global $_W,$_GPC;

        $sql2 = "SELECT fanid FROM" . tablename($this->tablename2) . "WHERE uniacid = :uniacid";
        $_rows = count(pdo_fetchall($sql2, array(':uniacid' => $_W['uniacid'])));

        $sql3 = "SELECT fanid FROM" . tablename($this->tablename2) . "WHERE uniacid = :uniacid AND follow = 1";
        $_rows2 = count(pdo_fetchall($sql3, array(':uniacid' => $_W['uniacid'])));

        $sqlw = "SELECT * FROM" . tablename($this->tablename) . " WHERE rid = :rid";
        $reply = pdo_fetch($sqlw, array(':rid' => $this->rule));

        if ($reply['acid'] == $_W['uniacid']) {
            $access_token = WeAccount::token();
        } else {
            load()->classs('weixin.account');
            $wxObj = WeixinAccount::create($reply['acid']);
            $access_token = $wxObj->fetch_token();
        }

        $openid = $_W['openid'];

        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $access_token . "&openid=" . $openid . "&lang=zh_CN";
        $output = file_get_contents($url);
        $outarr = json_decode($output, true);

        $url2 = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=" . $access_token;
        $output2 = file_get_contents($url2);
        $outarr2 = json_decode($output2, true);

        $textarr = array();
        $textarr = 'subscribe:' . $outarr['subscribe'] . "\n";
        $textarr .= 'openid:' . $outarr['openid'] . "\n";
        $textarr .= 'nickname:' . $outarr['nickname'] . "\n";
        $textarr .= 'sex:' . $outarr['sex'] . "\n";
        $textarr .= 'language:' . $outarr['language'] . "\n";
        $textarr .= 'city:' . $outarr['city'] . "\n";
        $textarr .= 'province:' . $outarr['province'] . "\n";
        $textarr .= 'country:' . $outarr['country'] . "\n";
        $textarr .= 'headimgurl:' . $outarr['headimgurl'] . "\n";
        $textarr .= 'subscribe_time:' . $outarr['subscribe_time'] . "\n\n";

        $textarr .= '粉丝总数(来自数据库含关注取消)：' . $_rows . "\n";
        $textarr .= '您是第(来自数据库)：' . $_rows2 . "位关注者\n\n";

        if (!empty($outarr2['total'])) {
            $textarr .= '粉丝总数(来自微信返回）：' . $outarr2['total'] . "\n";
            $textarr .= 'next_openid是：' . $outarr2['next_openid'] . "\n\n";

            $textarr .= '自定义粉丝起始数：' . $reply['num'] . "\n";
            $textarr .= '你是当前第 ' . ($reply['num'] + $outarr2['total']) . "个关注粉丝\n";
        } else {
            $textarr .= $output2;
        }

        if (!empty($outarr['headimgurl'])) {
            return $this->respText($reply['content'] . "\n" . $textarr . "\n" . "acid = " . $reply['acid'] . "\n" . "ruleid = " . $this->rule . "\n" . "系统获取的Openid是：" . $openid . "\n" . "系统获取的atoken是：" . $access_token);
        } else {
            return $this->respText($reply['content'] . "\n" . $output . "\n" . "acid = " . $reply['acid'] . "\n" . "ruleid = " . $this->rule . "\n" . "系统获取的Openid是：" . $openid . "\n" . "系统获取的atoken是：" . $access_token);
        }
    }
}
