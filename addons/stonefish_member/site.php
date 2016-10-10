<?php
/**
 * 会员中心模块微站定义
 *
 */
defined('IN_IA') or exit('Access Denied');

class Stonefish_memberModuleSite extends WeModuleSite {
	
	public function doWebLevel() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_W, $_GPC;
		checklogin();
		//查询是否参数设置过
		$moduleconfig = $this->module['config'];
		if(empty($moduleconfig)){
			message('请先设置会员中心参数配置', Url('profile/module/setting',array('m'=>'stonefish_member')), 'error');
		}
		//查询是否参数设置过
		load()->func('tpl');
		$creditnames = array();
		$unisettings = uni_setting($uniacid, array('creditnames'));
		foreach ($unisettings['creditnames'] as $key=>$credit) {
			if (!empty($credit['enabled'])) {
				$creditnames[$key] = $credit['title'];
			}
		}
		$setting = pdo_fetchall("SELECT * FROM ".tablename('stonefish_member_level')." WHERE uniacid = '{$_W['uniacid']}' order by integral_start asc");
		$config = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_config')." WHERE uniacid = '{$_W['uniacid']}' order by id desc");
		if (checksubmit('submit')) {
		    //删除等级
			$list_level = pdo_fetchall("SELECT id FROM ".tablename('stonefish_member_level')." WHERE uniacid =:uniacid ", array(':uniacid' => $_W['uniacid']));
			if(!empty($list_level)){
		    	foreach ($list_level as $list_levels) {
			    	$del=0;
					if (!empty($_GPC['id'])) {
				    	foreach ($_GPC['id'] as $index => $levelids) {
					    	if($_GPC['id'][$index]==$list_levels['id']){
						    	$del=1;
								break;
							}
						}
					}
					if($del==0){				    
						pdo_delete('stonefish_member_level', "id = '".$list_levels['id']."'");
					}
				}
			}
			//删除等级
			//保存等级
			if (!empty($_GPC['grade'])) {
			    foreach ($_GPC['grade'] as $index => $grade) {
				    $insert = array(
					    'uniacid'        => $_W['uniacid'],
						'grade'          => $grade,
					    'integral_start' => $_GPC['integral_start'][$index],
						'integral_end'   => $_GPC['integral_end'][$index]
				    );
					if(!empty($_GPC['id'][$index])) {
					    pdo_update('stonefish_member_level', $insert, array('id' => $_GPC['id'][$index]));
					}else{
					    pdo_insert('stonefish_member_level', $insert);
					}					
			    }
			}
			//保存等级
			//为等级排序并设置第一个和最后一个等级数
			$list_level = pdo_fetch("SELECT id,integral_start FROM ".tablename('stonefish_member_level')." WHERE uniacid =:uniacid order by integral_start asc", array(':uniacid' => $_W['uniacid']));
			if(!empty($list_level)&&$list_level['integral_start']>0){
		    	pdo_update('stonefish_member_level', array('integral_start' => 0), array('id' => $list_level['id']));
			}
			$list_level = pdo_fetch("SELECT id,integral_end FROM ".tablename('stonefish_member_level')." WHERE uniacid =:uniacid order by integral_end desc", array(':uniacid' => $_W['uniacid']));
			if(!empty($list_level)&&$list_level['integral_end']<999999999){
		    	pdo_update('stonefish_member_level', array('integral_end' => 999999999), array('id' => $list_level['id']));
			}
			//为等级排序并设置第一个和最后一个等级数
			if(!empty($config)){
				pdo_update('stonefish_member_config', array('levelcredit' => $_GPC['levelcredit']), array('uniacid' => $_W['uniacid']));
			}else{
				pdo_insert('stonefish_member_config', array('uniacid' => $_W['uniacid'],'levelcredit' => $_GPC['levelcredit']));
			}
			message('会员等级设置成功！', $this->createWebUrl('level'), 'success');
		}
		include $this->template('level');
	}
	
	public function doWebSms() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_W, $_GPC;
		checklogin();
		//查询是否参数设置过
		$moduleconfig = $this->module['config'];
		if(empty($moduleconfig)){
			message('请先设置会员中心参数配置', Url('profile/module/setting',array('m'=>'stonefish_member')), 'error');
		}
		//查询是否参数设置过
		load()->func('tpl');
		
		$setting = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_config')." WHERE uniacid = '{$_W['uniacid']}'");
		if(empty($setting)){
			$setting['smstype']=1;
		}
		if ($_W['ispost'] && $_W['isajax']) {
			$sql = 'SELECT `uniacid` FROM ' . tablename('stonefish_member_config') . " WHERE `uniacid` = :uniacid";
			$status = pdo_fetch($sql, array(':uniacid' => $_W['uniacid']));
			if (empty($status)) {
				$open = array('uniacid' => $_W['uniacid']);
				pdo_insert('stonefish_member_config', $open);
			}
			$data['smsstatus'] = intval($_GPC['status']);
			if (false === pdo_update('stonefish_member_config', $data, array('uniacid' => $_W['uniacid']))) {
				exit('error');
			}
			exit('success');
		}
		
		if (checksubmit('submit')) {
			if (empty($_GPC['smskey']) && $_GPC['smstype']) {
				message('请输入短信验证KEY！');
			}
			if (empty($_GPC['tpl_id']) && $_GPC['smstype']) {
				message('请输入短信验证模板！');
			}
			if (empty($_GPC['sign']) && $_GPC['smstype']) {
				message('请输入短信验证签名！');
			}
			if (empty($_GPC['aging'])) {
				message('请输入短信验证时效！');
			}
			if (empty($_GPC['agingrepeat'])) {
				message('请选择短信验证次数！');
			}					
			$data = array(
				'smskey' => $_GPC['smskey'],
				'tpl_id' => $_GPC['tpl_id'],
				'smstype' => $_GPC['smstype'],
				'sign' => $_GPC['sign'],
				'aging' => $_GPC['aging'],
				'agingrepeat' => $_GPC['agingrepeat'],
			);
			if (!empty($setting)) {
				pdo_update('stonefish_member_config', $data, array('uniacid' => $_W['uniacid']));
			} else {
				$data['uniacid'] = $_W['uniacid'];
				pdo_insert('stonefish_member_config', $data);
			}
			message('会员短信策略设置成功！', $this->createWebUrl('sms'), 'success');
		}
		include $this->template('sms');
	}
	
	public function doWebSmsrecord() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_W, $_GPC;
		checklogin();
		load()->func('tpl');
		$uniacid = $_W['uniacid'];
		
		$total = pdo_fetchcolumn("SELECT count(id) FROM " . tablename('stonefish_member_smsrecord') . "  WHERE uniacid = :uniacid ORDER BY createtime DESC",array(':uniacid' => $uniacid));
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $pager = pagination($total, $pindex, $psize);
        $start = ($pindex - 1) * $psize;
        $limit .= " LIMIT {$start},{$psize}";
        $record = pdo_fetchall("SELECT * FROM " . tablename('stonefish_member_smsrecord') . " WHERE uniacid=:uniacid ORDER BY createtime DESC " . $limit, array(':uniacid' => $uniacid));
		load()->model('mc');
		foreach ($record as &$records) {			
			$profile = mc_fetch($records['uid'], array('realname','avatar'));
			$records['realname'] = $profile['realname'];
			$records['avatar'] = $profile['avatar'];
		}
		include $this->template('smsrecord');
	}
	
	public function doWebDeletesmsrecord() {
        global $_GPC, $_W;
        foreach ($_GPC['idArr'] as $k => $id) {
            $id = intval($id);
            if ($id == 0)
                continue;
			pdo_delete('stonefish_member_smsrecord', array('id' => $id));
        }
        $this->web_message('验证码发放记录删除成功！', '', 0);
    }
	
	public function doWebSmsinfo() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_W, $_GPC;
		checklogin();
		load()->func('tpl');
		
		
		include $this->template('smsinfo');
	}
	
	public function doWebSmsadd() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_W, $_GPC;
		checklogin();
		load()->func('tpl');
		$uniacid = $_W['uniacid'];
		
		$total = pdo_fetchcolumn("SELECT count(id) FROM " . tablename('stonefish_member_sms') . "  WHERE uniacid = :uniacid ORDER BY createtime DESC",array(':uniacid' => $uniacid));
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $pager = pagination($total, $pindex, $psize);
        $start = ($pindex - 1) * $psize;
        $limit .= " LIMIT {$start},{$psize}";
        $record = pdo_fetchall("SELECT * FROM " . tablename('stonefish_member_sms') . " WHERE uniacid=:uniacid ORDER BY createtime DESC " . $limit, array(':uniacid' => $uniacid));
		load()->model('mc');		
		include $this->template('smsadd');
	}
	
	public function doWebsmsaddnum() {
        global $_GPC, $_W;
		if($_W['isajax']) {
			include $this->template('smsaddnum');
		}
		if($_GPC['save']=='yes'){
			$data = array(
				'uniacid' => $_W['uniacid'],
				'smstotal' => $_GPC['smstotal'],
				'smsinfo' => $_GPC['smsinfo'],
				'createtime' => TIMESTAMP
			);
			pdo_insert('stonefish_member_sms', $data);
			message('短信条数保存成功！', $this->createWebUrl('smsadd'), 'success');
		}
    }
	
	public function doWebSignin() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_W, $_GPC;
		checklogin();
		//查询是否参数设置过
		$moduleconfig = $this->module['config'];
		if(empty($moduleconfig)){
			message('请先设置会员中心参数配置', Url('profile/module/setting',array('m'=>'stonefish_member')), 'error');
		}
		//查询是否参数设置过
		load()->func('tpl');
		$creditnames = array();
		$unisettings = uni_setting($uniacid, array('creditnames'));
		foreach ($unisettings['creditnames'] as $key=>$credit) {
			if (!empty($credit['enabled'])) {
				$creditnames[$key] = $credit['title'];
			}
		}
		$timexiaoshi = array();
		for($i=0;$i<=23;$i++){
		    $timexiaoshi[].=$i;
		}
		$timefen = array();
		for($i=0;$i<=59;$i++){
		    $timefen[].=$i;
		}
		$setting = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_config')." WHERE uniacid = '{$_W['uniacid']}'");
		if ($_W['ispost'] && $_W['isajax']) {
			$sql = 'SELECT `uniacid` FROM ' . tablename('stonefish_member_config') . " WHERE `uniacid` = :uniacid";
			$status = pdo_fetch($sql, array(':uniacid' => $_W['uniacid']));
			if (empty($status)) {
				$open = array('uniacid' => $_W['uniacid']);
				pdo_insert('stonefish_member_config', $open);
			}
			$data['signinstatus'] = intval($_GPC['status']);
			if (false === pdo_update('stonefish_member_config', $data, array('uniacid' => $_W['uniacid']))) {
				exit('error');
			}
			exit('success');
		}
		if (empty($setting)) {
			$setting['dsigncredit'] = 1;
			$setting['showrank'] = 20;
			$setting['start_timexiaoshi'] = '6';
			$setting['start_timefen'] = '30';
			$setting['end_timexiaoshi'] = '8';
			$setting['end_timefen'] = '30';
			$setting['dsigninfo'] = implode("\n", (array)iunserializer('亲,就等您来签到了!\n签到是一个体力活,要持之以恒哟!'));
		}else{
			$start_time = explode(":", $setting['start_time']);
			$end_time = explode(":", $setting['end_time']);
			$setting['start_timexiaoshi'] = $start_time[0];
			$setting['start_timefen'] = $start_time[1];
			$setting['end_timexiaoshi'] = $end_time[0];
			$setting['end_timefen'] = $end_time[1];
			$setting['dsigninfo'] = implode("\n", (array)iunserializer($setting['dsigninfo']));
		}
		if (checksubmit('submit')) {
			if (empty($_GPC['dsigncredit'])) {
				message('请输入每天签到奖励积分！');
			}
			if (empty($_GPC['tsign'])) {
				message('请输入累计签到次数！');
			}
			if (empty($_GPC['tsigncredit'])) {
				message('请输入累计签到奖励多少积分！');
			}
			if (empty($_GPC['csign'])) {
				message('请输入连续签到次数！');
			}
			if (empty($_GPC['csigncredit'])) {
				message('请输入连续签到奖励多少积分！');
			}
			if (empty($_GPC['osign'])) {
				message('请输入签到第一累计次数！');
			}
			if (empty($_GPC['osigncredit'])) {
				message('请输入签到第一累计奖励多少积分！');
			}
			if (empty($_GPC['dsigninfo'])) {
				message('请输入签到提示词！');
			}
			$dsigninfo = explode("\n", $_GPC['dsigninfo']);
			$data = array(
				'dsigninfo' => iserializer($dsigninfo),
				'dsigncredit' => $_GPC['dsigncredit'],
				'dsigntype' => $_GPC['dsigntype'],
				'showrank' => $_GPC['showrank'],
				'tsign' => $_GPC['tsign'],
				'tsigncredit' => $_GPC['tsigncredit'],
				'tsigntype' => $_GPC['tsigntype'],
				'csign' => $_GPC['csign'],
				'csigncredit' => $_GPC['csigncredit'],
				'csigntype' => $_GPC['csigntype'],
				'osign' => $_GPC['osign'],
				'osigncredit' => $_GPC['osigncredit'],
				'osigntype' => $_GPC['osigntype'],
				'start_time' => $_GPC['start_time_xiaoshi'].':'.$_GPC['start_time_fen'],
				'end_time' => $_GPC['end_time_xiaoshi'].':'.$_GPC['end_time_fen'],
				'signindescription' => $_GPC['description']
			);
			if (!empty($setting)) {
				pdo_update('stonefish_member_config', $data, array('uniacid' => $_W['uniacid']));
			} else {
				$data['uniacid'] = $_W['uniacid'];
				pdo_insert('stonefish_member_config', $data);
			}
			message('会员积分策略设置成功！', $this->createWebUrl('signin'), 'success');
		}		
		include $this->template('signin');
	}
	
	public function doWebSigninrecord() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_W, $_GPC;
		checklogin();
		load()->func('tpl');
		$uniacid = $_W['uniacid'];
		
		$total = pdo_fetchcolumn("SELECT count(id) FROM " . tablename('stonefish_member_signin_record') . "  WHERE uniacid = :uniacid ORDER BY sign_time DESC",array(':uniacid' => $uniacid));
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $pager = pagination($total, $pindex, $psize);
        $start = ($pindex - 1) * $psize;
        $limit .= " LIMIT {$start},{$psize}";
        $record = pdo_fetchall("SELECT * FROM " . tablename('stonefish_member_signin_record') . " WHERE uniacid=:uniacid ORDER BY sign_time DESC " . $limit, array(':uniacid' => $uniacid));
		load()->model('mc');
		foreach ($record as &$records) {
			$uid = pdo_fetchcolumn("SELECT uid FROM ".tablename('mc_mapping_fans')." WHERE uniacid = :uniacid and openid = :openid",array(':uniacid' => $uniacid,':openid' => $records['from_user']));
			$profile = mc_fetch($uid, array('avatar','realname','mobile'));
			$records['realname'] = $profile['realname'];
			$records['mobile'] = $profile['mobile'];
			$records['avatar'] = $profile['avatar'];
		}
		include $this->template('signinrecord');
	}
	
	public function doWebDeletesignin() {
        global $_GPC, $_W;
        foreach ($_GPC['idArr'] as $k => $id) {
            $id = intval($id);
            if ($id == 0)
                continue;
			pdo_delete('stonefish_member_signin_record', array('id' => $id));
        }
        $this->web_message('签到记录删除成功！', '', 0);
    }
	
	public function doWebSigninprize() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_W, $_GPC;
		checklogin();
		load()->func('tpl');
		$uniacid = $_W['uniacid'];
		
		$total = pdo_fetchcolumn("SELECT count(id) FROM " . tablename('stonefish_member_signin_prize') . "  WHERE uniacid = :uniacid ORDER BY sign_time DESC",array(':uniacid' => $uniacid));
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $pager = pagination($total, $pindex, $psize);
        $start = ($pindex - 1) * $psize;
        $limit .= " LIMIT {$start},{$psize}";
        $prize = pdo_fetchall("SELECT * FROM " . tablename('stonefish_member_signin_prize') . " WHERE uniacid=:uniacid ORDER BY sign_time DESC " . $limit, array(':uniacid' => $uniacid));
		load()->model('mc');
		foreach ($prize as &$prizes) {
			$uid = pdo_fetchcolumn("SELECT uid FROM ".tablename('mc_mapping_fans')." WHERE uniacid = :uniacid and openid = :openid",array(':uniacid' => $uniacid,':openid' => $prizes['from_user']));
			$profile = mc_fetch($uid, array('avatar','realname','mobile'));
			$prizes['realname'] = $profile['realname'];
			$prizes['mobile'] = $profile['mobile'];
			$prizes['avatar'] = $profile['avatar'];
		}
	
		include $this->template('signinprize');
	}
	
	public function doWebDeletesigninprize() {
        global $_GPC, $_W;
        foreach ($_GPC['idArr'] as $k => $id) {
            $id = intval($id);
            if ($id == 0)
                continue;
			pdo_delete('stonefish_member_signin_prize', array('id' => $id));
        }
        $this->web_message('签到奖励记录删除成功！', '', 0);
    }
		
	public function doWebMessage() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_W, $_GPC;
		checklogin();
		//查询是否参数设置过
		$moduleconfig = $this->module['config'];
		if(empty($moduleconfig)){
			message('请先设置会员中心参数配置', Url('profile/module/setting',array('m'=>'stonefish_member')), 'error');
		}
		//查询是否参数设置过
		load()->func('tpl');
		load()->model('mc');
		$op = $_GPC['op'];
		$dos = array('display', 'post', 'postalone', 'del', 'record');
        $op = in_array($op, $dos) ? $op : 'display';
		$creditnames = array();
		$unisettings = uni_setting($_W['uniacid'], array('creditnames'));
		foreach ($unisettings['creditnames'] as $key=>$credit) {
			if (!empty($credit['enabled'])) {
				$creditnames[$key] = $credit['title'];
			}
		}		
		if($op == 'display') {
			$pindex = max(1, intval($_GPC['page']));
		    $psize = 30;
		    $condition = '';
		    if(!empty($_GPC['keyword'])) {
			    $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
		    }		    
		    if(intval($_GPC['groupid'])) {
			    $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('stonefish_member_message') . " WHERE uniacid = '{$_W['uniacid']}' AND type = 2 " . $condition . "  AND messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid = '{$_GPC['groupid']}')");
			    $list = pdo_fetchall('SELECT * FROM ' . tablename('stonefish_member_message') . " WHERE uniacid = '{$_W['uniacid']}' AND type = 2 " . $condition . " AND  messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid = '{$_GPC['groupid']}') ORDER BY messageid DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
		    } else {
			    $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('stonefish_member_message') . " WHERE uniacid = '{$_W['uniacid']}' AND type = 2" . $condition);
			    $list = pdo_fetchall('SELECT * FROM ' . tablename('stonefish_member_message') . " WHERE uniacid = '{$_W['uniacid']}' AND type = 2" . $condition . " ORDER BY messageid DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
		    }
			$groupall = pdo_fetchall('SELECT groupid,title FROM ' . tablename('mc_groups') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY orderlist DESC");
			foreach($list as &$lists) {
			    if(!empty($lists['thumb'])) {
				    $lists['thumb'] = tomedia($lists['thumb']);
			    }
				//所属会员组
				$coupongroup = pdo_fetchall('SELECT groupid FROM ' . tablename('stonefish_member_message_allocation') . " WHERE uniacid = '{$_W['uniacid']}' AND messageid = '{$lists['messageid']}'");
				if(!empty($coupongroup)) {
		            $grouptitle = '';
					foreach($coupongroup as $cgroup) {
			            $group_title = pdo_fetchcolumn('SELECT title FROM ' . tablename('mc_groups') . " WHERE uniacid = '{$_W['uniacid']}' and groupid = '{$cgroup['groupid']}' ORDER BY orderlist DESC");
						$grouptitle .= $group_title.';';
		            }
					$lists['grouptitle'] = substr($grouptitle,0,strlen($grouptitle)-1);
	            }				
				//所属会员组
				//所属UID
				$uid = pdo_fetchcolumn('SELECT uid FROM ' . tablename('stonefish_member_message_allocation') . " WHERE uniacid = '{$_W['uniacid']}' AND messageid = '{$lists['messageid']}'");
				if($uid){
			        $members = mc_fetch($uid, array('realname'));
					$lists['grouptitle'] = '会员UID:'.$uid.' '.$members['realname'];
					$lists['uid'] = 1;
				}
				//所属UID
				//查看人次
				$lists['view'] = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('stonefish_member_message_record')." WHERE uniacid = '{$_W['uniacid']}' and messageid = '{$lists['messageid']}' ORDER BY recid DESC");
				//查看人次
		    }
			$pager = pagination($total, $pindex, $psize);
	    }

        if($op == 'post') {
	        $messageid = intval($_GPC['id']);
	        $item = pdo_fetch('SELECT * FROM ' . tablename('stonefish_member_message') . " WHERE uniacid = '{$_W['uniacid']}' AND messageid = '{$messageid}'");
		        if(empty($item) || $messageid == 0) {
		        $item['starttime'] = time();
		        $item['endtime'] = time() + 6 * 86400;
	        }
		    $coupongroup = pdo_fetchall('SELECT groupid FROM ' . tablename('stonefish_member_message_allocation') . " WHERE uniacid = '{$_W['uniacid']}' AND messageid = '{$messageid}'");
	        if(!empty($coupongroup)) {
		        foreach($coupongroup as $cgroup) {
			        $grouparr[] = $cgroup['groupid'];
		        }
	        }
		    $group = pdo_fetchall('SELECT groupid,title FROM ' . tablename('mc_groups') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY orderlist DESC");
	        if(!empty($grouparr)) {
		        foreach($group as &$g){
			        if(in_array($g['groupid'], $grouparr)) {
				        $g['groupid_select'] = 1;
			        }
		        }
	        }		
	        if(checksubmit('submit')) {
			    $title = !empty($_GPC['title']) ? trim($_GPC['title']) : message('请输入群发消息标题！');
			    $groups = !empty($_GPC['group']) ? $_GPC['group'] : message('请选择会员组！');
			    $thumb = !empty($_GPC['thumb']) ? $_GPC['thumb'] : message('请上传群发消息缩略图！');
			    $description = !empty($_GPC['description']) ? trim($_GPC['description']) : message('请填写群发消息内容！');			   
			    $starttime = strtotime($_GPC['datelimit']['start']);
			    $endtime = strtotime($_GPC['datelimit']['end']);
			    if($endtime == $starttime) {
				    $endtime = $endtime + 86399;
			    }			   
			    $data = array(
				    'uniacid' => $_W['uniacid'],
				    'title' => $title,
				    'type' => '2',
				    'thumb' => $thumb,
				    'description' => $description,				    
				    'starttime' => $starttime,
				    'endtime' => $endtime,
			    );
			    if ($messageid) {
				    pdo_update('stonefish_member_message', $data, array('uniacid' => $_W['uniacid'], 'messageid' => $messageid));
			    } else {
				    pdo_insert('stonefish_member_message', $data);
				    $messageid = pdo_insertid();
			    }
			    pdo_delete('stonefish_member_message_allocation', array('uniacid' => $_W['uniacid'], 'messageid' => $messageid));
			    if(!empty($groups) && $messageid) {
				    foreach($groups as $gid) {
					    $gid = intval($gid);
					    $insert = array(
						    'uniacid' => $_W['uniacid'],
						    'messageid' => $messageid,
						    'groupid' => $gid
					    );
					    pdo_insert('stonefish_member_message_allocation', $insert) ? '' : message('抱歉，群发消息更新失败！', referer(), 'error');
					    unset($insert);
				    }
			    }
			    message('群发消息更新成功！', url('site/entry/message', array('m' => 'stonefish_member')), 'success');
		    }
	    }
		
		if($op == 'postalone') {
	        $messageid = intval($_GPC['id']);
	        $item = pdo_fetch('SELECT * FROM ' . tablename('stonefish_member_message') . " WHERE uniacid = '{$_W['uniacid']}' AND messageid = '{$messageid}'");
		    if(empty($item) || $messageid == 0) {
		        $item['starttime'] = time();
		        $item['endtime'] = time() + 6 * 86400;
	        }
			$uid = pdo_fetchcolumn('SELECT uid FROM ' . tablename('stonefish_member_message_allocation') . " WHERE uniacid = '{$_W['uniacid']}' AND messageid = '{$messageid}'");
	        if(checksubmit('submit')) {
			    $title = !empty($_GPC['title']) ? trim($_GPC['title']) : message('请输入消息标题！');
			    $uid = !empty($_GPC['uid']) ? $_GPC['uid'] : message('请选择会员！');
			    $thumb = !empty($_GPC['thumb']) ? $_GPC['thumb'] : message('请上传消息缩略图！');
			    $description = !empty($_GPC['description']) ? trim($_GPC['description']) : message('请填写消息内容！');			   
			    $starttime = strtotime($_GPC['datelimit']['start']);
			    $endtime = strtotime($_GPC['datelimit']['end']);
			    if($endtime == $starttime) {
				    $endtime = $endtime + 86399;
			    }			   
			    $data = array(
				    'uniacid' => $_W['uniacid'],
				    'title' => $title,
				    'type' => '2',
				    'thumb' => $thumb,
				    'description' => $description,				    
				    'starttime' => $starttime,
				    'endtime' => $endtime,
			    );
			    if ($messageid) {
				    pdo_update('stonefish_member_message', $data, array('uniacid' => $_W['uniacid'], 'messageid' => $messageid));
			    } else {
				    pdo_insert('stonefish_member_message', $data);
				    $messageid = pdo_insertid();
			    }
			    pdo_delete('stonefish_member_message_allocation', array('uniacid' => $_W['uniacid'], 'messageid' => $messageid));
			    if($uid && $messageid) {
					$insert = array(
						'uniacid' => $_W['uniacid'],
						'messageid' => $messageid,
						'uid' => $uid
					);
					pdo_insert('stonefish_member_message_allocation', $insert) ? '' : message('抱歉，消息更新失败！', referer(), 'error');
			    }
			    message('消息更新成功！', url('site/entry/message', array('m' => 'stonefish_member')), 'success');
		    }
	    }

	    if($op == 'del') {
		    $id = intval($_GPC['id']);
		    $row = pdo_fetch("SELECT messageid FROM ".tablename('stonefish_member_message')." WHERE uniacid = '{$_W['uniacid']}' AND messageid = :messageid", array(':messageid' => $id));
		    if (empty($row)) {
			    message('抱歉，通知不存在或是已经被删除！');
		    }
		    pdo_delete('stonefish_member_message_allocation', array('uniacid' => $_W['uniacid'],'messageid' => $id));
		    pdo_delete('stonefish_member_message', array('messageid' => $id, 'uniacid' => $_W['uniacid']));
		    message('群发消息删除成功！',url('site/entry/message', array('m' => 'stonefish_member')), 'success');
	    }  
		
	    if($op == 'record') {
			$coupons = pdo_fetchall('SELECT messageid, title FROM ' . tablename('stonefish_member_message') . ' WHERE uniacid = :uniacid AND type = 2 ORDER BY messageid DESC', array(':uniacid' => $_W['uniacid']), 'messageid');
		    $starttime = empty($_GPC['time']['start']) ? strtotime('-1 month') : strtotime($_GPC['time']['start']);
		    $endtime = empty($_GPC['time']['end']) ? TIMESTAMP : strtotime($_GPC['time']['end']) + 86399;
	
		    $where = " WHERE a.uniacid = {$_W['uniacid']} AND b.type = 2 AND a.granttime>=:starttime AND a.granttime<:endtime";
		    $params = array(
			    ':starttime' => $starttime,
			    ':endtime' => $endtime,
		    );
		    $uid = intval($_GPC['uid']);
		    if (!empty($uid)) {
			    $where .= ' AND a.uid=:uid';
			    $params[':uid'] = $uid;
		    }
		    $messageid = intval($_GPC['messageid']);
		    if (!empty($messageid)) {
			    $where .= " AND a.messageid = {$messageid}";
		    }
		    $pindex = max(1, intval($_GPC['page']));
		    $psize = 20;
	
		    $list = pdo_fetchall("SELECT a.*, b.title,b.thumb FROM ".tablename('stonefish_member_message_record'). ' AS a LEFT JOIN ' . tablename('stonefish_member_message') . ' AS b ON a.messageid = b.messageid ' . " $where ORDER BY a.messageid DESC,a.recid DESC LIMIT ".($pindex - 1) * $psize.','.$psize, $params);
		    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('stonefish_member_message_record') . ' AS a LEFT JOIN ' . tablename('stonefish_member_message') . ' AS b ON a.messageid = b.messageid '. $where , $params);
		    if(!empty($list)) {
			    foreach ($list as &$row) {
				    $members = mc_fetch($row['uid'], array('nickname','realname'));
				    $row['nickname'] = $members['realname'].'['.$members['nickname'].']';
				    $row['thumb'] = tomedia($row['thumb']);
			    }
		    }
		    $pager = pagination($total, $pindex, $psize);
	    }
		include $this->template('message');
	}
	
	public function doWebQuery(){
        global $_W, $_GPC;
        $kwd = $_GPC['keyword'];
        $params = array();
        $params[':uniacid'] = $_W['uniacid'];
        if (!empty($kwd)) {
            $sql = "SELECT a.uid, a.realname, a.nickname, b.uniacid  FROM ".tablename('stonefish_member')." as b  left join ".tablename('mc_members')." as a on a.uid = b.uid WHERE b.uniacid=:uniacid AND (a.realname LIKE :realname Or a.nickname LIKE :nickname)";
            $params[':realname'] = "%{$kwd}%";
			$params[':nickname'] = "%{$kwd}%";
        } else {
            $sql = "SELECT a.uid, a.realname, a.nickname, b.uniacid  FROM ".tablename('stonefish_member')." as b  left join ".tablename('mc_members')." as a on a.uid = b.uid  WHERE b.uniacid=:uniacid ORDER BY b.id DESC LIMIT 10";
        }
        $ds = pdo_fetchall($sql, $params);
        foreach ($ds as $k => $row) {
            $r = array();
            $r['realname'] = $row['realname'];
            $r['nickname'] = $row['nickname'];            
            $r['uid'] = $row['uid'];
            $ds[$k]['entry'] = $r;
        }
        include $this->template('query');
    }
	
	public function doWebFeedback() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_W, $_GPC;
		checklogin();
		//查询是否参数设置过
		$moduleconfig = $this->module['config'];
		if(empty($moduleconfig)){
			message('请先设置会员中心参数配置', Url('profile/module/setting',array('m'=>'stonefish_member')), 'error');
		}
		//查询是否参数设置过
		load()->func('tpl');
		$setting = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_config')." WHERE uniacid = '{$_W['uniacid']}'");
		$op = $_GPC['op'];
		$dos = array('display', 'delfeedback', 'category', 'postcategory', 'delcategory', 'setstatus', 'feedbackview', 'feedbackhuifu');
        $op = in_array($op, $dos) ? $op : 'display';
		if ($op == 'display') {
			if ($_W['ispost'] && $_W['isajax']) {
			    $sql = 'SELECT `uniacid` FROM ' . tablename('stonefish_member_config') . " WHERE `uniacid` = :uniacid";
			    $status = pdo_fetch($sql, array(':uniacid' => $_W['uniacid']));
			    if (empty($status)) {
				    $open = array('uniacid' => $_W['uniacid']);
				    pdo_insert('stonefish_member_config', $open);
			    }
			    $data['feedbackstatus'] = intval($_GPC['status']);
			    if (false === pdo_update('stonefish_member_config', $data, array('uniacid' => $_W['uniacid']))) {
				    exit('error');
			    }
			    exit('success');
		    }
			$category = pdo_fetchall("SELECT * FROM ".tablename('stonefish_member_feedback_category')." WHERE uniacid = '{$_W['uniacid']}' ORDER BY parentid, displayorder DESC, id");
			if(!empty($_GPC['fid'])){
				$cname = pdo_fetchcolumn("SELECT cname FROM ".tablename('stonefish_member_feedback_category')." WHERE id = '{$_GPC['fid']}'");
			}
			if(!empty($_GPC['fid'])){
				$total = pdo_fetchcolumn("SELECT count(id) FROM " . tablename('stonefish_member_feedback') . "  WHERE uniacid = :uniacid and fid=:fid ORDER BY createtime DESC",array(':uniacid' => $_W['uniacid'],':fid' => $_GPC['fid']));
			}else{
				$total = pdo_fetchcolumn("SELECT count(id) FROM " . tablename('stonefish_member_feedback') . "  WHERE uniacid = :uniacid ORDER BY createtime DESC",array(':uniacid' => $_W['uniacid']));
			}
			
            $pindex = max(1, intval($_GPC['page']));
            $psize = 20;
            $pager = pagination($total, $pindex, $psize);
            $start = ($pindex - 1) * $psize;
            $limit .= " LIMIT {$start},{$psize}";
			if(!empty($_GPC['fid'])){
				$record = pdo_fetchall("SELECT * FROM " . tablename('stonefish_member_feedback') . " WHERE uniacid=:uniacid and fid=:fid ORDER BY createtime DESC " . $limit, array(':uniacid' => $_W['uniacid'],':fid' => $_GPC['fid']));
			}else{
				$record = pdo_fetchall("SELECT * FROM " . tablename('stonefish_member_feedback') . " WHERE uniacid=:uniacid ORDER BY createtime DESC " . $limit, array(':uniacid' => $_W['uniacid']));
			}
		    load()->model('mc');
		    foreach ($record as &$records) {			
			    $uid = pdo_fetchcolumn("SELECT uid FROM ".tablename('mc_mapping_fans')." WHERE uniacid = :uniacid and openid = :openid",array(':uniacid' => $_W['uniacid'],':openid' => $records['from_user']));;
				$profile = mc_fetch($uid, array('realname','mobile','avatar'));
			    $records['realname'] = $profile['realname'];
			    $records['mobile'] = $profile['mobile'];
				$records['avatar'] = $profile['avatar'];
		    }			
		}
		if ($op == 'delfeedback') {
			foreach ($_GPC['idArr'] as $k => $id) {
            $id = intval($id);
            if ($id == 0)
                continue;
			    pdo_delete('stonefish_member_feedback', array('id' => $id));
            }
            $this->web_message('留言记录删除成功', '', 1);
		}
		if ($op == 'setstatus') {			
            $id = intval($_GPC['id']);
			$data = ($_GPC['data']==1?'0':'1');
            pdo_update('stonefish_member_feedback', array('status' => $data), array('id' => $id));
            die(json_encode(array("result" => 1, "data" => $data)));
		}
		if ($op == 'category') {
			if (!empty($_GPC['displayorder'])) {
				foreach ($_GPC['displayorder'] as $id => $displayorder) {
					$update = array('displayorder' => $displayorder);
					pdo_update('stonefish_member_feedback_category', $update, array('id' => $id));					
				}
				message('留言分类排序更新成功！', 'refresh', 'success');
			}
			$children = array();
			$category = pdo_fetchall("SELECT * FROM ".tablename('stonefish_member_feedback_category')." WHERE uniacid = '{$_W['uniacid']}' ORDER BY parentid, displayorder DESC, id");
			foreach ($category as $index => $row) {
				if (!empty($row['parentid'])){
					$children[$row['parentid']][] = $row;
					unset($category[$index]);
				}
				$category[$index]['total'] = pdo_fetchcolumn("SELECT count(id) FROM " . tablename('stonefish_member_feedback') . "  WHERE uniacid = :uniacid and fid = :fid ORDER BY createtime DESC",array(':uniacid' => $_W['uniacid'],':fid' => $row['id']));
			}
		}
		if($op == 'postcategory'){
	        $parentid = intval($_GPC['parentid']);
	        $id = intval($_GPC['id']);		    
	        if(!empty($id)) {
		        $category = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_feedback_category')." WHERE id = '$id' AND uniacid = {$_W['uniacid']}");
		        if(empty($category)) {
			        message('留言分类不存在或已删除', '', 'error');
	        	}		        
	        } else {
		        $category = array(
			        'displayorder' => 0,			       
		        );
	        }
	        if (!empty($parentid)) {
		        $parent = pdo_fetch("SELECT id, cname FROM ".tablename('stonefish_member_feedback_category')." WHERE id = '$parentid'");
		        if (empty($parent)) {
			        message('抱歉，上级分类不存在或是已经被删除！', url('site/entry/feedback', array('op'=>'category','m' => 'stonefish_member')), 'error');
		        }
	        }

	        if (checksubmit('submit')) {
		        if (empty($_GPC['cname'])) {
			        message('抱歉，请输入分类名称！');
		        }
		        $data = array(
			        'uniacid' => $_W['uniacid'],
			        'cname' => $_GPC['cname'],
			        'displayorder' => intval($_GPC['displayorder']),
			        'parentid' => intval($parentid),
			        'description' => $_GPC['description'],
					'pagesize' => $_GPC['pagesize'],
					'topimgurl' => $_GPC['topimgurl'],
					'pagecolor' => $_GPC['pagecolor'],
					'status' => $_GPC['status'],
					'open' => $_GPC['open'],
					'feedtype' => $_GPC['feedtype'],
		        );		       
		        
		        if (!empty($id)) {
			        unset($data['parentid']);
			        pdo_update('stonefish_member_feedback_category', $data, array('id' => $id));
		        } else {
			        pdo_insert('stonefish_member_feedback_category', $data);
			        $id = pdo_insertid();
		        }
		        message('更新分类成功！', url('site/entry/feedback', array('op'=>'category','m' => 'stonefish_member')), 'success');
	        }
		}
		if ($op == 'delcategory') {
			$id = intval($_GPC['id']);
	        $category = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_feedback_category')." WHERE id = '$id'");
	        if (empty($category)) {
		        message('抱歉，留言分类不存在或是已经被删除！', url('site/entry/feedback', array('op'=>'category','m' => 'stonefish_member')), 'error');
	        }
			pdo_delete('stonefish_member_feedback_category', array('id' => $id, 'parentid' => $id), 'OR');
			pdo_delete('stonefish_member_feedback', array('fid' => $id));
			message('分类删除成功！', url('site/entry/feedback', array('op'=>'category','m' => 'stonefish_member')), 'success');
		}		
		if ($op == 'feedbackview') {
		    if($_W['isajax']) {
				$id = intval($_GPC['id']);
	            $feedback = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_feedback')." WHERE id = '$id'");
	            if (empty($feedback)) {
		            echo '留言不存在';
					exit;
	            }else{
					$uid = pdo_fetchcolumn("SELECT uid FROM ".tablename('mc_mapping_fans')." WHERE uniacid = :uniacid and openid = :openid",array(':uniacid' => $_W['uniacid'],':openid' => $feedback['from_user']));
					load()->model('mc');
					$data = mc_fetch($uid, array('realname','mobile','uid'));
				}
				include $this->template('feedbackview');
				exit();
		    }			
		}
		
		if ($op == 'feedbackhuifu') {
			$id = intval($_GPC['id']);
	        $feedback = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_feedback')." WHERE id = '$id'");
	        if (empty($feedback)) {
		        message('抱歉，留言内容不存在或已删除！', url('site/entry/feedback', array('m' => 'stonefish_member','fid' => $_GPC['fid'],'page' => $_GPC['page'])), 'error');
	        }else{
				pdo_update('stonefish_member_feedback', array('contents' => $_GPC['contents'],'contentstatus' => 1), array('id' => $id));
				message('回复成功！', url('site/entry/feedback', array('m' => 'stonefish_member','fid' => $_GPC['fid'],'page' => $_GPC['page'])), 'success');
			}
		}
		
		include $this->template('feedback');
	}
	
	public function doWebTask() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_W, $_GPC;
		checklogin();
		//查询是否参数设置过
		$moduleconfig = $this->module['config'];
		if(empty($moduleconfig)){
			message('请先设置会员中心参数配置', Url('profile/module/setting',array('m'=>'stonefish_member')), 'error');
		}
		//查询是否参数设置过
		load()->func('tpl');
		load()->model('mc');
		$op = $_GPC['op'];
		$dos = array('display', 'post', 'postalone', 'del', 'record');
        $op = in_array($op, $dos) ? $op : 'display';
		$creditnames = array();
		$unisettings = uni_setting($_W['uniacid'], array('creditnames'));
		foreach ($unisettings['creditnames'] as $key=>$credit) {
			if (!empty($credit['enabled'])) {
				$creditnames[$key] = $credit['title'];
			}
		}
		if($op == 'display') {
			$pindex = max(1, intval($_GPC['page']));
		    $psize = 30;
		    $condition = '';
		    if(!empty($_GPC['keyword'])) {
			    $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
		    }
		    if(intval($_GPC['groupid'])) {
			    $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('stonefish_member_message') . " WHERE uniacid = '{$_W['uniacid']}' AND type = 1 " . $condition . "  AND messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid = '{$_GPC['groupid']}')");
			    $list = pdo_fetchall('SELECT * FROM ' . tablename('stonefish_member_message') . " WHERE uniacid = '{$_W['uniacid']}' AND type = 1 " . $condition . " AND  messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid = '{$_GPC['groupid']}') ORDER BY messageid DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
		    } else {
			    $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('stonefish_member_message') . " WHERE uniacid = '{$_W['uniacid']}' AND type = 1" . $condition);
			    $list = pdo_fetchall('SELECT * FROM ' . tablename('stonefish_member_message') . " WHERE uniacid = '{$_W['uniacid']}' AND type = 1" . $condition . " ORDER BY messageid DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
		    }
			$groupall = pdo_fetchall('SELECT groupid,title FROM ' . tablename('mc_groups') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY orderlist DESC");
			foreach($list as &$lists) {
			    if(!empty($lists['thumb'])) {
				    $lists['thumb'] = tomedia($lists['thumb']);
			    }
				//所属会员组
				$coupongroup = pdo_fetchall('SELECT groupid FROM ' . tablename('stonefish_member_message_allocation') . " WHERE uniacid = '{$_W['uniacid']}' AND messageid = '{$lists['messageid']}'");
				if(!empty($coupongroup)) {
		            $grouptitle = '';
					foreach($coupongroup as $cgroup) {
			            $group_title = pdo_fetchcolumn('SELECT title FROM ' . tablename('mc_groups') . " WHERE uniacid = '{$_W['uniacid']}' and groupid = '{$cgroup['groupid']}' ORDER BY orderlist DESC");
						$grouptitle .= $group_title.';';
		            }
					$lists['grouptitle'] = substr($grouptitle,0,strlen($grouptitle)-1);
	            }				
				//所属会员组
				//所属UID
				$uid = pdo_fetchcolumn('SELECT uid FROM ' . tablename('stonefish_member_message_allocation') . " WHERE uniacid = '{$_W['uniacid']}' AND messageid = '{$lists['messageid']}'");
				if($uid){
			        $members = mc_fetch($uid, array('realname'));
					$lists['grouptitle'] = '会员UID:'.$uid.' '.$members['realname'];
					$lists['uid'] = 1;
				}
				//所属UID
				//查看人次
				$lists['view'] = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('stonefish_member_message_record')." WHERE uniacid = '{$_W['uniacid']}' and messageid = '{$lists['messageid']}' ORDER BY recid DESC");
				//查看人次
		    }
			$pager = pagination($total, $pindex, $psize);
	    }

        if($op == 'post') {
	        $messageid = intval($_GPC['id']);
	        $item = pdo_fetch('SELECT * FROM ' . tablename('stonefish_member_message') . " WHERE uniacid = '{$_W['uniacid']}' AND messageid = '{$messageid}'");
		        if(empty($item) || $messageid == 0) {
		        $item['starttime'] = time();
		        $item['endtime'] = time() + 6 * 86400;
				$item['credit'] = 10;
				$item['creditshare'] = 0;
				$item['creditview'] = 1;
				$item['limit'] = 1;
				$item['amount'] = 1000;
				$item['share_cancel'] = '取消分享任务无法完成！';
				$item['share_fail'] = '网络出错，请重新分享！';
				$item['share_confirm'] = '任务分享成功，感谢您的参与！';
	        }
		    $coupongroup = pdo_fetchall('SELECT groupid FROM ' . tablename('stonefish_member_message_allocation') . " WHERE uniacid = '{$_W['uniacid']}' AND messageid = '{$messageid}'");
	        if(!empty($coupongroup)) {
		        foreach($coupongroup as $cgroup) {
			        $grouparr[] = $cgroup['groupid'];
		        }
	        }
		    $group = pdo_fetchall('SELECT groupid,title FROM ' . tablename('mc_groups') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY orderlist DESC");
	        if(!empty($grouparr)) {
		        foreach($group as &$g){
			        if(in_array($g['groupid'], $grouparr)) {
				        $g['groupid_select'] = 1;
			        }
		        }
	        }
	        if(checksubmit('submit')) {
			    $title = !empty($_GPC['title']) ? trim($_GPC['title']) : message('请输入群发任务标题！');
			    $groups = !empty($_GPC['group']) ? $_GPC['group'] : message('请选择会员组！');
			    $thumb = !empty($_GPC['thumb']) ? $_GPC['thumb'] : message('请上传群发任务缩略图！');
			    $description = !empty($_GPC['description']) ? trim($_GPC['description']) : message('请填写群发任务内容！');	
				$credittype = !empty($_GPC['credittype']) ? trim($_GPC['credittype']) : message('请选择任务奖励积分类型！');
				$credit = isset($_GPC['credit']) ? trim($_GPC['credit']) : message('请输入任务奖励积分最多数量！');
				$creditshare = isset($_GPC['creditshare']) ? trim($_GPC['creditshare']) : message('请输入分享任务奖励积分数量！');
				$creditview = isset($_GPC['creditview']) ? trim($_GPC['creditview']) : message('请输入阅读任务奖励积分数量！');
				$limit = !empty($_GPC['limit']) ? trim($_GPC['limit']) : message('请输入每人可使用数量！');
				$amount = !empty($_GPC['amount']) ? trim($_GPC['amount']) : message('请输入任务总数量！');
				$sharetitle = !empty($_GPC['sharetitle']) ? trim($_GPC['sharetitle']) : message('请输入分享标题！');
				$sharedesc = !empty($_GPC['sharedesc']) ? trim($_GPC['sharedesc']) : message('请输入分享简介！');
				$shareurl = !empty($_GPC['shareurl']) ? trim($_GPC['shareurl']) : message('请选择或输入分享网址！');
				$share_cancel = !empty($_GPC['share_cancel']) ? trim($_GPC['share_cancel']) : message('请输入分享取消时的提示词！');
				$share_fail = !empty($_GPC['share_fail']) ? trim($_GPC['share_fail']) : message('请输入分享时网络出错的提示词！');
				$share_confirm = !empty($_GPC['share_confirm']) ? trim($_GPC['share_confirm']) : message('请输入分享成功后的提示词！');
			    $starttime = strtotime($_GPC['datelimit']['start']);
			    $endtime = strtotime($_GPC['datelimit']['end']);
			    if($endtime == $starttime) {
				    $endtime = $endtime + 86399;
			    }
			    $data = array(
				    'uniacid' => $_W['uniacid'],
				    'title' => $title,
				    'type' => '1',
					'credit' => $credit,
					'creditshare' => $creditshare,
					'creditview' => $creditview,
					'credittype' => $credittype,
					'limit' => $limit,
					'amount' => $amount,
				    'thumb' => $thumb,
					'sharetitle' => $sharetitle,
					'sharedesc' => $sharedesc,
					'shareurl' => $shareurl,
					'share_cancel' => $share_cancel,
					'share_fail' => $share_fail,
					'share_confirm' => $share_confirm,
				    'description' => $description,				    
				    'starttime' => $starttime,
				    'endtime' => $endtime,
			    );
			    if ($messageid) {
				    pdo_update('stonefish_member_message', $data, array('uniacid' => $_W['uniacid'], 'messageid' => $messageid));
			    } else {
				    pdo_insert('stonefish_member_message', $data);
				    $messageid = pdo_insertid();
			    }
			    pdo_delete('stonefish_member_message_allocation', array('uniacid' => $_W['uniacid'], 'messageid' => $messageid));
			    if(!empty($groups) && $messageid) {
				    foreach($groups as $gid) {
					    $gid = intval($gid);
					    $insert = array(
						    'uniacid' => $_W['uniacid'],
						    'messageid' => $messageid,
						    'groupid' => $gid
					    );
					    pdo_insert('stonefish_member_message_allocation', $insert) ? '' : message('抱歉，群发任务更新失败！', referer(), 'error');
					    unset($insert);
				    }
			    }
			    message('群发任务更新成功！', url('site/entry/task', array('m' => 'stonefish_member')), 'success');
		    }
	    }
		
		if($op == 'postalone') {
	        $messageid = intval($_GPC['id']);
	        $item = pdo_fetch('SELECT * FROM ' . tablename('stonefish_member_message') . " WHERE uniacid = '{$_W['uniacid']}' AND messageid = '{$messageid}'");
		    if(empty($item) || $messageid == 0) {
		        $item['starttime'] = time();
		        $item['endtime'] = time() + 6 * 86400;
				$item['credit'] = 10;
				$item['creditshare'] = 0;
				$item['creditview'] = 1;
				$item['share_cancel'] = '取消分享任务无法完成！';
				$item['share_fail'] = '网络出错，请重新分享！';
				$item['share_confirm'] = '任务分享成功，感谢您的参与！';
	        }
			$uid = pdo_fetchcolumn('SELECT uid FROM ' . tablename('stonefish_member_message_allocation') . " WHERE uniacid = '{$_W['uniacid']}' AND messageid = '{$messageid}'");
	        if(checksubmit('submit')) {
			    $title = !empty($_GPC['title']) ? trim($_GPC['title']) : message('请输入任务标题！');
			    $uid = !empty($_GPC['uid']) ? $_GPC['uid'] : message('请选择会员！');
			    $thumb = !empty($_GPC['thumb']) ? $_GPC['thumb'] : message('请上传任务缩略图！');
			    $description = !empty($_GPC['description']) ? trim($_GPC['description']) : message('请填写任务内容！');	
				$credittype = !empty($_GPC['credittype']) ? trim($_GPC['credittype']) : message('请选择任务奖励积分类型！');
				$credit = !empty($_GPC['credit']) ? trim($_GPC['credit']) : message('请输入任务奖励积分最多数量！');
				$creditshare = !empty($_GPC['creditshare']) ? trim($_GPC['creditshare']) : message('请输入分享任务奖励积分数量！');
				$creditview = !empty($_GPC['creditview']) ? trim($_GPC['creditview']) : message('请输入阅读任务奖励积分数量！');
				$sharetitle = !empty($_GPC['sharetitle']) ? trim($_GPC['sharetitle']) : message('请输入分享标题！');
				$sharedesc = !empty($_GPC['sharedesc']) ? trim($_GPC['sharedesc']) : message('请输入分享简介！');
				$shareurl = !empty($_GPC['shareurl']) ? trim($_GPC['shareurl']) : message('请选择或输入分享网址！');
				$share_cancel = !empty($_GPC['share_cancel']) ? trim($_GPC['share_cancel']) : message('请输入分享取消时的提示词！');
				$share_fail = !empty($_GPC['share_fail']) ? trim($_GPC['share_fail']) : message('请输入分享时网络出错的提示词！');
				$share_confirm = !empty($_GPC['share_confirm']) ? trim($_GPC['share_confirm']) : message('请输入分享成功后的提示词！');
			    $starttime = strtotime($_GPC['datelimit']['start']);
			    $endtime = strtotime($_GPC['datelimit']['end']);
			    if($endtime == $starttime) {
				    $endtime = $endtime + 86399;
			    }			   
			    $data = array(
				    'uniacid' => $_W['uniacid'],
				    'title' => $title,
				    'type' => '1',
					'credit' => $credit,
					'creditshare' => $creditshare,
					'creditview' => $creditview,
					'credittype' => $credittype,
					'limit' => 1,
					'amount' => 1,
				    'thumb' => $thumb,
					'sharetitle' => $sharetitle,
					'sharedesc' => $sharedesc,
					'shareurl' => $shareurl,
					'share_cancel' => $share_cancel,
					'share_fail' => $share_fail,
					'share_confirm' => $share_confirm,
				    'description' => $description,				    
				    'starttime' => $starttime,
				    'endtime' => $endtime,
			    );
			    if ($messageid) {
				    pdo_update('stonefish_member_message', $data, array('uniacid' => $_W['uniacid'], 'messageid' => $messageid));
			    } else {
				    pdo_insert('stonefish_member_message', $data);
				    $messageid = pdo_insertid();
			    }
			    pdo_delete('stonefish_member_message_allocation', array('uniacid' => $_W['uniacid'], 'messageid' => $messageid));
			    if($uid && $messageid) {
					$insert = array(
						'uniacid' => $_W['uniacid'],
						'messageid' => $messageid,
						'uid' => $uid
					);
					pdo_insert('stonefish_member_message_allocation', $insert) ? '' : message('抱歉，任务更新失败！', referer(), 'error');
			    }
			    message('任务更新成功！', url('site/entry/task', array('m' => 'stonefish_member')), 'success');
		    }
	    }

	    if($op == 'del') {
		    $id = intval($_GPC['id']);
		    $row = pdo_fetch("SELECT messageid FROM ".tablename('stonefish_member_message')." WHERE uniacid = '{$_W['uniacid']}' AND messageid = :messageid", array(':messageid' => $id));
		    if (empty($row)) {
			    message('抱歉，任务不存在或是已经被删除！');
		    }
		    pdo_delete('stonefish_member_message_allocation', array('uniacid' => $_W['uniacid'],'messageid' => $id));
		    pdo_delete('stonefish_member_message', array('messageid' => $id, 'uniacid' => $_W['uniacid']));
		    message('群发任务删除成功！',url('site/entry/task', array('m' => 'stonefish_member')), 'success');
	    }  
		
	    if($op == 'record') {
			$coupons = pdo_fetchall('SELECT messageid, title FROM ' . tablename('stonefish_member_message') . ' WHERE uniacid = :uniacid AND type = 1 ORDER BY messageid DESC', array(':uniacid' => $_W['uniacid']), 'messageid');
		    $starttime = empty($_GPC['time']['start']) ? strtotime('-1 month') : strtotime($_GPC['time']['start']);
		    $endtime = empty($_GPC['time']['end']) ? TIMESTAMP : strtotime($_GPC['time']['end']) + 86399;
	
		    $where = " WHERE a.uniacid = {$_W['uniacid']} AND b.type = 1 AND a.granttime>=:starttime AND a.granttime<:endtime";
		    $params = array(
			    ':starttime' => $starttime,
			    ':endtime' => $endtime,
		    );
		    $uid = intval($_GPC['uid']);
		    if (!empty($uid)) {
			    $where .= ' AND a.uid=:uid';
			    $params[':uid'] = $uid;
		    }
		    $messageid = intval($_GPC['messageid']);
		    if (!empty($messageid)) {
			    $where .= " AND a.messageid = {$messageid}";
		    }
		    $pindex = max(1, intval($_GPC['page']));
		    $psize = 20;
	
		    $list = pdo_fetchall("SELECT a.*, b.title,b.thumb FROM ".tablename('stonefish_member_message_record'). ' AS a LEFT JOIN ' . tablename('stonefish_member_message') . ' AS b ON a.messageid = b.messageid ' . " $where ORDER BY a.messageid DESC,a.recid DESC LIMIT ".($pindex - 1) * $psize.','.$psize, $params);
		    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('stonefish_member_message_record') . ' AS a LEFT JOIN ' . tablename('stonefish_member_message') . ' AS b ON a.messageid = b.messageid '. $where , $params);
		    if(!empty($list)) {
			    foreach ($list as &$row) {
					$members = mc_fetch($row['uid'], array('nickname','realname'));
				    $row['nickname'] = $members['realname'].'['.$members['nickname'].']';
				    $row['thumb'] = tomedia($row['thumb']);
			    }
		    }
		    $pager = pagination($total, $pindex, $psize);
			
	    }
		include $this->template('task');
	}
	
	public function doWebMember() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_W, $_GPC;
		checklogin();
		//查询是否参数设置过
		$moduleconfig = $this->module['config'];
		if(empty($moduleconfig)){
			message('请先设置会员中心参数配置', Url('profile/module/setting',array('m'=>'stonefish_member')), 'error');
		}
		//查询是否参数设置过
		$op = $_GPC['op'];
		$dos = array('display', 'post','del');
		$op = in_array($op, $dos) ? $op : 'display';
		load()->model('mc');
		if($op == 'display') {
			$_W['page']['title'] = '会员列表 - 会员 - 会员中心';
			$config = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_config')." WHERE uniacid = '{$_W['uniacid']}' order by id desc");
			if(empty($config['levelcredit'])){
				message('等级头衔没有设置，请先设置等级头衔', $this->createWebUrl('level'), 'error');
			}
			$groups = mc_groups();
			$pindex = max(1, intval($_GPC['page']));
			$psize = 30;
			$condition = '';
			$condition .= empty($_GPC['mobile']) ? '' : " AND a.mobile LIKE '%".trim($_GPC['mobile'])."%'";
			$condition .= empty($_GPC['email']) ? '' : " AND a.email LIKE '%".trim($_GPC['email'])."%'";
			$condition .= empty($_GPC['username']) ? '' : " AND a.realname LIKE '%".trim($_GPC['username'])."%' OR a.nickname LIKE '%".trim($_GPC['username'])."%' ";
			$condition .= intval($_GPC['groupid']) > 0 ?  " AND a.groupid = '".intval($_GPC['groupid'])."'" : '';
			$list = pdo_fetchall("SELECT a.uid, a.uniacid, a.groupid, a.avatar, a.realname, a.nickname, a.email, a.mobile, a.".$config['levelcredit'].", b.createtime  FROM ".tablename('stonefish_member')." as b  left join ".tablename('mc_members')." as a on a.uid = b.uid  WHERE a.uniacid = '{$_W['uniacid']}' ".$condition." ORDER BY b.createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
			//查询等级
			foreach ($list as $mid => $lists) {
				$levelall = pdo_fetch("SELECT grade FROM ".tablename('stonefish_member_level')." WHERE uniacid = '{$_W['uniacid']}' AND integral_start<='{$lists[$config['levelcredit']]}' AND integral_end>='{$lists[$config['levelcredit']]}'");
				$list[$mid]['leveltitle'] = $levelall['grade'];
			}
			//查询等级
			$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('stonefish_member')." as b  left join ".tablename('mc_members')." as a on a.uid = b.uid WHERE a.uniacid = '{$_W['uniacid']}' ".$condition);
			$pager = pagination($total, $pindex, $psize);
		}		
		if($op == 'post') {
			$_W['page']['title'] = '编辑会员资料 - 会员 - 会员中心';
			$uid = intval($_GPC['uid']);
			if ($_W['ispost'] && $_W['isajax']) {
				$uid = $_GPC['uid'];
				$password = $_GPC['password'];
				$sql = 'SELECT `uid`, `salt` FROM ' . tablename('mc_members') . " WHERE `uniacid`=:uniacid AND `uid` = :uid";
				$user = pdo_fetch($sql, array(':uniacid' => $_W['uniacid'], ':uid' => $uid));
				if(empty($user) || $user['uid'] != $uid) {
					exit('error');
				}
				$password = md5($password . $user['salt'] . $_W['config']['setting']['authkey']);
				if (pdo_update('mc_members', array('password' => $password), array('uid' => $uid))) {
					exit('success');
				}
				exit('othererror');
			}
			if (checksubmit('submit')) {
				$uid = intval($_GPC['uid']);
				if (!empty($_GPC)) {
					if (!empty($_GPC['birth'])) {
						$_GPC['birthyear'] = $_GPC['birth']['year'];
						$_GPC['birthmonth'] = $_GPC['birth']['month'];
						$_GPC['birthday'] = $_GPC['birth']['day'];
					}
					if (!empty($_GPC['reside'])) {
						$_GPC['resideprovince'] = $_GPC['reside']['province'];
						$_GPC['residecity'] = $_GPC['reside']['city'];
						$_GPC['residedist'] = $_GPC['reside']['district'];
					}
					if (empty($_GPC['email']) && empty($_GPC['mobile'])) {
						$_GPC['email'] = md5($_GPC['openid']) . '@012wz.com';
					}
					unset($_GPC['uid']);
					$uid = mc_update($uid, $_GPC);
					if (!empty($_GPC['fanid']) && !empty($uid)) {
						pdo_update('mc_mapping_fans', array('uid' => $uid), array('fanid' => $_GPC['fanid']));
					}
				}
				message('更新资料成功！', referer(), 'success');
			}
	
			load()->func('tpl');
			$groups = mc_groups($_W['uniacid']);
			$profile = mc_fetch($uid);
			if(empty($uid)) {
				$fanid = intval($_GPC['fanid']);
				$tag = pdo_fetchcolumn('SELECT tag FROM ' . tablename('mc_mapping_fans') . ' WHERE uniacid = :uniacid AND fanid = :fanid', array(':uniacid' => $_W['uniacid'], ':fanid' => $fanid));
				$fan = iunserializer($tag) ? iunserializer($tag) : array();
				if(!empty($tag)) {
					if(!empty($fan['nickname'])) {
						$profile['nickname'] = $fan['nickname'];
					}
					if(!empty($fan['sex'])) {
						$profile['gender'] = $fan['sex'];
					}
					if(!empty($fan['city'])) {
						$profile['residecity'] = $fan['city'] . '市';
					}
					if(!empty($fan['province'])) {
						$profile['resideprovince'] = $fan['province'] . '省';
					}
					if(!empty($fan['country'])) {
						$profile['nationality'] = $fan['country'];
					}
					if(!empty($fan['headimgurl'])) {
						$profile['avatar'] = rtrim($fan['headimgurl'], '0') . 132;
					}
				}
			}
		}

		if($op == 'del') {
			$_W['page']['title'] = '删除会员资料 - 会员 - 会员中心';
			if(checksubmit('submit')) {
				if(!empty($_GPC['uid'])) {
					$instr = implode(',',$_GPC['uid']);
					pdo_query("DELETE FROM ".tablename('mc_members')." WHERE `uniacid` = {$_W['uniacid']} AND `uid` IN ({$instr})");
					message('删除成功！', referer(), 'success');
				}
				message('请选择要删除的项目！', referer(), 'error');
			}
		}
		
		include $this->template('member');
	}
	
	public function doWebTemplate() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_W, $_GPC;
		checklogin();
		//查询是否参数设置过
		$moduleconfig = $this->module['config'];
		if(empty($moduleconfig)){
			message('请先设置会员中心参数配置', Url('profile/module/setting',array('m'=>'stonefish_member')), 'error');
		}
		//查询是否参数设置过
		$uniacid = $_W['uniacid'];
		load()->func('tpl');
		//查询子公众号信息
		$acid_arr=uni_accounts();
		$ids = array();
		$ids = array_map('array_shift', $acid_arr);//子公众账号Arr数组
		$ids_num = count($ids);//多少个子公众账号
		$one = current($ids);
		if($_GPC['acid']){
			$acid = $_GPC['acid'];
			$acidsql = 'acid = '.$acid.' AND ';
		}
		//查询子公众号信息		
	    $uncount = uni_fetch($uniacid);
		$account = account_fetch($acid);
	    $_W['page']['title'] = $uncount['name'].$account['name'] . '会员风格管理';
		
		include $this->template('template');
	}
	
	public function doWebFanslog() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_W, $_GPC;
		checklogin();
		//查询是否参数设置过
		$moduleconfig = $this->module['config'];
		if(empty($moduleconfig)){
			message('请先设置会员中心参数配置', Url('profile/module/setting',array('m'=>'stonefish_member')), 'error');
		}
		//查询是否参数设置过
		$uniacid = $_W['uniacid'];
		load()->func('tpl');
		//查询子公众号信息
		$acid_arr=uni_accounts();
		$ids = array();
		$ids = array_map('array_shift', $acid_arr);//子公众账号Arr数组
		$ids_num = count($ids);//多少个子公众账号
		$one = current($ids);
		if($_GPC['acid']){
			$acid = $_GPC['acid'];
			$acidsql = 'acid = '.$acid.' AND ';
		}
		//查询子公众号信息		
	    $uncount = uni_fetch($uniacid);
		$account = account_fetch($acid);
	    $_W['page']['title'] = $uncount['name'].$account['name'] . '粉丝统计详细信息';
		
	    $scroll = intval($_GPC['scroll']);
	    $add_num = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('mc_mapping_fans') . ' WHERE '.$acidsql.' uniacid = :uniacid AND follow = :follow AND followtime >= :starttime AND followtime <= :endtime', array(':uniacid' => $uniacid, ':starttime' => strtotime(date('Y-m-d')) - 86400, ':endtime' => strtotime(date('Y-m-d')), ':follow' => 1));
	    $cancel_num = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('mc_mapping_fans') . ' WHERE '.$acidsql.' uniacid = :uniacid AND follow = :follow AND unfollowtime >= :starttime AND unfollowtime <= :endtime', array(':uniacid' => $uniacid, ':starttime' => strtotime(date('Y-m-d')) - 86400, ':endtime' => strtotime(date('Y-m-d')), ':follow' => 0));
	    $jing_num = $add_num - $cancel_num;
	    $total_num = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('mc_mapping_fans') . ' WHERE '.$acidsql.' uniacid = :uniacid AND follow = :follow AND followtime <= :endtime', array(':uniacid' => $uniacid, ':endtime' => strtotime(date('Y-m-d')), ':follow' => 1));

	    $today_add_num = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('mc_mapping_fans') . ' WHERE '.$acidsql.' uniacid = :uniacid AND follow = :follow AND followtime >= :starttime AND followtime <= :endtime', array(':uniacid' => $uniacid, ':starttime' => strtotime(date('Y-m-d')), ':endtime' => TIMESTAMP, ':follow' => 1));
	    $today_cancel_num = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('mc_mapping_fans') . ' WHERE '.$acidsql.' uniacid = :uniacid AND follow = :follow AND unfollowtime >= :starttime AND unfollowtime <= :endtime', array(':uniacid' => $uniacid, ':starttime' => strtotime(date('Y-m-d')), ':endtime' => TIMESTAMP, ':follow' => 0));
	    $today_jing_num = $today_add_num - $today_cancel_num;
	    $today_total_num = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('mc_mapping_fans') . ' WHERE '.$acidsql.' uniacid = :uniacid AND follow = :follow AND followtime <= :endtime', array(':uniacid' => $uniacid, ':endtime' => TIMESTAMP, ':follow' => 1));

	    $st = $_GPC['datelimit']['start'] ? strtotime($_GPC['datelimit']['start']) : strtotime('-30day');
	    $et = $_GPC['datelimit']['end'] ? strtotime($_GPC['datelimit']['end']) : strtotime(date('Y-m-d'));
	    $starttime = min($st, $et);
	    $endtime = max($st, $et);
	    $day_num = ($endtime - $starttime) / 86400 + 1;
	    $endtime += 86399;
	    $type = intval($_GPC['type']) ? intval($_GPC['type']) : 1;
		    if($_W['isajax'] && $_W['ispost']) {
		    $days = array();
		    $datasets = array();
		    for($i = 0; $i < $day_num; $i++){
			    $key = date('m-d', $starttime + 86400 * $i);
			    $days[$key] = 0;
			    $datasets['flow1'][$key] = 0;
			    $datasets['flow2'][$key] = 0;
			    $datasets['flow3'][$key] = 0;
			    $datasets['flow4'][$key] = 0;
		    }

			$data = pdo_fetchall('SELECT followtime FROM ' . tablename('mc_mapping_fans') . ' WHERE '.$acidsql.' uniacid = :uniacid AND follow = :follow AND followtime >= :starttime AND followtime <= :endtime', array(':uniacid' => $uniacid, ':starttime' => $starttime, ':endtime' => $endtime, ':follow' => 1));
		    foreach($data as $da) {
			    $key = date('m-d', $da['followtime']);
			    if(in_array($key, array_keys($days))) {
				    $datasets['flow1'][$key]++;
			    }
		    }

			$data = pdo_fetchall('SELECT unfollowtime FROM ' . tablename('mc_mapping_fans') . ' WHERE '.$acidsql.' uniacid = :uniacid AND follow = :follow AND unfollowtime >= :starttime AND unfollowtime <= :endtime', array(':uniacid' => $uniacid, ':starttime' => $starttime, ':endtime' => $endtime, ':follow' => 0));
		    foreach($data as $da) {
			    $key = date('m-d', $da['unfollowtime']);
			    if(in_array($key, array_keys($days))) {
				    $datasets['flow2'][$key]++;
			    }
		    }

			$data0 = pdo_fetchall('SELECT unfollowtime FROM ' . tablename('mc_mapping_fans') . ' WHERE '.$acidsql.' uniacid = :uniacid AND follow = :follow AND unfollowtime >= :starttime AND unfollowtime <= :endtime', array(':uniacid' => $uniacid, ':starttime' => $starttime, ':endtime' => $endtime, ':follow' => 0));
		    $data1 = pdo_fetchall('SELECT followtime FROM ' . tablename('mc_mapping_fans') . ' WHERE '.$acidsql.' uniacid = :uniacid AND follow = :follow AND followtime >= :starttime AND followtime <= :endtime', array(':uniacid' => $uniacid, ':starttime' => $starttime, ':endtime' => $endtime, ':follow' => 1));
		    foreach($data1 as $da) {
			    $key = date('m-d', $da['followtime']);
			    if(in_array($key, array_keys($days))) {
				    $day[date('m-d', $da['followtime'])] ++;
				    $datasets['flow3'][$key]++;
			    }
		    }
		    foreach($data0 as $da) {
			    $key = date('m-d', $da['unfollowtime']);
			    if(in_array($key, array_keys($days))) {
				    $datasets['flow3'][$key]--;
			    }
		    }

			for($i = 0; $i < $day_num; $i++){
			    $key = date('m-d', $starttime + 86400 * $i);
			    $datasets['flow4'][$key] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('mc_mapping_fans') . ' WHERE '.$acidsql.' uniacid = :uniacid AND follow = :follow AND followtime < ' . ($starttime + 86400 * $i + 86439), array(':uniacid' => $uniacid, ':follow' => 1));;
		    }

		    $shuju['label'] = array_keys($days);
		    $shuju['datasets'] = $datasets;
		
		    if ($day_num == 1) {
			    $day_num = 2;
			    $shuju['label'][] = $shuju['label'][0];
			
			    foreach ($shuju['datasets']['flow1'] as $ky => $va) {
				    $k = $ky;
				    $v = $va;
			    }
			    $shuju['datasets']['flow1']['-'] = $v;
			
			    foreach ($shuju['datasets']['flow2'] as $ky => $va) {
				    $k = $ky;
				    $v = $va;
			    }
			    $shuju['datasets']['flow2']['-'] = $v;
			
			    foreach ($shuju['datasets']['flow3'] as $ky => $va) {
				    $k = $ky;
				    $v = $va;
			    }
			    $shuju['datasets']['flow3']['-'] = $v;
			
			    foreach ($shuju['datasets']['flow4'] as $ky => $va) {
				    $k = $ky;
				    $v = $va;
			    }
			    $shuju['datasets']['flow4']['-'] = $v;
		    }

		    $shuju['datasets']['flow1'] = array_values($shuju['datasets']['flow1']);
		    $shuju['datasets']['flow2'] = array_values($shuju['datasets']['flow2']);
		    $shuju['datasets']['flow3'] = array_values($shuju['datasets']['flow3']);
		    $shuju['datasets']['flow4'] = array_values($shuju['datasets']['flow4']);
		    exit(json_encode($shuju));
	    }
		include $this->template('fanslog');
	}
	
	public function doWebMemberlog() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_W, $_GPC;
		checklogin();
		//查询是否参数设置过
		$moduleconfig = $this->module['config'];
		if(empty($moduleconfig)){
			message('请先设置会员中心参数配置', Url('profile/module/setting',array('m'=>'stonefish_member')), 'error');
		}
		//查询是否参数设置过
		$uniacid = $_W['uniacid'];
		load()->func('tpl');
		//查询子公众号信息
		$acid_arr=uni_accounts();
		$ids = array();
		$ids = array_map('array_shift', $acid_arr);//子公众账号Arr数组
		$ids_num = count($ids);//多少个子公众账号
		//查询子公众号信息		
		if($_GPC['acid']){
			$acid = $_GPC['acid'];
			$acidsql = 'b.acid = '.$acid.' AND ';
		}
		//查询是否开启短信验证
		$config = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_config')." WHERE uniacid = '{$uniacid}' order by id desc");
		if($config['smsstatus']){
			$acidsql .= "a.status = 2 AND";
		}
		//查询是否开启短信验证
		$uncount = uni_fetch($uniacid);
		$account = account_fetch($acid);
	    $_W['page']['title'] = $uncount['name'].$account['name'] . '会员统计详细信息';
	
	    $scroll = intval($_GPC['scroll']);
	    $add_num = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('stonefish_member') . ' as a left join '.tablename('mc_mapping_fans').' as b on a.uniacid=b.uniacid and a.uid=b.uid WHERE '.$acidsql.' a.uniacid = :uniacid AND b.follow = :follow AND a.createtime >= :starttime AND a.createtime <= :endtime', array(':uniacid' => $uniacid, ':starttime' => strtotime(date('Y-m-d')) - 86400, ':endtime' => strtotime(date('Y-m-d')), ':follow' => 1));
	    $cancel_num = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('stonefish_member') . ' as a left join '.tablename('mc_mapping_fans').' as b on a.uniacid=b.uniacid and a.uid=b.uid WHERE '.$acidsql.' a.uniacid = :uniacid AND b.follow = :follow AND b.unfollowtime >= :starttime AND b.unfollowtime <= :endtime', array(':uniacid' => $uniacid, ':starttime' => strtotime(date('Y-m-d')) - 86400, ':endtime' => strtotime(date('Y-m-d')), ':follow' => 0));
	    $jing_num = $add_num - $cancel_num;
	    $total_num = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('stonefish_member') . ' as a left join '.tablename('mc_mapping_fans').' as b on a.uniacid=b.uniacid  and a.uid=b.uid WHERE '.$acidsql.' a.uniacid = :uniacid AND b.follow = :follow AND a.createtime <= :endtime', array(':uniacid' => $uniacid, ':endtime' => strtotime(date('Y-m-d')), ':follow' => 1));

	    $today_add_num = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('stonefish_member') . ' as a left join '.tablename('mc_mapping_fans').' as b on a.uniacid=b.uniacid  and a.uid=b.uid WHERE '.$acidsql.' a.uniacid = :uniacid AND b.follow = :follow AND a.createtime >= :starttime AND a.createtime <= :endtime', array(':uniacid' => $uniacid, ':starttime' => strtotime(date('Y-m-d')), ':endtime' => TIMESTAMP, ':follow' => 1));
	    $today_cancel_num = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('stonefish_member') . ' as a left join '.tablename('mc_mapping_fans').' as b on a.uniacid=b.uniacid  and a.uid=b.uid WHERE '.$acidsql.' a.uniacid = :uniacid AND b.follow = :follow AND b.unfollowtime >= :starttime AND b.unfollowtime <= :endtime', array(':uniacid' => $uniacid, ':starttime' => strtotime(date('Y-m-d')), ':endtime' => TIMESTAMP, ':follow' => 0));
	    $today_jing_num = $today_add_num - $today_cancel_num;
	    $today_total_num = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('stonefish_member') . ' as a left join '.tablename('mc_mapping_fans').' as b on a.uniacid=b.uniacid  and a.uid=b.uid WHERE '.$acidsql.' a.uniacid = :uniacid AND b.follow = :follow AND a.createtime <= :endtime', array(':uniacid' => $uniacid, ':endtime' => TIMESTAMP, ':follow' => 1));

	    $st = $_GPC['datelimit']['start'] ? strtotime($_GPC['datelimit']['start']) : strtotime('-30day');
	    $et = $_GPC['datelimit']['end'] ? strtotime($_GPC['datelimit']['end']) : strtotime(date('Y-m-d'));
	    $starttime = min($st, $et);
	    $endtime = max($st, $et);
	    $day_num = ($endtime - $starttime) / 86400 + 1;
	    $endtime += 86399;
	    $type = intval($_GPC['type']) ? intval($_GPC['type']) : 1;
		    if($_W['isajax'] && $_W['ispost']) {
		    $days = array();
		    $datasets = array();
		    for($i = 0; $i < $day_num; $i++){
			    $key = date('m-d', $starttime + 86400 * $i);
			    $days[$key] = 0;
			    $datasets['flow1'][$key] = 0;
			    $datasets['flow2'][$key] = 0;
			    $datasets['flow3'][$key] = 0;
			    $datasets['flow4'][$key] = 0;
		    }

			$data = pdo_fetchall('SELECT a.createtime FROM ' . tablename('stonefish_member') . ' as a left join '.tablename('mc_mapping_fans').' as b on a.uniacid=b.uniacid  and a.uid=b.uid WHERE '.$acidsql.' a.uniacid = :uniacid AND b.follow = :follow AND a.createtime >= :starttime AND a.createtime <= :endtime', array(':uniacid' => $uniacid, ':starttime' => $starttime, ':endtime' => $endtime, ':follow' => 1));
		    foreach($data as $da) {
			    $key = date('m-d', $da['createtime']);
			    if(in_array($key, array_keys($days))) {
				    $datasets['flow1'][$key]++;
			    }
		    }

			$data = pdo_fetchall('SELECT b.unfollowtime FROM ' . tablename('stonefish_member') . ' as a left join '.tablename('mc_mapping_fans').' as b on a.uniacid=b.uniacid  and a.uid=b.uid WHERE '.$acidsql.' a.uniacid = :uniacid AND b.follow = :follow AND b.unfollowtime >= :starttime AND b.unfollowtime <= :endtime', array(':uniacid' => $uniacid, ':starttime' => $starttime, ':endtime' => $endtime, ':follow' => 0));
		    foreach($data as $da) {
			    $key = date('m-d', $da['unfollowtime']);
			    if(in_array($key, array_keys($days))) {
				    $datasets['flow2'][$key]++;
			    }
		    }

			$data0 = pdo_fetchall('SELECT b.unfollowtime FROM ' . tablename('stonefish_member') . ' as a left join '.tablename('mc_mapping_fans').' as b on a.uniacid=b.uniacid  and a.uid=b.uid WHERE '.$acidsql.' a.uniacid = :uniacid AND b.follow = :follow AND b.unfollowtime >= :starttime AND b.unfollowtime <= :endtime', array(':uniacid' => $uniacid, ':starttime' => $starttime, ':endtime' => $endtime, ':follow' => 0));
		    $data1 = pdo_fetchall('SELECT a.createtime FROM ' . tablename('stonefish_member') . ' as a left join '.tablename('mc_mapping_fans').' as b on a.uniacid=b.uniacid  and a.uid=b.uid WHERE '.$acidsql.' a.uniacid = :uniacid AND b.follow = :follow AND a.createtime >= :starttime AND a.createtime <= :endtime', array(':uniacid' => $uniacid, ':starttime' => $starttime, ':endtime' => $endtime, ':follow' => 1));
		    foreach($data1 as $da) {
			    $key = date('m-d', $da['createtime']);
			    if(in_array($key, array_keys($days))) {
				    $day[date('m-d', $da['createtime'])] ++;
				    $datasets['flow3'][$key]++;
			    }
		    }
		    foreach($data0 as $da) {
			    $key = date('m-d', $da['unfollowtime']);
			    if(in_array($key, array_keys($days))) {
				    $datasets['flow3'][$key]--;
			    }
		    }

			for($i = 0; $i < $day_num; $i++){
			    $key = date('m-d', $starttime + 86400 * $i);
			    $datasets['flow4'][$key] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('stonefish_member') . ' as a left join '.tablename('mc_mapping_fans').' as b on a.uniacid=b.uniacid and a.uid=b.uid WHERE '.$acidsql.' a.uniacid = :uniacid AND b.follow = :follow AND a.createtime < ' . ($starttime + 86400 * $i + 86439), array(':uniacid' => $uniacid, ':follow' => 1));;
		    }

		    $shuju['label'] = array_keys($days);
		    $shuju['datasets'] = $datasets;
		
		    if ($day_num == 1) {
			    $day_num = 2;
			    $shuju['label'][] = $shuju['label'][0];
			
			    foreach ($shuju['datasets']['flow1'] as $ky => $va) {
				    $k = $ky;
				    $v = $va;
			    }
			    $shuju['datasets']['flow1']['-'] = $v;
			
			    foreach ($shuju['datasets']['flow2'] as $ky => $va) {
				    $k = $ky;
				    $v = $va;
			    }
			    $shuju['datasets']['flow2']['-'] = $v;
			
			    foreach ($shuju['datasets']['flow3'] as $ky => $va) {
				    $k = $ky;
				    $v = $va;
			    }
			    $shuju['datasets']['flow3']['-'] = $v;
			
			    foreach ($shuju['datasets']['flow4'] as $ky => $va) {
				    $k = $ky;
				    $v = $va;
			    }
			    $shuju['datasets']['flow4']['-'] = $v;
		    }

		    $shuju['datasets']['flow1'] = array_values($shuju['datasets']['flow1']);
		    $shuju['datasets']['flow2'] = array_values($shuju['datasets']['flow2']);
		    $shuju['datasets']['flow3'] = array_values($shuju['datasets']['flow3']);
		    $shuju['datasets']['flow4'] = array_values($shuju['datasets']['flow4']);
		    exit(json_encode($shuju));		
	    }        
		include $this->template('memberlog');
	}
	
	public function doMobileMember() {
		//这个操作被定义用来呈现 微站首页导航图标
		global $_W, $_GPC;
		$uniacid = $_W['uniacid'];
		//微信下才可以使用
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'MicroMessenger') === false) {
			include $this->template('remindnotweixin');
			exit;
		}
		//微信下才可以使用
		//回话状态清空退出
		if(!$_W['member']['uid']){
			include $this->template('remind');
			exit;
		}
		//回话状态清空退出
		//没有关注用户提示
		$status = $this->stonefish_member_checklogin();
		if (is_error($status)) {
			include $this->template('remindnotweixin');
			exit;
		}
		//没有关注用户提示		
		$do = $_GPC['do'];
		$config = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_config')." WHERE uniacid = '{$uniacid}' order by id desc");
		if(empty($config['levelcredit'])){
			$config['levelcredit']='credit1';
		}
		if($config['homebg']!='../addons/stonefish_member/template/images/head-bg.png'){
			$config['homebg'] = tomedia($config['homebg']);
		}
		//签到提示词
		$dsigninfo = iunserializer($config['dsigninfo']);
		$dsigninfoid = array_rand($dsigninfo);
		$signinfo =$dsigninfo[$dsigninfoid];
		//签到提示词
		//查询关联功能
		$module_branch['status']=$this->module['config']['module_branch'];
		$module_shopping['status']=$this->module['config']['module_shopping'];
		$mc_activity['status']=$this->module['config']['mc_activity'];
		$mc_card['status']=$this->module['config']['mc_card'];
		$mc_pay['status']=$this->module['config']['mc_pay'];
		//查询关联功能
		$profile = mc_fetch($_W['member']['uid'], array('nickname', 'realname', 'avatar', 'mobile', $config['levelcredit'], 'groupid'));
		$member = pdo_fetch("SELECT * FROM " . tablename('stonefish_member') . " WHERE uniacid = :uniacid and uid = :uid", array(':uniacid' => $uniacid,':uid' => $_W['member']['uid']), 'id');
		if(empty($profile['mobile']) || ($member['status']!=2 && $this->module['config']['smsverification'] && $config['smsstatus'])) {
		    //没有绑定手机，跳转手机验证处
			$_W['page']['title'] = '身份验证';
			if($module_branch['status']){
				$district = pdo_fetchall("SELECT * FROM " . tablename('stonefish_branch_district') . " WHERE uniacid = '{$uniacid}' ORDER BY orderid desc, id DESC", array(), 'id');
			    $items = pdo_fetchall("SELECT id,title,districtid FROM " . tablename('stonefish_branch_business') . " WHERE uniacid = '{$uniacid}' ORDER BY id DESC", array(), 'id');
                if (!empty($items)) {
                    $business = '';
                    foreach ($items as $cid => $cate) {
                        $business[$cate['districtid']][$cate['id']] = array($cate['id'], $cate['title']);
                    }
                }
			}
			
			if($_W['ispost'] && $_W['isajax']) {
				$post = $_GPC['__input'];
				$mode = $post['mode'];
				$modes = array('basic', 'code');
				$mode = in_array($mode, $modes) ? $mode : 'basic';		
				if($mode == 'code') {
				    if($config['smsstatus']) {
					    if(!$this->code_verify($uniacid, $post['username'], $post['password'])) {
						    exit('验证码错误.');
					    }
					}
					$members = pdo_fetch("SELECT `uid` FROM ".tablename('mc_mapping_fans')." WHERE `uniacid`=:uniacid AND `openid` = :openid",array(':uniacid' => $uniacid,':openid' => $_SESSION['openid']));					
					if($members['uid']==0) {					    
						//exit('不存在该账号的用户资料');
						if(!empty($_SESSION['openid'])) {
							$map_fans = pdo_fetchcolumn('SELECT tag FROM ' . tablename('mc_mapping_fans') . ' WHERE uniacid = :uniacid AND openid = :openid', array(':uniacid' => $uniacid, ':openid' => $_SESSION['openid']));
							if(!empty($map_fans)) {
								$map_fans = iunserializer($map_fans) ? iunserializer($map_fans) : array();
							}
						}
						$default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' .tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(':uniacid' => $uniacid));
						$salt = random(8);
						$data = array(
							'uniacid' => $uniacid, 
							'salt' => $salt,
							'groupid' => $default_groupid,
							'realname' => $post['realname'],							
							'createtime' => TIMESTAMP	
						);
						if(!empty($map_fans)) {
							$data['nickname'] = $map_fans['nickname'];
							$data['gender'] = $map_fans['sex'];
							$data['residecity'] = $map_fans['city'] ? $map_fans['city'] . '市' : '';
							$data['resideprovince'] = $map_fans['province'] ? $map_fans['province'] . '省' : '';
							$data['nationality'] = $map_fans['country'];
							$data['avatar'] = rtrim($map_fans['headimgurl'], '0') . 132;							
						}
						pdo_insert('mc_members', $data);
						$members['uid'] = pdo_insertid();
						pdo_update('mc_mapping_fans', array('uid' => $members['uid']), array('openid' => $_SESSION['openid']));
						//查询是否已绑定过此手机号
						$mobile = pdo_fetch("SELECT mobile FROM ".tablename('mc_members')." WHERE uniacid=:uniacid and mobile=:mobile",array(':uniacid' => $uniacid,':mobile' => $post['username']));
						if(!empty($mobile)){
						    //提供重复绑定手机号						    
						    exit('此手机号：'.$post['username'].'已绑定过，<br/>请换个手机号重新绑定！');
						}else{
						    //保存手机号
							$updata = array(
							    'mobile' => $post['username'],
							    'email' => $post['username'].'@163.com',
							    'password' => md5($post['username'] . $salt . $_W['config']['setting']['authkey']),
							);
						    pdo_update('mc_members', $updata, array('uid' => $members['uid']));
							//添加会员记录							
							$member = pdo_fetch("SELECT * FROM ".tablename('stonefish_member')." WHERE uniacid=:uniacid and uid=:uid",array(':uniacid' => $uniacid,':uid' => $members['uid']));
							if(empty($member)){
								$insertdata = array(
							        'uniacid' => $uniacid, 
							        'uid' => $members['uid'],
							        'districtid' => $post['districtid'],
							        'status' => $config['smsstatus']+1,							
							        'createtime' => TIMESTAMP	
						        );
							    pdo_insert('stonefish_member', $insertdata);
							}else{
								$updatedata = array(
							        'districtid' => $post['districtid'],
							        'status' => $config['smsstatus']+1,							
							        'createtime' => TIMESTAMP
						        );
							    pdo_update('stonefish_member', $updatedata, array('id' => $member['id']));
							}
							//添加会员记录
						    exit('success');
						}
					}else{
					    //查询是否已绑定过此手机号
						$mobile = pdo_fetch("SELECT mobile FROM ".tablename('mc_members')." WHERE uniacid=:uniacid and mobile=:mobile and uid<>:uid",array(':uniacid' => $uniacid,':mobile' => $post['username'],':uid' => $members['uid']));
						if(!empty($mobile)){
						    //提供重复绑定手机号						    
						    exit('此手机号：'.$post['username'].'已绑定过，<br/>请换个手机号重新绑定！');
						}else{
						    //保存手机号以及真实姓名
						    pdo_update('mc_members', array('mobile' => $post['username'],'realname' => $post['realname'],'email' => $post['username'].'@163.com'), array('uid' => $members['uid']));
							//添加会员记录
							$member = pdo_fetch("SELECT * FROM ".tablename('stonefish_member')." WHERE uniacid=:uniacid and uid=:uid",array(':uniacid' => $uniacid,':uid' => $members['uid']));
							if(empty($member)){
								$insertdata = array(
							        'uniacid' => $uniacid, 
							        'uid' => $members['uid'],
							        'districtid' => $post['districtid'],
							        'status' => $config['smsstatus']+1,							
							        'createtime' => TIMESTAMP	
						        );
							    pdo_insert('stonefish_member', $insertdata);
							}else{
								$updatedata = array(
							        'districtid' => $post['districtid'],
							        'status' => $config['smsstatus']+1,							
							        'createtime' => TIMESTAMP
						        );
							    pdo_update('stonefish_member', $updatedata, array('id' => $member['id']));
							}
							//添加会员记录
						    exit('success');
						}
					}
				}
				exit('未知错误导致登陆失败');
			}
            include $this->template('register');
			exit;
		}else{
		    //判断是否从其他模块跳转过来的
			if(!empty($_GPC['url'])){
			    header("HTTP/1.1 301 Moved Permanently");
                header("Location: " . $_GPC['url'] . "");
                exit();
			}
			//已绑定用户，直接显示会员中心
			$_W['page']['title'] = '会员中心';
			load()->model('activity');
			$filter = array('used'=>1);
			$coupons = activity_coupon_owned($_W['member']['uid'], $filter);
			$tokens = activity_token_owned($_W['member']['uid'], $filter);
			$setting = uni_setting($_W['uniacid'], array('creditnames', 'creditbehaviors', 'uc', 'payment', 'passport'));
			$behavior = $setting['creditbehaviors'];
			$creditnames = $setting['creditnames'];
			$credits = mc_credit_fetch($_W['member']['uid'], '*');
			//查询实物奖品数量
			$goods['total'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('activity_exchange_trades_shipping') . ' WHERE uid = :uid AND status < :status', array(':uid' => $_W['member']['uid'], ':status' => 2));
			//查询实物奖品数量
			//兑换商城
			$activity['total'] = $goods['total'] + $tokens['total'] + $coupons['total'];
			//兑换商城
			$config = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_config')." WHERE uniacid = '{$uniacid}'");
			//查询会员组以及等级
			$levelall = pdo_fetch("SELECT grade,id FROM ".tablename('stonefish_member_level')." WHERE uniacid = '{$uniacid}' AND integral_start<='{$profile[$config['levelcredit']]}' AND integral_end>='{$profile[$config['levelcredit']]}'");
			$levelname = $levelall['grade'];
			$groupall = pdo_fetch("SELECT title FROM ".tablename('mc_groups')." WHERE uniacid = '{$uniacid}' and groupid='{$profile['groupid']}'");
			$groupname = $groupall['title'];
			//查询会员组以及等级
			//查询是否有通知消息
		    $message['total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('stonefish_member_message') . " WHERE uniacid =:uniacid AND type = 2 and (messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid =:groupid) or messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." where uid=:uid)) and endtime >:time and starttime<:time and messageid NOT IN (SELECT messageid FROM ".tablename('stonefish_member_message_record')." WHERE uid =:uid)", array(':uniacid' => $_W['uniacid'],':time' => TIMESTAMP,':groupid' => $profile['groupid'],':uid' => $_W['member']['uid']));
			$message['totals'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('stonefish_member_message') . " WHERE uniacid =:uniacid AND type = 2 and (messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid =:groupid) or messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." where uid=:uid)) and endtime >:time and starttime<:time", array(':uniacid' => $_W['uniacid'],':time' => TIMESTAMP,':groupid' => $profile['groupid'],':uid' => $_W['member']['uid']));
			//查询是否有通知消息
			//查询是否有任务
		    $task['total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('stonefish_member_message') . " WHERE uniacid =:uniacid AND type = 1 and (messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid =:groupid) or messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." where uid=:uid)) and endtime >:time and starttime<:time and messageid NOT IN (SELECT messageid FROM ".tablename('stonefish_member_message_record')." WHERE uid =:uid)", array(':uniacid' => $_W['uniacid'],':time' => TIMESTAMP,':groupid' => $profile['groupid'],':uid' => $_W['member']['uid']));
			$task['totals'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('stonefish_member_message') . " WHERE uniacid =:uniacid AND type = 1 and (messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid =:groupid) or messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." where uid=:uid)) and endtime >:time and starttime<:time", array(':uniacid' => $_W['uniacid'],':time' => TIMESTAMP,':groupid' => $profile['groupid'],':uid' => $_W['member']['uid']));
		    //查询是否有任务
			//留言中心分类
			if($config['feedbackstatus']){
				$feedback_category = pdo_fetchall('SELECT id,cname FROM ' . tablename('stonefish_member_feedback_category') . ' WHERE uniacid = :uniacid', array(':uniacid' => $_W['uniacid']));
				if(empty($feedback_category)){
					$config['feedbackstatus'] = 0;
				}
			}
			//留言中心分类
			//会员是否需要昵称
			$sql = 'SELECT `mf`.*, `pf`.`field` FROM ' . tablename('mc_member_fields') . ' AS `mf` JOIN ' . tablename('profile_fields') . " AS `pf` ON `mf`.`fieldid` = `pf`.`id` WHERE `uniacid` = :uniacid AND `mf`.`available` = :available";
		    $params = array(':uniacid' => $_W['uniacid'], ':available' => '1');
		    $mcFields = pdo_fetchall($sql, $params, 'field');
		    //会员是否需要昵称
			//其他模块会员中心导航
			load()->model('app');
			$navs = app_navs('profile');
			$modules = uni_modules();
			$groups = $others = array();
			if(!empty($navs)) {
				foreach($navs as $row) {
					if(!empty($row['module'])) {
						$groups[$row['module']][] = $row;
					} else {
						$others[] = $row;
					}
				}
			}
			$mcgroups = mc_groups();
			$profile['group'] = $mcgroups[$profile['groupid']];
			if(isset($setting['uc']['status']) && $setting['uc']['status'] == '1') {
				$uc = $setting['uc'];
				$sql = 'SELECT * FROM ' . tablename('mc_mapping_ucenter') . ' WHERE `uniacid`=:uniacid AND `uid`=:uid';
				$pars = array();
				$pars[':uniacid'] = $_W['uniacid'];
				$pars[':uid'] = $_W['member']['uid'];
				$mapping = pdo_fetch($sql, $pars);
				if(empty($mapping)) {
	
				} else {
					mc_init_uc();
					$u = uc_get_user($mapping['centeruid'], true);
					$ucUser = array(
						'uid' => $u[0],
						'username' => $u[1],
						'email' => $u[2]
					);
				}
			}
			if (empty($setting['passport']['focusreg'])) {
				$reregister = false;
				if ($_W['member']['email'] == md5($_SESSION['openid']).'@xxx.com') {
					$reregister = true;
				}
			}
			//其他模块会员中心导航
			include $this->template('index');
			exit;						
		}
	}
	public function doMobileProfile() {
		//这个操作被定义用来呈现 微站个人中心导航
		global $_W, $_GPC;
		//微信下才可以使用
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'MicroMessenger') === false) {
			include $this->template('remindnotweixin');
			exit;
		}
		//微信下才可以使用
		//回话状态清空退出
		if(!$_W['member']['uid']){
			include $this->template('remind');
			exit;
		}
		//回话状态清空退出
		//没有关注用户提示
		$status = $this->stonefish_member_checklogin();
		if (is_error($status)) {
			include $this->template('remindnotweixin');
			exit;
		}
		//没有关注用户提示
		//没有成为会员提示
		$status = $this->stonefish_member_check_login();
		if (is_error($status)) {
			$this->doMobileMember();
			exit;
		}
		//没有成为会员提示
		//兑换商城
		load()->model('activity');
		$filter = array('used'=>1);
		$coupons = activity_coupon_owned($_W['member']['uid'], $filter);
		$tokens = activity_token_owned($_W['member']['uid'], $filter);
		//查询实物奖品数量
		$goods['total'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('activity_exchange_trades_shipping') . ' WHERE uid = :uid AND status < :status', array(':uid' => $_W['member']['uid'], ':status' => 2));
		//查询实物奖品数量
		$activity['total'] = $goods['total'] + $tokens['total'] + $coupons['total'];
		//兑换商城
		//查询是否有通知消息
		$profile = mc_fetch($_W['member']['uid'], array('realname', 'groupid'));
		$message['total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('stonefish_member_message') . " WHERE uniacid =:uniacid AND type = 2 and (messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid =:groupid) or messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." where uid=:uid)) and endtime >:time and starttime<:time and messageid NOT IN (SELECT messageid FROM ".tablename('stonefish_member_message_record')." WHERE uid =:uid)", array(':uniacid' => $_W['uniacid'],':time' => TIMESTAMP,':groupid' => $profile['groupid'],':uid' => $_W['member']['uid']));
		//查询是否有通知消息
		//查询是否有任务
		$task['total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('stonefish_member_message') . " WHERE uniacid =:uniacid AND type = 1 and (messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid =:groupid) or messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." where uid=:uid)) and endtime >:time and starttime<:time and messageid NOT IN (SELECT messageid FROM ".tablename('stonefish_member_message_record')." WHERE uid =:uid)", array(':uniacid' => $_W['uniacid'],':time' => TIMESTAMP,':groupid' => $profile['groupid'],':uid' => $_W['member']['uid']));
		//查询是否有任务
		//查询关联功能
		$module_branch['status']=$this->module['config']['module_branch'];
		$module_shopping['status']=$this->module['config']['module_shopping'];
		$mc_activity['status']=$this->module['config']['mc_activity'];
		$mc_card['status']=$this->module['config']['mc_card'];
		$mc_pay['status']=$this->module['config']['mc_pay'];
		//查询关联功能
		$do = 'member';
		$_W['page']['title'] = '修改个人详细资料';
		$config = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_config')." WHERE uniacid = '{$_W['uniacid']}'");
		load()->func('tpl');
		$profile = mc_fetch($_W['member']['uid']);
		if(!empty($_SESSION['openid'])) {
			$map_fans = pdo_fetchcolumn('SELECT tag FROM ' . tablename('mc_mapping_fans') . ' WHERE uniacid = :uniacid AND openid = :openid', array(':uniacid' => $_W['uniacid'], ':openid' => $_SESSION['openid']));
			if(!empty($map_fans)) {
				if (is_base64($map_fans)){
					$map_fans = base64_decode($map_fans);
				}
				if (is_serialized($map_fans)) {
					$map_fans = iunserializer($map_fans);
				}
				if(!empty($map_fans) && is_array($map_fans)) {
					empty($profile['nickname']) ? ($data['nickname'] = $map_fans['nickname']) : '';
					empty($profile['gender']) ? ($data['gender'] = $map_fans['sex']) : '';
					empty($profile['residecity']) ? ($data['residecity'] = ($map_fans['city']) ? $map_fans['city'] . '市' : '') : '';
					empty($profile['resideprovince']) ? ($data['resideprovince'] = ($map_fans['province']) ? $map_fans['province'] . '省' : '') : '';
					empty($profile['nationality']) ? ($data['nationality'] = $map_fans['country']) : '';
					empty($profile['avatar']) ? ($data['avatar'] = rtrim($map_fans['headimgurl'], '0') . 132) : '';
					if(!empty($data)) {
						mc_update($_W['member']['uid'], $data);
					}
				}
			}
		}
		$profile = mc_fetch($_W['member']['uid']);
		if(!empty($profile)) {
			if(empty($profile['email']) || (!empty($profile['email']) && substr($profile['email'], -6) == '012wz.com' && strlen($profile['email']) == 39)) {
				$profile['email'] = '';
				$profile['email_effective'] = 1;
			}
		}

		$sql = 'SELECT `mf`.*, `pf`.`field` FROM ' . tablename('mc_member_fields') . ' AS `mf` JOIN ' . tablename('profile_fields') . " AS `pf`
		ON `mf`.`fieldid` = `pf`.`id` WHERE `uniacid` = :uniacid AND `mf`.`available` = :available";
		$params = array(':uniacid' => $_W['uniacid'], ':available' => '1');
		$mcFields = pdo_fetchall($sql, $params, 'field');

		if (checksubmit('submit')) {
			if (!empty($_GPC)) {
				$_GPC['createtime'] = TIMESTAMP;
				foreach ($_GPC as $field => $value) {
					if (!isset($value) || in_array($field, array('uid','act', 'name', 'token', 'submit', 'session'))) {
						unset($_GPC[$field]);
						continue;
					}
				}
				if(empty($_GPC['email']) && $profile['email_effective'] == 1) {
					unset($_GPC['email']);
				}
				$_GPC['birthyear'] = $_GPC['birth']['year'];
				$_GPC['birthmonth'] = $_GPC['birth']['month'];
				$_GPC['birthday'] = $_GPC['birth']['day'];
				$_GPC['resideprovince'] = $_GPC['reside']['province'];
				$_GPC['residecity'] = $_GPC['reside']['city'];
				$_GPC['residedist'] = $_GPC['reside']['district'];
				mc_update($_W['member']['uid'], $_GPC);
			}
			message('更新资料成功！', referer(), 'success');
		}
		include $this->template('profile');
	}
	public function doMobileBond() {
		//这个操作被定义用来呈现 微站个人中心导航
		global $_W, $_GPC;
		//微信下才可以使用
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'MicroMessenger') === false) {
			include $this->template('remindnotweixin');
			exit;
		}
		//微信下才可以使用
		//回话状态清空退出
		if(!$_W['member']['uid']){
			include $this->template('remind');
			exit;
		}
		//回话状态清空退出
		//没有关注用户提示
		$status = $this->stonefish_member_checklogin();
		if (is_error($status)) {
			include $this->template('remindnotweixin');
			exit;
		}
		//没有关注用户提示
		//没有成为会员提示
		$status = $this->stonefish_member_check_login();
		if (is_error($status)) {
			$this->doMobileMember();
			exit;
		}
		//没有成为会员提示
		//兑换商城
		load()->model('activity');
		$filter = array('used'=>1);
		$coupons = activity_coupon_owned($_W['member']['uid'], $filter);
		$tokens = activity_token_owned($_W['member']['uid'], $filter);
		//查询实物奖品数量
		$goods['total'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('activity_exchange_trades_shipping') . ' WHERE uid = :uid AND status < :status', array(':uid' => $_W['member']['uid'], ':status' => 2));
		//查询实物奖品数量
		$activity['total'] = $goods['total'] + $tokens['total'] + $coupons['total'];
		//兑换商城
		//查询是否有通知消息
		$profile = mc_fetch($_W['member']['uid'], array('realname', 'groupid'));
		$message['total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('stonefish_member_message') . " WHERE uniacid =:uniacid AND type = 2 and (messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid =:groupid) or messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." where uid=:uid)) and endtime >:time and starttime<:time and messageid NOT IN (SELECT messageid FROM ".tablename('stonefish_member_message_record')." WHERE uid =:uid)", array(':uniacid' => $_W['uniacid'],':time' => TIMESTAMP,':groupid' => $profile['groupid'],':uid' => $_W['member']['uid']));
		//查询是否有通知消息
		//查询是否有任务
		$task['total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('stonefish_member_message') . " WHERE uniacid =:uniacid AND type = 1 and (messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid =:groupid) or messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." where uid=:uid)) and endtime >:time and starttime<:time and messageid NOT IN (SELECT messageid FROM ".tablename('stonefish_member_message_record')." WHERE uid =:uid)", array(':uniacid' => $_W['uniacid'],':time' => TIMESTAMP,':groupid' => $profile['groupid'],':uid' => $_W['member']['uid']));
		//查询是否有任务
		//查询关联功能
		$module_branch['status']=$this->module['config']['module_branch'];
		$module_shopping['status']=$this->module['config']['module_shopping'];
		$mc_activity['status']=$this->module['config']['mc_activity'];
		$mc_card['status']=$this->module['config']['mc_card'];
		$mc_pay['status']=$this->module['config']['mc_pay'];
		//查询关联功能
		$do = 'member';
		$profile = mc_fetch($_W['member']['uid'], array('nickname', 'realname', 'avatar', 'mobile', 'credit1', 'credit2', 'groupid'));
		$_W['page']['title'] = '修改个人详细资料';
		$setting = uni_setting($_W['uniacid'], array('creditnames', 'creditbehaviors', 'uc', 'payment', 'passport'));
		$behavior = $setting['creditbehaviors'];
		$creditnames = $setting['creditnames'];
		$config = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_config')." WHERE uniacid = '{$_W['uniacid']}'");
		$op = $_GPC['op'];
		$dos = array('display', 'credits', 'address', 'card', 'mycard', 'mobile');
		$op = in_array($op, $dos) ? $op : 'display';
		load()->func('tpl');
		load()->model('user');
		if ($op == 'credits') {
			$_W['page']['title'] = '我的'.$creditnames[$_GPC['credittype']]['title'];
			$where = '';
	        $params = array(':uid' => $_W['member']['uid']);
	        $pindex = max(1, intval($_GPC['page']));
	        $psize  = 15;
	
	        if (empty($starttime) || empty($endtime)) {
		        $starttime =  strtotime('-1 month');
		        $endtime = time();
	        }
	        if ($_GPC['time']) {
		        $starttime = strtotime($_GPC['time']['start']);
		        $endtime = strtotime($_GPC['time']['end']) + 86399;
		        $where = ' AND `createtime` >= :starttime AND `createtime` < :endtime';
		        $params[':starttime'] = $starttime;
		        $params[':endtime'] = $endtime;
	        }
	
	        $sql = 'SELECT `realname`, `avatar` FROM ' . tablename('mc_members') . " WHERE `uid` = :uid";
	        $user = pdo_fetch($sql, array(':uid' => $_W['member']['uid']));
	        if ($_GPC['credittype']) {
		        if ($_GPC['type'] == 'order') {
			        $sql = 'SELECT * FROM ' . tablename('mc_credits_recharge') . " WHERE `uid` = :uid $where LIMIT " . ($pindex - 1) * $psize. ',' . $psize;
			        $orders = pdo_fetchall($sql, $params);
			        foreach ($orders as &$value) {
				        $value['createtime'] = date('Y-m-d', $value['createtime']);
				        $value['fee'] = number_format($value['fee'], 2);
				        if ($value['status'] == 1) {
					        $orderspay += $value['fee'];
				        }
				        unset($value);
			        }			
			        $ordersql = 'SELECT COUNT(*) FROM ' .tablename('mc_credits_recharge') . "WHERE `uid` = :uid {$where}";
			        $total = pdo_fetchcolumn($ordersql, $params);
			        $orderpager = pagination($total, $pindex, $psize, '', array('before' => 0, 'after' => 0, 'ajaxcallback' => ''));
			        include $this->template('bond');
			        exit();
		        }
		        $where .= " AND `credittype` = '{$_GPC['credittype']}'";
	        }
	
	
	        $sql = 'SELECT `num` FROM ' . tablename('mc_credits_record') . " WHERE `uid` = :uid $where";
	        $nums = pdo_fetchall($sql, $params);
	        $pay = $income = 0;
	        foreach ($nums as $value) {
		        if ($value['num'] > 0) {
			        $income += $value['num'];
		        } else {
			        $pay += abs($value['num']);
		        }
	        }
	        $pay = number_format($pay, 2);
	        $income = number_format($income, 2);
	
	        $sql = 'SELECT * FROM ' . tablename('mc_credits_record') . " WHERE `uid` = :uid {$where} ORDER BY `createtime` DESC LIMIT " . ($pindex - 1) * $psize.','. $psize;
	        $data = pdo_fetchall($sql, $params);
	        foreach ($data as $key=>$value) {
		        $data[$key]['credittype'] = $creditnames[$data[$key]['credittype']]['title'];
		        $data[$key]['createtime'] = date('Y-m-d H:i', $data[$key]['createtime']);
		        $data[$key]['num'] = number_format($value['num'], 2);
	        }
	
	        $pagesql = 'SELECT COUNT(*) FROM ' .tablename('mc_credits_record') . "WHERE `uid` = :uid {$where}";
	        $total = pdo_fetchcolumn($pagesql, $params);
	        $pager = pagination($total, $pindex, $psize, '', array('before' => 0, 'after' => 0, 'ajaxcallback' => ''));
		}

		if ($op == 'address') {
			$_W['page']['title'] = '管理收货地址';
			if (checksubmit('submit')) {
		
				$data = $_GPC['data'];
				$data['resideprovince'] = $_GPC['dis']['province'];
				$data['residecity'] = $_GPC['dis']['city'];
				$data['residedist'] = $_GPC['dis']['district'];
				pdo_update('mc_members', $data, array('uid'=>$_SESSION['uid']));
				message('修改系统联系地址成功！', $this->createMobileUrl('bond',array('op'=>'address')), 'success');
			}
			$sql = 'SELECT * FROM ' . tablename('mc_members') . " WHERE `uid` = :uid";
			$data = pdo_fetch($sql, array(':uid' => $_W['member']['uid']));
			$reside['province'] = $data['resideprovince'];
			$reside['city'] = $data['residecity'];
			$reside['district'] = $data['residedist'];
			//是否已关联商城收货地址
			if($this->module['config']['module_shopping']){
                $shoping_address = pdo_fetch("select isdefault from " . tablename('stonefish_shopping_address') . " where id='{$id}' and uniacid='{$_W['uniacid']}' and openid='{$_SESSION['openid']}' limit 1 ");
                if(!empty($shoping_address) && empty($shoping_address['isdefault'])){
                    pdo_update('stonefish_shopping_address', array('isdefault' => 0), array('uniacid' => $_W['uniacid'], 'openid' => $_SESSION['openid']));
                    pdo_update('stonefish_shopping_address', array('isdefault' => 1), array('uniacid' => $_W['uniacid'], 'openid' => $_SESSION['openid'], 'id' => $id));
                }
                $profile = fans_search($_SESSION['openid'], array('resideprovince', 'residecity', 'residedist', 'address', 'realname', 'mobile'));
                $shoping_address = pdo_fetchall("SELECT * FROM " . tablename('stonefish_shopping_address') . " WHERE deleted=0 and openid = :openid", array(':openid' => $_SESSION['openid']));
			}			
			//是否已关联商城收货地址
		}


		if ($op == 'card') {
			$_W['page']['title'] = '申请会员卡';
			$mcard = pdo_fetch('SELECT * FROM ' . tablename('mc_card_members') . ' WHERE uniacid = :uniacid AND uid = :uid', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
			if(!empty($mcard)) {
				header('Location:' . $this->createMobileUrl('bond',array('op'=>'mycard')));
			}
			
			$sql = 'SELECT * FROM ' . tablename('mc_card') . "WHERE `uniacid` = :uniacid AND `status` = '1'";
			$setting = pdo_fetch($sql, array(':uniacid' => $_W['uniacid']));

			if (!empty($setting)) {
				$setting['color'] = iunserializer($setting['color']);
				$setting['background'] = iunserializer($setting['background']);
				$setting['fields'] = iunserializer($setting['fields']);
			} else {
				message('公众号尚未开启会员卡功能', url('mc'), 'error');
			}
			if(!empty($setting['fields'])) {
				$fields = array();
				foreach($setting['fields'] as $li) {
					if($li['bind'] == 'birth') {
						$fields[] = 'birthyear';
						$fields[] = 'birthmonth';
						$fields[] = 'birthday';
					} elseif($li['bind'] == 'reside') {
						$fields[] = 'resideprovince';
						$fields[] = 'residecity';
						$fields[] = 'residedist';
					} else {
						$fields[] = $li['bind'];
					}
				}
				$member_info = mc_fetch($_W['member']['uid'], $fields);
			}
			if (checksubmit('submit')) {
				$data = array();
				if (!empty($setting['fields'])) {
					foreach ($setting['fields'] as $row) {
						if (!empty($row['require']) && empty($_GPC[$row['bind']])) {
							message('请输入'.$row['title'].'！');
						}
						$data[$row['bind']] = $_GPC[$row['bind']];
					}
				}
		
				$sql = 'SELECT COUNT(*)  FROM ' . tablename('mc_card_members') . " WHERE `uid` = :uid AND `cid` = :cid AND uniacid = :uniacid";
				$count = pdo_fetchcolumn($sql, array(':uid' => $_W['member']['uid'], ':cid' => $_GPC['cardid'], ':uniacid' => $_W['uniacid']));
				if ($count >= 1) {
					message('抱歉,您已经领取过该会员卡.', referer(), 'error');
				}
		
 				$cardsn = $_GPC['format'];
				preg_match_all('/(\*+)/', $_GPC['format'], $matchs);
				if (!empty($matchs)) {
					foreach ($matchs[1] as $row) {
						$cardsn = str_replace($row, random(strlen($row), 1), $cardsn);
					}
				}
				preg_match('/(\#+)/', $_GPC['format'], $matchs);
				$length = strlen($matchs[1]);
				$pos = strpos($_GPC['format'], '#');
				$cardsn = str_replace($matchs[1], str_pad($_GPC['snpos']++, $length - strlen($number), '0', STR_PAD_LEFT), $cardsn);
				pdo_update('mc_card', array('snpos' => $_GPC['snpos']), array('uniacid' => $_W['uniacid'], 'id' => $_GPC['cardid']));
		
				$record = array(
						'uniacid' => $_W['uniacid'],
						'uid' => $_W['member']['uid'],
						'cid' => $_GPC['cardid'],
						'cardsn' => $cardsn,
						'status' => '1',
						'createtime' => TIMESTAMP
				);
				$check = mc_check($data);
				if(is_error($check)) {
					message($check['message'], '', 'error');
				}
				if(pdo_insert('mc_card_members', $record)) {
					if(!empty($data)){
						mc_update($_W['member']['uid'], $data);
					}
					message('领取会员卡成功.', $this->createMobileUrl('bond',array('op'=>'mycard')), 'success');
				} else {
					message('领取会员卡失败.', referer(), 'error');
				}
			}
		}


		if ($op == 'mycard') {
			$_W['page']['title'] = '我的会员卡';
			$mcard = pdo_fetch('SELECT * FROM ' . tablename('mc_card_members') . ' WHERE uniacid = :uniacid AND uid = :uid', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
			if(empty($mcard)) {
				header('Location:' . $this->createMobileUrl('bond',array('op'=>'card')));
			}
			$setting = pdo_fetch('SELECT * FROM ' . tablename('mc_card') . ' WHERE uniacid = :uniacid', array(':uniacid' => $_W['uniacid']));
			if(!empty($setting)) {
				$setting['color'] = iunserializer($setting['color']);
				$setting['background'] = iunserializer($setting['background']);
				$setting['business'] = iunserializer($setting['business']) ? iunserializer($setting['business']) : array();
			}
		}

		if($op == 'mobile') {
			$_W['page']['title'] = '重新绑定手机号';
			$profile = mc_fetch($_W['member']['uid'], array('mobile'));
			$mobile_exist = empty($profile['mobile']) ? 0 : 1;
			if($_W['ispost'] && $_W['isajax']) {
				$post = $_GPC['__input'];
				$mode = $post['mode'];
				$modes = array('basic', 'code');
				$mode = in_array($mode, $modes) ? $mode : 'basic';		
				$mobile = trim($post['mobile']) ? trim($post['mobile']) : exit('请填写新手机号');
				if(!preg_match('/^\d{11}$/', $mobile)) {
					exit('新手机号格式有误');
				}
				if($mode == 'code') {
				    $config = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_config')." WHERE uniacid = '{$_W['uniacid']}'");
					if($config['smsstatus']) {
					    if(!$this->code_verify($_W['uniacid'], $post['mobile'], $post['password'])) {
						    exit('验证码错误.');
					    }
					}
				}
				if($mobile_exist == 1) {					
					$info = pdo_fetch('SELECT uid FROM ' . tablename('mc_members') . ' WHERE mobile = :mobile AND uniacid = :uniacid AND uid = :uid', array(':mobile' => $post['oldmobile'],':uniacid' => $_W['uniacid'],':uid' => $_W['member']['uid']));
					if(!empty($info)) {
						$is_exist = pdo_fetch('SELECT uid FROM ' . tablename('mc_members') . ' WHERE uniacid = :uniacid AND mobile = :mobile AND uid != :uid', array(':uniacid' => $_W['uniacid'], ':mobile' => $mobile, ':uid' => $_W['member']['uid']));
						if(!empty($is_exist)) {
							exit('该手机号已被绑定,换个手机号试试');
						}else{
							pdo_update('mc_members', array('mobile' => $mobile), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
							//exit('已成功绑新的手机号，请查看温馨提示绑定的号码');
							exit('success');
						}							
					} else {
						exit('原手机号错误');
					}
				} else {
					$mobile = trim($post['mobile']) ? trim($post['mobile']) : exit('请填写手机号');
					if(!preg_match('/^\d{11}$/', $mobile)) {
						exit('手机号格式有误');
					}
					$is_exist = pdo_fetch('SELECT uid FROM ' . tablename('mc_members') . ' WHERE uniacid = :uniacid AND mobile = :mobile AND uid != :uid', array(':uniacid' => $_W['uniacid'], ':mobile' => $mobile, ':uid' => $_W['member']['uid']));
					if(!empty($is_exist)) {
						exit('该手机号已被绑定,换个手机号试试');
					}
					pdo_update('mc_members', array('mobile' => $mobile), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
					//exit('已成功绑新的手机号，请查看温馨提示绑定的号码');
					exit('success');
				}
			}
		}
		include $this->template('bond');
	}
	public function doMobileAddress() {
        global $_W, $_GPC;
		$status = $this->stonefish_member_checklogin();
		if (is_error($status)) {
			include $this->template('remindnotweixin');
			exit;
			//没有关注用户提示关注
		}
        $from = $_GPC['from'];
        $returnurl = urldecode($_GPC['returnurl']);
        $this->checkAuth();
        // $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'post';
        $operation = $_GPC['op'];
		$config = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_config')." WHERE uniacid = '{$_W['uniacid']}'");
        if ($operation == 'post') {
            $id = intval($_GPC['id']);
            $data = array(
                'uniacid' => $_W['uniacid'],
                'openid' => $_SESSION['openid'],
                'realname' => $_GPC['realname'],
                'mobile' => $_GPC['mobile'],
                'province' => $_GPC['province'],
                'city' => $_GPC['city'],
                'area' => $_GPC['area'],
                'address' => $_GPC['address'],
            );
            if (empty($_GPC['realname']) || empty($_GPC['mobile']) || empty($_GPC['address'])) {
                message('请输完善您的资料！');
            }
            if (!empty($id)) {
                unset($data['uniacid']);
                unset($data['openid']);
                pdo_update('stonefish_shopping_address', $data, array('id' => $id));
                message($id, '', 'ajax');
            } else {
                pdo_update('stonefish_shopping_address', array('isdefault' => 0), array('uniacid' => $_W['uniacid'], 'openid' => $_SESSION['openid']));
                $data['isdefault'] = 1;
                pdo_insert('stonefish_shopping_address', $data);
                $id = pdo_insertid();
                if (!empty($id)) {
                    message($id, '', 'ajax');
                } else {
                    message(0, '', 'ajax');
                }
            }
        } elseif ($operation == 'default') {
            $id = intval($_GPC['id']);
            $address = pdo_fetch("select isdefault from " . tablename('stonefish_shopping_address') . " where id='{$id}' and uniacid='{$_W['uniacid']}' and openid='{$_SESSION['openid']}' limit 1 ");
            if(!empty($address) && empty($address['isdefault'])){
                pdo_update('stonefish_shopping_address', array('isdefault' => 0), array('uniacid' => $_W['uniacid'], 'openid' => $_SESSION['openid']));
                pdo_update('stonefish_shopping_address', array('isdefault' => 1), array('uniacid' => $_W['uniacid'], 'openid' => $_SESSION['openid'], 'id' => $id));
            }
            message(1, '', 'ajax');
        } elseif ($operation == 'detail') {
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT id, realname, mobile, province, city, area, address FROM " . tablename('stonefish_shopping_address') . " WHERE id = :id", array(':id' => $id));
            message($row, '', 'ajax');
        } elseif ($operation == 'remove') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $address = pdo_fetch("select isdefault from " . tablename('stonefish_shopping_address') . " where id='{$id}' and uniacid='{$_W['uniacid']}' and openid='{$_SESSION['openid']}' limit 1 ");

                if (!empty($address)) {
                    //pdo_delete("stonefish_shopping_address",  array('id'=>$id, 'uniacid' => $_W['uniacid'], 'openid' => $_SESSION['openid']));
                    //修改成不直接删除，而设置deleted=1
                    pdo_update("stonefish_shopping_address", array("deleted" => 1, "isdefault" => 0), array('id' => $id, 'uniacid' => $_W['uniacid'], 'openid' => $_SESSION['openid']));

                    if ($address['isdefault'] == 1) {
                        //如果删除的是默认地址，则设置是新的为默认地址
                        $maxid = pdo_fetchcolumn("select max(id) as maxid from " . tablename('stonefish_shopping_address') . " where uniacid='{$_W['uniacid']}' and openid='{$_SESSION['openid']}' limit 1 ");
                        if (!empty($maxid)) {
                            pdo_update('stonefish_shopping_address', array('isdefault' => 1), array('id' => $maxid, 'uniacid' => $_W['uniacid'], 'openid' => $_SESSION['openid']));
                            die(json_encode(array("result" => 1, "maxid" => $maxid)));
                        }
                    }
                }
            }
            die(json_encode(array("result" => 1, "maxid" => 0)));
        } else {
            $profile = fans_search($_SESSION['openid'], array('resideprovince', 'residecity', 'residedist', 'address', 'realname', 'mobile'));
            $address = pdo_fetchall("SELECT * FROM " . tablename('stonefish_shopping_address') . " WHERE deleted=0 and openid = :openid", array(':openid' => $_SESSION['openid']));
           
        }
    }
	
	public function doMobileFeedback() {
		//这个操作被定义用来呈现 微站个人中心导航
		global $_W, $_GPC;
		//微信下才可以使用
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'MicroMessenger') === false) {
			include $this->template('remindnotweixin');
			exit;
		}
		//微信下才可以使用
		//回话状态清空退出
		if(!$_W['member']['uid']){
			include $this->template('remind');
			exit;
		}
		//回话状态清空退出
		//没有关注用户提示
		$status = $this->stonefish_member_checklogin();
		if (is_error($status)) {
			include $this->template('remindnotweixin');
			exit;
		}
		//没有关注用户提示
		//没有成为会员提示
		$status = $this->stonefish_member_check_login();
		if (is_error($status)) {
			$this->doMobileMember();
			exit;
		}
		//没有成为会员提示
		$config = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_config')." WHERE uniacid = '{$_W['uniacid']}'");
		$do = 'feedback';
		$dos = array('display', 'sendreply');
		$op = in_array($_GPC['op'], $dos) ? $_GPC['op'] : 'display';
		$fid = intval($_GPC['fid']);
		if(!empty($fid)){
			$feedback = pdo_fetch('SELECT * FROM ' . tablename('stonefish_member_feedback_category') . ' WHERE id = :id ', array(':id' => $fid));
		}
		$_W['page']['title'] = $feedback['cname'];
		load()->model('mc');
		$profile = mc_fetch($_W['member']['uid'], array('avatar', 'realname', 'nickname'));
        if($op == 'display') {
		    $ischeck = $feedback['status'];
            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;
            if (!empty($feedback)) {
                $psize = intval($feedback['pagesize']) == 0? 10 : intval($feedback['pagesize']);
                if (!empty($feedback['topimgurl'])) {
                    if (strstr($feedback['topimgurl'], 'http')) {
                        $topimgurl = $feedback['topimgurl'];
                    } else {
                        $topimgurl = $_W['attachurl'].$feedback['topimgurl'];
                    }
                }
                $ischeck = intval($feedback['ischeck']);
            }
		
            $where = 'AND status=1 AND parentid=0 and fid='.$fid.'';
			$isopen = $feedback['open'];
			if(!$isopen){
				$where .= " and from_user='".$_SESSION['openid']."'";
			}
            $list = pdo_fetchall("SELECT * FROM " . tablename('stonefish_member_feedback') . " WHERE uniacid=".$_W['uniacid']." {$where} ORDER BY displayorder DESC,id DESC LIMIT " . ($pindex - 1) * $psize . ",{$psize}", array(), 'id');

            $parentids = array_keys($list);
            $childlist = pdo_fetchall("SELECT * FROM ".tablename('stonefish_member_feedback')." WHERE parentid IN ('".implode("','", is_array($parentids) ? $parentids : array($parentids))."') AND parentid!=0 AND uniacid=:uniacid ORDER BY displayorder DESC,id DESC", array(':uniacid' => $_W['uniacid']));
            foreach ($childlist as $index => $row) {
                if (!empty($row['parentid'])) {
                    $children[$row['parentid']][] = $row;
                }
            }

            if (!empty($list)) {
                $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('stonefish_member_feedback') . " WHERE uniacid=".$_W['uniacid']." {$where}");
                $pager = pagination($total, $pindex, $psize);
            }
		}
		if($op == 'sendreply') {
			$parentid = intval($_GPC['parentid']);			
            $type = trim($_GPC['type']);           
            $content = trim($_GPC['content']);

            if (empty($content)) {
                $this->showMessage('请输入回复内容!');
            }

            if ($type == 'feedback') { //留言
                $parentid = 0;
            } else { //回复
                $item = pdo_fetch("SELECT * FROM " . tablename('stonefish_member_feedback') . " WHERE id=:id AND uniacid=:uniacid AND status=1 LIMIT 1", array(':id' => $parentid, ':uniacid' => $_W['uniacid']));
                if (empty($item)) {
                    $this->showMessage('要回复的留言可能已经被删除了!'.$parentid);
                }
            }
           
            if (empty($feedback)) {
                $status = 0;
            } else {
                if($feedback['status']){
					$status = 0;
				}else{
					$status = 1;
				}
            }

            $data = array(
                'uniacid' => $_W['uniacid'],
                'from_user' => $_SESSION['openid'],
                'parentid' => $parentid,
				'fid' => $fid,
                'username' => $profile['realname'],
                'nickname' => $profile['nickname'],
                'headimgurl' => $profile['avatar'],
                'status' => $status,
                'content' => $content,
				'visitorsip' => $_W['clientip'],
                'createtime' => TIMESTAMP
            );

            pdo_insert('stonefish_member_feedback', $data);
            if ($status) {
                $this->showMessage($feedback['cname'].'成功!', 1);
            } else {
                $this->showMessage($feedback['cname'].'成功,请等待管理员的审核!', 1);
            }
		}
		include $this->template('feedback');
	}
	
	public function showMessage($msg, $status = 0){
        $result = array('message' => $msg, 'status' => $status);
        echo json_encode($result);
        exit;
    }
	
	public function doMobileMessage() {
		//这个操作被定义用来呈现 微站个人中心导航
		global $_W, $_GPC;
		//微信下才可以使用
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'MicroMessenger') === false) {
			include $this->template('remindnotweixin');
			exit;
		}
		//微信下才可以使用
		//回话状态清空退出
		if(!$_W['member']['uid']){
			include $this->template('remind');
			exit;
		}
		//回话状态清空退出
		//没有关注用户提示
		$status = $this->stonefish_member_checklogin();
		if (is_error($status)) {
			include $this->template('remindnotweixin');
			exit;
		}
		//没有关注用户提示
		//没有成为会员提示
		$status = $this->stonefish_member_check_login();
		if (is_error($status)) {
			$this->doMobileMember();
			exit;
		}
		//没有成为会员提示
		load()->model('mc');
		$do = 'message';
		$dos = array('display', 'mine', 'view');
		$op = in_array($_GPC['op'], $dos) ? $_GPC['op'] : 'display';
		$config = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_config')." WHERE uniacid = '{$_W['uniacid']}'");
		$profile = mc_fetch($_W['member']['uid'], array('realname', 'groupid'));
		//兑换商城
		load()->model('activity');
		$filter = array('used'=>1);
		$coupons = activity_coupon_owned($_W['member']['uid'], $filter);
		$tokens = activity_token_owned($_W['member']['uid'], $filter);
		//查询实物奖品数量
		$goods['total'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('activity_exchange_trades_shipping') . ' WHERE uid = :uid AND status < :status', array(':uid' => $_W['member']['uid'], ':status' => 2));
		//查询实物奖品数量
		$activity['total'] = $goods['total'] + $tokens['total'] + $coupons['total'];
		//兑换商城			
		//查询是否有通知消息
		$message['total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('stonefish_member_message') . " WHERE uniacid =:uniacid AND type = 2 and (messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid =:groupid) or messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." where uid=:uid)) and endtime >:time and starttime<:time and messageid NOT IN (SELECT messageid FROM ".tablename('stonefish_member_message_record')." WHERE uid =:uid)", array(':uniacid' => $_W['uniacid'],':time' => TIMESTAMP,':groupid' => $profile['groupid'],':uid' => $_W['member']['uid']));
		//查询是否有通知消息
		//查询是否有任务
		$task['total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('stonefish_member_message') . " WHERE uniacid =:uniacid AND type = 1 and (messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid =:groupid) or messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." where uid=:uid)) and endtime >:time and starttime<:time and messageid NOT IN (SELECT messageid FROM ".tablename('stonefish_member_message_record')." WHERE uid =:uid)", array(':uniacid' => $_W['uniacid'],':time' => TIMESTAMP,':groupid' => $profile['groupid'],':uid' => $_W['member']['uid']));
		//查询是否有任务
		//查询关联功能
		$module_branch['status']=$this->module['config']['module_branch'];
		$module_shopping['status']=$this->module['config']['module_shopping'];
		$mc_activity['status']=$this->module['config']['mc_activity'];
		$mc_card['status']=$this->module['config']['mc_card'];
		$mc_pay['status']=$this->module['config']['mc_pay'];
		//查询关联功能
		if($op == 'display') {
			$_W['page']['title'] = '我未读的消息';
			$total = $message['total'];
			$pindex = max(1, intval($_GPC['page']));
			$psize = 10;
			$lists = pdo_fetchall('SELECT * FROM ' . tablename('stonefish_member_message') . " WHERE uniacid =:uniacid AND type = 2 and (messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid =:groupid) or messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." where uid=:uid)) and messageid NOT IN (SELECT messageid FROM ".tablename('stonefish_member_message_record')." WHERE uid =:uid) and endtime >:time and starttime<:time ORDER BY endtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $_W['uniacid'],':time' => TIMESTAMP,':groupid' => $profile['groupid'],':uid' => $_W['member']['uid']));
			$pager = pagination($total, $pindex, $psize);
		}
		
		if($op == 'mine') {
			$_W['page']['title'] = '我全部的消息';
			$total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('stonefish_member_message') . " WHERE uniacid =:uniacid AND type = 2 and (messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid =:groupid) or messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." where uid=:uid)) and endtime >:time and starttime<:time", array(':uniacid' => $_W['uniacid'],':time' => TIMESTAMP,':groupid' => $profile['groupid'],':uid' => $_W['member']['uid']));
			$pindex = max(1, intval($_GPC['page']));
			$psize = 10;
			$lists = pdo_fetchall('SELECT * FROM ' . tablename('stonefish_member_message') . " WHERE uniacid =:uniacid AND type = 2 and (messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid =:groupid) or messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." where uid=:uid)) and endtime >:time and starttime<:time ORDER BY endtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $_W['uniacid'],':time' => TIMESTAMP,':groupid' => $profile['groupid'],':uid' => $_W['member']['uid']));
			$pager = pagination($total, $pindex, $psize);
		}

		if($op == 'view') {
			$_W['page']['title'] = '查看消息详细信息';
			$id = intval($_GPC['id']);
			$data = pdo_fetch('SELECT * FROM ' . tablename('stonefish_member_message') . " WHERE messageid=:messageid", array(':messageid' => $id));
			//查询是否已读过
			$datas = pdo_fetch('SELECT * FROM ' . tablename('stonefish_member_message_record') . " WHERE messageid=:messageid and uid=:uid", array(':messageid' => $id,':uid' => $_W['member']['uid']));			
			if(empty($datas)){
				$update = array(
				    'messageid' => $id,
				    'uniacid' => $_W['uniacid'],
				    'uid' => $_W['member']['uid'],
				    'grantmodule' => 'system',
				    'granttime' => TIMESTAMP,
				    'remark' => '用户查看',
				    'status' => 2,
				    'usemodule' => 'system',
				    'usetime' => TIMESTAMP,
				    'operator' => $profile['realname']
				);
				pdo_insert('stonefish_member_message_record',$update);
			}
			//查询是否已读过
		}
		include $this->template('message');
	}
	
	public function doMobileTask() {
		//这个操作被定义用来呈现 微站个人中心导航
		global $_W, $_GPC;
		//微信下才可以使用
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'MicroMessenger') === false) {
			include $this->template('remindnotweixin');
			exit;
		}
		//微信下才可以使用
		//回话状态清空退出
		if(!$_W['member']['uid']){
			include $this->template('remind');
			exit;
		}
		//回话状态清空退出
		//没有关注用户提示
		$status = $this->stonefish_member_checklogin();
		if (is_error($status)) {
			include $this->template('remindnotweixin');
			exit;
		}
		//没有关注用户提示
		//没有成为会员提示
		$status = $this->stonefish_member_check_login();
		if (is_error($status)) {
			$this->doMobileMember();
			exit;
		}
		//没有成为会员提示
		load()->model('mc');
		$do = 'task';
		$dos = array('display', 'mine', 'view');
		$op = in_array($_GPC['op'], $dos) ? $_GPC['op'] : 'display';
		$config = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_config')." WHERE uniacid = '{$_W['uniacid']}'");
		$profile = mc_fetch($_W['member']['uid'], array('realname', 'groupid'));
		//兑换商城
		load()->model('activity');
		$filter = array('used'=>1);
		$coupons = activity_coupon_owned($_W['member']['uid'], $filter);
		$tokens = activity_token_owned($_W['member']['uid'], $filter);
		//查询实物奖品数量
		$goods['total'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('activity_exchange_trades_shipping') . ' WHERE uid = :uid AND status < :status', array(':uid' => $_W['member']['uid'], ':status' => 2));
		//查询实物奖品数量
		$activity['total'] = $goods['total'] + $tokens['total'] + $coupons['total'];
		//兑换商城			
		//查询是否有通知消息
		$message['total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('stonefish_member_message') . " WHERE uniacid =:uniacid AND type = 2 and (messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid =:groupid) or messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." where uid=:uid)) and endtime >:time and starttime<:time and messageid NOT IN (SELECT messageid FROM ".tablename('stonefish_member_message_record')." WHERE uid =:uid)", array(':uniacid' => $_W['uniacid'],':time' => TIMESTAMP,':groupid' => $profile['groupid'],':uid' => $_W['member']['uid']));
		//查询是否有通知消息
		//查询是否有任务
		$task['total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('stonefish_member_message') . " WHERE uniacid =:uniacid AND type = 1 and (messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid =:groupid) or messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." where uid=:uid)) and endtime >:time and starttime<:time and messageid NOT IN (SELECT messageid FROM ".tablename('stonefish_member_message_record')." WHERE uid =:uid)", array(':uniacid' => $_W['uniacid'],':time' => TIMESTAMP,':groupid' => $profile['groupid'],':uid' => $_W['member']['uid']));
		//查询是否有任务
		//查询关联功能
		$module_branch['status']=$this->module['config']['module_branch'];
		$module_shopping['status']=$this->module['config']['module_shopping'];
		$mc_activity['status']=$this->module['config']['mc_activity'];
		$mc_card['status']=$this->module['config']['mc_card'];
		$mc_pay['status']=$this->module['config']['mc_pay'];
		//查询关联功能
		if($op == 'display') {
			$_W['page']['title'] = '我未完成的任务';
			$total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('stonefish_member_message') . " WHERE uniacid =:uniacid AND type = 1 and (messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid =:groupid) or messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." where uid=:uid)) and endtime >:time and starttime<:time and messageid NOT IN (SELECT messageid FROM ".tablename('stonefish_member_message_record')." WHERE uid =:uid)", array(':uniacid' => $_W['uniacid'],':time' => TIMESTAMP,':groupid' => $profile['groupid'],':uid' => $_W['member']['uid']));
			$pindex = max(1, intval($_GPC['page']));
			$psize = 10;
			$lists = pdo_fetchall('SELECT * FROM ' . tablename('stonefish_member_message') . " WHERE uniacid =:uniacid AND type = 1 and (messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid =:groupid) or messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." where uid=:uid)) and messageid NOT IN (SELECT messageid FROM ".tablename('stonefish_member_message_record')." WHERE uid =:uid) and endtime >:time and starttime<:time ORDER BY endtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $_W['uniacid'],':time' => TIMESTAMP,':groupid' => $profile['groupid'],':uid' => $_W['member']['uid']));
			$pager = pagination($total, $pindex, $psize);
		}
		
		if($op == 'mine') {
			$_W['page']['title'] = '我已完成的任务';
			$total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('stonefish_member_message') . " WHERE uniacid =:uniacid AND type = 1 and (messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid =:groupid) or messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." where uid=:uid)) and messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_record')." WHERE uid =:uid) and endtime >:time and starttime<:time", array(':uniacid' => $_W['uniacid'],':time' => TIMESTAMP,':groupid' => $profile['groupid'],':uid' => $_W['member']['uid']));
			$pindex = max(1, intval($_GPC['page']));
			$psize = 10;
			$lists = pdo_fetchall('SELECT * FROM ' . tablename('stonefish_member_message') . " WHERE uniacid =:uniacid AND type = 1 and (messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid =:groupid) or messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." where uid=:uid)) and messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_record')." WHERE uid =:uid) and endtime >:time and starttime<:time ORDER BY endtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $_W['uniacid'],':time' => TIMESTAMP,':groupid' => $profile['groupid'],':uid' => $_W['member']['uid']));
			$pager = pagination($total, $pindex, $psize);
		}

		if($op == 'view') {
			$_W['page']['title'] = '查看任务详细信息';
			$id = intval($_GPC['id']);
			$data = pdo_fetch('SELECT * FROM ' . tablename('stonefish_member_message') . " WHERE messageid=:messageid", array(':messageid' => $id));
			
		}
		include $this->template('task');
	}
	
	public function doMobileTaskshare() {
		//这个操作被定义用来呈现 微站个人中心导航
		global $_W, $_GPC;
		$id = intval($_GPC['tid']);
		$uid = intval($_GPC['uid']);
		$data = pdo_fetch('SELECT * FROM ' . tablename('stonefish_member_message') . " WHERE messageid=:messageid", array(':messageid' => $id));
		if($_W['account']['level']==4){//服务号
			$from_user = $_SESSION['openid'];
			$fansID =$_W['member']['uid'];
			$fans = mc_fetch($fansID, array('avatar','nickname'));
		}else{//非服务号
			if (isset($_COOKIE["user_oauth2_wuopenid"])){
				$user_oauth2_wuopenid = $_COOKIE["user_oauth2_wuopenid"];
			}else{
				$user_oauth2_wuopenid = time();
			}			
			//设置cookie信息
			setcookie("user_oauth2_wuopenid", $user_oauth2_wuopenid, time()+3600*24*7);
			$from_user = $user_oauth2_wuopenid;
			$fansID = 0;
			$fans['avatar'] = '../addons/stonefish_member/template/images/default-headimg.jpg';
			$fans['nickname'] = '昵称';
		}
		//查询任务分享人的fromuser
		$fromuser = pdo_fetchcolumn("SELECT openid FROM " . tablename('mc_mapping_fans') . " WHERE uniacid =:uniacid and uid =:uid order by fanid desc",array(':uniacid' => $_W['uniacid'],':uid' => $uid));
		//查询任务分享人的fromuser
		if($uid!=$fansID){
			$datanum = pdo_fetchcolumn("SELECT count(id) FROM " . tablename('stonefish_member_message_data') . " WHERE uniacid =:uniacid and messageid =:messageid and fromuser =:fromuser order by id desc",array(':uniacid' => $_W['uniacid'],':messageid' => $id,':fromuser' => $fromuser));
			if($datanum < $data['credit']){
				//查询 是否助力过
				$share = pdo_fetch("SELECT * FROM " . tablename('stonefish_member_message_data') . " WHERE uniacid =:uniacid and messageid =:messageid and fromuser =:fromuser and from_user =:from_user order by id desc",array(':uniacid' => $_W['uniacid'],':messageid' => $id,':fromuser' => $fromuser,':from_user' => $from_user));
				//查询 是否助力过
				$datainsert = array(
                    'uniacid' => $_W['uniacid'],
                    'messageid' => $id,
                    'from_user' => $from_user,
                    'fromuser' => $fromuser,
				    'avatar' => $fans['avatar'],
				    'nickname' => $fans['nickname'],
                    'visitorsip' =>$_W['clientip'],
				    'visitorstime' => time(),
					'viewnum' => 1
                );
				if(!empty($share)){
					pdo_update('stonefish_member_message_data', array('viewnum'=>$share['viewnum']+1),array('id'=>$share['id']));
				}else{
					pdo_insert('stonefish_member_message_data', $datainsert);
					//增送积分
					if($data['creditview']){
						$creditnames = array();
					    $unisettings = uni_setting($uniacid, array('creditnames'));
					    if (!empty($unisettings) && !empty($unisettings['creditnames'])) {
						    foreach ($unisettings['creditnames'] as $key=>$credit) {
							    $creditnames[$key] = $credit['title'];
						    }
					    }
					    mc_credit_update($uid, $data['credittype'], $data['creditview'], array($uid, '分享任务 查看奖励'.$data['creditview'].'个'.$creditnames[$data['credittype']]));
					}					
					//增送积分
					//任务完成状态
					$record = pdo_fetch("SELECT * FROM " . tablename('stonefish_member_message_record') . " WHERE uniacid =:uniacid and messageid =:messageid and uid =:uid order by recid desc",array(':uniacid' => $_W['uniacid'],':messageid' => $id,':uid' => $uid));
					$recordinsert = array(
					    'messageid' => $id,
					    'uniacid' => $_W['uniacid'],
					    'uid' => $uid,
					    'grantmodule' => 'system',
					    'granttime' => TIMESTAMP,
					    'status' => 2,
					    'remark' => '用户分享任务完成'
					);
					if(empty($record)){
						pdo_insert('stonefish_member_message_record',$recordinsert);
					}
					//任务完成状态
				}
			}
		}
		if(!empty($data['shareurl'])) {
			header("HTTP/1.1 301 Moved Permanently");
            header("Location: " .$data['shareurl']."");
            exit();
        }else{
			header("HTTP/1.1 301 Moved Permanently");
            header("Location: " . $this->createMobileUrl('member')."");
            exit();
		}
	}
	
	public function doMobileTask_share() {
		//这个操作被定义用来呈现 微站个人中心导航
		global $_W, $_GPC;
		$id = intval($_GPC['tid']);
		$uid = intval($_GPC['uid']);
		$uniacid = $_W['uniacid'];
		$from_user = $_SESSION['openid'];
		$taskdata = pdo_fetch('SELECT * FROM ' . tablename('stonefish_member_message') . " WHERE messageid=:messageid", array(':messageid' => $id));
		//判断是否为关注用户
		$follow = pdo_fetchcolumn("select follow from ".tablename('mc_mapping_fans') ." where openid=:openid and uniacid=:uniacid order by `fanid` desc",array(":openid"=>$from_user,":uniacid"=>$uniacid));
		if($follow==0){
			$data = array(
                'msg' => '您还没有关注公众号['.$_W['account']['childname'].']，即使分享成功了！'.$_W['account']['childname'].'没有办法为您奖励！请先关注公众号['.$_W['account']['childname'].']再来分享，谢谢！',
                'success' => 2,
            );
		}
		$record = pdo_fetch("SELECT * FROM " . tablename('stonefish_member_message_record') . " WHERE uniacid =:uniacid and messageid =:messageid and uid =:uid order by recid desc",array(':uniacid' => $uniacid,':messageid' => $id,':uid' => $uid));
		if($taskdata['creditshare'] && empty($record)){
			$recordinsert = array(
				'messageid' => $id,
				'uniacid' => $_W['uniacid'],
				'uid' => $uid,
				'grantmodule' => 'system',
				'granttime' => TIMESTAMP,
				'status' => 2,
				'remark' => '用户分享任务完成'
			);
			pdo_insert('stonefish_member_message_record',$recordinsert);
			//增送积分
			if($taskdata['creditview']){
				$creditnames = array();
				$unisettings = uni_setting($uniacid, array('creditnames'));
				if (!empty($unisettings) && !empty($unisettings['creditnames'])) {
					foreach ($unisettings['creditnames'] as $key=>$credit) {
						$creditnames[$key] = $credit['title'];
					}
				}
			    mc_credit_update($uid, $taskdata['credittype'], $taskdata['creditshare'], array($uid, '分享任务 分享奖励'.$taskdata['creditshare'].'个'.$creditnames[$taskdata['credittype']]));
			}
			//增送积分
			$data = array(
                'msg' => $taskdata['share_confirm'],
                'success' => 1,
            );
		}else{
			$data = array(
                'msg' => $taskdata['share_confirm'],
                'success' => 1,
            );
		}
        echo json_encode($data);		
	}
	
	public function doMobileCoupon() {
		//这个操作被定义用来呈现 微站个人中心导航
		global $_W, $_GPC;
		//微信下才可以使用
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'MicroMessenger') === false) {
			include $this->template('remindnotweixin');
			exit;
		}
		//微信下才可以使用
		//回话状态清空退出
		if(!$_W['member']['uid']){
			include $this->template('remind');
			exit;
		}
		//回话状态清空退出
		//没有关注用户提示
		$status = $this->stonefish_member_checklogin();
		if (is_error($status)) {
			include $this->template('remindnotweixin');
			exit;
		}
		//没有关注用户提示
		//没有成为会员提示
		$status = $this->stonefish_member_check_login();
		if (is_error($status)) {
			$this->doMobileMember();
			exit;
		}
		//没有成为会员提示
		$do = 'coupon';
		$dos = array('display', 'post', 'mine', 'use');
		$op = in_array($_GPC['op'], $dos) ? $_GPC['op'] : 'display';
		$config = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_config')." WHERE uniacid = '{$_W['uniacid']}'");
		load()->model('activity');
		load()->model('mc');
		$creditnames = array();
		$unisettings = uni_setting($uniacid, array('creditnames'));
		if (!empty($unisettings) && !empty($unisettings['creditnames'])) {
			foreach ($unisettings['creditnames'] as $key=>$credit) {
				$creditnames[$key] = $credit['title'];
			}
		}
		//兑换商城
		$filter = array('used'=>1);
		$coupons = activity_coupon_owned($_W['member']['uid'], $filter);
		$tokens = activity_token_owned($_W['member']['uid'], $filter);
		//查询实物奖品数量
		$goods['total'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('activity_exchange_trades_shipping') . ' WHERE uid = :uid AND status < :status', array(':uid' => $_W['member']['uid'], ':status' => 2));
		//查询实物奖品数量
		$activity['total'] = $goods['total'] + $tokens['total'] + $coupons['total'];
		//兑换商城
		//查询是否有通知消息
		$profile = mc_fetch($_W['member']['uid'], array('realname', 'groupid'));
		$message['total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('stonefish_member_message') . " WHERE uniacid =:uniacid AND type = 2 and (messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid =:groupid) or messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." where uid=:uid)) and endtime >:time and starttime<:time and messageid NOT IN (SELECT messageid FROM ".tablename('stonefish_member_message_record')." WHERE uid =:uid)", array(':uniacid' => $_W['uniacid'],':time' => TIMESTAMP,':groupid' => $profile['groupid'],':uid' => $_W['member']['uid']));
		//查询是否有通知消息
		//查询是否有任务
		$task['total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('stonefish_member_message') . " WHERE uniacid =:uniacid AND type = 1 and (messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid =:groupid) or messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." where uid=:uid)) and endtime >:time and starttime<:time and messageid NOT IN (SELECT messageid FROM ".tablename('stonefish_member_message_record')." WHERE uid =:uid)", array(':uniacid' => $_W['uniacid'],':time' => TIMESTAMP,':groupid' => $profile['groupid'],':uid' => $_W['member']['uid']));
		//查询是否有任务
		//查询关联功能
		$module_branch['status']=$this->module['config']['module_branch'];
		$module_shopping['status']=$this->module['config']['module_shopping'];
		$mc_activity['status']=$this->module['config']['mc_activity'];
		$mc_card['status']=$this->module['config']['mc_card'];
		$mc_pay['status']=$this->module['config']['mc_pay'];
		//查询关联功能
		if($op == 'display') {
			$_W['page']['title'] = '兑换折扣券';
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM '. tablename('activity_coupon'). ' WHERE uniacid = :uniacid AND type = :type AND endtime > :endtime' , array(':uniacid' => $_W['uniacid'], ':type' => 1, ':endtime' => TIMESTAMP));
			$pindex = max(1, intval($_GPC['page']));
			$psize = 10;
			$lists = pdo_fetchall('SELECT couponid,title,thumb,type,credittype,credit,endtime,description FROM ' . tablename('activity_coupon') . ' WHERE uniacid = :uniacid AND type = :type AND endtime > :endtime ORDER BY endtime ASC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $_W['uniacid'], ':type' => 1, ':endtime' => TIMESTAMP));
			$pager = pagination($total, $pindex, $psize);
		}
		if($op == 'post') {
			$id = intval($_GPC['id']); 
			$coupon = activity_coupon_info($id, $_W['uniacid']);
			if(empty($coupon)){
				message('没有指定的礼品兑换.');
			}
			$credit = mc_credit_fetch($_W['member']['uid'], array($coupon['credittype']));
			if ($credit[$coupon['credittype']] < $coupon['credit']) {
				message('您的' . $creditnames[$coupon['credittype']] . '数量不够,无法兑换.');
			}
	
			$ret = activity_coupon_grant($_W['member']['uid'], $id, 'system', '用户使用' . $coupon['credit'] . $creditnames[$coupon['credittype']] . '兑换');
			if(is_error($ret)) {
				message($ret['message']);
			}
			mc_credit_update($_W['member']['uid'], $coupon['credittype'], -1 * $coupon['credit'], array($_W['member']['uid'], '礼品兑换 消耗 '.$coupon['credit'].'个' . $creditnames[$coupon['credittype']] . '(' . $coupon['title'] . ')'));
			message("兑换成功,您消费了 {$coupon['credit']} {$creditnames[$coupon['credittype']]}", $this->createMobileUrl('coupon',array('op'=>'mine')));
		}
		if($op == 'mine') {
			$_W['page']['title'] = '我的折扣券';
			$psize = 10;
			$pindex = max(1, intval($_GPC['page']));
			$params = array(':uid' => $_W['member']['uid']);
			$filter['used'] = '1';
			$type = 1;
			if($_GPC['type'] == 'used') {
				$filter['used'] = '2';
				$type = 2;
			}
			$total = pdo_fetchall('SELECT COUNT(*) FROM ' . tablename('activity_coupon_record') . ' AS a LEFT JOIN ' . tablename('activity_coupon') . ' AS b ON a.couponid = b.couponid WHERE b.type = 1 AND a.uid = :uid AND a.status = :status GROUP BY a.couponid', array(':uid' => $_W['member']['uid'], ':status' => $type));
			$coupon = activity_coupon_owned($_W['member']['uid'], $filter, $pindex, $psize);
			if(!empty($coupon['data'])) {
				foreach($coupon['data'] as &$value){
					$value['cototal'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('activity_coupon_record') . ' WHERE uid = :uid AND couponid = :couponid AND status = :status', array(':uid' => $_W['member']['uid'], ':couponid' => $value['couponid'], ':status' => $type));
					$value['thumb'] = tomedia($value['thumb']);
					$value['description'] = htmlspecialchars_decode($value['description']);
					$data[$value['couponid']] = $value;
				}
			}
			unset($coupon);
			$pager = pagination(count($total), $pindex, $psize);
		}
		if($op == 'use') {
			$_W['page']['title'] = '使用折扣券';
			$id = intval($_GPC['id']);
			$data = activity_coupon_owned($_W['member']['uid'], array('couponid' => $id, 'used' => 1 ));
			$data = $data['data'][0];

			if(checksubmit('submit')) {
				load()->model('user');
				$password = $_GPC['password'];
				$sql = 'SELECT * FROM ' . tablename('activity_coupon_password') . " WHERE `uniacid` = :uniacid AND `password` = :password";
				$clerk = pdo_fetch($sql, array(':uniacid' => $_W['uniacid'], ':password' => $password));
				if(!empty($clerk)) {
					$status = activity_coupon_use($_W['member']['uid'], $id, $clerk['name']);
					if (!is_error($status)) {
						message('折扣券使用成功！', $this->createMobileUrl('coupon',array('op'=>'mine','type' => 'used')), 'success');
					} else {
						message($status['message'], $this->createMobileUrl('coupon',array('op'=>'mine','type' => $_GPC['type'])), 'error');
					}
				}
				message('密码错误！', referer(), 'error');
			}
		}
		include $this->template('coupon');
	}
	
	public function doMobileToken() {
		//这个操作被定义用来呈现 微站个人中心导航
		global $_W, $_GPC;
		//微信下才可以使用
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'MicroMessenger') === false) {
			include $this->template('remindnotweixin');
			exit;
		}
		//微信下才可以使用
		//回话状态清空退出
		if(!$_W['member']['uid']){
			include $this->template('remind');
			exit;
		}
		//回话状态清空退出
		//没有关注用户提示
		$status = $this->stonefish_member_checklogin();
		if (is_error($status)) {
			include $this->template('remindnotweixin');
			exit;
		}
		//没有关注用户提示
		//没有成为会员提示
		$status = $this->stonefish_member_check_login();
		if (is_error($status)) {
			$this->doMobileMember();
			exit;
		}
		//没有成为会员提示
		$do = 'token';
		$dos = array('display', 'post', 'mine', 'use');
		$op = in_array($_GPC['op'], $dos) ? $_GPC['op'] : 'display';
		$config = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_config')." WHERE uniacid = '{$_W['uniacid']}'");
		load()->model('activity');
		load()->model('mc');
		$creditnames = array();
		$unisettings = uni_setting($uniacid, array('creditnames'));
		if (!empty($unisettings) && !empty($unisettings['creditnames'])) {
			foreach ($unisettings['creditnames'] as $key=>$credit) {
				$creditnames[$key] = $credit['title'];
			}
		}
		//兑换商城
		$filter = array('used'=>1);
		$coupons = activity_coupon_owned($_W['member']['uid'], $filter);
		$tokens = activity_token_owned($_W['member']['uid'], $filter);
		//查询实物奖品数量
		$goods['total'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('activity_exchange_trades_shipping') . ' WHERE uid = :uid AND status < :status', array(':uid' => $_W['member']['uid'], ':status' => 2));
		//查询实物奖品数量
		$activity['total'] = $goods['total'] + $tokens['total'] + $coupons['total'];
		//兑换商城
		//查询是否有通知消息
		$profile = mc_fetch($_W['member']['uid'], array('realname', 'groupid'));
		$message['total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('stonefish_member_message') . " WHERE uniacid =:uniacid AND type = 2 and (messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid =:groupid) or messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." where uid=:uid)) and endtime >:time and starttime<:time and messageid NOT IN (SELECT messageid FROM ".tablename('stonefish_member_message_record')." WHERE uid =:uid)", array(':uniacid' => $_W['uniacid'],':time' => TIMESTAMP,':groupid' => $profile['groupid'],':uid' => $_W['member']['uid']));
		//查询是否有通知消息
		//查询是否有任务
		$task['total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('stonefish_member_message') . " WHERE uniacid =:uniacid AND type = 1 and (messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid =:groupid) or messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." where uid=:uid)) and endtime >:time and starttime<:time and messageid NOT IN (SELECT messageid FROM ".tablename('stonefish_member_message_record')." WHERE uid =:uid)", array(':uniacid' => $_W['uniacid'],':time' => TIMESTAMP,':groupid' => $profile['groupid'],':uid' => $_W['member']['uid']));
		//查询是否有任务
		//查询关联功能
		$module_branch['status']=$this->module['config']['module_branch'];
		$module_shopping['status']=$this->module['config']['module_shopping'];
		$mc_activity['status']=$this->module['config']['mc_activity'];
		$mc_card['status']=$this->module['config']['mc_card'];
		$mc_pay['status']=$this->module['config']['mc_pay'];
		//查询关联功能
		if($op == 'display') {
			$_W['page']['title'] = '兑换代金券';
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM '. tablename('activity_coupon'). ' WHERE uniacid = :uniacid AND type = :type AND endtime > :endtime' , array(':uniacid' => $_W['uniacid'], ':type' => 2, ':endtime' => TIMESTAMP));
			$pindex = max(1, intval($_GPC['page']));
			$psize = 10;
			$lists = pdo_fetchall('SELECT couponid,title,thumb,type,credittype,credit,endtime,description FROM ' . tablename('activity_coupon') . ' WHERE uniacid = :uniacid AND type = :type AND endtime > :endtime ORDER BY endtime ASC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $_W['uniacid'], ':type' => 2, ':endtime' => TIMESTAMP));
			$pager = pagination($total, $pindex, $psize);
		}
		if($op == 'post') {
			$id = intval($_GPC['id']); 
			$token = activity_token_info($id, $_W['uniacid']);
			if(empty($token)){
				message('没有指定的礼品兑换.');
			}
			$credit = mc_credit_fetch($_W['member']['uid'], array($token['credittype']));
			if ($credit[$token['credittype']] < $token['credit']) {
				message('您的' . $creditnames[$token['credittype']] . '数量不够,无法兑换.');
			}
	
			$ret = activity_token_grant($_W['member']['uid'], $id, 'system', '用户使用' . $token['credit'] . $creditnames[$token['credittype']] . '兑换');
			if(is_error($ret)) {
				message($ret['message']);
			}
				mc_credit_update($_W['member']['uid'], $token['credittype'], -1 * $token['credit'], array($_W['member']['uid'], '礼品兑换 消耗 '.$token['credit'].'个' . $creditnames[$token['credittype']] . '(' . $token['title'] . ')'));
			message("兑换成功,您消费了 {$token['credit']} {$creditnames[$token['credittype']]}", $this->createMobileUrl('token',array('op'=>'mine')));
		}
		if($op == 'mine') {
			$_W['page']['title'] = '我的代金券';
			$psize = 10;
			$pindex = max(1, intval($_GPC['page']));
			$params = array(':uid' => $_W['member']['uid']);
			$filter['used'] = '1';
			$type = 1;
			if($_GPC['type'] == 'used') {
				$filter['used'] = '2';
				$type = 2;
			}
			$total = pdo_fetchall('SELECT COUNT(*) FROM ' . tablename('activity_coupon_record') . ' WHERE uid = :uid AND status = :status GROUP BY couponid', array(':uid' => $_W['member']['uid'], ':status' => $type));
			$coupon = activity_token_owned($_W['member']['uid'], $filter, $pindex, $psize);
			if(!empty($coupon['data'])) {
				foreach($coupon['data'] as &$value){
					$value['cototal'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('activity_coupon_record') . ' WHERE uid = :uid AND couponid = :couponid AND status = :status', array(':uid' => $_W['member']['uid'], ':couponid' => $value['couponid'], ':status' => $type));
					$value['thumb'] = tomedia($value['thumb']);
					$value['description'] = htmlspecialchars_decode($value['description']);
					$data[$value['couponid']] = $value;
				}
			}
			unset($coupon);
			$pager = pagination(count($total), $pindex, $psize);
		}
		if($op == 'use') {
			$_W['page']['title'] = '使用代金券';
			$id = intval($_GPC['id']);
			$data = activity_token_owned($_W['member']['uid'], array('couponid' => $id, 'used' => 1));
			$data = $data['data'][0];

			if(checksubmit('submit')) {
				load()->model('user');
				$password = $_GPC['password'];
				$sql = 'SELECT * FROM ' . tablename('activity_coupon_password') . " WHERE `uniacid` = :uniacid AND `password` = :password";
				$clerk = pdo_fetch($sql, array(':uniacid' => $_W['uniacid'], ':password' => $password));
				if(!empty($clerk)) {
					$status = activity_token_use($_W['member']['uid'], $id, $clerk['name']);
					if (!is_error($status)) {
						message('代金券使用成功！', $this->createMobileUrl('token',array('op'=>'mine','type' => 'used')), 'success');
					} else {
						message($status['message'], $this->createMobileUrl('token',array('op'=>'mine','type' => $_GPC['type'])), 'error');
					}
				}
				message('密码错误！', referer(), 'error');
			}
		}
		include $this->template('token');
	}
	
	public function doMobileGoods() {
		//这个操作被定义用来呈现 微站个人中心导航
		global $_W, $_GPC;
		//微信下才可以使用
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'MicroMessenger') === false) {
			include $this->template('remindnotweixin');
			exit;
		}
		//微信下才可以使用
		//回话状态清空退出
		if(!$_W['member']['uid']){
			include $this->template('remind');
			exit;
		}
		//回话状态清空退出
		//没有关注用户提示
		$status = $this->stonefish_member_checklogin();
		if (is_error($status)) {
			include $this->template('remindnotweixin');
			exit;
		}
		//没有关注用户提示
		//没有成为会员提示
		$status = $this->stonefish_member_check_login();
		if (is_error($status)) {
			$this->doMobileMember();
			exit;
		}
		//没有成为会员提示
		$do = 'goods';
		$config = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_config')." WHERE uniacid = '{$_W['uniacid']}'");
		load()->model('activity');
		load()->model('mc');
		$creditnames = array();
		$unisettings = uni_setting($uniacid, array('creditnames'));
		if (!empty($unisettings) && !empty($unisettings['creditnames'])) {
			foreach ($unisettings['creditnames'] as $key=>$credit) {
				$creditnames[$key] = $credit['title'];
			}
		}
		//兑换商城
		$filter = array('used'=>1);
		$coupons = activity_coupon_owned($_W['member']['uid'], $filter);
		$tokens = activity_token_owned($_W['member']['uid'], $filter);
		//查询实物奖品数量
		$goods['total'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('activity_exchange_trades_shipping') . ' WHERE uid = :uid AND status < :status', array(':uid' => $_W['member']['uid'], ':status' => 2));
		//查询实物奖品数量
		$activity['total'] = $goods['total'] + $tokens['total'] + $coupons['total'];
		//兑换商城
		//查询是否有通知消息
		$profile = mc_fetch($_W['member']['uid'], array('realname', 'groupid'));
		$message['total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('stonefish_member_message') . " WHERE uniacid =:uniacid AND type = 2 and (messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid =:groupid) or messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." where uid=:uid)) and endtime >:time and starttime<:time and messageid NOT IN (SELECT messageid FROM ".tablename('stonefish_member_message_record')." WHERE uid =:uid)", array(':uniacid' => $_W['uniacid'],':time' => TIMESTAMP,':groupid' => $profile['groupid'],':uid' => $_W['member']['uid']));
		//查询是否有通知消息
		//查询是否有任务
		$task['total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('stonefish_member_message') . " WHERE uniacid =:uniacid AND type = 1 and (messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." WHERE groupid =:groupid) or messageid IN (SELECT messageid FROM ".tablename('stonefish_member_message_allocation')." where uid=:uid)) and endtime >:time and starttime<:time and messageid NOT IN (SELECT messageid FROM ".tablename('stonefish_member_message_record')." WHERE uid =:uid)", array(':uniacid' => $_W['uniacid'],':time' => TIMESTAMP,':groupid' => $profile['groupid'],':uid' => $_W['member']['uid']));
		//查询是否有任务
		//查询关联功能
		$module_branch['status']=$this->module['config']['module_branch'];
		$module_shopping['status']=$this->module['config']['module_shopping'];
		$mc_activity['status']=$this->module['config']['mc_activity'];
		$mc_card['status']=$this->module['config']['mc_card'];
		$mc_pay['status']=$this->module['config']['mc_pay'];
		//查询关联功能
		$dos = array('display', 'post', 'mine', 'use', 'deliver', 'confirm');
		$op = in_array($_GPC['op'], $dos) ? $_GPC['op'] : 'display';
		if($op == 'display') {
			$_W['page']['title'] = '兑换实物类奖品';
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM '. tablename('activity_exchange'). ' WHERE uniacid = :uniacid AND type = :type AND endtime > :endtime' , array(':uniacid' => $_W['uniacid'], ':type' => 3, ':endtime' => TIMESTAMP));
			$pindex = max(1, intval($_GPC['page']));
			$psize = 10;
			$lists = pdo_fetchall('SELECT id,title,extra,thumb,type,credittype,endtime,description,credit FROM ' . tablename('activity_exchange') . ' WHERE uniacid = :uniacid AND type = :type AND endtime > :endtime ORDER BY endtime ASC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $_W['uniacid'], ':type' => 3, ':endtime' => TIMESTAMP));
			foreach($lists as &$li) {
				$li['extra'] = iunserializer($li['extra']);
				if(!is_array($li['extra'])) {
					$li['extra'] = array();
				}
			}
			$pager = pagination($total, $pindex, $psize);
		}
		if($op == 'post') {
			$id = intval($_GPC['id']); 
			$goods = activity_exchange_info($id, $_W['uniacid']);
			if(empty($goods)){
				message('没有指定的礼品兑换.');
			}
			$credit = mc_credit_fetch($_W['member']['uid'], array($goods['credittype']));
			if ($credit[$goods['credittype']] < $goods['credit']) {
				message('您的' . $creditnames[$goods['credittype']] . '数量不够,无法兑换.');
			}
	
			$ret = activity_goods_grant($_W['member']['uid'], $id, 'system', '用户使用' . $goods['credit'] . $creditnames[$goods['credittype']] . '兑换');
			if(is_error($ret)) {
				message($ret['message']);
			}
			mc_credit_update($_W['member']['uid'], $goods['credittype'], -1 * $goods['credit'], array($_W['member']['uid'], '礼品兑换 消耗 '.$goods['credit'].'个' . $creditnames[$goods['credittype']] . '(' . $goods['title'] . ')'));
			message("兑换成功,您消费了 {$goods['credit']} {$creditnames[$goods['credittype']]},现在去完善订单信息", $this->createMobileUrl('goods',array('op'=>'deliver','tid' => $ret)));
		}
		if($op == 'deliver') {
			$_W['page']['title'] = '更新收货资料';
			load()->func('tpl');
			$tid = intval($_GPC['tid']);
			$ship = pdo_fetch('SELECT * FROM ' . tablename('activity_exchange_trades_shipping') . ' WHERE uid = :uid AND tid = :tid', array(':uid' => $_W['member']['uid'], ':tid' => $tid));
			if(empty($ship)) {
				message('没有找到该兑换的收货人信息', '', 'error');
			}
			$member = mc_fetch($_W['member']['uid'], array('uid','realname','resideprovince','residecity','residedist','address','zipcode','mobile'));
			$ship['name'] = !empty($ship['name']) ? $ship['name'] : $member['realname'];
			$ship['province'] = !empty($ship['province']) ? $ship['province'] : $member['resideprovince'];
			$ship['city'] = !empty($ship['city']) ? $ship['city'] : $member['residecity'];
			$ship['district'] = !empty($ship['district']) ? $ship['district'] : $member['residedist'];
			$ship['address'] = !empty($ship['address']) ? $ship['address'] : $member['address'];
			$ship['zipcode'] = !empty($ship['zipcode']) ? $ship['zipcode'] : $member['zipcode'];
			$ship['mobile'] = !empty($ship['mobile']) ? $ship['mobile'] : $member['mobile'];
			if(checksubmit('submit')) {
				$data = array(
					'name'=>$_GPC['realname'],
					'mobile'=>$_GPC['mobile'],
					'province'=>$_GPC['reside']['province'],
					'city'=>$_GPC['reside']['city'],
					'district'=>$_GPC['reside']['district'],
					'address'=>$_GPC['address'],
					'zipcode'=>$_GPC['zipcode'],
				);
				pdo_update('activity_exchange_trades_shipping', $data, array('tid' => $tid, 'uid' => $_W['member']['uid']));
				message('收货人信息更新成功', $this->createMobileUrl('goods',array('op'=>'mine')));
			}
		}
		if($op == 'mine') {
			$_W['page']['title'] = '我的实物奖品';
			$psize = 10;
			$pindex = max(1, intval($_GPC['page']));
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('activity_exchange_trades_shipping') . ' WHERE uid = :uid AND status = :status', array(':uid' => $_W['member']['uid'], ':status' => intval($_GPC['status']))); 
			$lists = pdo_fetchall('SELECT a.*, b.id AS gid,b.title,b.extra,b.thumb,b.type,b.credittype,b.endtime,b.description,b.credit FROM ' . tablename('activity_exchange_trades_shipping') . ' AS a LEFT JOIN ' . tablename('activity_exchange'). ' AS b ON a.exid = b.id WHERE a.uid = :uid AND a.status = :status LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':uid' => $_W['member']['uid'], ':status' => intval($_GPC['status'])));
	
			foreach($lists as &$list) {
				$list['extra'] = iunserializer($list['extra']);
				if(!is_array($list['extra'])) {
					$list['extra'] = array();
				}
			}	
			$pager = pagination($total, $pindex, $psize);
		}
		if($op == 'confirm') {
			$tid = intval($_GPC['tid']);	$ship = pdo_fetch('SELECT tid FROM ' . tablename('activity_exchange_trades_shipping') . ' WHERE tid = :tid AND uid = :uid', array(':tid' => $tid, ':uid' => $_W['member']['uid']));
			if(empty($ship)) {
				message('没有找到订单信息', '', 'error');
			}
			pdo_update('activity_exchange_trades_shipping', array('status' => 2), array('uid' => $_W['member']['uid'], 'tid' => $tid));
			message('确认收货成功', $this->createMobileUrl('goods',array('op'=>'mine','status' => 2)), 'success');
		}
		
		include $this->template('goods');
	}
	function stonefish_member_checklogin() {
	    global $_W;
	    $members = pdo_fetch("SELECT `follow` FROM ".tablename('mc_mapping_fans')." WHERE `uniacid`=:uniacid AND `openid` = :openid",array(':uniacid' => $_W['uniacid'],':openid' => $_SESSION['openid']));
		if(!empty($members)){
		    //没有关注用户提示信息
			if($members['follow']==0){
			    return error(-1,'没有关注');
			}
		}else{
			return error(-1,'没有关注');
		}
	    return true;
    }
	function stonefish_member_check_login() {
	    global $_W;
	    //查询是否注册为会员并绑定手机号
		$config = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_config')." WHERE uniacid = '{$_W['uniacid']}' order by id desc");
		$profile = mc_fetch($_W['member']['uid'], array('mobile'));
		$members = pdo_fetch("SELECT `status` FROM ".tablename('stonefish_member')." WHERE `uniacid`=:uniacid AND `uid` = :uid",array(':uniacid' => $_W['uniacid'],':uid' => $_W['member']['uid']));
		if(empty($profile['mobile'])) {
		    return error(-1,'没有绑定手机号');
		}
		if(empty($members)) {
		    return error(-2,'还没有注册成为会员');
		}
		if($members['status']!=2 && $this->module['config']['smsverification'] && $config['smsstatus']) {
		    return error(-3,'还没有短信验证');
		}
	    return true;
    }
	
	public function doMobileMymember() {
		//这个操作被定义用来呈现 微站个人中心导航
	}
	public function doMobileSignin() {
		//这个操作被定义用来呈现 微站个人中心导航
		global $_W, $_GPC;		
		$stonefish_member_res = '../addons/stonefish_member/template/';
		//微信下才可以使用
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'MicroMessenger') === false) {
			include $this->template('remindnotweixin');
			exit;
		}
		//微信下才可以使用
		//回话状态清空退出
		if(!$_W['member']['uid']){
			include $this->template('remind');
			exit;
		}
		//回话状态清空退出
		//没有关注用户提示
		$status = $this->stonefish_member_checklogin();
		if (is_error($status)) {
			include $this->template('remindnotweixin');
			exit;
		}
		//没有关注用户提示
		//没有成为会员提示
		$status = $this->stonefish_member_check_login();
		if (is_error($status)) {
			$this->doMobileMember();
			exit;
		}
		//没有成为会员提示
		$uniacid = $_W['uniacid'];		
		$from_user = $_SESSION['openid'];
		$page_from_user = base64_encode(authcode($from_user, 'ENCODE'));
		$current_date = date('Y-m-d');
		
		$setting = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_config')." WHERE uniacid = :uniacid",array(':uniacid' => $uniacid));
		$start_time = strtotime($setting['start_time']);
		$end_time = strtotime($setting['end_time']);
		
		$bd = $_GPC['bd'];
		$ed = $_GPC['ed'];
		if (!empty($bd) && !empty($ed) ){		
			$current_month = $this -> getThisMonth($bd);
			$current_last_month = $this -> getLastMonth($bd);
			$current_next_month = $this -> getNextMonth($bd);
		}
		else{		
			$current_month = $this -> getThisMonth($current_date);
			$current_last_month = $this -> getLastMonth($current_date);
			$current_next_month = $this -> getNextMonth($current_date);
		}		
		$this_month_b = $current_month['0'];
		$this_month_e = $current_month['1'];
		$this_year = substr($this_month_b,0,4);
		$this_month = substr($this_month_b,5,2);
		$last_month_b = $current_last_month['0'];		
		$last_month_e = $current_last_month['1']; 		
		$last_month = substr(str_replace('-','',$last_month_b),0,6);
		$next_month_b = $current_next_month['0'];		
		$next_month_e = $current_next_month['1'];		
		$next_month = substr(str_replace('-','',$next_month_b),0,6);		
		$month_usersigned_info = pdo_fetchall("SELECT * FROM " . tablename('stonefish_member_signin_record') . " WHERE `from_user` = :from_user AND `sign_time` >= :this_month_b AND `sign_time` <= :this_month_e and uniacid = :uniacid", array(':from_user' => $from_user, ':this_month_b' => strtotime($this_month_b), ':this_month_e' => strtotime($this_month_e), ':uniacid' => $uniacid));
		$value = array(); 
		foreach( $month_usersigned_info as $value )	{
			$user_signed_days .= date('d',$value['sign_time']).',';//粉丝当月签到日期
		}
		$user_signed_days = '['.$user_signed_days.']';
		$user_lastsign_info = pdo_fetch("SELECT * FROM " . tablename('stonefish_member_signin_record') . " WHERE `from_user` = :from_user  and uniacid = :uniacid ORDER BY sign_time DESC LIMIT 1 ", array(':from_user' => $from_user, ':uniacid' => $uniacid));
		$user_maxallsign_num = $user_lastsign_info['maxtotal_sign_num'];
		$continue_sign_maxdays = $user_lastsign_info['continue_sign_maxdays'];
		$today_usersigned_info = pdo_fetchall("SELECT * FROM " . tablename('stonefish_member_signin_record') . " WHERE `from_user` = :from_user AND sign_time >= :current_date  and uniacid = :uniacid", array(':from_user' => $from_user, ':current_date' => strtotime($current_date), ':uniacid' => $uniacid));		
		$today_usersigned_num = count($today_usersigned_info);
		if(empty($user_maxallsign_num)){
			$user_maxallsign_num = 0;
		}
		if(empty($continue_sign_maxdays)){
			$continue_sign_maxdays = 0;
		}
		$profile = mc_fetch($_W['member']['uid'], array('nickname', 'realname', 'avatar', 'mobile'));
		include $this->template('signin');
	}
	
	public function getThisMonth($date){	
		$firstday = date("Y-m-01",strtotime($date));		
		$lastday = date("Y-m-d",strtotime("$firstday +1 month -1 day"));		
		return array($firstday,$lastday);		
	}	
	
	public function getLastMonth($date){	
		$timestamp=strtotime($date);		
		$firstday=date('Y-m-01',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)-1).'-01'));		
		$lastday=date('Y-m-d',strtotime("$firstday +1 month -1 day"));		
		return array($firstday,$lastday);		
	}
	
	public function getNextMonth($date){	
		$timestamp=strtotime($date);		
		$arr=getdate($timestamp);		
		if($arr['mon'] == 12){		
			$year=$arr['year'] +1;			
			$month=$arr['mon'] -11;			
			$firstday=$year.'-0'.$month.'-01';			
			$lastday=date('Y-m-d',strtotime("$firstday +1 month -1 day"));
		}
		else{		
			$firstday=date('Y-m-01',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)+1).'-01'));			
			$lastday=date('Y-m-d',strtotime("$firstday +1 month -1 day"));		
		}		
		return array($firstday,$lastday);		
	}
	public function doMobileSign() {
		global $_GPC,$_W;
		$uniacid = $_W['uniacid'];
		$fansID = $_W['member']['uid'];
		$from_user = authcode(base64_decode($_GPC['from_user']), 'DECODE');
		$page_from_user = $_GPC['from_user'];
		$now = time();
		
		$setting = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_config')." WHERE uniacid = :uniacid",array(':uniacid' => $uniacid));
		$start_time = strtotime($setting['start_time']);
		$end_time = strtotime($setting['end_time']);
		//是否开启签到
		if($setting['signinstatus']==0){
			$data = array(
			    'msg' => '签到暂时关闭，请稍候再来签到！',			
			    'status' => 0,	
		    );		
		    $msg = json_encode($data);		
		    return $msg;
		}
		//是否开启签到
		$current_date = date('Y-m-d');
		$current_date = strtotime($current_date);
		//今天签到名次
		$today_allsigned_info = pdo_fetchall("SELECT * FROM " . tablename('stonefish_member_signin_record') . " WHERE `sign_time` >= :current_date AND uniacid = :uniacid", array(':current_date' => $current_date , ':uniacid' => $uniacid));
		$today_allsigned_num = count($today_allsigned_info);//今天多少人签到
		$today_user_rank = $today_allsigned_num + 1;
		//今天签到名次
		//今天是否签到
		$today_usersigned_info = pdo_fetchall("SELECT * FROM " . tablename('stonefish_member_signin_record') . " WHERE `from_user` = :from_user AND sign_time >= :current_date  AND uniacid = :uniacid", array(':from_user' => $from_user, ':current_date' => $current_date, ':uniacid' => $uniacid));//今天签到记录
		$today_usersigned_num = count($today_usersigned_info);//今天签到次数
		//今天是否签到
		//最近签到数据
		$user_lastsign_info = pdo_fetch("SELECT * FROM " . tablename('stonefish_member_signin_record') . " WHERE `from_user` = :from_user AND uniacid = :uniacid ORDER BY sign_time DESC LIMIT 1 ", array(':from_user' => $from_user, ':uniacid' => $uniacid));
		$user_last_sign_time = $user_lastsign_info['last_sign_time'];//最后签到时间
		$user_continue_sign_days = $user_lastsign_info['continue_sign_days'];//连续签到次数
		$user_maxcontinue_sign_days = $user_lastsign_info['maxcontinue_sign_days'];
		$user_continue_sign_maxdays = $user_lastsign_info['continue_sign_maxdays'];
		$user_first_sign_days = $user_lastsign_info['first_sign_days'];//签到第一累计数
		$user_maxfirst_sign_days = $user_lastsign_info['maxfirst_sign_days'];		
		$user_allsign_num = $user_lastsign_info['total_sign_num'];//签到累计数
		$user_maxallsign_num = $user_lastsign_info['maxtotal_sign_num'];
		//最近签到数据
		$profile = mc_fetch($fansID, array('nickname', 'realname','mobile'));
		if(!empty($from_user)){
			if(!empty($profile['realname']) && !empty($profile['mobile']) ){			
				if($today_usersigned_num == 0){				
					if($now >= $start_time && $now <= $end_time){//在活动时间内
						if( $user_last_sign_time == 0){
							$user_last_sign_time = $now;
						}
						//签到时间是否为连续签到7 9
						if( ($now - $user_last_sign_time) - ($end_time - $start_time) < 86400 ){
							$continue_sign_days = $user_continue_sign_days + 1;
							$continue_sign_maxdays = $user_continue_sign_maxdays + 1;
						}else{
							$continue_sign_days = 0;
							$continue_sign_maxdays = 0;
						}
						//签到时间是否为连续签到
						//连续签到最多一次天数
						if( $continue_sign_days < $user_maxcontinue_sign_days){
							$maxcontinue_sign_days = $user_maxcontinue_sign_days;
						}else{
							$maxcontinue_sign_days = $continue_sign_days;
						}
						//连续签到最多一次天数
						//是否为签到第一名，以及签到第一累计数
						if($today_user_rank == 1){
							$first_sign_days = $user_first_sign_days + 1;
							$maxfirst_sign_days = $user_maxfirst_sign_days + 1;
						}else{
							$first_sign_days = $user_first_sign_days;
							$maxfirst_sign_days = $user_maxfirst_sign_days;
						}
						//是否为签到第一名，以及签到第一累计数
						$total_sign_num = $user_allsign_num + 1;
						$maxtotal_sign_num = $user_maxallsign_num + 1;
						//添加签到记录
						$insert = array(
							'uniacid' => $uniacid,
							'from_user' => $from_user,
							'today_rank' => $today_user_rank,
							'sign_time' => $now,
							'last_sign_time' => $now,
							'continue_sign_days' => $continue_sign_days,
							'maxcontinue_sign_days' => $maxcontinue_sign_days,
							'continue_sign_maxdays' => $continue_sign_maxdays,
							'total_sign_num' => $total_sign_num,
							'maxtotal_sign_num' => $maxtotal_sign_num,
							'first_sign_days' => $first_sign_days,
							'maxfirst_sign_days' => $maxfirst_sign_days,
						);
						pdo_insert('stonefish_member_signin_record', $insert);
						$insertid =  pdo_insertid();
						//添加签到记录
						//签到奖励
						if($setting['dsigncredit']){
							$unisetting_s = uni_setting($uniacid, array('creditnames'));		                     
						    foreach ($unisetting_s['creditnames'] as $key=>$credit) {
		    	                if ($setting['dsigntype']==$key) {
			    	                $credit_names = $credit['title'];
					                break;
			                    }
		                    }
			                //添加积分到粉丝数据库
			                mc_credit_update($fansID, $setting['dsigntype'], $setting['dsigncredit'], array($fansID, '会员每日签到奖励'.$setting['dsigncredit'].'个'.$credit_names));
			                //添加积分到粉丝数据库
							//添加积分到签到库
							pdo_update('stonefish_member_signin_record', array('dsigncredit' => $setting['dsigncredit'],'dsigntype' => $credit_names), array('id' => $insertid));
							//添加积分到签到库
						    $tip = '每日签到奖励'.$setting['dsigncredit'].'个'.$credit_names;
						}						
						//签到奖励
						//累计签到奖励
						if($total_sign_num == $setting['tsign']){
							//奖励积分
							if($setting['tsigncredit']){
							    $unisetting_s = uni_setting($uniacid, array('creditnames'));		                     
							    foreach ($unisetting_s['creditnames'] as $key=>$credit) {
		    	                    if ($setting['tsigntype']==$key) {
			    	                    $credit_names = $credit['title'];
					                    break;
			                        }
		                        }
			                    //添加积分到粉丝数据库
			                    mc_credit_update($fansID, $setting['tsigntype'], $setting['tsigncredit'], array($fansID, '会员累计签到奖励'.$setting['tsigncredit'].'个'.$credit_names));
			                    //添加积分到粉丝数据库
							    $tip .= '、累计签到奖励'.$setting['tsigncredit'].'个'.$credit_names;
								//添加奖励记录
								$insertsigntype = array(
							        'uniacid' => $uniacid,
							        'from_user' => $from_user,
									'prizetype' => '累计签到'.$setting['tsign'].'天奖励',
							        'signcredit' => $setting['tsigncredit'],
							        'signtype' => $credit_names,
							        'sign_time' => $now,
						        );
						        pdo_insert('stonefish_member_signin_prize', $insertsigntype);
								//添加奖励记录
							}
							//奖励积分
							//累计清零
							pdo_update('stonefish_member_signin_record', array('total_sign_num' => 0), array('id' => $insertid));
							//累计清零
						}
						//累计签到奖励
						//连续签到奖励
						if($continue_sign_days == $setting['csign']){
							//奖励积分
							if($setting['csigncredit']){
							    $unisetting_s = uni_setting($uniacid, array('creditnames'));		                     
							    foreach ($unisetting_s['creditnames'] as $key=>$credit) {
		    	                    if ($setting['csigntype']==$key) {
			    	                    $credit_names = $credit['title'];
					                    break;
			                        }
		                        }
			                    //添加积分到粉丝数据库
			                    mc_credit_update($fansID, $setting['csigntype'], $setting['csigncredit'], array($fansID, '会员连续签到奖励'.$setting['csigncredit'].'个'.$credit_names));
			                    //添加积分到粉丝数据库							    
							    $tip .= '、连续签到奖励'.$setting['csigncredit'].'个'.$credit_names;
							    //添加奖励记录
								$insertsigntype = array(
							        'uniacid' => $uniacid,
							        'from_user' => $from_user,
									'prizetype' => '连续签到'.$setting['csign'].'天奖励',
							        'signcredit' => $setting['csigncredit'],
							        'signtype' => $credit_names,
							        'sign_time' => $now,
						        );
						        pdo_insert('stonefish_member_signin_prize', $insertsigntype);
								//添加奖励记录
							}
							//奖励积分
							//累计清零
							pdo_update('stonefish_member_signin_record', array('continue_sign_days' => 0), array('id' => $insertid));
							//累计清零
						}
						//连续签到奖励
						//累计第一签到奖励
						if($first_sign_days == $setting['osign']){
							//奖励积分
							if($setting['osigncredit']){
								$unisetting_s = uni_setting($uniacid, array('creditnames'));		                     
							    foreach ($unisetting_s['creditnames'] as $key=>$credit) {
		    	                    if ($setting['osigntype']==$key) {
			    	                    $credit_names = $credit['title'];
					                    break;
			                        }
		                        }
			                    //添加积分到粉丝数据库
			                    mc_credit_update($fansID, $setting['osigntype'], $setting['osigncredit'], array($fansID, '会员连续签到奖励'.$setting['osigncredit'].'个'.$credit_names));
			                    //添加积分到粉丝数据库							
							    $tip .= '、第一累计奖励'.$setting['osigncredit'].'个'.$credit_names;
							    //添加奖励记录
								$insertsigntype = array(
							        'uniacid' => $uniacid,
							        'from_user' => $from_user,
									'prizetype' => '累计签到第一'.$setting['osign'].'天奖励',
							        'signcredit' => $setting['osigncredit'],
							        'signtype' => $credit_names,
							        'sign_time' => $now,
						        );
						        pdo_insert('stonefish_member_signin_prize', $insertsigntype);
								//添加奖励记录
							}
							//奖励积分
							//累计清零
							pdo_update('stonefish_member_signin_record', array('first_sign_days' => 0), array('id' => $insertid));
							//累计清零
						}
						//累计第一签到奖励
						$status = 1;
						if($tip==''){
							$tip = '签到成功!';
						}else{
							$tip = '签到成功，获得'.$tip.'.';
						}
					}else{
						$status = 0;
					    if($now > $end_time){
					        $tip = '亲！您签到也太晚了吧~~';
					    }else{
						    $tip = '亲！您签到也太早了吧~~';
					    }
					}
				}else{
					$status = 0;
					$tip = '今日已签过到了~~';					
				}
			}else{
				$status = 0;
				$tip = '请先注册会员';
				$url = $this->createMobileUrl('member');
			}
		}else{
			$status = 0;			
			$tip = '请先注册'.$_W['account']['childname'];
			$url = $this->createMobileUrl('member');
		}
		$data = array(
			'msg' => $tip,			
			'status' => $status,			
			'url' => $url,		
		);		
		$msg = json_encode($data);		
		return $msg;		
		//print_r($start_day);
	}
	
	public function doMobileSigninrecord() {	
		global $_GPC, $_W;		
		$stonefish_member_res = '../addons/stonefish_member/template/';
		$uniacid = $_W['uniacid'];
		$fansID = $_W['member']['uid'];
		$from_user = authcode(base64_decode($_GPC['from_user']), 'DECODE');
		$page_from_user = $_GPC['from_user'];
		$record = pdo_fetchall("SELECT * FROM ".tablename('stonefish_member_signin_record')." WHERE uniacid = :uniacid AND from_user = :from_user ORDER BY sign_time DESC ", array(':uniacid' => $uniacid, ':from_user' => $from_user ));
		include $this->template('signinrecord');
	}
	
	public function doMobileSignintop() {	
		global $_GPC, $_W;		
		global $_GPC, $_W;		
		$stonefish_member_res = '../addons/stonefish_member/template/';
		$uniacid = $_W['uniacid'];
		$fansID = $_W['member']['uid'];
		$from_user = authcode(base64_decode($_GPC['from_user']), 'DECODE');
		$page_from_user = $_GPC['from_user'];
		$current_date = date('Y-m-d');
		$showrank = pdo_fetchcolumn("SELECT showrank FROM ".tablename('stonefish_member_config')." WHERE uniacid = :uniacid",array(':uniacid' => $uniacid));
		$top = pdo_fetchall("SELECT * FROM ".tablename('stonefish_member_signin_record')." WHERE uniacid = :uniacid AND sign_time >= :current_date ORDER BY today_rank ASC LIMIT {$showrank}", array(':uniacid' => $uniacid, ':current_date' => strtotime($current_date) ));
		foreach ($top as &$tops) {
			$uid = pdo_fetchcolumn("SELECT uid FROM ".tablename('mc_mapping_fans')." WHERE uniacid = :uniacid and openid = :openid",array(':uniacid' => $uniacid,':openid' => $tops['from_user']));
			$tops['username'] = pdo_fetchcolumn("SELECT realname FROM ".tablename('mc_members')." WHERE uid = :uid",array(':uid' => $uid));
			if(empty($tops['username'])){
				$tops['username'] = pdo_fetchcolumn("SELECT nickname FROM ".tablename('mc_members')." WHERE uid = :uid",array(':uid' => $uid));
			}
			if(empty($tops['username'])){
				$tops['username'] = '匿名';
			}
		}
		include $this->template('signintop');
	}
	
	public function doMobileSigninprize() {	
		global $_GPC, $_W;		
		$stonefish_member_res = '../addons/stonefish_member/template/';
		$uniacid = $_W['uniacid'];
		$fansID = $_W['member']['uid'];
		$from_user = authcode(base64_decode($_GPC['from_user']), 'DECODE');
		$page_from_user = $_GPC['from_user'];
		$prize = pdo_fetchall("SELECT * FROM ".tablename('stonefish_member_signin_prize')." WHERE uniacid = :uniacid AND from_user = :from_user ORDER BY sign_time DESC ", array(':uniacid' => $uniacid, ':from_user' => $from_user ));
		include $this->template('signinprize');
	}
	
	public function doMobileMyintegration() {
		//这个操作被定义用来呈现 微站个人中心导航
	}
	
	public function doMobileMyprivilege() {
		//这个操作被定义用来呈现 微站个人中心导航
	}
	
	public function doMobileMyannounce() {
		//这个操作被定义用来呈现 微站个人中心导航
	}
	
	public function web_message($error, $url = '', $errno = -1) {
        $data = array();
        $data['errno'] = $errno;
        if (!empty($url)) {
            $data['url'] = $url;
        }
        $data['error'] = $error;
        echo json_encode($data);
        exit;
    }
	
	public function doMobileVerifycode() {//获取手机验证码
		global $_GPC, $_W;
		$receiver = trim($_GPC['receiver']);
		if($receiver == ''){
			exit('请输入手机号');
		} elseif(preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/", $receiver)){
			$receiver_type = 'mobile';
		} else {
			exit('您输入的手机号格式错误');
		}
		$sql = 'DELETE FROM ' . tablename('uni_verifycode') . ' WHERE `createtime`<' . (TIMESTAMP - 1800);
		pdo_query($sql);

		$sql = 'SELECT * FROM ' . tablename('uni_verifycode') . ' WHERE `receiver`=:receiver AND `uniacid`=:uniacid';
		$pars = array();
		$pars[':receiver'] = $receiver;
		$pars[':uniacid'] = $_W['uniacid'];
		$row = pdo_fetch($sql, $pars);
		$record = array();
		if(!empty($row)) {
			if($row['total'] >= 5) {
				exit('您的操作过于频繁,请稍后再试');
			}
			$code = $row['verifycode'];
			$record['total']++;
		} else {
			$code = random(6, true); 
			$record['uniacid'] = $_W['uniacid'];
			$record['receiver'] = $receiver;
			$record['verifycode'] = $code;
			$record['total'] = 1;
			$record['createtime'] = TIMESTAMP;
		}		
		$result = $this->sms_send($receiver, $code);
		if(is_error($result)) {
			header('error: ' . urlencode($result['message']));
			exit($result['message']);
		} else {
			if(!empty($row)) {
				pdo_update('uni_verifycode', $record, array('id' => $row['id']));
			} else {
				pdo_insert('uni_verifycode', $record);
			}
			exit('success');
		}		
	}
	function sms_send($mobile, $code) {//发送短信手机验证码
	    global $_W;
	    load()->func('communication');
	    //读取短信配置
	    $config = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_config')." WHERE uniacid = '{$_W['uniacid']}' order by id desc");
		//查询短信配额数量
		$smstotal = pdo_fetchcolumn("SELECT sum(smstotal) FROM ".tablename('stonefish_member_sms')." WHERE uniacid = '".$_W['uniacid']."'");
		$smsdraw = pdo_fetchcolumn("SELECT sum(smsdraw) FROM ".tablename('stonefish_member_sms')." WHERE uniacid = '".$_W['uniacid']."'");
		//查询短信配额数量
	    if($config['smsstatus']) {
			if($smstotal-$smsdraw>=1){
				$tpl_value = urlencode("#code#=".$code."&#app#=".$config['sign']."");
				$dat = ihttp_post('http://v.juhe.cn/sms/send?key='.$config['smskey'].'&mobile='.$mobile.'&tpl_id='.$config['tpl_id'].'&tpl_value='.$tpl_value.'');
		        if($dat){
			        $result =json_decode($dat,true);
			        #错误码判断
			        $error_code = $result['error_code'];
			        if($error_code==0){
				        //添加短信记录
						$insertsms = array();
						$insertsms['uniacid'] = $_W['uniacid'];
						$insertsms['uid'] = $_W['member']['uid'];
						$insertsms['code'] = $code;
						$insertsms['mobile'] = $mobile;
						$insertsms['createtime'] = TIMESTAMP;
						pdo_insert('stonefish_member_smsrecord', $insertsms);
						//添加短信记录
						//添加使用次数
						$smsconfig = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_sms')." WHERE uniacid = '".$_W['uniacid']."' and smstotal>smsdraw order by id asc");
						pdo_update('stonefish_member_sms', array('smsdraw' => $smsconfig['smsdraw']+1), array('id' => $smsconfig['id']));
						//添加使用次数
						return true;
			        }else{
				        return error(-1, $result['reason']);
			        }
		        }
		        return error(-1, '发送短信失败, 请联系系统管理人员. 错误详情: 不能链接短信服务网关');
			}else{
				return error(-1, '发送短信失败, 短信配额已使用完');
			}		    
	    }
	    return error(-1, '发送短信失败, 请联系系统管理人员. 错误详情: 没有设置短信参数');
		return true;
    }
	
	
	function code_verify($uniacid, $receiver, $code) {	
	    //验证手机验证码是否正确
		//读取短信配置
	    $config = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_config')." WHERE uniacid = '{$uniacid}' order by id desc");
	    if($config['smsstatus']) {
	        $data = pdo_fetch('SELECT * FROM ' . tablename('uni_verifycode') . ' WHERE uniacid = :uniacid AND receiver = :receiver AND verifycode = :verifycode AND createtime > :createtime', array(':uniacid' => $uniacid, ':receiver' => $receiver, ':verifycode' => $code, ':createtime' => time() - $config['aging']));
	        if(empty($data)) {
		        return false;
	        }else{
	            if($config['agingrepeat']){
		            pdo_delete('uni_verifycode', array('id' => $data['id']));
		        }
	        }
	        return true;
	    }else{
	        $data = pdo_fetch('SELECT * FROM ' . tablename('uni_verifycode') . ' WHERE uniacid = :uniacid AND receiver = :receiver AND verifycode = :verifycode AND createtime > :createtime', array(':uniacid' => $uniacid, ':receiver' => $receiver, ':verifycode' => $code, ':createtime' => time() - 1800));
	        if(empty($data)) {
		        return false;
	        }
	        return true;
	    }
		//验证手机验证码是否正确
    }

}
?>