<?php
//session_start();
//$_SESSION['__:proxy:openid'] = 'oyIjYt9lQx9flMXl9F9NiAqrJd3g';
//debug
global $_W, $_GPC;

$this->_doMobileAuth();
$user=$this->_user;
$is_user_infoed=$this->_is_user_infoed;

$this->_doMobileInitialize();
$cmd=$this->_cmd;
$wall=$this->_wall;
$wall_status=$this->_wall_status;
$mine=$this->_mine;

if(empty($mine) || !($mine['admin']>0)){
	$this->returnError('抱歉，您没有该操作的访问权限');
}

$cmd=$_GPC['cmd']; // 请求命令

// 动态内管理
if(in_array($cmd,array('op_close','op_open','op_top','op_untop','op_password','op_unpassword','op_notify'))){
	// 目前仅针对具体广告进行管理，所以该部分提出来
	// 获取当前操作的广告内容
	$piid = $_GPC['piid'];
	if(empty($piid)){
		$this->returnError('访问错误，缺少参数');
	}
	$piid=pdecode($piid);
	if(empty($piid)){
		$this->returnError('访问错误，参数错误');
	}
	$piid = intval($piid);
	if($piid<=0){
		$this->returnError('访问错误，参数有误');
	}
	$piece = pdo_fetch("select * from " . tablename('gandl_wall_piece') . " where uniacid=:uniacid and wall_id=:wall_id and id=:id ", array(':uniacid' => $_W['uniacid'],':wall_id' => $wall['id'],':id' => $piid));
	if(empty($piece)){
		$this->returnError('该内容不存在');
	}

	if($cmd=='op_close'){ // 关闭
		// 将该内容关闭
		pdo_query('UPDATE '.tablename('gandl_wall_piece') .' SET op=:op,op_remark=:op_remark,op_admin=:op_admin where uniacid=:uniacid and wall_id=:wall_id and id=:id', array(':uniacid' => $_W['uniacid'],':wall_id' => $wall['id'],':id'=>$piece['id'],'op'=>1,'op_remark'=>'内容违规','op_admin'=>$user['uid']));
		
		$this->returnSuccess('关闭成功');

	}else if($cmd=='op_open'){ // 显示内容
		// 将该内容显示
		pdo_query('UPDATE '.tablename('gandl_wall_piece') .' SET op=:op,op_admin=:op_admin where uniacid=:uniacid and wall_id=:wall_id and id=:id', array(':uniacid' => $_W['uniacid'],':wall_id' => $wall['id'],':id'=>$piece['id'],'op'=>0,'op_admin'=>$user['uid']));
		
		$this->returnSuccess('显示成功');
	}else if($cmd=='op_top'){// 置顶
		pdo_query('UPDATE '.tablename('gandl_wall_piece') .' SET top_level=total_amount where uniacid=:uniacid and wall_id=:wall_id and id=:id', array(':uniacid' => $_W['uniacid'],':wall_id' => $wall['id'],':id'=>$piece['id']));
		
		$this->returnSuccess('置顶成功');
	}else if($cmd=='op_untop'){// 取消置顶
		pdo_query('UPDATE '.tablename('gandl_wall_piece') .' SET top_level=0 where uniacid=:uniacid and wall_id=:wall_id and id=:id', array(':uniacid' => $_W['uniacid'],':wall_id' => $wall['id'],':id'=>$piece['id']));
		
		$this->returnSuccess('取消置顶成功');
	}else if($cmd=='op_password'){// 隐藏口令
		pdo_query('UPDATE '.tablename('gandl_wall_piece') .' SET password_show=0 where uniacid=:uniacid and wall_id=:wall_id and id=:id', array(':uniacid' => $_W['uniacid'],':wall_id' => $wall['id'],':id'=>$piece['id']));
		
		$this->returnSuccess('隐藏口令成功');
	}else if($cmd=='op_unpassword'){// 显示口令
		pdo_query('UPDATE '.tablename('gandl_wall_piece') .' SET password_show=1 where uniacid=:uniacid and wall_id=:wall_id and id=:id', array(':uniacid' => $_W['uniacid'],':wall_id' => $wall['id'],':id'=>$piece['id']));
		
		$this->returnSuccess('显示口令成功');
	}else if($cmd=='op_notify'){// 推送撒钱通知

		if($wall['notify']!=1){
			$this->returnError('该圈子没有开启消息推送功能');
		}
		$wall['notify_tpl']=iunserializer($wall['notify_tpl']);
		if(empty($wall['notify_tpl']['newpiece'])){
			$this->returnError('该圈子没有设置相关消息模板');
		}

		$accObj = WeiXinAccount::create($_W['oauth_account']);

		// 查询订阅通知的用户
		$subscribers =  pdo_fetchall("select F.openid from " . tablename('mc_mapping_fans') . " F where F.follow=1 and F.uid IN(select user_id from " . tablename('gandl_wall_user') . " where uniacid=:uniacid and wall_id=:wall_id and notify_newpiece=1) ",array(':uniacid' => $_W['uniacid'],':wall_id' => $wall['id']));

		$postdata = array(
			'first' => array(
				'value' => "新任务",
				'color' => '#576b95'
			),
			'keyword1' => array(
				'value' => '有土豪撒钱了，赶紧去抢',
				'color' => '#576b95'
			),
			'keyword2' => array(
				'value' => date('Y-m-d H:i:s',time()),
				'color' => '#576b95'
			),
			'keyword3' => array(
				'value' => '即将开始',
				'color' => '#576b95'
			),
			'remark' => array(
				'value' => '如果您不想接收该消息提醒，可在个人中心页面选择关闭哦~',
				'color' => '#999999'
			),
		);
		$gotourl=$_W['siteroot'] . 'app/' . substr($this->createMobileUrl('piece',array('pid'=>pencode($piece['wall_id']),'piid'=>pencode($piece['id']))), 2);
		$j=0;
		for($i=0;$i<count($subscribers);$i++){
			$status=$accObj->sendTplNotice($subscribers[$i]['openid'], $wall['notify_tpl']['newpiece'], $postdata, $gotourl,'#FF5454');
			if($status===true){
				$j++;
			}
		}

		pdo_query('UPDATE '.tablename('gandl_wall_piece') .' SET notify_time=:notify_time,notify_cnt=:notify_cnt where uniacid=:uniacid and wall_id=:wall_id and id=:id', array(':uniacid' => $_W['uniacid'],':wall_id' => $wall['id'],':id'=>$piece['id'],':notify_time'=>time(),':notify_cnt'=>$j));


		$this->returnSuccess('通知已送达'.$j.'人');

	}else{
		$this->returnError('缺少指令');
	}
}else{
	if($cmd=='static'){ // 统计管理
		$submit = $_GPC['submit'];
		if($submit=='save'){
			$fake_money = intval($_GPC['fake_money']);
			$fake_user = intval($_GPC['fake_user']);
			$fake_online = intval($_GPC['fake_online']);
			pdo_query('UPDATE '.tablename('gandl_wall') .' SET fake_money=:fake_money,fake_user=:fake_user,fake_online=:fake_online where uniacid=:uniacid and id=:id ', array(':uniacid' => $_W['uniacid'],':id' => $wall['id'],':fake_money' => $fake_money,':fake_user' => $fake_user,':fake_online' => $fake_online));
			// 更新缓存
			$wall_static_cache_key='gandl_wall_wall_static:'.$wall['id'];
			cache_delete($wall_static_cache_key);
			$this->returnSuccess('保存成功');
		}else{
			include $this->template('admin_static');
			exit();
		}
	}else if($cmd=='test_loc'){ // 测试定位
		$submit = $_GPC['submit'];
		if($submit=='loc'){
			$latitude=$_GPC['latitude'];
			$longitude=$_GPC['longitude'];

			if(empty($latitude) || empty($longitude)){
				$this->returnError('位置获取失败');
			}

			// 百度反地址查询接口
			$url = "http://api.map.baidu.com/geocoder/v2/?ak=".$_W['module_setting']['bd_ak']."&location=".$latitude.",".$longitude."&output=json&pois=0";
					
			load()->func('communication');
			$response = ihttp_get($url);
			if(!is_error($response)) {
				//$data = $response//@json_decode($response, true);
				$data = @json_decode($response['content'], true);

				if(empty($data) || $data['status']!=0){
					$this->returnError('位置获取失败：'.$data['message'].'('.$data['status'].')');
				}else{
					$data=$data['result'];
					// 把地址格式化操作放在服务器端，以便后期更换接口服务方
					$city='';
					// 地址
					if(!empty($data['addressComponent'])){ 
						$city=$data['addressComponent']['city'];
					}
					if(empty($city)){
						$this->returnError('城市获取失败');
					}
					// 去掉市字
					$city=str_replace("市", "", $city);
					//$city='南京';
					// 获取成功，保存用户本次位置
					//pdo_query('UPDATE '.tablename('gandl_wall_user') .' SET last_city=:city where id=:id', array(':id' => $mine['id'],':city'=>$city));
					$this->returnSuccess('城市定位成功',$response['content']);
				}
			}else{
				$this->returnError('位置获取失败，请重试');
			}
		}else{
			include $this->template('admin_test_loc');
			exit();
		}
	}
}




?>