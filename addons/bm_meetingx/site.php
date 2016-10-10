<?php


defined('IN_IA') or exit('Access Denied');
class Bm_meetingxModuleSite extends WeModuleSite
{
    public function doWebRecord()
    {
        global $_GPC, $_W;
        checklogin();
        load()->func('tpl');
        $id  = intval($_GPC['id']);
        $mid = intval($_GPC['mid']);
        $op  = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($op == 'set') {
            $data = array(
                'status' => intval($_GPC['status'])
            );
            pdo_update('bm_meetingx_record', $data, array(
                "id" => intval($_GPC['mid'])
            ));
            $record = pdo_fetch("SELECT * FROM " . tablename('bm_meetingx_record') . " WHERE id = " . $_GPC['mid']);
            $reply  = pdo_fetch("SELECT * FROM " . tablename('bm_meetingx_reply') . " WHERE rid = :rid ORDER BY `id` DESC", array(
                ':rid' => $id
            ));
            if ($_GPC['status'] == 0) {
                if (empty($reply['tmpsmstitle1'])) {
                    $tmpsmstitle = '您在' . $_W['account']['name'] . '提交的申请单状态已更新！';
                } else {
                    $tmpsmstitle = $reply['tmpsmstitle1'];
                }
                if (empty($reply['tmpsmsdesc1'])) {
                    $tmpsmsdesc = "很遗憾的通知您，您的申请单未通过审核，您可以重新提交申请资料。";
                } else {
                    $tmpsmsdesc = $reply['tmpsmsdesc1'];
                }
            } else {
                if (empty($reply['tmpsmstitle'])) {
                    $tmpsmstitle = '您在' . $_W['account']['name'] . '提交的申请单状态已更新！';
                } else {
                    $tmpsmstitle = $reply['tmpsmstitle'];
                }
                if (empty($reply['tmpsmsdesc'])) {
                    $tmpsmsdesc = "很高兴的通知您，您的申请单已通过审核，我们恭候您的到来！";
                } else {
                    $tmpsmsdesc = $reply['tmpsmsdesc'];
                }
            }
            $url          = $_W['siteroot'] . 'app/' . $this->createmobileUrl('show', array(
                'op' => 'display',
                'rid' => $_GPC['id']
            ));
            $template     = array(
                'touser' => $record['from_user'],
                'template_id' => $reply['templateid'],
                'url' => $url,
                'topcolor' => "#7B68EE",
                'data' => array(
                    'first' => array(
                        'value' => urlencode($tmpsmstitle),
                        'color' => "#743A3A"
                    ),
                    'keyword1' => array(
                        'value' => urlencode($reply['mtitle']),
                        'color' => "#FF0000"
                    ),
                    'keyword2' => array(
                        'value' => urlencode(date('Y-m-d H:i:s', $record['sign_time'])),
                        'color' => "#0000FF"
                    ),
                    'remark' => array(
                        'value' => urlencode($tmpsmsdesc),
                        'color' => "#008000"
                    )
                )
            );
            $sql          = 'SELECT `key`,`secret` FROM ' . tablename('account_wechats') . ' WHERE `acid`=:acid';
            $row          = pdo_fetch($sql, array(
                ':acid' => $_W['account']['uniacid']
            ));
            $appid        = $row['key'];
            $appsecret    = $row['secret'];
            $url          = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appsecret;
            $res          = $this->http_request($url);
            $result       = json_decode($res, true);
            $access_token = $result["access_token"];
            $lasttime     = time();
            $x            = $this->send_template_message(urldecode(json_encode($template)), $access_token);
            message('更新成功', $this->createWebUrl('record', array(
                'op' => 'display',
                'id' => $_GPC['id']
            )), 'success');
        }
        $condition = '';
        if (!empty($_GPC['username'])) {
            $condition .= " AND username like '%{$_GPC['username']}%' ";
        }
        if (!empty($_GPC['sign_time'])) {
            $condition .= " AND sign_time = '%{$_GPC['username']}%' ";
        }
        if (empty($starttime) || empty($endtime)) {
            $starttime = strtotime('-1 month');
            $endtime   = TIMESTAMP;
        }
        if (!empty($_GPC['time'])) {
            $starttime = strtotime($_GPC['time']['start']);
            $endtime   = strtotime($_GPC['time']['end']) + 86399;
            $condition .= " AND sign_time >= '{$starttime}' AND sign_time <= '{$endtime}' ";
        }
        $pindex      = max(1, intval($_GPC['page']));
        $psize       = 20;
        $list        = pdo_fetchall("SELECT * FROM " . tablename('bm_meetingx_record') . " WHERE rid = '$id' $condition ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        $total       = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('bm_meetingx_record') . " WHERE rid = '$id' ");
        $payedtotal  = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('bm_meetingx_record') . " WHERE rid = '$id' and pay_status=1 ");
        $pager       = pagination($total, $pindex, $psize);
        $memberlist  = pdo_fetchall("SELECT distinct from_user FROM " . tablename('bm_meetingx_record') . "  WHERE rid = '$id' ");
        $membertotal = count($memberlist);
        include $this->template('record');
    }
    private function http_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
    private function send_template_message($data, $access_token)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $access_token;
        $res = $this->http_request($url, $data);
        return json_decode($res, true);
    }
    public function doWebMeeting()
    {
        global $_W, $_GPC;
        $id  = intval($_GPC['id']);
        $mid = intval($_GPC['mid']);
        load()->func('tpl');
        $op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($op == 'post') {
            if (!empty($_GPC['mid'])) {
                $item = pdo_fetch("SELECT * FROM" . tablename('bm_meetingx_meeting') . " WHERE id='{$_GPC['mid']}'");
            }
            $data = array(
                'weid' => $_W['weid'],
                'sort' => intval($_GPC['sort']),
                'rid' => $_GPC['id'],
                'name' => $_GPC['name'],
                'memo' => $_GPC['memo'],
                'picurl' => $_GPC['picurl'],
                'status' => intval($_GPC['status']),
                'datetime' => $_GPC['datetime']
            );
            if ($_W['ispost']) {
                if (empty($_GPC['mid'])) {
                    pdo_insert('bm_meetingx_meeting', $data);
                } else {
                    pdo_update('bm_meetingx_meeting', $data, array(
                        "id" => $_GPC['mid']
                    ));
                }
                message('更新成功', $this->createWebUrl('meeting', array(
                    'op' => 'display',
                    'id' => $_GPC['id']
                )), 'success');
            }
        } elseif ($op == 'display') {
            $list = pdo_fetchAll("SELECT * FROM" . tablename('bm_meetingx_meeting') . " WHERE weid='{$_W['weid']}' and rid='{$_GPC['id']}'");
        } elseif ($op == 'delete') {
            pdo_delete("bm_meetingx_meeting", array(
                'id' => $_GPC['mid']
            ));
            message('删除成功', $this->createWebUrl('meeting', array(
                'op' => 'display',
                'id' => $_GPC['id']
            )), 'success');
        }
        include $this->template('meeting');
    }
    public function doWebTeacher()
    {
        global $_W, $_GPC;
        $id  = intval($_GPC['id']);
        $mid = intval($_GPC['mid']);
        load()->func('tpl');
        $op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($op == 'post') {
            if (!empty($_GPC['mid'])) {
                $item = pdo_fetch("SELECT * FROM" . tablename('bm_meetingx_teacher') . " WHERE id='{$_GPC['mid']}'");
            }
            $data = array(
                'weid' => $_W['weid'],
                'sort' => intval($_GPC['sort']),
                'rid' => $_GPC['id'],
                'name' => $_GPC['name'],
                'memo' => $_GPC['memo'],
                'picurl' => $_GPC['picurl'],
                'status' => intval($_GPC['status']),
                'datetime' => $_GPC['datetime']
            );
            if ($_W['ispost']) {
                if (empty($_GPC['mid'])) {
                    pdo_insert('bm_meetingx_teacher', $data);
                } else {
                    pdo_update('bm_meetingx_teacher', $data, array(
                        "id" => $_GPC['mid']
                    ));
                }
                message('更新成功', $this->createWebUrl('teacher', array(
                    'op' => 'display',
                    'id' => $_GPC['id']
                )), 'success');
            }
        } elseif ($op == 'display') {
            $list = pdo_fetchAll("SELECT * FROM" . tablename('bm_meetingx_teacher') . " WHERE weid='{$_W['weid']}' and rid='{$_GPC['id']}'");
        } elseif ($op == 'delete') {
            pdo_delete("bm_meetingx_teacher", array(
                'id' => $_GPC['mid']
            ));
            message('删除成功', $this->createWebUrl('teacher', array(
                'op' => 'display',
                'id' => $_GPC['id']
            )), 'success');
        }
        include $this->template('teacher');
    }
    public function doMobileShow()
    {
        global $_W, $_GPC;
        $useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
        if (strpos($useragent, 'MicroMessenger') === false && strpos($useragent, 'Windows Phone') === false) {
            message('非法访问，请通过微信打开！');
            die();
        }
        $rid   = trim($_GPC['rid']);
        $reply = pdo_fetch("SELECT * FROM " . tablename('bm_meetingx_reply') . " WHERE rid = :rid ORDER BY `id` DESC", array(
            ':rid' => $rid
        ));
        if (time() > strtotime($reply['endtime'])) {
            if (empty($reply['memo2'])) {
                $msg = '对不起，活动已经于' . $reply['endtime'] . '结束，感谢您的参与！！！';
            } else {
                $msg = $reply['memo2'];
            }
            message($msg, $reply['url2'], 'success');
        }
        if (time() < strtotime($reply['starttime'])) {
            if (empty($reply['memo1'])) {
                $msg = '对不起，活动将于' . $reply['starttime'] . '开始，敬请期待！！！';
            } else {
                $msg = $reply['memo1'];
            }
            message($msg, $reply['url1'], 'success');
        }
        if (empty($_W['fans']['nickname'])) {
            mc_oauth_userinfo();
        }
        if ($reply['pictype'] == 1) {
            if ((empty($_W['fans']['follow'])) || ($_W['fans']['follow'] == 0)) {
                header("Location: " . $reply['urlx']);
                exit;
            }
        }
        if (!empty($reply['picurl'])) {
            $qrpicurl = $_W['attachurl'] . $reply['picurl'];
        } else {
            $qrpicurl = $_W['attachurl'] . $reply['qrcode'];
        }
        $meetings = pdo_fetchall("SELECT * FROM " . tablename('bm_meetingx_meeting') . " WHERE rid = :rid and status=1 ORDER BY `sort`", array(
            ':rid' => $rid
        ));
        $teachers = pdo_fetchall("SELECT * FROM " . tablename('bm_meetingx_teacher') . " WHERE rid = :rid and status=1 ORDER BY `sort` LIMIT 3", array(
            ':rid' => $rid
        ));
        include $this->template('show');
    }
    public function doMobileMeeting()
    {
        global $_W, $_GPC;
        $useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
        if (strpos($useragent, 'MicroMessenger') === false && strpos($useragent, 'Windows Phone') === false) {
            message('非法访问，请通过微信打开！');
            die();
        }
        $rid   = trim($_GPC['rid']);
        $reply = pdo_fetch("SELECT * FROM " . tablename('bm_meetingx_reply') . " WHERE rid = :rid ORDER BY `id` DESC", array(
            ':rid' => $rid
        ));
        if (time() > strtotime($reply['endtime'])) {
            if (empty($reply['memo2'])) {
                $msg = '对不起，活动已经于' . $reply['endtime'] . '结束，感谢您的参与！！！';
            } else {
                $msg = $reply['memo2'];
            }
            message($msg, $reply['url2'], 'success');
        }
        if (time() < strtotime($reply['starttime'])) {
            if (empty($reply['memo1'])) {
                $msg = '对不起，活动将于' . $reply['starttime'] . '开始，敬请期待！！！';
            } else {
                $msg = $reply['memo1'];
            }
            message($msg, $reply['url1'], 'success');
        }
        if (empty($_W['fans']['nickname'])) {
            mc_oauth_userinfo();
        }
        if ($reply['pictype'] == 1) {
            if ((empty($_W['fans']['follow'])) || ($_W['fans']['follow'] == 0)) {
                header("Location: " . $reply['urlx']);
                exit;
            }
        }
        if (!empty($reply['picurl'])) {
            $qrpicurl = $_W['attachurl'] . $reply['picurl'];
        } else {
            $qrpicurl = $_W['attachurl'] . $reply['qrcode'];
        }
        $meetings = pdo_fetchall("SELECT * FROM " . tablename('bm_meetingx_meeting') . " WHERE rid = :rid and status=1 ORDER BY `sort`", array(
            ':rid' => $rid
        ));
        include $this->template('meeting');
    }
    public function doMobileApply()
    {
        global $_W, $_GPC;
        $useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
        if (strpos($useragent, 'MicroMessenger') === false && strpos($useragent, 'Windows Phone') === false) {
            message('非法访问，请通过微信打开！');
            die();
        }
        $rid   = trim($_GPC['rid']);
        $reply = pdo_fetch("SELECT * FROM " . tablename('bm_meetingx_reply') . " WHERE rid = :rid ORDER BY `id` DESC", array(
            ':rid' => $rid
        ));
        $op    = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if (time() > strtotime($reply['endtime'])) {
            if (empty($reply['memo2'])) {
                $msg = '对不起，活动已经于' . $reply['endtime'] . '结束，感谢您的参与！！！';
            } else {
                $msg = $reply['memo2'];
            }
            message($msg, $reply['url2'], 'success');
        }
        if (time() < strtotime($reply['starttime'])) {
            if (empty($reply['memo1'])) {
                $msg = '对不起，活动将于' . $reply['starttime'] . '开始，敬请期待！！！';
            } else {
                $msg = $reply['memo1'];
            }
            message($msg, $reply['url1'], 'success');
        }
        $count = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('bm_meetingx_record') . " WHERE rid = '$rid' and (pay_status=1 or status=1)");
        if (($reply['count'] > 0) && ($count >= $reply['count'])) {
            if (empty($reply['memo3'])) {
                $msg = '对不起，活动报名人数已满，感谢您的参与！！！';
            } else {
                $msg = $reply['memo3'];
            }
            message($msg, $reply['url3'], 'success');
        }
        if (empty($_W['fans']['nickname'])) {
            mc_oauth_userinfo();
        }
        if ($reply['pictype'] == 1) {
            if ((empty($_W['fans']['follow'])) || ($_W['fans']['follow'] == 0)) {
                header("Location: " . $reply['urlx']);
                exit;
            }
        }
        if (!empty($reply['picurl'])) {
            $qrpicurl = $_W['attachurl'] . $reply['picurl'];
        } else {
            $qrpicurl = $_W['attachurl'] . $reply['qrcode'];
        }
        $meetings = pdo_fetchall("SELECT * FROM " . tablename('bm_meetingx_meeting') . " WHERE rid = :rid and status=1 ORDER BY `sort`", array(
            ':rid' => $rid
        ));
        if (checksubmit('submit')) {
            if ($op == 'post') {
                $data = array(
                    'weid' => $_W['weid'],
                    'rid' => $_GPC['rid'],
                    'from_user' => $_W['fans']['openid'],
                    'username' => $_W['fans']['nickname'],
                    'sign_time' => TIMESTAMP,
                    'avatar' => $_W['fans']['tag']['avatar'],
                    'name' => $_GPC['name'],
                    'comp' => $_GPC['comp'],
                    'mobile' => $_GPC['mobile'],
                    'status' => 0,
                    'price' => $reply['price'],
                    'pay_status' => 0,
                    'pay_time' => 0,
                    'clientOrderId' => TIMESTAMP . random(6, 1)
                );
                pdo_insert('bm_meetingx_record', $data);
                $urlto        = $_W['siteroot'] . 'app/' . $this->createmobileUrl('show', array(
                    'op' => 'display',
                    'rid' => $_GPC['rid']
                ));
                $template     = array(
                    'touser' => $reply['openid'],
                    'template_id' => $reply['templateid1'],
                    'url' => $urlto,
                    'topcolor' => "#7B68EE",
                    'data' => array(
                        'first' => array(
                            'value' => urlencode($_W['account']['name'] . '的活动有新的申请单！'),
                            'color' => "#743A3A"
                        ),
                        'keyword1' => array(
                            'value' => urlencode($reply['mtitle']),
                            'color' => "#FF0000"
                        ),
                        'keyword2' => array(
                            'value' => urlencode(date('Y-m-d H:i:s', time())),
                            'color' => "#0000FF"
                        ),
                        'remark' => array(
                            'value' => urlencode("客户Openid：" . $_W['fans']['openid'] . ",客户昵称：" . $_W['fans']['nickname']),
                            'color' => "#008000"
                        )
                    )
                );
                $sql          = 'SELECT `key`,`secret` FROM ' . tablename('account_wechats') . ' WHERE `acid`=:acid';
                $row          = pdo_fetch($sql, array(
                    ':acid' => $_W['account']['uniacid']
                ));
                $appid        = $row['key'];
                $appsecret    = $row['secret'];
                $url          = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appsecret;
                $res          = $this->http_request($url);
                $result       = json_decode($res, true);
                $access_token = $result["access_token"];
                $lasttime     = time();
                $x            = $this->send_template_message(urldecode(json_encode($template)), $access_token);
                $template     = array(
                    'touser' => $data['from_user'],
                    'template_id' => $reply['templateid1'],
                    'url' => $urlto,
                    'topcolor' => "#7B68EE",
                    'data' => array(
                        'first' => array(
                            'value' => urlencode('感谢您报名' . $_W['account']['name'] . '的活动！'),
                            'color' => "#743A3A"
                        ),
                        'keyword1' => array(
                            'value' => urlencode($reply['mtitle']),
                            'color' => "#FF0000"
                        ),
                        'keyword2' => array(
                            'value' => urlencode(date('Y-m-d H:i:s', time())),
                            'color' => "#0000FF"
                        ),
                        'remark' => array(
                            'value' => urlencode("客户Openid：" . $_W['fans']['openid'] . ",客户昵称：" . $_W['fans']['nickname']),
                            'color' => "#008000"
                        )
                    )
                );
                $x            = $this->send_template_message(urldecode(json_encode($template)), $access_token);
                message('您的申请单已经提交，我们将尽快联系您确', $this->createmobileUrl('show', array(
                    'op' => 'display',
                    'rid' => $_GPC['rid']
                )), 'success');
            }
        }
        include $this->template('apply');
    }
}