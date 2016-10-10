<?php
/**
 */
defined('IN_IA') or exit ('Access Denied');
class Zombie_fightingModuleSite extends WeModuleSite
{
    public $tablename = 'fighting_setting';

    //
    public function doMobileIndex()  {
        global $_GPC, $_W;
        //   $this->doCheckedMobile();
        // $this->doCheckedParam();
        $id = intval($_GPC['id']);
        $weid = $_W['uniacid'];
		
        $flight_setting=pdo_fetch("SELECT * FROM " . tablename('fighting_setting') . " WHERE rid = '$id' LIMIT 1");
        if (empty($flight_setting)) {
            message('非法访问，请重新发送消息进入一战到底页面！');
        }

        if (time() < $flight_setting['start']) {//未开始
            message($flight_setting['title'].'活动还未开始，请关注其他活动吧。');
        } elseif ((time() > $flight_setting['end']) || ($flight_setting['status'] == 2)) {//活动已结束时回复语
            message($flight_setting['title'].'活动活动已结束，请关注其他活动吧。');
        } elseif ($flight_setting['status'] == 1) {//暂停
            message($flight_setting['title'].'活动活动已暂停，请关注其他活动吧。');
        }
		load()->model('account');
        $_W['account'] = account_fetch($_W['uniacid']);
		$followed = !empty($_W['openid']);
        if ($followed) {
            $mf = pdo_fetch("select follow from " . tablename('mc_mapping_fans') . " where openid=:openid limit 1", array(":openid" => $_W['openid']));
            $followed = $mf['follow'] == 1;
        } 
        if(!$followed){
			 $followurl = $flight_setting['followurl']; 
            header("location:$followurl");
		}
        $openid = $_W['openid'];
        $user = fans_search($openid, array('nickname', 'mobile'));
        $userinfo=1;
        if (empty($user['nickname']) || empty($user['mobile'])) { //注册
            $userinfo = 0;
        }
        $starturl= $_W['siteroot']."app/".substr($this->createMobileUrl('start',array('id' => $id,'openid'=>$openid),true), 2);
        //$worngurl= $_W['siteroot']."app/".substr($this->createMobileUrl('worng',array('id' => $id,'openid'=>$openid),true), 2);
        /*$fighting = pdo_fetch("SELECT * FROM " . tablename('fighting') . " WHERE `from_user`=:from_user AND `fid`=" . $flight_setting['id'] . " ORDER BY id DESC LIMIT 1", array (':from_user' => $fromuser ));
        $updateData = array (
            'answerNum' => 0,
        );
        pdo_update('fighting', $updateData, array ( 'id' => $fighting['id'] ));*/

        include $this->template('start');
    }

    //注册
    public function doMobileMregister()  {
        global $_GPC, $_W;
        $fid = intval($_GPC['fid']);
        $flight_setting = pdo_fetch("SELECT * FROM " . tablename('fighting_setting') . " WHERE rid = '$fid' LIMIT 1");
        if (empty($flight_setting)) {
            message('非法访问，请重新发送消息进入页面！');
        }
        $fromuser = $_W['fans']['from_user'];
        if (empty($fromuser)) {
            $fromuser = $_GPC['openid'];
        }

        $data = array(
            'nickname' => $_GPC['nickname'],
            'mobile' => $_GPC['mobile'], 
        );

        if (empty($data['nickname'])) {
            return $this->fightJson(-1, '请填写您的昵称！');
            exit;
        }
        if (empty($data['mobile'])) {
            return $this->fightJson(-1, '请填写您的手机号码！');
            exit;
        }

        fans_update($fromuser, array('nickname' => $_GPC['nickname'], 'mobile' => $_GPC['mobile']));
        $p = pdo_fetch("SELECT * FROM ".tablename('fighting_user')." WHERE openid='".$fromuser."' AND fid=".$fid);
		$insert1 = array(
            'weid' => $_W['uniacid'],
            'fid' => $fid,
            'openid' => $fromuser,
            'nickname' =>$_GPC['nickname'],
            'mobile' => $_GPC['mobile'],
        );
		if(!empty($p['id'])){
			$insert1['id'] = $p['id'];
            pdo_update('fighting_user',$insert1,array('id'=>$p['id']));
		}else{ 
			$add = pdo_insert('fighting_user', $insert1);  
		}
		return $this->fightJson(1, '');
		exit;
		
    }
 
