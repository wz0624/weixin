<?php


defined('IN_IA') or exit('Access Denied');
class Bm_inbarkModuleSite extends WeModuleSite
{
    public function doWebRecord()
    {
        global $_GPC, $_W;
        checklogin();
        load()->func('tpl');
        $rid       = intval($_GPC['id']);
        $condition = '';
        if (!empty($_GPC['userName'])) {
            $condition .= " AND userName like '%{$_GPC['userName']}%' ";
        }
        if (empty($starttime) || empty($endtime)) {
            $starttime = strtotime('-1 month');
            $endtime   = TIMESTAMP;
        }
        if (!empty($_GPC['time'])) {
            $starttime = strtotime($_GPC['time']['start']);
            $endtime   = strtotime($_GPC['time']['end']) + 86399;
            $condition .= " AND dataline >= '{$starttime}' AND dataline <= '{$endtime}' ";
        }
        $pindex      = max(1, intval($_GPC['page']));
        $psize       = 20;
        $list        = pdo_fetchall("SELECT * FROM " . tablename('bm_inbark_record') . " WHERE rid = '$rid' $condition ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        $total       = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('bm_inbark_record') . " WHERE rid = '$rid' ");
        $pager       = pagination($total, $pindex, $psize);
        $memberlist  = pdo_fetchall("SELECT distinct fromuser FROM " . tablename('bm_inbark_record') . "  WHERE rid = '$rid' ");
        $membertotal = count($memberlist);
        include $this->template('record');
    }
    public function doMobileShow()
    {
        global $_W, $_GPC;
        $jssdkconfig = json_encode($_W['account']['jssdkconfig']);
        $useragent   = addslashes($_SERVER['HTTP_USER_AGENT']);
        if (strpos($useragent, 'MicroMessenger') === false && strpos($useragent, 'Windows Phone') === false) {
        }
        $rid      = trim($_GPC['rid']);
        $fromuser = trim($_GPC['fromuser']);
        $reply    = pdo_fetch("SELECT * FROM " . tablename('bm_inbark_reply') . " WHERE rid = :rid ORDER BY `id` DESC", array(
            ':rid' => $rid
        ));
        $info     = pdo_fetch("SELECT * FROM " . tablename('bm_inbark_record') . " WHERE rid = :rid and fromuser = :fromuser", array(
            ':rid' => $rid,
            ':fromuser' => $fromuser
        ));
        if (empty($_W['fans']['nickname'])) {
            mc_oauth_userinfo();
        }
        $op        = trim($_GPC['op']);
        $from_user = $_W['fans']['openid'];
        if (checksubmit('submit')) {
            if ($op == 'post') {
                $data = array(
                    'rid' => $rid,
                    'dateline' => TIMESTAMP,
                    'fromuser' => $from_user,
                    'nickname' => $_W['fans']['nickname'],
                    'avatar' => $_W['fans']['tag']['avatar'],
                    'userName' => $_GPC['userName'],
                    'industry' => $_GPC['industry'],
                    'case' => $_GPC['case']
                );
                if (empty($info)) {
                    pdo_insert('bm_inbark_record', $data);
                } else {
                    pdo_update('bm_inbark_record', $data, array(
                        'rid' => $rid,
                        'fromuser' => $from_user
                    ));
                }
            }
            $info = pdo_fetch("SELECT * FROM " . tablename('bm_inbark_record') . " WHERE rid = :rid and fromuser = :fromuser", array(
                ':rid' => $rid,
                ':fromuser' => $from_user
            ));
        }
        if (empty($info)) {
            $info = array(
                'userName' => '力昂软件',
                'industry' => '微信开发',
                'case' => '创业游戏'
            );
        }
        include $this->template('show');
    }
}