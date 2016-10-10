<?php
defined('IN_IA') or exit('Access Denied');
class Jy_readsModuleSite extends WeModuleSite
{
    public $m = 'jy_reads';
    public $table_reply = 'jy_reads_reply';
    public $table_prize = 'jy_reads_prize';
    public $table_user = 'jy_reads_user';
    public $table_info = 'jy_reads_user_info';
    public $table_property = 'jy_reads_user_property';
    public $table_verifier = 'jy_reads_verifier';
    public $table_log = 'jy_reads_log';
    public $table_bonus = 'jy_reads_bonus';
    public $table_bonus_log = 'jy_reads_bonus_log';
    public $table_coupon = 'jy_reads_coupon';
    public $table_coupon_log = 'jy_reads_coupon_log';
    public function doWebInstall()
    {
        global $_W, $_GPC;
        include_once 'sys/install.php';
        echo 'installed';
    }
    public function doWebUpgrade()
    {
        global $_W, $_GPC;
        include_once 'sys/upgrade.php';
        echo 'upgraded';
    }
    private function getLogic($_name, $type = "web", $auth = false)
    {
        global $_W, $_GPC;
        if ($type == 'web') {
            checkLogin();
            include_once 'inc/web/' . strtolower(substr($_name, 5)) . '.php';
        } else if ($type == 'mobile') {
            if ($auth) {
                include_once 'inc/func/isauth.php';
            }
            include_once 'inc/mobile/' . strtolower(substr($_name, 8)) . '.php';
        } else if ($type == 'func') {
            include_once 'inc/func/' . strtolower(substr($_name, 8)) . '.php';
        }
    }
    public function doMobileAuth()
    {
        $this->getLogic(__FUNCTION__, 'func');
    }
    public function doMobileIndex()
    {
        $this->getLogic(__FUNCTION__, 'mobile', true);
    }
    public function doMobileBonus()
    {
        $this->getLogic(__FUNCTION__, 'mobile', true);
    }
    public function doMobileCoupon()
    {
        $this->getLogic(__FUNCTION__, 'mobile', true);
    }
    public function doMobileIndexAjax()
    {
        $this->getLogic(__FUNCTION__, 'mobile', false);
    }
    public function doMobileVerifier()
    {
        $this->getLogic(__FUNCTION__, 'mobile', true);
    }
    public function doMobileVerified()
    {
        $this->getLogic(__FUNCTION__, 'mobile', true);
    }
    public function doMobileVerify()
    {
        $this->getLogic(__FUNCTION__, 'mobile', true);
    }
    public function doMobilePerson()
    {
        $this->getLogic(__FUNCTION__, 'mobile', true);
    }
    public function doWebActivity()
    {
        $this->getLogic(__FUNCTION__, 'web');
    }
    public function doWebChangeStatus()
    {
        $this->getLogic(__FUNCTION__, 'web');
    }
    public function doWebPrize()
    {
        $this->getLogic(__FUNCTION__, 'web');
    }
    public function doWebBonus()
    {
        $this->getLogic(__FUNCTION__, 'web');
    }
    public function doWebUserExport()
    {
        $this->getLogic(__FUNCTION__, 'web');
    }
    public function doWebUserLog()
    {
        $this->getLogic(__FUNCTION__, 'web');
    }
    public function doWebCoupon()
    {
        global $_W, $_GPC;
        $sql        = "SELECT * FROM " . tablename('uni_settings') . " WHERE `uniacid`=:uniacid";
        $unisetting = pdo_fetch($sql, array(
            ':uniacid' => $_W['uniacid']
        ));
        if (!empty($unisetting['oauth'])) {
            $temp = unserialize($unisetting['oauth']);
            $weid = empty($temp['account']) ? 0 : $temp['account'];
        }
        $sql           = "SELECT * FROM " . tablename('account_wechats') . " WHERE `uniacid`=:uniacid ORDER BY acid ASC LIMIT 1";
        $wechataccount = pdo_fetch($sql, array(
            ':uniacid' => $_W['uniacid']
        ));
        if ($wechataccount['level'] == 4 && (empty($weid) || $weid == $_W['uniacid'])) {
            $this->getLogic(__FUNCTION__, 'web');
        } else {
            $this->getLogic('doWebCouponReject', 'web');
        }
    }
    public function doWebUser()
    {
        $this->getLogic(__FUNCTION__, 'web');
    }
    protected function getEndTime($time)
    {
        $time = $time - time();
        if ($time > 0) {
            $d = floor($time / 86400);
            $h = floor(($time - $d * 86400) / 3600);
            $m = floor(($time - $d * 86400 - $h * 3600) / 60);
            return $d . '天' . $h . '小时' . $m . '分钟';
        } else {
            return '已结束';
        }
    }
    protected function getVisitNum($replyid)
    {
        return pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_user) . " WHERE replyid = '{$replyid}'");
    }
    protected function getHitsNum($replyid)
    {
        return pdo_fetchcolumn('SELECT SUM(hits) FROM ' . tablename($this->table_user) . " WHERE replyid = '{$replyid}'");
    }
    protected function getActivityStatus($replyid)
    {
        return true;
    }
    protected function getValueCount($bonusid)
    {
        return pdo_fetchColumn('SELECT COUNT(*) FROM ' . tablename($this->table_user) . ' WHERE sn like "bonus-%" AND prizeid=:bonusid', array(
            ':bonusid' => $bonusid
        ));
    }
    protected function getValueCouponCount($couponid)
    {
        return pdo_fetchColumn('SELECT COUNT(*) FROM ' . tablename($this->table_user) . ' WHERE sn like "coupon-%" AND prizeid=:coupon', array(
            ':coupon' => $couponid
        ));
    }
    protected function getCityId($ip)
    {
        $url    = "http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip;
        $ipinfo = json_decode(file_get_contents($url));
        if ($ipinfo->code == '1') {
            return false;
        }
        return substr($ipinfo->data->city_id, 0, 4);
    }
    private function encode($value)
    {
        return $value;
        return iconv('utf-8', 'gb2312', $value);
    }
}