    //开始答题
    public function doMobileStart(){
		//
        global $_GPC, $_W;
        //  $this->doCheckedMobile();
        // $this->doCheckedParam();
        $weid = $_W['uniacid'];
		
        $year = ((int)date('Y', time())); //取得年份
        $month = ((int)date('m', time())); //取得月份
        $day = ((int)date('d', time())); //取得几号
        $start = ((int)mktime(0, 0, 0, $month, $day, $year));
        $id = intval($_GPC['id']);
        $flight_setting = pdo_fetch("SELECT * FROM " . tablename('fighting_setting') . " WHERE rid = '$id' LIMIT 1");
        if (empty($flight_setting)) {
            message('非法访问，请重新发送消息进入一战到底页面！');
        }
         $openid = $_GPC['openid'];
		load()->model('account');
        $_W['account'] = account_fetch($_W['uniacid']);
			
		$followed = !empty($_GPC['openid']);
        if ($followed) {
            $mf = pdo_fetch("select follow from " . tablename('mc_mapping_fans') . " where openid=:openid limit 1", array(":openid" => $_GPC['openid']));
            $followed = $mf['follow'] == 1;
        } 
        if(!$followed){
			$followurl = $flight_setting['followurl'];
            header("location:$followurl");
		}
          
        $fighting=pdo_fetch("SELECT * FROM ".tablename('fighting')." WHERE `from_user`=:from_user AND `fid`=" . $flight_setting['id'] . " ORDER BY id DESC LIMIT 1", array(':from_user' => $openid));
        if(empty($fighting)){
			$answerNum=0;
		}else{
			$answerNum=$fighting['answerNum'];
		} 

		$linkUrl = $_W['siteroot'].'app/'.$this->createMobileUrl('start', array('id'=>$id,'wid'=>$openid),true);

        $question = pdo_fetch("SELECT * FROM ims_fighting_question_bank where  weid='{$weid}'  AND sid =" . $flight_setting['id'] . " ORDER BY RAND() LIMIT 1");//调用题目
        $an_arr = $question['answer'];//正确答案
        //是否已经答题
		$ds=pdo_fetchall("SELECT *  FROM `ims_fighting` WHERE weid =$weid AND fid =$flight_setting[id] ORDER BY lastcredit DESC  LIMIT 0 , 10");
		$sql_fighting = "SELECT  B.lastcredit ,(SELECT COUNT(1) +1 FROM `ims_fighting` A WHERE A.lastcredit > B.lastcredit )PM FROM `ims_fighting` B WHERE  B.fid ='$flight_setting[id]' and B.weid =$weid  AND B.from_user='{$openid}' ORDER BY PM ,B.lastcredit ";
        $theone = pdo_fetch($sql_fighting);
        $total = pdo_fetchcolumn('SELECT count(id) as total FROM '.tablename('fighting').' WHERE fid= :fid group by `fid` desc ',array(':fid' => $flight_setting['id']));
        if ($theone['PM'] == 1 && $total == 1) {
            $percent = round((($theone['PM']) / $total) * 100, 2);
        } else {
            $percent = round((($total - $theone['PM']) / $total) * 100, 2);
        }
 
        if ((time() > $flight_setting['end']) || ($flight_setting['status_fighting'] == 2)) { //活动已结束时回复语
			include $this->template('ranking');
            exit;
        }
        if ($fighting['answerNum'] == $flight_setting['qnum']) {
			include $this->template('ranking');
			exit;
        }

        if ($fighting['lasttime'] >= $start) {
            if ($flight_setting['is_shared'] == '1') { //是否开启分享 如果已经分享了 则直接到 排名页面
                include $this->template('shareing');
                exit;
            } else { //0 不需要直接到 排名
                include $this->template('ranking');
                exit;
            }
        }
        include $this->template('exam');
        exit;
    }

