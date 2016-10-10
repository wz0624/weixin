<?php

//decode by QQ:270656184 http://www.yunlu99.com/
global $_W, $_GPC;
if ("save" == $_GPC['submit']) {
	if (GANL_WALL_BRANCH == 'S') {
		if (!empty($_GPC['wall']['province'])) {
			returnError('您的程序为合作运营版，不能限制到省');
		}
		if (empty($_GPC['wall']['city']) && empty($_GPC['wall']['district'])) {
			returnError('您的程序为合作运营版，必须填写一个限制城市或区县');
		}
		if (!empty($_GPC['wall']['city']) && count(explode(',', $_GPC['wall']['city'])) > 1) {
			returnError('您的程序为合作运营版，每个圈子只能限制一个城市');
		}
		if (!empty($_GPC['wall']['district']) && count(explode(',', $_GPC['wall']['district'])) > 1) {
			returnError('您的程序为合作运营版，每个圈子只能限制一个区县');
		}
	}
	if (GANL_WALL_BRANCH == 'C') {
		if (!empty($_GPC['wall']['province'])) {
			returnError('您的程序为城市版，不能限制到省');
		}
		if (empty($_GPC['wall']['city']) && empty($_GPC['wall']['district'])) {
			returnError('您的程序为城市版，必须填写一个限制城市或区县');
		}
		if (!empty($_GPC['wall']['city']) && count(explode(',', $_GPC['wall']['city'])) > 1) {
			returnError('您的程序为城市版，每个圈子只能限制一个城市');
		}
		if (!empty($_GPC['wall']['district']) && count(explode(',', $_GPC['wall']['district'])) > 1) {
			returnError('您的程序为城市版，每个圈子只能限制一个区县');
		}
	}
	$GandlWallModel = new GandlWallModel();
	$wall = $_GPC['wall'];
	if (empty($wall['piece_model'])) {
		returnError('至少开启一种撒钱模式');
	}
	if (!empty($wall['piece_model']) && count($wall['piece_model']) > 0) {
		if (in_array('3', $wall['piece_model']) && empty($wall['groupmax'])) {
			returnError('开启团伙模式必须设置团伙人数上限');
		}
		$wall['piece_model'] = implode(',', $wall['piece_model']);
	} else {
		$wall['piece_model'] = '';
	}
	if (false === $GandlWallModel->create($wall)) {
		returnError('验证出错：' . $GandlWallModel->getError());
	}
	$GandlWallModel->__unset('uniacid');
	$GandlWallModel->__set('detail', htmlspecialchars_decode($GandlWallModel->__get('detail')));
	$GandlWallModel->__set('remark', htmlspecialchars_decode($GandlWallModel->__get('remark')));
	$wall_slider = $_GPC['wall_slider'];
	$wall_slider_link = $_GPC['wall_slider_link'];
	if (!empty($wall_slider) && count($wall_slider) > 0) {
		$GandlWallModel->__set('slider', iserializer(array('images' => $wall_slider, 'links' => $wall_slider_link)));
	} else {
		$GandlWallModel->__set('slider', '');
	}
	$GandlWallModel->__set('notify_tpl', iserializer($GandlWallModel->__get('notify_tpl')));
	$time = $GandlWallModel->__get('time');
	if (!empty($time) && count($time) > 0) {
		foreach ($time as $k => $v) {
			$GandlWallModel->__set($k . '_time', strtotime($v));
		}
		$GandlWallModel->__unset('time');
	}
	$GandlWallModel->__set('share', iserializer(array('title' => $GandlWallModel->__get('share_title'), 'img' => $GandlWallModel->__get('share_img'), 'desc' => $GandlWallModel->__get('share_desc'))));
	$GandlWallModel->__unset('share_title');
	$GandlWallModel->__unset('share_img');
	$GandlWallModel->__unset('share_desc');
	if (false === $GandlWallModel->save()) {
		returnError('操作失败，请重试');
	}
	returnSuccess("圈子修改成功", $this->createWebUrl('list'));
} else {
	$id = intval($_GPC['id']);
	if (empty($id)) {
		message('抱歉，传递的参数错误！', '', 'error');
	}
	$wall = pdo_fetch("select * from " . tablename('gandl_wall') . " where uniacid=:uniacid and id=:id ", array(':uniacid' => $_W['uniacid'], ':id' => $id));
	if (empty($wall)) {
		message('抱歉，没有相关数据！', '', 'error');
	}
	$wall['time'] = array('start' => date('Y-m-d H:i:s', $wall['start_time']), 'end' => date('Y-m-d H:i:s', $wall['end_time']));
	if (!empty($wall['piece_model'])) {
		$wall['piece_model'] = explode(',', $wall['piece_model']);
	}
	if (!empty($wall['slider'])) {
		$wall['slider'] = iunserializer($wall['slider']);
	}
	if (!empty($wall['notify_tpl'])) {
		$wall['notify_tpl'] = iunserializer($wall['notify_tpl']);
	}
	$wall['share'] = iunserializer($wall['share']);
	$wall['share_title'] = $wall['share']['title'];
	$wall['share_img'] = $wall['share']['img'];
	$wall['share_desc'] = $wall['share']['desc'];
	load()->func("tpl");
	include $this->template('web/edit');
}