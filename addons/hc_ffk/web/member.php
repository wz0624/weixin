<?php
	if($op=='display'){
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$members = pdo_fetchall("select * from ".tablename('hc_ffk_member')." where uniacid = ".$uniacid." order by id desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		$total = pdo_fetchcolumn("select count(id) from ".tablename('hc_ffk_member')." where uniacid = ".$uniacid);
		$pager = pagination($total, $pindex, $psize);
	}
	
	if($op=='sort'){
		$sort = array(
			'realname'=>$_GPC['realname'],
			'mobile'=>$_GPC['mobile']
		);
		$members = pdo_fetchall("select * from ".tablename('hc_ffk_member')." where realname like '%".$sort['realname']."%' and mobile like '%".$sort['mobile']."%' and uniacid = ".$uniacid." order by id desc");
	}
	
	if($op=='detail'){
		$id = $_GPC['id'];
		if(!empty($id)){
			$member = pdo_fetch("select * from ".tablename('hc_ffk_member')." where id = ".$id);
			$follow = pdo_fetch("select uid, follow from ".tablename('mc_mapping_fans')." where uniacid = ".$uniacid." and openid = '".$member['openid']."'");
			load()->model('mc');
			$fcredit = mc_fetch($follow['uid'], array('credit1'));
			if(checksubmit('submit')){
				$member = array(
					'realname'=>trim($_GPC['realname']),
					'mobile'=>$_GPC['mobile'],
					'status'=>$_GPC['status'],
					'ffktimes'=>$_GPC['ffktimes'],
				);
				pdo_update('hc_ffk_member', $member, array('id'=>$id));
				pdo_update('mc_members', array('credit1'=>intval($_GPC['credit1'])), array('uid'=>$follow['uid']));
				message('提交成功！', $this->createWebUrl('member'), 'success');
			}
		}
		include $this->template('web/member_detail');
		exit;
	}
	
	if($op=='delete'){
		$id = $_GPC['id'];
		pdo_delete('hc_ffk_member', array('id'=>$id));
		message('删除成功！', $this->createWebUrl('member'), 'success');
	}
	
	include $this->template('web/member_list');
?>