     //获取
    public function doMobileGetAnswer(){
        global $_GPC, $_W;
        $fid = intval($_GPC['fid']);
        $weid = $_W['uniacid'];
        $flight_setting = pdo_fetch("SELECT * FROM " . tablename('fighting_setting') . " WHERE id = '$fid' LIMIT 1");
        if (empty($flight_setting)) {
            message('非法访问，请重新发送消息进入一战到底页面！');
        }

        load()->model('mc');
        load()->func('compat.biz');
        $openid = $_W['openid'];
        $user = fans_search($openid, array('nickname', 'mobile','credit1'));
        if (!empty($user)) {
            $credit = $user['credit1'];
        }
        $qid = intval($_GPC['qestionid']);
        $answer = $_GPC['answer'];
        $answerNum =$_GPC['answerNum'];
        $sql_fighting = pdo_fetch("SELECT * FROM " . tablename('fighting') . " WHERE `from_user`=:from_user AND `fid`=:fid ORDER BY id DESC LIMIT 1", array(':from_user' => $fromuser, ':fid' => $fid));
        $question = pdo_fetch("SELECT * FROM ".tablename('fighting_question_bank')." WHERE id = '$qid'");

        $isupdate = pdo_fetch("SELECT * FROM ".tablename('fighting')." WHERE fid = ".$fid." and from_user='".$openid."'");

        if ($answer == $question['answer']) { //正确答案
            $figure = intval($question['figure']);
            if($isupdate == false) {
                $insert1 = array(
                    'weid' =>$weid,
                    'fid' => $fid,
                    'answerNum' => $answerNum+1,
                    'from_user' => $openid,
                    'nickname' => $user['nickname'],
                    'lastcredit' => $figure,
                );
                $add = pdo_insert('fighting', $insert1);
                $flightid = pdo_insertid();
                $awn=$insert1['$answerNum'];
                if ($awn>=$flight_setting['qnum']) {
                    $updateData = array(
                        'lasttime' => time(),
                        'answerNum' => 0,
                    );
                    pdo_update('fighting', $updateData, array('id' => $flightid));
                    return $this->fightJson(3, '');
                    exit;
                }
            } else {
                $updateData = array(
                    'answerNum' => $isupdate['answerNum'] + 1,
                    'lastcredit' => $isupdate['lastcredit'] + $figure,
                );
                pdo_update('fighting', $updateData, array('id' => $isupdate['id']));

                $awn=$updateData['answerNum'];
                if ($awn>=$flight_setting['qnum']) {
                    $updateData = array(
                        'lasttime' => time(),
                        'lastcredit' => $isupdate['lastcredit'] + $figure,
                        'answerNum' => 0,
                    );
                    pdo_update('fighting', $updateData, array('id' => $isupdate['id']));
                    return $this->fightJson(1,$answerNum);
                    exit;
                }
            }
            pdo_update('mc_members', array("credit1" => $credit + $figure), array('uid' => $uid,'uniacid'=>$weid));
            return $this->fightJson(1, '');
            exit;
        } else {
            if ($isupdate == false) {
                $insert1 = array(
                    'weid' =>$weid,
                    'fid' => $fid,
                    'answerNum' => $answerNum+1,
                    'from_user' => $openid,
                    'nickname' => $user['nickname'],
                    'lastcredit' => 0,
                );
                $addworng = pdo_insert('fighting', $insert1);
                $flightid = pdo_insertid();
                $awn=$insert1['answerNum'];
                if ($awn >= $flight_setting['qnum']) {
                    $updateData = array(
                        'lasttime' => time(),
                        'answerNum' => 0,
                    );
                    pdo_update('fighting', $updateData, array('id' => $flightid));
                    return $this->fightJson(3, '答题满了');
                    exit;
                }else{
                    return $this->fightJson(2, $question[answer]);
                    exit;
                }
            } else {
                $updateData = array('answerNum' => $isupdate['answerNum']+1);
                pdo_update('fighting', $updateData, array('id'=>$isupdate['id']));

                $awn=$updateData['answerNum'];
                if ($awn >= $flight_setting['qnum']) {
                    $updateData2 = array(
                        'lasttime' => time(),
                        'answerNum' => 0,
                    );
                    pdo_update('fighting', $updateData2, array('id'=>$isupdate['id']));
                    return $this->fightJson(3, '答题满了');
                    exit;
                }else{
                    return $this->fightJson(2, $question[answer]);
                    exit;
                }
            }
            //错误答案 回看答错的题目 $answer fighting_question_worng
            $insertworng = array(
                'weid' => $_W['uniacid'],
                'fightingid' => $isupdate['id'],
                'wornganswer' => $answer ? $answer : '超时没选择答案',
                'qname' => $question['question'],
                'answer' => $question['answer'],
                'optionA' => $question['optionA'],
                'optionB' => $question['optionB'],
                'optionC' => $question['optionC'],
                'optionD' => $question['optionD'],
                'optionE' => $question['optionE'],
                'optionF' => $question['optionF'],
            );
            pdo_insert('fighting_question_worng', $insertworng);
            return $this->fightJson(2, '答案错误');
            exit;
        }
    }


