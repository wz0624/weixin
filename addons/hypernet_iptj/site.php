<?php
defined('IN_IA') or exit('Access Denied');
class hypernet_iptjModuleSite extends WeModuleSite
{
    public function doMobilePtjindex()
    {
        global $_W, $_GPC;
        require_once('config.php');
        require_once('template_conf_bottom.php');
        $share  = json_decode($share_config, true);
        $bottom = json_decode($bottom, true);
        load()->model('mc');
        $avatar = '';
        $date   = date('Y-m-d');
        $farr   = array();
        $user   = $this->DboperateSearchUser($_W['openid']);
        if (empty($avatar)) {
            $userinfo = mc_oauth_userinfo();
            if (!is_error($userinfo) && !empty($userinfo) && is_array($userinfo) && !empty($userinfo['avatar'])) {
                $avatar   = $userinfo['avatar'];
                $nickname = $userinfo['nickname'];
            }
        }
        if (empty($avatar)) {
        } else {
            $src = $avatar;
        }
        if ($_GPC['salarybtn']) {
            $ground = $this->DboperateSorybysalary();
        } else if ($_GPC['timebtn']) {
            $ground = $this->DboperateSortbytime();
        } else if ($_GPC['ownerbtn']) {
            $ground = $this->DboperateSortbyownerid();
        } else if ($_GPC['type']) {
            $type       = $_GPC['type'];
            $groundtype = $this->DboperateSearchGround($type, FALSE, FALSE, FALSE, TRUE);
            foreach ($groundtype as $k => $v) {
                if ($v['privacy'] == 0) {
                    array_push($farr, $k);
                }
            }
        } else {
            $ground = $this->DboperateSearchGroundinfo();
            foreach ($ground as $k => $v) {
                if ($v['privacy'] == 0) {
                    array_push($farr, $k);
                }
                $admitted = $this->ifAllSure($v['jobid']);
                if ($admitted >= $v['mount']) {
                    pdo_update('ptj_ground', array(
                        'visible' => 0
                    ), array(
                        'jobid' => $v['jobid']
                    ));
                }
            }
        }
        $search = $_GPC['Ptjindex']['Search'];
        if ($search) {
            $searchinfo = $this->DboperateSearchGround($search, 0, 0, 0, 0);
            foreach ($searchinfo as $k => $v) {
                if ($v['privacy'] == 0) {
                    array_push($farr, $k);
                }
            }
        }
        $avatar     = $_W['account']['avatar'];
        $first      = $farr[0];
        $img        = pdo_fetchall("SELECT * FROM" . tablename('ptj_pic') . "ORDER BY priority", array(), '');
        $proot      = $_W['attachurl'];
        $attachroot = $_W['attachurl'];
        $Tinfo      = pdo_fetch("SELECT COUNT(*) as count FROM" . tablename('ptj_ground') . "WHERE visible=:vis", array(
            ':vis' => 1
        ));
        $count      = $Tinfo['count'];
        $pagesize   = 20;
        $pageindex  = $_GPC['page'] ? $_GPC['page'] : 1;
        $page       = pagination($count, $pageindex, $pagesize);
        include $this->template('index');
    }
    public function doWebPtjrules()
    {
        global $_W, $_GPC;
    }
    public function doWebPtjtiezi()
    {
        global $_W, $_GPC;
        $search = array();
        if ($_GPC['search'] == 'on') {
            if ($_GPC['title'] !== "") {
                $search['title'] = $_GPC['title'];
            }
            if ($_GPC['cname'] !== "") {
                $search['cname'] = $_GPC['cname'];
            }
            if ($_GPC['privacy'] !== "") {
                $search['privacy'] = $_GPC['privacy'];
            }
            if ($_GPC['visible'] !== "") {
                $search['visible'] = $_GPC['visible'];
            }
        }
        $order  = empty($_GPC['order']) ? "privacy DESC" : $_GPC['order'];
        $ground = $this->DboperateSearchTopicinfo($order, $search);
        if ($_GPC['status']) {
            if ($_GPC['status'] == 'down') {
                pdo_update('ptj_ground', array(
                    'privacy' => 0
                ), array(
                    'jobid' => $_GPC['jobid']
                ));
            } elseif ($_GPC['status'] == 'up') {
                pdo_update('ptj_ground', array(
                    'privacy' => 1
                ), array(
                    'jobid' => $_GPC['jobid']
                ));
                pdo_update('ptj_ground', array(
                    'pdate' => date('Y-m-d H:i:s')
                ), array(
                    'jobid' => $_GPC['jobid']
                ));
            } elseif ($_GPC['status'] == 'close') {
                pdo_update('ptj_ground', array(
                    'visible' => 0
                ), array(
                    'jobid' => $_GPC['jobid']
                ));
            } elseif ($_GPC['status'] == 'open') {
                pdo_update('ptj_ground', array(
                    'visible' => 1
                ), array(
                    'jobid' => $_GPC['jobid']
                ));
            }
            $_GPC = array();
            self::doWebPtjtiezi();
            return;
        }
        if ($_GPC['delete'] == 'on') {
            pdo_delete('ptj_ground', array(
                'jobid' => $_GPC['jobid']
            ));
            $url  = $this->createWebUrl('ptjtiezi');
            $_GPC = array();
            self::doWebPtjtiezi();
            return;
        }
        if ($_GPC['edit'] == 'on') {
            $detail = $this->DboperateTopicDetail($_GPC['jobid']);
            include $this->template('topic_edit');
            return;
        }
        if ($_GPC['add'] == 'on') {
            include $this->template('topic_add');
            return;
        }
        include $this->template('topic');
    }
    public function dowebPtjtopic_edit()
    {
        global $_W, $_GPC;
        $jobid = $_GPC['jobid'];
        pdo_update('ptj_ground', array(
            'title' => $_GPC['title'],
            'workplace' => $_GPC['workplace'],
            'salary' => $_GPC['salary'],
            'date' => $_GPC['date'],
            'stopdate' => $_GPC['stopdate'],
            'type' => $_GPC['type'],
            'mount' => $_GPC['mount'],
            'content' => $_GPC['content'],
            'haslimit' => $_GPC['haslimit']
        ), array(
            'jobid' => $jobid
        ));
        $this->doWebPtjtiezi();
    }
    public function dowebPtjtopic_add()
    {
        global $_W, $_GPC;
        $jobid     = substr(md5(substr(time(), 4, 8)), 4, 9);
        $nowtime   = date('Y-m-d');
        $now       = date('Y-m-d H:i:s');
        $title     = $_GPC['title'];
        $content   = $_GPC['content'];
        $salary    = $_GPC['salary'];
        $mount     = $_GPC['mount'];
        $starttime = $_GPC['date'];
        $type      = $_GPC['type'];
        $limit     = $_GPC['haslimit'];
        $endtime   = $_GPC['endtime'];
        $workplace = $_GPC['workplace'];
        $this->DboperateInsertGroundInfo('admin', $jobid, $title, $content, $salary, $mount, $starttime, $type, $limit, $endtime, $nowtime, $workplace, 0, $now);
        $this->doWebPtjtiezi();
    }
    public function doWebPtjadmin()
    {
        global $_W, $_GPC;
        $pinfo    = $this->DboperateGetAllProfile();
        $adminget = $_GPC['PtjAdmin'];
        if ($adminget) {
            $info = pdo_fetch("SELECT * from" . tablename('ptj_profile') . "WHERE id=:i", array(
                ':i' => $_GPC['id']
            ));
            $this->SendTpl($info['openid'], $info, 'refuse', $adminget['Res']);
            $t   = $this->DboperateDeleteUserInfo($_GPC['id']);
            $url = $this->createWebUrl('ptjadmin');
            echo "<script language='javascript'>
	   	 location.href=\"$url\";
	   	 </script>";
        }
        if ($_GPC['surebtn'] == 1) {
            $t    = $this->DboperateAdminSurePro($_GPC['id']);
            $info = pdo_fetch("SELECT * from" . tablename('ptj_profile') . "WHERE id=:i", array(
                ':i' => $_GPC['id']
            ));
            $this->SendTpl($info['openid'], $info, 'auth');
            $url = $this->createWebUrl('ptjadmin');
            echo "<script language='javascript'>
	   	 location.href=\"$url\";
	   	 </script>";
        }
        if ($_GPC['redo'] == 'on') {
            pdo_update('ptj_profile', array(
                'identity' => 'worker'
            ), array(
                'id' => $_GPC['id']
            ));
            $url = $this->createWebUrl('ptjadmin');
            echo "<script language='javascript'>
	   	 location.href=\"$url\";
	   	 </script>";
        }
        if ($_GPC['setadmin'] == 'on') {
            pdo_update('ptj_profile', array(
                'identity' => 'admin'
            ), array(
                'id' => $_GPC['id']
            ));
            $url = $this->createWebUrl('ptjadmin');
            echo "<script language='javascript'>
	   	location.href=\"$url\";
	   	</script>";
        }
        if ($_GPC['xu'] == 'on') {
            pdo_update('ptj_profile', array(
                'identity' => 'xued'
            ), array(
                'id' => $_GPC['id']
            ));
            $url = $this->createWebUrl('ptjadmin');
            echo "<script language='javascript'>
	   	location.href=\"$url\";
	   	</script>";
        }
        if ($_GPC['rexu'] == 'on') {
            $user = pdo_fetch("SELECT cname FROM" . tablename('ptj_profile') . "WHERE id=:ID", array(
                ':ID' => $_GPC['id']
            ));
            if ($user['cname']) {
                pdo_update('ptj_profile', array(
                    'identity' => 'owner'
                ), array(
                    'id' => $_GPC['id']
                ));
            } else {
                pdo_update('ptj_profile', array(
                    'identity' => 'worker'
                ), array(
                    'id' => $_GPC['id']
                ));
            }
            $url = $this->createWebUrl('ptjadmin');
            echo "<script language='javascript'>
	   	location.href=\"$url\";
	   	</script>";
        }
        include $this->template('Admin');
    }
    public function dowebPtjfunc()
    {
        global $_W, $_GPC;
        $url = $_GPC['Ptjfunc']['Link'];
        pdo_update('ptj_link', array(
            'url' => $url
        ), array(
            'Linkid' => 1
        ));
        include $this->template('webfunc');
    }
    public function doMobilePtjlink()
    {
    }
    public function doMobilePtjpay()
    {
        global $_W, $_GPC;
        $credit = $_GPC['credit'];
        include $this->template('pay');
    }
    public function doMobilePtjmyinfo()
    {
        global $_W, $_GPC;
        require_once('config.php');
        require_once('template_conf_bottom.php');
        $share  = json_decode($share_config, true);
        $bottom = json_decode($bottom, true);
        load()->model('mc');
        load()->func('tpl');
        $avatar = '';
        $openid = $_W['openid'];
        $count  = 0;
        $avatar = '';
        $openid = $_W['openid'];
        $count  = 0;
        $uid    = $_W['member']['uid'];
        $sinfo  = pdo_fetch("SELECT * FROM" . tablename('ptj_link') . "WHERE linkid=:id", array(
            ':id' => 1
        ));
        $url    = $sinfo['url'];
        if (empty($avatar)) {
            $userinfo = mc_oauth_userinfo();
            if (!is_error($userinfo) && !empty($userinfo) && is_array($userinfo) && !empty($userinfo['avatar'])) {
                $avatar   = $userinfo['avatar'];
                $nickname = $userinfo['nickname'];
            }
        }
        if (empty($avatar)) {
            echo "最终没有获取到头像,follow: {$_W['fans']['follow']}";
        } else {
            $src = $avatar;
        }
        if ($this->DboperateIsWaiting($openid)) {
            $waiting = true;
        }
        $uid         = mc_openid2uid($openid);
        $creditarray = mc_credit_fetch($uid);
        $credit      = intval($creditarray['credit1']);
        $image       = $_W['attachurl'] . 'confirm.jpg';
        $user        = $this->DboperateSearchUser($openid);
        if ($user['identity'] == 'worker') {
            $isworker = 1;
        } else if ($user['identity'] == 'owner') {
            $isworker = 2;
        } else if ($user['identity'] == 'admin') {
            $isworker = 3;
        } else if ($user['identity'] == 'xued') {
            $isworker = 4;
        }
        $now  = date("y-m-d");
        $work = $this->DboperationSearchMyworkEx($openid);
        foreach ($work as $k => $v) {
            $this->Dboperatechecktime($v['jobid'], $now, $openid);
            $comment = $profile['comm'];
            if ($comment AND $v['status']) {
                $this->Dboperatepushcomment($comment, $v['jobid'], $openid);
            }
        }
        $workerEx    = $this->DboperationSearchMyworkEx($openid);
        $myworkerbtn = $_GPC['myworkerbtn'];
        if ($myworkerbtn) {
            $myworker = $this->DboperateGetworkerinfo($openid);
            if ($_GPC['undowker'] && $_GPC['jobid']) {
                $undo = $this->DboperateUndoWanna($_GPC['jobid']);
                $url  = $this->createMobileUrl('ptjmyinfo', array(
                    'myworkerbtn' => 'on'
                ));
                echo "<script language='javascript'>
                 alert('哇!!你的一条招募令消失了~~');
                 </script>";
                echo "<script language='javascript'>
                 location.href=\"$url\";
                 </script>";
            }
        }
        $workmanbtn = $_GPC['workmanbtn'];
        if ($workmanbtn) {
            $workmaninfo = $this->DboperateFindWk($openid);
        }
        include $this->template('myinfo');
    }
    public function doMobilePtjdetails()
    {
        global $_W, $_GPC;
        require_once('config.php');
        $share = json_decode($share_config, true);
        load()->model('mc');
        $openid      = $_W['openid'];
        $flag        = $_GPC['flag'];
        $ground      = $_GPC['ground'];
        $uid         = mc_openid2uid($openid);
        $creditarray = mc_credit_fetch($uid);
        $credit      = $creditarray['credit1'];
        if (empty($ground)) {
            $ground = pdo_fetch("SELECT * FROM" . tablename('ptj_ground') . "WHERE jobid=:jid ", array(
                ':jid' => $_GPC['jobid']
            ));
            $exists = $this->DboperateGetIntworkers($_GPC['jobid']);
        }
        $oid       = $ground['openid'];
        $Ownerinfo = $this->DboperateSearchUser($oid);
        $user      = $this->DboperateSearchUser($_W['openid']);
        $name      = $Ownerinfo['cname'] ? $Ownerinfo['cname'] : $Ownerinfo['name'];
        $bmbutton  = $_GPC['button'];
        $entry     = 'details';
        $user      = $this->DboperateSearchUser($openid);
        $isworker  = $user['identity'] == 'worker' ? 1 : 2;
        if (($_W['openid'] == $oid) OR $user['identity'] == 'admin') {
            $isworker = 3;
        }
        if ($user['identity'] == 'xued') {
            $isworker = 4;
        }
        if ($this->DboperateCheckUser($openid)) {
            $arr = $this->DboperateGetjob($openid, $oid);
            if ($oid != $openid) {
                if ($arr AND in_array($ground['jobid'], $arr)) {
                    $commitFlag = 1;
                    if ($this->Checkifadmit($ground['jobid'], $openid)) {
                        $commitFlag = 2;
                    }
                } else if ($bmbutton == 'on') {
                    $this->DboperateSurepostinfo($oid, $openid, $ground['jobid'], $ground['mount']);
                    $worker = $this->DboperateGetIntworkers($ground['jobid']);
                    if ($worker == intval($ground['mount'] / 3)) {
                        $this->SendTpl($oid, $ground, 'first_third');
                    } else if ($worker == intval($ground['mount'] / 3 * 2)) {
                        $this->SendTpl($oid, $ground, 'second_third');
                    } else if ($worker == $ground['mount']) {
                        $this->SendTpl($oid, $ground, 'full');
                    }
                    $this->SendTpl($openid, $Ownerinfo, 'apply');
                    echo "<script language='javascript'>
					alert('报名成功 请联系商家核实报名信息哦~');
                  	location.reload();
					</script>";
                }
            } else if ($bmbutton == 'on') {
                echo "<script language='javascript'>
					alert('?(????)商家号不能参与报名哦~');
                 				location.reload();
					</script>";
            }
        } else if ($bmbutton == 'on' AND !$this->DboperateCheckUser($openid)) {
            echo "<script language='javascript'>
					alert('(～￣▽￣)～先去个人中心完善信息并等待管理员确认哦~');
							location.reload();
					</script>";
        }
        $ginfo     = pdo_fetch("SELECT * from" . tablename('ptj_ground') . "WHERE jobid=:jid", array(
            ':jid' => $ground['jobid']
        ));
        $ifprivacy = $ginfo['privacy'];
        if ($_GPC['top'] == 'on') {
            if ($this->GetPrivacyNum() >= 10) {
                message('置顶已经满啦,请下一次早点来哦。。');
            } else if ($credit >= 100) {
                $f = pdo_update('ptj_ground', array(
                    'privacy' => 1,
                    'pdate' => date('Y-m-d H:i:s')
                ), array(
                    'jobid' => $ground['jobid']
                ));
                $s = mc_credit_update(mc_openid2uid($_W['openid']), 'credit1', -100);
                if ($f AND $s) {
                    $this->SendTpl($_W['openid'], $user, 'credit1', $credit - 100);
                    message('置顶成功!', '', 'success');
                }
            } else {
                message('所需积分不足,请前往个人中心充值!');
            }
        }
        include $this->template('details');
    }
    public function doMobilePtjorders()
    {
    }
    public function doMobilePtjfunc()
    {
        global $_W, $_GPC;
        $sinfo = pdo_fetch("SELECT * FROM" . tablename('ptj_link') . "WHERE linkid=:id", array(
            ':id' => 1
        ));
        $url   = $sinfo['url'];
        include $this->template('divfunc');
    }
    public function doWebPtjshare()
    {
        global $_W, $_GPC;
        require_once('config.php');
        $share = json_decode($share_config, true);
        include $this->template('share');
    }
    public function doWebPtjshare_edit()
    {
        global $_W, $_GPC;
        foreach ($_GPC as $key => $value) {
            if (strpos($key, 'title') === false && strpos($key, 'imgurl') === false && strpos($key, 'desc') === false) {
                unset($_GPC[$key]);
            }
        }
        $share = json_encode($_GPC);
        $share_file = fopen(dirname(preg_replace('@\(.*\(.*$@', '', __FILE__)) . "/config.php", "w") or die("Unable to open file!");
        $data = "<?php ";
        fwrite($share_file, $data);
        $data = '$share_config=\'' . $share . '\';';
        fwrite($share_file, $data);
        fclose($share_file);
        self::doWebPtjshare();
    }
    public function doMobilePtjshare()
    {
        require_once('config.php');
        echo $share_config;
    }
    public function doMobilePtjperfect()
    {
        global $_W, $_GPC;
        load()->model('mc');
        load()->func('tpl');
        $avatar = '';
        $openid = $_W['openid'];
        $count  = 0;
        $uid    = $_W['member']['uid'];
        if (!empty($_W['member']['uid'])) {
            $member = mc_fetch(intval($_W['member']['uid']), array(
                'avatar',
                'nickname'
            ));
            if (!empty($member)) {
                $avatar   = $member['avatar'];
                $nickname = $member['nickname'];
            }
        }
        if (empty($avatar)) {
            $fan = mc_fansinfo($_W['openid']);
            if (!empty($fan)) {
                $avatar = $fan['avatar'];
            }
        }
        if (empty($avatar)) {
            $userinfo = mc_oauth_userinfo();
            if (!is_error($userinfo) && !empty($userinfo) && is_array($userinfo) && !empty($userinfo['avatar'])) {
                $avatar   = $userinfo['avatar'];
                $nickname = $userinfo['nickname'];
            }
        }
        if (empty($avatar) && !empty($_W['member']['uid'])) {
            $avatar = mc_require($_W['member']['uid'], array(
                'avatar'
            ));
        }
        if (empty($avatar)) {
            echo "最终没有获取到头像,follow: {$_W['fans']['follow']}";
        } else {
            $src = $avatar;
        }
        $user     = $this->DboperateSearchUser($openid);
        $profile  = $_GPC['Ptjperfect'];
        $name     = $profile['Name'];
        $phone    = $profile['Phone'];
        $location = $profile['Address'];
        $sex      = $profile['Sex'];
        $email    = $profile['Email'];
        $iden     = $profile['Usertype'];
        $Cname    = $profile['Cname'];
        $Caddress = $profile['Caddress'];
        $Cphone   = $profile['Cphone'];
        if ($this->DboperateIsWaiting($openid)) {
            $waiting = true;
        }
        if ($_GPC['postbtn'] == 'on') {
            if ($name AND $phone AND $location AND $sex AND $iden == 'owner' AND $email AND $Cname AND $Caddress AND $Cphone) {
                $this->DboperateInsertIntoProfile($openid, $name, $nickname, $phone, $sex, $location, $email, $iden, $Cname, $Caddress, $Cphone);
                mc_update($uid, array(
                    'realname' => $name,
                    'mobile' => $phone,
                    'email' => $email
                ));
                $url = $this->createMobileUrl('ptjperfect');
                echo "<script language='javascript'>
			alert('耐心等待管理员审核哦,审核成功后会通知你哒~~谢谢光顾~');
			location.href=\"$url\";
			</script>";
            } else if ($name AND $phone AND $location AND $sex AND $iden == 'worker' AND $email) {
                $this->DboperateInsertIntoProfile($openid, $name, $nickname, $phone, $sex, $location, $email, $iden);
                mc_update($uid, array(
                    'realname' => $name,
                    'mobile' => $phone,
                    'email' => $email
                ));
                $url = $this->createMobileUrl('ptjmyinfo');
                echo "<script language='javascript'>
			alert('耐心等待管理员审核哦,审核成功后会通知你哒~~谢谢光顾~');
			location.href=\"$url\";
			</script>";
            } else {
                $url = $this->createMobileUrl('ptjperfect');
                echo "<script language='javascript'>
			alert('你的信息好像哪里不符合规范~~~再试一遍 咯!');
			location.href=\"$url\";
			</script>";
            }
        }
        include $this->template('perfect');
    }
    public function doMobilePtjOwnerindex()
    {
        global $_W, $_GPC;
        $openid  = $_W['openid'];
        $user    = $this->DboperateSearchUser($_GPC['ground']['openid']);
        $OwnerEx = $this->DboperateGetworkerinfo($_GPC['ground']['openid']);
        $ground  = $_GPC['ground'];
        load()->model('mc');
        $ouid   = mc_openid2uid($_GPC['ground']['openid']);
        $member = mc_fetch(intval($ouid), array(
            'avatar',
            'nickname'
        ));
        $src    = $member['avatar'];
        $des    = $_GPC['description'];
        $last   = $_GPC['last'];
        include $this->template('ownerindex');
    }
    public function doMobilePtjWorkerinfo()
    {
        global $_W, $_GPC;
        $exists    = $_GPC['exists'];
        $ground    = $_GPC['ground'];
        $openid    = $ground['openid'];
        $jobid     = $ground['jobid'];
        $Ownerinfo = $this->DboperateSearchUser($openid);
        $url       = $this->createMobileUrl('ptjworkerinfo', array(
            'ground' => $ground
        ));
        $Wkinfo    = $this->DboperateFindWk($openid, $jobid);
        if ($_GPC['oid']) {
            $Ownerinfo = $this->DboperateSearchUser($_GPC['oid']);
            $Wkinfo    = $this->DboperateFindWk($_GPC['oid'], $_GPC['jobid']);
            $url       = $this->createMobileUrl('ptjworkerinfo', array(
                'oid' => $_GPC['oid']
            ));
            $flag      = 1;
        }
        if ($_GPC['ebutton'] == 'on') {
            $t = $this->DboperateEmploy($_GPC['wopenid'], $jobid);
            $this->SendTpl($_GPC['wopenid'], $Ownerinfo, 'admit', $ground['date']);
            echo "<script language='javascript'>
			alert('终于招到肯为你干活的了! 开心吧! ');
			location.href=\"$url\";
			</script>";
        }
        if ($_GPC['abutton'] == 'on') {
            foreach ($Wkinfo as $k => $v) {
                if ($v['status'] == 0) {
                    $All = $this->DboperateEmploy($v['wopenid'], $jobid);
                    $this->SendTpl($v['wopenid'], $Ownerinfo, 'apply', $ground['date']);
                }
            }
            echo "<script language='javascript'>
			alert('全部录用完毕了耶~');
			location.href=\"$url\";
			</script>";
        }
        if ($_GPC['sbutton'] == 'on') {
            $url = $this->createMobileUrl('ptjindex');
            $de  = $this->DboperateUndoWanna($ground['jobid']);
            echo "<script language='javascript'>
				alert('你的招募令神奇地消失了!');
				location.href=\"$url\";
				</script>";
        }
        include $this->template('workerinfo');
    }
    public function doMobilePtjmypost()
    {
        global $_W, $_GPC;
        $ground = $_GPC['ground'];
        $user   = $this->DboperateSearchUser($_W['openid']);
        if ($user['identity'] == 'worker' OR $user['identity'] == 'admin') {
            $isworker = 1;
        } else if ($user['identity'] == 'owner') {
            $isworker = 2;
        }
        include $this->template('posttype');
    }
    public function doMobilePtjpostinfo()
    {
        global $_W, $_GPC;
        load()->func('tpl');
        load()->model('mc');
        $uid         = mc_openid2uid($_W['openid']);
        $creditarray = mc_credit_fetch($uid);
        $credit      = $creditarray['credit1'];
        $ground      = $_GPC['ground'];
        $mypost      = $_GPC['Ptjpostinfo'];
        $user        = $this->DboperateSearchUser($_W['openid']);
        if ($_GPC['postbtn'] == 'on') {
            $postinfo  = $_GPC['Ptjpostinfo'];
            $starttime = $_GPC['startdate'];
            $endtime   = $_GPC['enddate'];
            $suretime  = (strtotime($starttime) <= strtotime($endtime)) ? 1 : 0;
            $title     = $mypost['title'];
            $content   = $mypost['content'];
            $salary    = $mypost['salary'];
            $mount     = $mypost['mount'];
            $type      = $mypost['wktype'];
            $limit     = $mypost['limit'];
            $privacy   = $mypost['privacy'];
            if ($limit == 'man') {
                $limit = '仅限男生';
            } else if ($limit == 'no') {
                $limit = '不限男女';
            } else if ($limit == 'woman') {
                $limit = '仅限女生';
            }
            $nowtime   = date('Y-m-d');
            $now       = date('Y-m-d H:i:s');
            $workplace = $mypost['wkplace'];
            if ($title AND $content AND $salary AND $mount AND $type AND $limit AND $nowtime AND $workplace AND $suretime) {
                if ($this->GetPrivacyNum() > 10) {
                    message('置顶招聘已满...请稍后重试!', '', 'warning');
                } else {
                    if ($privacy AND $credit >= 100) {
                        $value = mc_credit_update(mc_openid2uid($_W['openid']), 'credit1', -100);
                        $this->SendTpl($_W['openid'], $user, 'credit1', $credit - 100);
                    } else if ($privacy) {
                        message('积分不足,您无权进行此操作,请前往个人中心充值。如有问题,请与管理员联系!');
                    }
                }
                $jobid = substr(md5(substr(time(), 4, 8)), 4, 9);
                if ($this->DboperateSearchUser($_W['openid'])) {
                    $this->DboperateInsertGroundInfo($_W['openid'], $jobid, $title, $content, $salary, $mount, $starttime, $type, $limit, $endtime, $nowtime, $workplace, $privacy, $now);
                    $url = $this->createMobileUrl('ptjindex', array(
                        'ground' => $ground
                    ));
                    echo "<script language='javascript'>
        alert('招募信息发布成功,准备好接收一波鲜肉了吗!');
        location.href=\"$url\";
        </script>";
                } else {
                    $url = $this->createMobileUrl('ptjpostinfo', array(
                        'ground' => $ground
                    ));
                    echo "<script language='javascript'>
         	alert('先去完善信息哦！');
         	location.href=\"$url\";
         	</script>";
                }
            } else {
                $url = $this->createMobileUrl('ptjpostinfo', array(
                    'ground' => $ground
                ));
                echo "<script language='javascript'>
        	alert('好像哪里出错了...再来一遍呗^_^  ');
        	location.href=\"$url\";
        	</script>";
            }
        }
        include $this->template('postinfo');
    }
    public function doMobilePtjWorkerindex()
    {
        global $_W, $_GPC;
        load()->model('mc');
        $exists  = $_GPC['exists'];
        $ground  = $_GPC['ground'];
        $wopenid = $_GPC['wopenid'];
        $entry   = $_GPC['entry'];
        $wuid    = mc_openid2uid($wopenid);
        $member  = mc_fetch(intval($wuid), array(
            'avatar',
            'nickname'
        ));
        $src     = $member['avatar'];
        $winfo   = $this->DboperateSearchUser($wopenid);
        $wEx     = $this->DboperationSearchMyworkEx($wopenid);
        include $this->template('workerindex');
    }
    public function doMobilePtjmodel()
    {
        global $_W, $_GPC;
        $openid = $_W['openid'];
        $id     = $_GPC['id'];
        if ($id == 'owner') {
            $OwnerEx = $this->DboperateGetworkerinfo($_W['openid']);
        } else if ($id == 'worker') {
            $wEx = $this->DboperationSearchMyworkEx($_W['openid']);
        }
        include $this->template('usemodel');
    }
    public function doMobilePtjing()
    {
        global $_W, $_GPC;
        $ground = $_GPC['ground'];
        include $this->template('ing');
    }
    private function DboperateTopicDetail($jobid)
    {
        $detail = pdo_fetch("SELECT * FROM" . tablename('ptj_ground') . "WHERE jobid=:oid", array(
            'oid' => $jobid
        ));
        return $detail;
    }
    private function DboperateInsertIntoProfile($openid, $name, $nickname, $phone, $sex, $location, $email, $iden, $Cname = NULL, $Caddress = NULL, $Cphone = NULL)
    {
        $T = pdo_insert('ptj_profile', array(
            'openid' => $openid,
            'name' => $name,
            'nickname' => $nickname,
            'phone' => $phone,
            'location' => $location,
            'identity' => $iden,
            'count' => 0,
            'sex' => $sex,
            'email' => $email,
            'sure' => '0',
            'cname' => $Cname,
            'clocation' => $Caddress,
            'cphone' => $Cphone
        ));
        return $T;
    }
    private function DboperateSearchUser($openid)
    {
        $t = pdo_fetch("SELECT * FROM" . tablename('ptj_profile') . "WHERE openid=:oid AND sure=:su", array(
            'oid' => $openid,
            ':su' => 1
        ));
        return $t;
    }
    private function DboperateInsertGroundInfo($openid, $jobid, $title, $content, $salary, $mount, $date, $type, $limit, $stopdate, $nowtime, $workplace, $privay, $pdate)
    {
        $t     = pdo_insert('ptj_ground', array(
            'title' => $title,
            'jobid' => $jobid,
            'content' => $content,
            'salary' => $salary,
            'mount' => $mount,
            'date' => $date,
            'openid' => $openid,
            'visible' => 1,
            'type' => $type,
            'haslimit' => $limit,
            'stopdate' => $stopdate,
            'nowtime' => $nowtime,
            'workplace' => $workplace,
            'privacy' => $privay,
            'pdate' => $pdate
        ));
        $admin = pdo_fetch("SELECT * FROM" . tablename('ptj_profile') . "WHERE openid='admin'", array(), '');
        if (empty($admin)) {
            $t = pdo_insert('ptj_profile', array(
                'sure' => 1,
                'openid' => 'admin',
                'count' => 0,
                'name' => 'administrator',
                'identity' => 'admin',
                'sex' => 'Man',
                'email' => 'admin@admin.admin'
            ));
        }
        return $t;
    }
    private function DboperateSearchGroundinfo()
    {
        $ground = array();
        $Ground = pdo_fetchall("SELECT * FROM" . tablename('ptj_ground') . "WHERE visible=:vi ORDER BY privacy DESC", array(
            ':vi' => 1
        ), '');
        foreach ($Ground as $k => $v) {
            $this->RefreshPrivacy($v['pdate'], $v['jobid']);
            $oprofile = $this->DboperateSearchUser($v['openid']);
            $oname    = $oprofile['cname'] ? $oprofile['cname'] : $oprofile['name'];
            $phone    = $oprofile['cphone'] ? $oprofile['cphone'] : $oprofile['phone'];
            $exists   = $this->DboperateGetIntworkers($v['jobid']);
            if ($exists <= $v['mount']) {
                $arr = array(
                    'phone' => $phone,
                    'exists' => $exists,
                    'nowtime' => $v['nowtime'],
                    'jobid' => $v['jobid'],
                    'title' => $v['title'],
                    'content' => $v['content'],
                    'mount' => $v['mount'],
                    'salary' => $v['salary'],
                    'date' => $v['date'],
                    'openid' => $v['openid'],
                    'now' => $v['nowtime'],
                    'haslimit' => $v['haslimit'],
                    'stopdate' => $v['stopdate'],
                    'workplace' => $v['workplace'],
                    'cname' => $oname,
                    'privacy' => $v['privacy']
                );
                array_push($ground, $arr);
            }
        }
        return $ground;
    }
    private function DboperateSearchTopicinfo($order, $search)
    {
        $ground     = array();
        $flag       = 0;
        $sortcolumn = "";
        $where      = "1=1";
        if ($order == 'exists') {
            $flag       = 1;
            $sortcolumn = "exists";
            $order      = 'privacy DESC';
        } elseif ($order == 'exists DESC') {
            $flag       = 2;
            $sortcolumn = "exists";
            $order      = 'privacy DESC';
        } elseif ($order == 'cname') {
            $flag       = 1;
            $sortcolumn = "cname";
            $order      = 'privacy DESC';
        } elseif ($order == 'cname DESC') {
            $flag       = 2;
            $sortcolumn = "cname";
            $order      = 'privacy DESC';
        }
        if ($search['privacy'] != "" && $search['privacy'] != "2") {
            $where = 'privacy=' . $search['privacy'];
        }
        if ($search['title'] != "") {
            if ($where == '1=1') {
                $where = 'title LIKE "%' . $search['title'] . '%"';
            } else {
                $where .= ' AND title LIKE "%' . $search['title'] . '%"';
            }
        }
        if ($search['cname'] != "") {
            if ($where == '1=1') {
                $where = '(name LIKE "%' . $search['cname'] . '%"  AND cname = "") OR (cname LIKE "%' . $search['cname'] . '%")';
            } else {
                $where .= ' AND ((name LIKE "%' . $search['cname'] . '%" AND cname = "") OR (cname LIKE "%' . $search['cname'] . '%"))';
            }
        }
        if ($search['visible'] != "" && $search['visible'] != "2") {
            if ($where == '1=1') {
                $where = 'visible=' . $search['visible'];
            } else {
                $where .= ' AND visible=' . $search['visible'];
            }
        }
        $Ground = pdo_fetchall("SELECT * FROM" . tablename('ptj_ground') . "AS A LEFT JOIN" . tablename("ptj_profile") . "AS B on A.openid = B.openid WHERE " . $where . " ORDER BY " . $order, array(), '');
        foreach ($Ground as $k => $v) {
            $this->RefreshPrivacy($v['pdate'], $v['jobid']);
            $oprofile = $this->DboperateSearchUser($v['openid']);
            $oname    = $oprofile['cname'] ? $oprofile['cname'] : $oprofile['name'];
            $exists   = $this->DboperateGetIntworkers($v['jobid']);
            $arr      = array(
                'vis' => $v['visible'],
                'exists' => $exists,
                'nowtime' => $v['nowtime'],
                'jobid' => $v['jobid'],
                'title' => $v['title'],
                'content' => $v['content'],
                'mount' => $v['mount'],
                'salary' => $v['salary'],
                'date' => $v['date'],
                'openid' => $v['openid'],
                'now' => $v['nowtime'],
                'limit' => $v['limit'],
                'stopdate' => $v['stopdate'],
                'workplace' => $v['workplace'],
                'cname' => $oname,
                'privacy' => $v['privacy']
            );
            array_push($ground, $arr);
        }
        if ($flag == 1) {
            for ($i = 0; $i < count($ground); $i++) {
                for ($j = $i; $j < count($ground); $j++) {
                    if ($ground[$i][$sortcolumn] < $ground[$j][$sortcolumn]) {
                        $temp       = $ground[$j];
                        $ground[$j] = $ground[$i];
                        $ground[$i] = $temp;
                    }
                }
            }
        } elseif ($flag == 2) {
            for ($i = 0; $i < count($ground); $i++) {
                for ($j = $i; $j < count($ground); $j++) {
                    if ($ground[$i][$sortcolumn] > $ground[$j][$sortcolumn]) {
                        $temp       = $ground[$j];
                        $ground[$j] = $ground[$i];
                        $ground[$i] = $temp;
                    }
                }
            }
        }
        return $ground;
    }
    private function DboperateSurepostinfo($owneroid, $workoid, $jobid, $hadmount)
    {
        $t = pdo_insert('ptj_working', array(
            'owneroid' => $owneroid,
            'workeroid' => $workoid,
            'jobid' => $jobid,
            'hadmount' => $hadmount,
            'visible' => 1,
            'sure' => 0
        ));
        return $t;
    }
    private function DboperateCheckUser($openid)
    {
        $T = pdo_fetch("SELECT * FROM" . tablename('ptj_profile') . "WHERE openid=:oid AND sure=:su", array(
            'oid' => $openid,
            ':su' => 1
        ));
        return $T;
    }
    private function DboperateGetjob($wopenid, $oopenid)
    {
        $arr = array();
        $t   = pdo_fetchall("SELECT jobid FROM" . tablename('ptj_working') . "WHERE workeroid=:oid and owneroid=:ooid ", array(
            ':oid' => $wopenid,
            ':ooid' => $oopenid
        ), '');
        foreach ($t as $k => $v) {
            array_push($arr, $v['jobid']);
        }
        return $arr;
    }
    private function DboperateSearchGround($info, $salary, $time, $owner, $type)
    {
        $ground = array();
        if ($salary) {
            $t = pdo_fetchall("SELECT * FROM" . tablename('ptj_ground') . "WHERE title REGEXP :ti AND WHERE visible=:vi ORDER BY salary DESC", array(
                ':ti' => $info,
                ':vi' => 1
            ), '');
        } else if ($time) {
            $t = pdo_fetchall("SELECT * FROM" . tablename('ptj_ground') . "WHERE title REGEXP :ti AND WHERE visible=:vi ORDER BY date ", array(
                ':ti' => $info,
                ':vi' => 1
            ), '');
        } else if ($owner) {
            $t = pdo_fetchall("SELECT * FROM" . tablename('ptj_ground') . "WHERE title REGEXP :ti AND WHERE visible=:vi ORDER BY openid DESC", array(
                ':ti' => $info,
                ':vi' => 1
            ), '');
        } else if ($type) {
            $t = pdo_fetchall("SELECT * FROM" . tablename('ptj_ground') . "WHERE type=:ti  AND  visible=:vi ORDER BY privacy DESC", array(
                ':ti' => $info,
                ':vi' => 1
            ), '');
        } else {
            $t = pdo_fetchall("SELECT * FROM" . tablename('ptj_ground') . "WHERE title REGEXP :ti AND visible=:vi ORDER BY privacy DESC", array(
                ':ti' => $info,
                ':vi' => 1
            ), '');
        }
        foreach ($t as $k => $v) {
            $exists   = $this->DboperateGetIntworkers($v['jobid']);
            $oprofile = $this->DboperateSearchUser($v['openid']);
            $oname    = $oprofile['cname'] ? $oprofile['cname'] : $oprofile['name'];
            $arr      = array(
                'exists' => $exists,
                'now' => $v['nowtime'],
                'jobid' => $v['jobid'],
                'title' => $v['title'],
                'content' => $v['content'],
                'mount' => $v['mount'],
                'salary' => $v['salary'],
                'date' => $v['date'],
                'openid' => $v['openid'],
                'now' => $v['nowtime'],
                'limit' => $v['limit'],
                'stopdate' => $v['stopdate'],
                'workplace' => $v['workplace'],
                'vis' => $v['visible'],
                'cname' => $oname,
                'privacy' => $v['privacy']
            );
            array_push($ground, $arr);
        }
        return $ground;
    }
    private function DboperationSearchMyworkEx($workeroid)
    {
        $check = pdo_fetchall("SELECT * FROM" . tablename('ptj_working') . "WHERE workeroid=:woid", array(
            ':woid' => $workeroid
        ));
        foreach ($check as $key => $value) {
            $this->Dboperatechecktime($value['jobid'], date('Y-m-d H:i:s'), $value['workeroid']);
        }
        $arr = array();
        $T   = pdo_fetchall("SELECT jobid,sure,visible FROM" . tablename('ptj_working') . "WHERE workeroid=:woid", array(
            ':woid' => $workeroid
        ));
        foreach ($T as $k => $v) {
            $jobid   = $v['jobid'];
            $T1      = pdo_fetch("SELECT * FROM" . tablename('ptj_ground') . "WHERE jobid=:jid", array(
                ':jid' => $jobid
            ));
            $profile = $this->DboperateSearchUser($T1['openid']);
            $Arr     = array(
                'cname' => $profile['cname'],
                'name' => $profile['name'],
                'jobid' => $jobid,
                'title' => $T1['title'],
                'content' => $T1['content'],
                'now' => $T1['nowtime'],
                'status' => $v['sure'],
                'vis' => $v['visible']
            );
            array_push($arr, $Arr);
        }
        return $arr;
    }
    private function DboperateGetIntworkers($jobid)
    {
        $C = pdo_fetch("SELECT COUNT(*) AS count FROM" . tablename('ptj_working') . "WHERE jobid=:jid", array(
            ':jid' => $jobid
        ));
        return $C['count'];
    }
    private function DboperateSorybysalary()
    {
        $ground = array();
        $t      = pdo_fetchall("SELECT * FROM" . tablename('ptj_ground') . "WHERE visible=:vi ORDER BY salary DESC", array(
            ':vi' => 1
        ), '');
        foreach ($t as $k => $v) {
            $exists = $this->DboperateGetIntworkers($v['jobid']);
            $arr    = array(
                'exists' => $exists,
                'jobid' => $v['jobid'],
                'title' => $v['title'],
                'content' => $v['content'],
                'mount' => $v['mount'],
                'salary' => $v['salary'],
                'date' => $v['date'],
                'openid' => $v['openid']
            );
            array_push($ground, $arr);
        }
        return $ground;
    }
    private function DboperateSortbytime()
    {
        $ground = array();
        $t      = pdo_fetchall("SELECT * FROM" . tablename('ptj_ground') . "WHERE visible=:vi ORDER BY date ", array(
            ':vi' => 1
        ), '');
        foreach ($t as $k => $v) {
            $exists = $this->DboperateGetIntworkers($v['jobid']);
            $arr    = array(
                'exists' => $exists,
                'jobid' => $v['jobid'],
                'title' => $v['title'],
                'content' => $v['content'],
                'mount' => $v['mount'],
                'salary' => $v['salary'],
                'date' => $v['date'],
                'openid' => $v['openid']
            );
            array_push($ground, $arr);
        }
        return $ground;
    }
    private function DboperateSortbyownerid()
    {
        $ground = array();
        $t      = pdo_fetchall("SELECT * FROM" . tablename('ptj_ground') . "WHERE visible=:vi ORDER BY openid DESC", array(
            ':vi' => 1
        ), '');
        foreach ($t as $k => $v) {
            $exists = $this->DboperateGetIntworkers($v['jobid']);
            $arr    = array(
                'exists' => $exists,
                'jobid' => $v['jobid'],
                'title' => $v['title'],
                'content' => $v['content'],
                'mount' => $v['mount'],
                'salary' => $v['salary'],
                'date' => $v['date'],
                'openid' => $v['openid']
            );
            array_push($ground, $arr);
        }
        return $ground;
    }
    private function Dboperatechecktime($jobid, $now, $openid)
    {
        $jobtime = pdo_fetch("select stopdate FROM" . tablename('ptj_ground') . "WHERE jobid=:jid", array(
            ':jid' => $jobid
        ));
        if (strtotime($jobtime['stopdate']) <= strtotime($now)) {
            $i = pdo_update('ptj_working', array(
                'visible' => 0
            ), array(
                'workeroid' => $openid,
                'jobid' => $jobid
            ));
        }
        return $i;
    }
    private function DboperateCheckGroundTime($jobid, $date, $now)
    {
        if (strtotime($date) <= strtotime($now)) {
            $t = pdo_update('ptj_ground', array(
                'visible' => 0
            ), array(
                'jobid' => $jobid
            ));
        }
        return $t;
    }
    private function Dboperatepushcomment($comment, $jobid, $workeroid)
    {
        $T = pdo_insert('ptj_comment', array(
            'jobid' => $jobid,
            'comment' => $comment,
            'workerid' => $workeroid
        ));
        return $T;
    }
    private function Dboperateshowcomment($jobid)
    {
        $info = pdo_fetchall("SELECT * FROM" . tablename('ptj_comment') . "WHERE jobid=:jid", array(
            ':jid' => $jobid
        ), '');
        return $info;
    }
    private function DboperateGetworkerinfo($openid)
    {
        $ground = array();
        $Ground = pdo_fetchall("SELECT * FROM" . tablename('ptj_ground') . "WHERE openid=:oid", array(
            ':oid' => $openid
        ), '');
        foreach ($Ground as $k => $v) {
            $exists = $this->DboperateGetIntworkers($v['jobid']);
            $arr    = array(
                'exists' => $exists,
                'jobid' => $v['jobid'],
                'title' => $v['title'],
                'content' => $v['content'],
                'mount' => $v['mount'],
                'salary' => $v['salary'],
                'date' => $v['date'],
                'openid' => $v['openid'],
                'now' => $v['nowtime'],
                'limit' => $v['limit'],
                'stopdate' => $v['stopdate'],
                'workplace' => $v['workplace'],
                'vis' => $v['visible']
            );
            array_push($ground, $arr);
        }
        return $ground;
    }
    private function DboperateUndoWanna($jobid)
    {
        $t1 = pdo_update('ptj_ground', array(
            'visible' => 0
        ), array(
            'jobid' => $jobid
        ));
        return $t1;
    }
    private function DboperateFindWk($ownerid, $jobid)
    {
        $Allarray = array();
        $info     = pdo_fetchall("SELECT * FROM" . tablename('ptj_working') . "WHERE owneroid=:oid and jobid=:jid", array(
            ':oid' => $ownerid,
            ':jid' => $jobid
        ), '');
        foreach ($info as $k => $v) {
            $winfo   = pdo_fetch("SELECT * FROM" . tablename('ptj_profile') . "WHERE openid=:oid", array(
                ':oid' => $v['workeroid']
            ));
            $speinfo = pdo_fetch("SELECT * FROM" . tablename('ptj_ground') . "WHERE jobid=:jid", array(
                ':jid' => $v['jobid']
            ));
            $wkinfo  = array(
                'wkinfo' => $speinfo['title'],
                'wkphone' => $winfo['phone'],
                'wksex' => $winfo['sex'],
                'wopenid' => $winfo['openid'],
                'wkname' => $winfo['name'],
                'wklocation' => $winfo['location'],
                'status' => $v['sure'],
                'wopenid' => $v['workeroid']
            );
            array_push($Allarray, $wkinfo);
        }
        return $Allarray;
    }
    private function DboperateEmploy($wopenid, $jobid)
    {
        $t = pdo_update('ptj_working', array(
            'sure' => 1
        ), array(
            'workeroid' => $wopenid,
            'jobid' => $jobid
        ));
        return $t;
    }
    private function RefreshPrivacy($time, $jobid)
    {
        if (strtotime(date('Y-m-d H:i:s')) - strtotime($time) >= strtotime("+48 hours")) {
            pdo_update('ptj_ground', array(
                'privacy' => 0,
                'pdate' => NULL
            ), array(
                'jobid' => $jobid
            ));
        } else {
            return TRUE;
        }
    }
    private function upimgurl($imginfo, $aurl)
    {
        $IMG = array();
        foreach ($imginfo as $k => $v) {
            if ($v['imgurl'] == $aurl) {
                $v['imgurl'] = NULL;
            }
            array_push($IMG, $v['imgurl']);
        }
        return $IMG;
    }
    private function GetPrivacyNum()
    {
        $info = pdo_fetchall("SELECT COUNT(*) as num FROM" . tablename('ptj_ground') . "WHERE visible=:vi and privacy=:pr", array(
            ':vi' => 1,
            ':pr' => 1
        ));
        return $info['num'];
    }
    private function Checkifadmit($jid, $oid)
    {
        $info = pdo_fetch("SELECT sure FROM" . tablename('ptj_working') . "WHERE jobid=:jid and workeroid=:oid", array(
            ':jid' => $jid,
            ':oid' => $oid
        ));
        return $info['sure'];
    }
    private function ifAllSure($jobid)
    {
        $workingStatus = pdo_fetch("SELECT COUNT(*) as count FROM" . tablename('ptj_working') . "WHERE jobid=:jid", array(
            ':jid' => $jobid
        ));
        return $workingStatus['count'];
    }
    private function DboperateGetAllProfile()
    {
        $info = pdo_fetchall("SELECT * FROM" . tablename('ptj_profile') . "ORDER BY id DESC", array(), '');
        return $info;
    }
    private function DboperateAdminSurePro($id)
    {
        $t = pdo_update('ptj_profile', array(
            'sure' => 1
        ), array(
            'id' => $id
        ));
        return $t;
    }
    private function DboperateIsWaiting($openid)
    {
        $info = pdo_fetch("SELECT * FROM" . tablename('ptj_profile') . "WHERE openid=:oid", array(
            ':oid' => $openid
        ));
        if ($info['sure'] == 1) {
            return false;
        } else if ($info AND $info['sure'] == 0) {
            return true;
        } else {
            return false;
        }
    }
    private function DboperateDeleteUserInfo($id)
    {
        $t = pdo_delete('ptj_profile', array(
            'id' => $id
        ));
        return $t;
    }
    private function SendTpl($openid, $info, $type, $res)
    {
        load()->func('communication');
        require_once 'template_conf_ap.php';
        require_once 'template_conf_auth.php';
        require_once 'template_conf_re.php';
        require_once 'template_conf_cd.php';
        require_once 'template_conf_fl.php';
        global $_W;
        $access_token = WeAccount::token();
        $apply        = json_decode($apply, true);
        $auth         = json_decode($auth, true);
        $refu         = json_decode($refu, true);
        $credit       = json_decode($credit, true);
        $full         = json_decode($full, true);
        $url          = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $access_token;
        if ($type == 'apply') {
            $data = array(
                'touser' => $openid,
                'template_id' => $apply['apply_tid'],
                'url' => $_W['siteroot'] . 'app/index.php?i=1&c=entry&do=ptjindex&m=hypernet_iptj',
                'topcolor' => '#FF0000',
                'data' => array(
                    'first' => array(
                        'value' => $apply['apply_first'],
                        'color' => $apply['apply_fcolor']
                    ),
                    'keynote1' => array(
                        'value' => $apply['apply_keynote1'],
                        'color' => $apply['apply_kfcolor']
                    ),
                    'keynote2' => array(
                        'value' => '请联系商家确认....',
                        'color' => '#000'
                    ),
                    'remark' => array(
                        'value' => $apply['apply_remark'],
                        'color' => $apply['apply_mkcolor']
                    )
                )
            );
        } else if ($type == 'auth') {
            $date = date('Y-m-d H:i');
            $data = array(
                'touser' => $openid,
                'template_id' => $auth['auth_tid'],
                'url' => $_W['siteroot'] . 'app/index.php?i=1&c=entry&do=ptjindex&m=hypernet_iptj',
                'topcolor' => '#FF0000',
                'data' => array(
                    'first' => array(
                        'value' => $auth['auth_first'],
                        'color' => $apply['auth_first']
                    ),
                    'keyword1' => array(
                        'value' => $info['name'],
                        'color' => '#000'
                    ),
                    'keyword2' => array(
                        'value' => $info['phone'],
                        'color' => '#000'
                    ),
                    'keyword3' => array(
                        'value' => $date,
                        'color' => '#000'
                    ),
                    'remark' => array(
                        'value' => $auth['auth_remark'],
                        'color' => $auth['auth_mkcolor']
                    )
                )
            );
        } else if ($type == 'refuse') {
            $date = date('Y-m-d H:i');
            $data = array(
                'touser' => $openid,
                'template_id' => $refu['refu_tid'],
                'url' => $_W['siteroot'] . 'app/index.php?i=1&c=entry&do=ptjindex&m=hypernet_iptj',
                'topcolor' => '#FF0000',
                'data' => array(
                    'first' => array(
                        'value' => $refu['refu_first'],
                        'color' => $refu['refu_fcolor']
                    ),
                    'keyword1' => array(
                        'value' => $info['name'],
                        'color' => '#000'
                    ),
                    'keyword2' => array(
                        'value' => $info['phone'],
                        'color' => '#000'
                    ),
                    'keyword3' => array(
                        'value' => $date,
                        'color' => '#000'
                    ),
                    'remark' => array(
                        'value' => $refu['refu_remark'],
                        'color' => $refu['refu_mkcolor']
                    )
                )
            );
        } else if ($type == 'credit1') {
            $date = date('Y-m-d H:i:s');
            $data = array(
                'touser' => $openid,
                'template_id' => $credit['credit_tid'],
                'url' => $_W['siteroot'] . 'app/index.php?i=1&c=entry&do=ptjindex&m=hypernet_iptj',
                'topcolor' => '#FF0000',
                'data' => array(
                    'first' => array(
                        'value' => $credit['credit_first'],
                        'color' => $credit['credit_fcolor']
                    ),
                    'account' => array(
                        'value' => $info['name'],
                        'color' => '#000'
                    ),
                    'time' => array(
                        'value' => $date,
                        'color' => '#000'
                    ),
                    'type' => array(
                        'value' => $credit['credit_type'],
                        'color' => $credit['credit_tpcolor']
                    ),
                    'creditChange' => array(
                        'value' => '支出',
                        'color' => '#000'
                    ),
                    'number' => array(
                        'value' => '100',
                        'color' => '#000'
                    ),
                    'creditName' => array(
                        'value' => '账户积分',
                        'color' => '#000'
                    ),
                    'amount' => array(
                        'value' => $res,
                        'color' => '#000'
                    ),
                    'remark' => array(
                        'value' => $credit['credit_remark'],
                        'color' => $credit['crefit_mkcolor']
                    )
                )
            );
        } else if ($type == 'full') {
            $data = array(
                'touser' => $openid,
                'template_id' => $full['full_tid'],
                'url' => $_W['siteroot'] . 'app/index.php?i=1&c=entry&do=ptjmyinfo&m=hypernet_iptj',
                'topcolor' => '#FF0000',
                'data' => array(
                    'first' => array(
                        'value' => $full['full_first'],
                        'color' => $full['full_fcolor']
                    ),
                    'keyword1' => array(
                        'value' => $info['title'],
                        'color' => '#000'
                    ),
                    'keyword2' => array(
                        'value' => '待办',
                        'color' => 'red'
                    ),
                    'remark' => array(
                        'value' => $full['full_remark'],
                        'color' => $full['full_mkcolor']
                    )
                )
            );
        } else if ($type == 'first_third') {
            $data = array(
                'touser' => $openid,
                'template_id' => $full['full_tid'],
                'url' => $_W['siteroot'] . 'app/index.php?i=1&c=entry&do=ptjmyinfo&m=hypernet_iptj',
                'topcolor' => '#FF0000',
                'data' => array(
                    'first' => array(
                        'value' => '请尽快去录取应聘者，完成本次招聘。',
                        'color' => '#000'
                    ),
                    'keyword1' => array(
                        'value' => $info['title'],
                        'color' => '#000'
                    ),
                    'keyword2' => array(
                        'value' => '待办',
                        'color' => 'red'
                    ),
                    'remark' => array(
                        'value' => '招聘帖子人数有人啦,请速去验收.',
                        'color' => '#0542FA'
                    )
                )
            );
        } else if ($type == 'second_third') {
            $data = array(
                'touser' => $openid,
                'template_id' => $full['full_tid'],
                'url' => $_W['siteroot'] . 'app/index.php?i=1&c=entry&do=ptjmyinfo&m=hypernet_iptj',
                'topcolor' => '#FF0000',
                'data' => array(
                    'first' => array(
                        'value' => '请尽快去录取应聘者，完成本次招聘。',
                        'color' => '#000'
                    ),
                    'keyword1' => array(
                        'value' => $info['title'],
                        'color' => '#000'
                    ),
                    'keyword2' => array(
                        'value' => '待办',
                        'color' => 'red'
                    ),
                    'remark' => array(
                        'value' => '招聘帖子人数快满了,请速去验收.',
                        'color' => '#0542FA'
                    )
                )
            );
        } else if ($type == 'admit') {
            $data = array(
                'touser' => $openid,
                'template_id' => $apply['apply_tid'],
                'url' => $_W['siteroot'] . 'app/index.php?i=1&c=entry&do=ptjindex&m=hypernet_iptj',
                'topcolor' => '#FF0000',
                'data' => array(
                    'first' => array(
                        'value' => 'congrats!你已被' . $info['name'] . '录取',
                        'color' => '#FF0000'
                    ),
                    'keynote1' => array(
                        'value' => $info['name'] . '欢迎你的加入~',
                        'color' => '#000'
                    ),
                    'keynote2' => array(
                        'value' => '请联系商家确认....',
                        'color' => '#000'
                    ),
                    'remark' => array(
                        'value' => '要愉快地工作哦~~~~',
                        'color' => '#000'
                    )
                )
            );
        }
        return ihttp_post($url, json_encode($data));
    }
}