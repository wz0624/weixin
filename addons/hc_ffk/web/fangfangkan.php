<?php	
	if (checksubmit('submit')) {
		$id = intval($_GPC['id']);
		$data = array(
			'uniacid'=>$uniacid,
			'level'=>intval($_GPC['level']),
			'easycredit'=>intval($_GPC['easycredit']),
			'normalcredit'=>intval($_GPC['normalcredit']),
			'hardcredit'=>intval($_GPC['hardcredit']),
			'gametime'=>intval($_GPC['gametime']),
			'gametimes'=>intval($_GPC['gametimes']),
			'showtime'=>intval($_GPC['showtime']),
			'ffpicture'=>$_GPC['ffpicture'],
			'picture1'=>$_GPC['picture1'],
			'picture2'=>$_GPC['picture2'],
			'picture3'=>$_GPC['picture3'],
			'picture4'=>$_GPC['picture4'],
			'picture5'=>$_GPC['picture5'],
			'isopen'=>intval($_GPC['isopen']),
			'createtime'=>time(),
		);
		if (empty($id)) {
			pdo_insert('hc_ffk_fangfangkan',$data);
			message('提交成功',$this->createWebUrl('fangfangkan',array('op' => 'display')),'success');
		}else{
			unset($data['createtime']);
			pdo_update('hc_ffk_fangfangkan',$data,array('id' => $id));
			message('提交成功',$this->createWebUrl('fangfangkan',array('op' => 'display')),'success');
		}
	}
	$item = pdo_fetch("select * from ".tablename('hc_ffk_fangfangkan')." where uniacid = ".$uniacid);
	if(empty($item)){
		$item['isopen'] = 1;
	}
	include $this->template('web/fangfangkan');
?>