    //排行页面
    public function doMobileRank(){
        global $_GPC, $_W;
        $id = intval($_GPC['id']);
        $year = ((int)date('Y', time())); //取得年份
        $month = ((int)date('m', time())); //取得月份
        $day = ((int)date('d', time())); //取得几号
        $start = ((int)mktime(0, 0, 0, $month, $day, $year));
        $flight_setting = pdo_fetch("SELECT * FROM " . tablename('fighting_setting') . " WHERE id = '$id' LIMIT 1");
        if (empty($flight_setting)) {
            message('非法访问，请重新发送消息进入一战到底页面！');
        }
        $fromuser = $_W['fans']['from_user'];
		if($fromuser){
			$fromuser=$_W['openid'];
		}

        $fighting = pdo_fetch("SELECT * FROM " . tablename('fighting') . " WHERE `from_user`=:from_user AND `fid`=" . $flight_setting['id'] . " ORDER BY id DESC LIMIT 1", array(':from_user' => $fromuser));

        $ds = pdo_fetchall("SELECT B.nickname,B.from_user, B.lastcredit , ( SELECT COUNT( 1 ) +1 FROM ".tablename('fighting').
            " A WHERE A.lastcredit > B.lastcredit )PM FROM" . tablename('fighting')." B  WHERE  B.fid ='$flight_setting[id]'  ORDER BY PM ,B.nickname,B.from_user LIMIT 10");

        $sql_fighting = "SELECT  B.lastcredit , ( SELECT COUNT( 1 ) +1 FROM `ims_fighting` A WHERE A.lastcredit > B.lastcredit )PM FROM `ims_fighting` B WHERE  B.fid ='$flight_setting[id]'  AND B.from_user='{$fromuser}' ORDER BY PM ,B.lastcredit ";
        $theone = pdo_fetch($sql_fighting);

        $total = pdo_fetchcolumn('SELECT count(id) as total FROM ' . tablename('fighting') . ' WHERE fid= :fid group by `fid` desc ', array(':fid' => $flight_setting['id']));
        if ($theone['PM'] == 1 && $total == 1) {
            $percent = round((($theone['PM']) / $total) * 100, 2);
        } else {
            $percent = round((($total - $theone['PM']) / $total) * 100, 2);
        }

        if ((time() > $flight_setting['end']) || ($flight_setting['status'] == 2)) { //活动已结束时回复语
            include $this->template('ranking');
            exit;
        }

        if ($fighting['answerNum'] == $flight_setting['qnum']) {
            if ($flight_setting['is_shared'] == '1') { //是否开启分享 如果已经分享了 则直接到 排名页面
                include $this->template('shareing');
                exit;
            } else { //0 不需要直接到 排名
                include $this->template('ranking');
                exit;
            }
        }

        if ($fighting['lasttime'] >= $start) {
            if ($flight_setting['is_shared'] == '1') { //是否开启分享 如果已经分享了 则直接到 排名页面
                include $this->template('shareing');
                exit;
            } else { //0 不需要直接到 排名
                include $this->template('ranking');
                exit;
            }
        }
    }

    //错误答题
    public function doMobileWorng() {
        global $_GPC, $_W;
        $id = intval($_GPC['id']);
        $flight_setting = pdo_fetch("SELECT * FROM " . tablename('fighting_setting') . " WHERE rid = '$id' LIMIT 1");
        if (empty($flight_setting)) {
            message('非法访问，请重新发送消息进入一战到底页面！');
        }
        $fromuser = $_W['fans']['from_user'];
        $member = fans_search($fromuser);
        if (empty($member)) {
            $followurl = $flight_setting['followurl']; //分享URL
            header("location:$followurl");
        }

        $sql = "SELECT  * FROM " . tablename('fighting_question_worng') . " AS a LEFT JOIN " . tablename('fighting') . " AS b ON b.id = a.fightingid ";
        $list = pdo_fetchAll($sql);
        include $this->template('worng');
        exit;
    }


    public function fightJson($resultCode, $resultMsg) {
        $jsonArray = array(
            'resultCode' => $resultCode,
            'resultMsg' => $resultMsg
        );
        $jsonStr = json_encode($jsonArray);
        return $jsonStr;
    }

    public function doCheckedMobile() {
        global $_GPC, $_W;
        $servername = $_SERVER['SERVER_NAME'];
        $useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
        if (strpos($useragent, 'MicroMessenger') === false && strpos($useragent, 'Windows Phone') === false) {
            message('非法访问，请通过微信打开！');
        }
    }


    public function doCheckedParam()
    {
        global $_GPC, $_W;
        if (empty($_GPC['id'])) {
            message('非法访问，请重新发送消息进入页面！');
        }
    }


    //题库管理
    public function doWebQuestions()
    {
        global $_GPC, $_W;
        //checklogin();
        $op = $_GPC['op'] ? $_GPC['op'] : 'display';
        $weid = $_W['uniacid'];
        $select_question =pdo_fetchall("SELECT * FROM ".tablename('fighting_setting')." WHERE `weid` = :weid ", array(':weid' => $weid));
        if ($op == 'display') {
            $pindex = max(1, intval($_GPC['page']));
            $psize = 15;
            $condition = "WHERE `weid` =$weid ";
            if (!empty($_GPC['keyword'])) {
                $condition .= " AND question LIKE '%" . $_GPC['keyword'] . "%'";
            }
            $fsArr = array();
            foreach ($select_question as $v) {
                $fsArr[$v['id']] = $v['title'];
            }
            $list = pdo_fetchall('SELECT * FROM ' . tablename('fighting_question_bank') . " $condition ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize); //分页
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('fighting_question_bank') . $condition, array());
            $pager = pagination($total, $pindex, $psize);
        } elseif ($op == 'post') {
            $id = intval($_GPC['id']);
            if ($id > 0) {
                $item = pdo_fetch('SELECT * FROM ' . tablename('fighting_question_bank') . " WHERE id=:id", array(':id' => $id));
            }
            if (checksubmit('submit')) {
                $answer = strtoupper($_GPC['answer']);
                $insert = array(
                    'sid' => $_GPC['sid'],
                    'figure' => $_GPC['figure'],
                    'question' => $_GPC['question'],
                    'option_num' => 1,
                    'optionA' => $_GPC['optionA'],
                    'optionB' => $_GPC['optionB'],
                    'optionC' => $_GPC['optionC'],
                    'optionD' => $_GPC['optionD'],
                    'optionE' => $_GPC['optionE'],
                    'optionF' => $_GPC['optionF'],
                    'weid' => $weid,
                    'answer' => $answer,
                );

                if (empty($id)) {
                    pdo_insert('fighting_question_bank', $insert);
                } else {
                    if (pdo_update('fighting_question_bank', $insert, array('id' => $id)) === false) {
                        message('更新题目数据失败, 请稍后重试.', 'error');
                    }
                }
                message('更新题目数据成功！', $this->createWebUrl('questions', array('op' => 'display', 'name' => 'zombie_fighting')), 'success');
            }
        } elseif ($op == 'delBanks') {
            $id = intval($_GPC['id']);
            $temp = pdo_delete("fighting_question_bank", array('id' => $id));
            if ($temp == false) {
                message('抱歉，删除题库数据失败！', '', 'error');
            } else {
                message('删除题库成功！', $this->createWebUrl('questions', array('op' => 'display', 'name' => 'zombie_fighting')), 'success');
            }
        }elseif ($op == 'delActivity') {
            //删除
            $id = intval($_GPC['id']);
            $temp = pdo_delete("fighting_setting", array("weid" => $weid, 'id' => $id));
            if ($temp == false) {
                message('抱歉，删除活动数据失败！', '', 'error');
            } else {
                pdo_delete("rule", array("uniacid" => $weid, 'id' => $id));
                pdo_delete("rule_keyword", array("uniacid" => $weid, 'rid' => $id));
                pdo_delete("stat_rule", array("uniacid" => $weid, 'rid' => $id));

                message('删除活动成功！', $this->createWebUrl('questions', array('op' => 'list', 'name' => 'zombie_fighting')), 'success');
            }
        }elseif ($op == 'list') { //活动列表
            $id = intval($_GPC['id']);
            if (checksubmit('delete') && !empty ($_GPC['select'])) {
                pdo_delete('fighting_setting', " id  IN  ('" . implode("','", $_GPC['select']) . "')");
                message('删除题目数据成功！', $this->createWebUrl('questions', array('op' => 'deleteSet', 'name' => 'zombie_fighting')), 'success');
            }

            $pindex = max(1, intval($_GPC['page']));
            $psize = 20;
            $list = pdo_fetchall("SELECT * FROM " . tablename('fighting_setting') . " WHERE weid = '$weid' ORDER BY id ASC LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
            if (!empty ($list)) {
                $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('fighting_setting') . " WHERE weid = '{$_W['uniacid']}' ");
                $pager = pagination($total, $pindex, $psize);
            }

        } elseif ($op == 'rankList') { //排名
            $rid = intval($_GPC['rid']);

            $pindex = max(1, intval($_GPC['page']));
            $psize = 20;
            $sql="SELECT a.id,a.fid,b.nickname,b.mobile,a.lasttime,a.lastcredit FROM ".tablename('fighting_user')." AS b LEFT JOIN ".tablename('fighting')." AS a ON a.from_user = b.openid WHERE a.fid = '$rid' ORDER BY a.lastcredit DESC LIMIT ".($pindex -1) * $psize.",{$psize}";
            $list=pdo_fetchall($sql);
            $series =pdo_fetchall("SELECT * FROM ".tablename('fighting_setting')." WHERE `rid` = :rid ", array(':rid' => $rid));
            $seriesArr = array();
            foreach ($series as $v) {
                $seriesArr[$v['id']] = $v['title'];
            }

            if (!empty ($list)) {
                $total=pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('fighting')." AS a LEFT JOIN ".tablename('fighting_user')." AS b ON a.from_user = b.openid WHERE a.fid = '$rid' ");
                $pager=pagination($total, $pindex, $psize);
            }

            if(isset($_GPC['delete'])) {
                $ids= implode(",", $_GPC['delete']);
                $sqls= "delete from  ".tablename('fighting')."  where id in(".$ids.")";
                pdo_query($sqls);

                message('删除成功！', referer(), 'success');
            }

        } elseif ($op == 'postRank') {
            $id = intval($_GPC['id']);
            $fid = intval($_GPC['fid']);
            if ($id > 0) {
                $rank = pdo_fetch('SELECT a.id,b.nickname,a.lastcredit,b.id,b.mobile FROM ' . tablename('fighting').
                    "AS a LEFT JOIN " . tablename('fighting_user') . " AS b ON a.from_user = b.openid WHERE a.weid=:weid AND a.id=:id", array(':weid' => $weid, ':id' => $id));
            }
            if (checksubmit('submit')) {
                $update = array(
                    'lastcredit' => $_GPC['lastcredit'],
                );
                pdo_update('amouse_fighting', $update, array('id' => $id,'fid'=>$fid));

                message('修改成功！', $this->createWebUrl('questions', array('op' => 'rankList', 'rid' => $fid, 'name' => 'zombie_fighting')), 'success');
            }
        } elseif ($op == 'delRank') { //删除排名信息
            //删除
            if (isset($_GPC['delete'])) {
                $ids = implode(",", $_GPC['delete']);
                $sqls = "delete from  " . tablename('fighting') . "  where id in(" . $ids . ")";
                pdo_query($sqls);
                message('删除成功！', referer(), 'success');
            }
            $id = intval($_GPC['rid']);
            $temp = pdo_delete("fighting", array("weid" => $weid, 'id' => $id));
            if ($temp == false) {
                message('抱歉，删除数据失败！', '', 'error');
            } else {
                message('删除数据成功！', $this->createWebUrl('questions', array('op' => 'rankList', 'name' => 'zombie_fighting')), 'success');
            }
        }

        include $this->template('question_list');
    }
  

    //题库管理
    public function doWebLists(){
        global $_GPC, $_W; 
        $op = $_GPC['op'] ? $_GPC['op'] : 'list';
        $weid = $_W['uniacid'];
        if ($op == 'list') { //活动列表
            $id = intval($_GPC['id']);
            if (checksubmit('delete') && !empty ($_GPC['select'])) {
                pdo_delete('fighting_setting', " id  IN  ('" . implode("','", $_GPC['select']) . "')");
                message('删除题目数据成功！', $this->createWebUrl('questions', array('op' => 'deleteSet', 'name' => 'zombie_fighting')), 'success');
            }

            $pindex = max(1, intval($_GPC['page']));
            $psize = 20;
            $list = pdo_fetchall("SELECT * FROM " . tablename('fighting_setting') . " WHERE weid = '{$_W['uniacid']}' ORDER BY id ASC LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
            if (!empty ($list)) {
                $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('fighting_setting') . " WHERE weid = '{$_W['uniacid']}' ");
                $pager = pagination($total, $pindex, $psize);
            } 
        }
        include $this->template('question_list');
    }

    //错误题目
    public function doWebWorngquestion()
    {
        global $_GPC, $_W;
        if (checksubmit('delete') && !empty ($_GPC['select'])) {
            $fid = intval($_GPC['fid']);
            pdo_delete('fighting_question_worng', " id  IN  ('" . implode("','", $_GPC['select']) . "')");
            message('删除数据成功！', $this->createWebUrl('Worngquestion', array('name' => 'zombie_fighting', 'id' => $fid)), 'success');
        }
        $pindex = max(1, intval($_GPC['page']));
        $fightingid = $_GPC['id'];
        $psize = 20;
        $list = pdo_fetchall("SELECT * FROM " . tablename('fighting_question_worng') . " WHERE weid = '{$_W['uniacid']}' and fightingid= '{$_GPC['id']}' ORDER BY id ASC LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
        $fid = pdo_fetchcolumn("SELECT fid FROM " . tablename('fighting') . " WHERE id = :id ORDER BY `id` DESC", array(
            ':id' => $fightingid));
        if (!empty ($list)) {
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('fighting_question_worng') . " WHERE weid = '{$_W['uniacid']}' and fightingid= '{$_GPC['id']}' ");
            $pager = pagination($total, $pindex, $psize);
        }

        include $this->template('worngquestion');
    }

    public function doWebdelworngquestion()
    {
        global $_GPC, $_W;
        checklogin();
        $id = intval($_GPC['id']);
        $fid = intval($_GPC['fid']);
        pdo_delete('fighting_question_worng', " id=$id");
        message('删除数据成功！', $this->createWebUrl('Worngquestion', array('name' => 'zombie_fighting', 'id' => $fid)), 'success');
    }


    public function  doWebDownload() {
        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');

        global $_GPC,$_W;

        $id = intval($_GPC['id']);
        echo $id;
        $sql = "SELECT a.id,a.fid,b.nickname,b.mobile,a.lasttime,a.lastcredit FROM ".tablename('fighting').
            "AS a LEFT JOIN " . tablename('fighting_user') . " AS b ON a.from_user = b.openid WHERE a.fid = '$id' LIMIT 50000" ;
        $list = pdo_fetchall($sql);

        $tableheader = array('排名',  '昵称', '手机号','最后得分','最后时间');
        $html = "\xEF\xBB\xBF";
        foreach ($tableheader as $value) {
            $html .= $value . "\t ,";
        }
        $html .= "\n";
        foreach ($list as $mid => $value) {
            $p = $mid + 1;
            $html .= $p . "\t ,";
            $html .= $value['nickname'] . "\t ,";
            $html .= $value['mobile'] . "\t ,";
            $html .= $value['lastcredit'] . "\t ,";
            $html .= date('Y年m月d日 H:i:s',$value['lasttime']) . "\t ,";
            $html .= "\n";
        }
        $filename = '一站到底排名_'.$id.'_'.$now;

        header("Content-type:text/csv");
        header("Content-Disposition:attachment; filename=".$filename.".csv");

        echo $html;
        exit();
    }
	
	


}