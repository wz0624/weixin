<?php

//decode by QQ:270656184 http://www.yunlu99.com/
global $_W, $_GPC;
$this->_doMobileAuth();
$user = $this->_user;
$is_user_infoed = $this->_is_user_infoed;
$this->_doMobileInitialize();
$cmd = $this->_cmd;
$wall = $this->_wall;
$wall_status = $this->_wall_status;
$mine = $this->_mine;
$cmd = $_GPC['cmd'];
if ($cmd == 'location') {
	$latitude = $_GPC['latitude'];
	$longitude = $_GPC['longitude'];
	if (empty($latitude) || empty($longitude)) {
		$this->returnError('位置获取失败');
	}
	$url = "http://api.map.baidu.com/geocoder/v2/?ak=" . $_W['module_setting']['bd_ak'] . "&location=" . $latitude . "," . $longitude . "&output=json&pois=0";
	load()->func('communication');
	$response = ihttp_get($url);
	if (!is_error($response)) {
		$data = @json_decode($response['content'], true);
		if (empty($data) || $data['status'] != 0) {
			$this->returnError('位置获取失败：' . $data['message'] . '(' . $data['status'] . ')');
		} else {
			$data = $data['result'];
			if (!empty($wall['district'])) {
				if (empty($data['addressComponent']['district'])) {
					$this->returnError('区县获取失败');
				}
				$district = str_replace("自治区", "", $data['addressComponent']['district']);
				$district = str_replace("区", "", $district);
				$district = str_replace("县", "", $district);
				$district = str_replace("市", "", $district);
				if (false === strstr($wall['district'], $district)) {
					pdo_query('UPDATE ' . tablename('gandl_wall_user') . ' SET in_position=2,last_position=:last_position where id=:id', array(':id' => $mine['id'], ':last_position' => iserializer($data)));
					$this->returnSuccess('区县定位成功', 2);
				}
			}
			if (!empty($wall['city'])) {
				if (empty($data['addressComponent']['city'])) {
					$this->returnError('城市获取失败');
				}
				$city = str_replace("市", "", $data['addressComponent']['city']);
				if (false === strstr($wall['city'], $city)) {
					pdo_query('UPDATE ' . tablename('gandl_wall_user') . ' SET in_position=2,last_position=:last_position where id=:id', array(':id' => $mine['id'], ':last_position' => iserializer($data)));
					$this->returnSuccess('城市定位成功', 2);
				}
			}
			if (!empty($wall['province'])) {
				if (empty($data['addressComponent']['province'])) {
					$this->returnError('省获取失败');
				}
				$province = str_replace("省", "", $data['addressComponent']['province']);
				if (false === strstr($wall['province'], $province)) {
					pdo_query('UPDATE ' . tablename('gandl_wall_user') . ' SET in_position=2,last_position=:last_position where id=:id', array(':id' => $mine['id'], ':last_position' => iserializer($data)));
					$this->returnSuccess('省定位成功', 2);
				}
			}
			pdo_query('UPDATE ' . tablename('gandl_wall_user') . ' SET in_position=1,last_position=:last_position,last_position_exp=:last_position_exp where id=:id', array(':id' => $mine['id'], ':last_position' => iserializer($data), ':last_position_exp' => time() + 864000));
			$this->returnSuccess('城市定位成功', 1);
		}
	} else {
		$this->returnError('位置获取失败，请重试');
	}
} else {
	if ($cmd == 'piece_add') {
		$model = intval($_GPC['model']);
		if (!in_array($model, array(1, 2, 3))) {
			$this->returnError('暂不支持该撒钱模式');
		}
		if (!in_array('1', $wall['piece_model']) && $model == 1) {
			$this->returnError('暂不支持普通模式');
		}
		if (!in_array('2', $wall['piece_model']) && $model == 2) {
			$this->returnError('暂不支持口令模式');
		}
		if (!in_array('3', $wall['piece_model']) && $model == 3) {
			$this->returnError('暂不支持组团模式');
		}
		if ($model == 2) {
			$wall['total_min'] = $wall['total_min2'];
			$wall['total_max'] = $wall['total_max2'];
			$wall['avg_min'] = $wall['avg_min2'];
			$wall['fee'] = $wall['fee2'];
		} else {
			if ($model == 3) {
				$wall['total_min'] = $wall['total_min3'];
				$wall['total_max'] = $wall['total_max3'];
				$wall['avg_min'] = $wall['avg_min3'];
				$wall['fee'] = $wall['fee3'];
			}
		}
		$submit = $_GPC['submit'];
		if ($submit == 'add') {
			$content = $_GPC['content'];
			$link = trim($_GPC['link']);
			$total_num = intval($_GPC['total_num']);
			$total_amount = floatval($_GPC['total_amount']);
			$hot_time = intval($_GPC['hot_time']);
			$fee = floatval($_GPC['fee']);
			$total_pay = floatval($_GPC['total_pay']);
			if (empty($content)) {
				$this->returnError('请说点儿什么吧~');
			}
			if (text_len($content) > 5000) {
				$this->returnError('内容不能超过5000字哦~');
			}
			if (!empty($link)) {
				if (!preg_match("/^(http|ftp):/", $link)) {
					$link = 'http://' . $link;
				}
			}
			if (text_len($link) > 500) {
				$this->returnError('链接内容超长啦！');
			}
			if (empty($total_amount) || $total_amount <= 0) {
				$this->returnError('请填撒出金额');
			}
			if ($total_amount < floatval($wall['total_min'] / 100)) {
				$this->returnError('撒出金额不能低于' . floatval($wall['total_min'] / 100) . '元');
			}
			if ($total_amount > floatval($wall['total_max'] / 100)) {
				$this->returnError('撒出金额不能超过' . floatval($wall['total_max'] / 100) . '元');
			}
			if (empty($total_num) || $total_num < 1) {
				$this->returnError('请填写份数');
			}
			if ($total_num > intval($total_amount * 100 / $wall['avg_min'])) {
				$this->returnError($total_amount . '元最多可分' . intval($total_amount * 100 / $wall['avg_min']) . '份');
			}
			$password = trim($_GPC['password']);
			if ($model == 2) {
				if (empty($password)) {
					$this->returnError('您还没填写口令哦~');
				}
				if (text_len($password) > 6) {
					$this->returnError('口令最多6个字哦~');
				}
			}
			$group_size = intval($_GPC['group_size']);
			if ($model == 3) {
				if ($group_size < 2) {
					$this->returnError('组团人数至少为2人');
				}
				if ($group_size > $wall['groupmax']) {
					$this->returnError('组团人数最多为' . $wall['groupmax'] . '人');
				}
			}
			$the_fee = intval($total_amount * $wall['fee']) / 100;
			if ($the_fee != $fee) {
				$this->returnError('服务费已变化，请刷新后重新发布');
			}
			$the_pay = intval(($total_amount + $the_fee) * 100) / 100;
			if ($the_pay != $total_pay) {
				$this->returnError('金额计算出错，请刷新后重新发布');
			}
			$images = $_GPC['images'];
			if (!empty($images) && count($images) > 0) {
				load()->func('file');
				$down_images = array();
				$WeiXinAccountService = WeiXinAccount::create($_W['oauth_account']);
				foreach ($images as $imgid) {
					if (strpos($imgid, 'images/') === 0) {
						$down_images[] = $imgid;
					} else {
						$ret = $WeiXinAccountService->downloadMedia(array('media_id' => $imgid, 'type' => 'image'));
						if (is_error($ret)) {
							$this->returnError('图片上传失败:' . $ret['message']);
						}
						$ret = VP_IMAGE_SAVE($ret);
						if (!empty($ret['error'])) {
							$this->returnError('上传图片失败:' . $ret['error']);
						}
						$down_images[] = $ret['image'];
					}
				}
				$images = iserializer($down_images);
			}
			$avg_max = $total_amount * 100 - intval(($total_amount * 100 - intval($total_amount * 100 / $total_num)) / 2);
			if ($wall['avg_max'] > 0) {
				$avg_max = intval($total_amount * 100 / $total_num) * $wall['avg_max'];
				if ($avg_max > $total_amount * 100) {
					$avg_max = $total_amount * 100;
				}
			}
			$rob_plan = redpack_plan($total_amount * 100, $total_num, $avg_max, 1);
			$rob_plan = implode(',', $rob_plan);
			$top_level = 0;
			if ($wall['top_line'] > 0 && $total_amount * 100 >= $wall['top_line']) {
				$top_level = $total_amount * 100;
			}
			$piece = array('uniacid' => $_W['uniacid'], 'wall_id' => $wall['id'], 'user_id' => $mine['user_id'], 'model' => $model, 'content' => $content, 'images' => $images, 'link' => $link, 'total_num' => $total_num, 'total_amount' => $total_amount * 100, 'hot_time' => $hot_time, 'top_level' => $top_level, 'fee' => $fee * 100, 'total_pay' => $the_pay * 100, 'status' => 0, 'views' => 0, 'links' => 0, 'rob_plan' => $rob_plan, 'rob_amount' => 0, 'rob_users' => 0, 'create_time' => time(), 'op' => $wall['piece_verify'] == 1 ? -1 : 0);
			if ($model == 2) {
				$piece['password'] = $password;
				$piece['password_show'] = 0;
			}
			if ($model == 3) {
				$piece['group_size'] = $group_size;
			}
			pdo_insert('gandl_wall_piece', $piece);
			$piece_id = pdo_insertid();
			if ($piece_id > 0) {
				$params = array('tid' => $piece_id, 'ordersn' => $piece_id, 'title' => $wall['topic'] . '撒钱', 'fee' => $the_pay, 'user' => $user['nickname']);
				$params = $this->payReady($params);
				$this->returnSuccess('', base64_encode(json_encode($params)));
			} else {
				$this->returnError('发表失败，请重试');
			}
		} else {
			if ($is_user_infoed == 0) {
				$this->doMobileLogin();
			}
			$piid = $_GPC['piid'];
			$piece = null;
			if (!empty($piid)) {
				$piid = pdecode($piid);
				if (!empty($piid)) {
					$piid = intval($piid);
					if ($piid > 0) {
						$piece = pdo_fetch("select model,total_amount,total_num,content,images,link,password,group_size from " . tablename('gandl_wall_piece') . " where uniacid=:uniacid and wall_id=:wall_id and id=:id ", array(':uniacid' => $_W['uniacid'], ':wall_id' => $wall['id'], ':id' => $piid));
						if (!empty($piece) && !empty($piece['images'])) {
							$piece['images'] = iunserializer($piece['images']);
						}
					}
				}
			}
			include $this->template('piece_add');
		}
	} else {
		if ($cmd == 'robs_list') {
			$start = $_GPC['start'];
			if (!isset($start) || empty($start) || intval($start <= 0)) {
				$start = 0;
			} else {
				$start = intval($start);
			}
			$limit = 20;
			$list = pdo_fetchall("select P.id,P.total_amount,P.content,P.publish_time,P.user_id,R.money,R.create_time from " . tablename('gandl_wall_rob') . " R INNER JOIN " . tablename('gandl_wall_piece') . " P ON R.piece_id=P.id  where R.uniacid=:uniacid  and R.wall_id=:wall_id and R.user_id=:user_id ORDER BY R.create_time DESC limit " . $start . "," . $limit . " ", array(':uniacid' => $_W['uniacid'], ':wall_id' => $wall['id'], ':user_id' => $user['uid']));
			$more = 1;
			if (empty($list) || count($list) < $limit) {
				$more = 0;
			}
			$start += count($list);
			if (!empty($list)) {
				for ($i = 0; $i < count($list); $i++) {
					$list[$i]['_url'] = $_W['siteroot'] . 'app/' . substr($this->createMobileUrl('piece', array('pid' => pencode($wall['id']), 'piid' => pencode($list[$i]['id']))), 2);
				}
			}
			returnSuccess('', array('start' => $start, 'more' => $more, 'list' => $list, 'now' => time()));
		} else {
			if ($cmd == 'robs') {
				include $this->template('mine_robs');
			} else {
				if ($cmd == 'sends_list') {
					$start = $_GPC['start'];
					if (!isset($start) || empty($start) || intval($start <= 0)) {
						$start = 0;
					} else {
						$start = intval($start);
					}
					$limit = 20;
					$list = pdo_fetchall("select id,total_amount,total_num,content,publish_time,hot_time,rob_start_time,rob_users,views from " . tablename('gandl_wall_piece') . " where uniacid=:uniacid  and wall_id=:wall_id and user_id=:user_id and status>0 ORDER BY create_time DESC limit " . $start . "," . $limit . " ", array(':uniacid' => $_W['uniacid'], ':wall_id' => $wall['id'], ':user_id' => $user['uid']));
					$more = 1;
					if (empty($list) || count($list) < $limit) {
						$more = 0;
					}
					$start += count($list);
					if (!empty($list)) {
						for ($i = 0; $i < count($list); $i++) {
							$list[$i]['_url'] = $_W['siteroot'] . 'app/' . substr($this->createMobileUrl('piece', array('pid' => pencode($wall['id']), 'piid' => pencode($list[$i]['id']))), 2);
						}
					}
					returnSuccess('', array('start' => $start, 'more' => $more, 'list' => $list, 'now' => time()));
				} else {
					if ($cmd == 'sends') {
						$total_views = pdo_fetchcolumn("select SUM(views) from " . tablename('gandl_wall_piece') . " where uniacid=:uniacid and wall_id=:wall_id and user_id=:user_id ", array(':uniacid' => $_W['uniacid'], ':wall_id' => $wall['id'], ':user_id' => $user['uid']));
						include $this->template('mine_sends');
					} else {
						if ($cmd == 'transfers_list') {
							$start = $_GPC['start'];
							if (!isset($start) || empty($start) || intval($start <= 0)) {
								$start = 0;
							} else {
								$start = intval($start);
							}
							$limit = 20;
							$list = pdo_fetchall("select id,money,status,channel,out_money,create_time from " . tablename('gandl_wall_user_transfer') . " where uniacid=:uniacid  and wall_id=:wall_id and user_id=:user_id ORDER BY create_time DESC limit " . $start . "," . $limit . " ", array(':uniacid' => $_W['uniacid'], ':wall_id' => $wall['id'], ':user_id' => $user['uid']));
							$more = 1;
							if (empty($list) || count($list) < $limit) {
								$more = 0;
							}
							$start += count($list);
							returnSuccess('', array('start' => $start, 'more' => $more, 'list' => $list, 'now' => time()));
						} else {
							if ($cmd == 'transfers') {
								$out_moneys = pdo_fetchcolumn("select SUM(out_money) from " . tablename('gandl_wall_user_transfer') . " where uniacid=:uniacid and wall_id=:wall_id and user_id=:user_id ", array(':uniacid' => $_W['uniacid'], ':wall_id' => $wall['id'], ':user_id' => $user['uid']));
								include $this->template('mine_transfers');
							} else {
								if ($cmd == 'transfer') {
									if (date('G') >= 23 || date('G') < 9) {
										$this->returnError('每天23点至次日9点期间暂停转账');
									}
									$total_cnt = pdo_fetchcolumn("select COUNT(id) from " . tablename('gandl_wall_user_transfer') . " where uniacid=:uniacid AND create_time>:cold_time ", array(':uniacid' => $_W['uniacid'], ':cold_time' => time() - 55));
									if ($total_cnt > 1800) {
										$this->returnError('当前操作人数较多，请稍后再试');
									}
									$user_cnt = pdo_fetchcolumn("select COUNT(id) from " . tablename('gandl_wall_user_transfer') . " where uniacid=:uniacid AND openid=:openid AND create_time>:cold_time ", array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid'], ':cold_time' => time() - 55));
									if ($user_cnt >= 1) {
										$this->returnError('每分钟只能转账1次，请稍后再试');
									}
									$money = $mine['money'];
									$transfer_min = empty($wall['transfer_min']) ? 100 : $wall['transfer_min'];
									if ($money < $transfer_min) {
										$this->returnError('至少满' . $transfer_min / 100 . '元才可转哦~');
									}
									if ($money > 20000) {
										$money = 20000;
									}
									pdo_insert('gandl_wall_user_transfer', array('uniacid' => $_W['uniacid'], 'wall_id' => $wall['id'], 'user_id' => $user['uid'], 'openid' => $_W['openid'], 'money' => $money, 'money_before' => $mine['money'], 'money_after' => $mine['money'] - $money, 'status' => 0, 'create_time' => time(), 'update_time' => time()));
									$transfer_id = pdo_insertid();
									if (empty($transfer_id)) {
										$this->returnError('操作失败，请重试');
									}
									$ret1 = pdo_query('UPDATE ' . tablename('gandl_wall_user') . ' SET money=money-:money,money_out=money_out+:money_out where id=:id', array(':id' => $mine['id'], ':money' => $money, ':money_out' => $money));
									if (false === $ret1) {
										$this->returnError('操作失败，请重试');
									}
									$ret2 = $this->transferByRedpack(array('id' => $transfer_id, 'nick_name' => '抢钱提现', 'send_name' => '抢钱提现', 'money' => $money, 'wishing' => '祝您天天开心！', 'act_name' => '提现红包', 'remark' => '用户微信红包提现'));
									if (is_error($ret2)) {
										pdo_query('UPDATE ' . tablename('gandl_wall_user') . ' SET money=money+:money,money_out=money_out-:money_out where id=:id', array(':id' => $mine['id'], ':money' => $money, ':money_out' => $money));
										pdo_query('UPDATE ' . tablename('gandl_wall_user_transfer') . ' SET status=3,update_time=:update_time where id=:id', array(':update_time' => time(), ':id' => $transfer_id));
										$this->returnError('操作失败：' . $ret2['message']);
									} else {
										pdo_query('UPDATE ' . tablename('gandl_wall_user_transfer') . ' SET status=1,channel=:channel,mch_billno=:mch_billno,out_billno=:out_billno,out_money=:out_money,tag=:tag,update_time=:update_time where id=:id', array(':channel' => 1, ':mch_billno' => $ret2['mch_billno'], ':out_billno' => $ret2['out_billno'], ':out_money' => $ret2['out_money'], ':tag' => $ret2['tag'], ':update_time' => time(), ':id' => $transfer_id));
										$this->returnSuccess('成功提出' . $money / 100 . '元');
									}
								} else {
									if ($cmd == 'profile') {
										$submit = $_GPC['submit'];
										if ($submit == 'save') {
											$avatar = $_GPC['avatar'];
											$nickname = $_GPC['nickname'];
											$who = $_GPC['who'];
											$home = $_GPC['home'];
											if (empty($avatar)) {
												$this->returnError('请上传头像');
											}
											if (empty($nickname)) {
												$this->returnError('请设置名称');
											}
											if (text_len($nickname) > 10) {
												$this->returnError('名称不能超过10个字哦~');
											}
											if (!in_array($who, array(0, 1, 2, 3, 4, 5))) {
												$this->returnError('请选择类型');
											}
											if (strlen($home) > 200) {
												$this->returnError('主页内容太长了');
											}
											if ($avatar != $mine['avatar']) {
												load()->func('file');
												$WeiXinAccountService = WeiXinAccount::create($_W['oauth_account']);
												$ret = $WeiXinAccountService->downloadMedia(array('media_id' => $avatar, 'type' => 'image'));
												if (is_error($ret)) {
													$this->returnError('头像上传失败:' . $ret['message']);
												}
												$ret = VP_IMAGE_SAVE($ret);
												if (!empty($ret['error'])) {
													$this->returnError('上传头像失败:' . $ret['error']);
												}
												$avatar = $ret['image'];
											}
											pdo_update('gandl_wall_user', array('avatar' => $avatar, 'nickname' => $nickname, 'who' => $who, 'home' => $home), array('id' => $mine['id'], 'uniacid' => $_W['uniacid']));
											$this->returnSuccess('保存成功');
										}
										include $this->template('mine_profile');
									} else {
										if ($cmd == 'admin') {
											$exp = $_GPC['exp'];
											if (empty($exp)) {
												$this->returnError('操作过期');
											}
											$exp = pdecode($exp);
											if (empty($exp)) {
												$this->returnError('操作过期');
											}
											if ($exp < time()) {
												$this->returnError('操作过期');
											}
											pdo_query('UPDATE ' . tablename('gandl_wall_user') . ' SET admin=1 where uniacid=:uniacid and wall_id=:wall_id and id=:id', array(':uniacid' => $_W['uniacid'], ':wall_id' => $wall['id'], ':id' => $mine['id']));
											$this->returnSuccess('您已成为' . $wall['topic'] . '的管理员');
										} else {
											if ($cmd == 'cold') {
												$mine_cold_time = $mine['follow'] == 1 ? $wall['cold_time'] - $wall['task_follow'] : $wall['cold_time'];
												$mine_cold_time = $mine_cold_time - ($wall['task_invite'] == 0 ? 0 : ($mine['rob_fast'] > $wall['task_invite_max'] ? $wall['task_invite_max'] : $mine['rob_fast']));
												include $this->template('mine_cold');
											} else {
												if ($cmd == 'up') {
													if (empty($mine['inviter_id'])) {
														$this->returnError('我自成一派，没有老大');
													}
													$boss = pdo_fetch("SELECT id,user_id FROM " . tablename('gandl_wall_user') . " WHERE  uniacid=:uniacid and wall_id=:wall_id and id=:id ", array(':uniacid' => $_W['uniacid'], ':wall_id' => $wall['id'], ':id' => $mine['inviter_id']));
													if (empty($boss)) {
														$this->returnError('我的老大已退隐江湖');
													}
													$boss['_user'] = mc_fetch($boss['user_id'], array('nickname', 'avatar'));
													$up_total = pdo_fetchcolumn("select SUM(up_money) from " . tablename('gandl_wall_up_rob') . " where uniacid=:uniacid and wall_id=:wall_id and mine_id=:mine_id and up_id=:up_id ", array(':uniacid' => $_W['uniacid'], ':wall_id' => $wall['id'], ':mine_id' => $mine['id'], ':up_id' => $boss['id']));
													include $this->template('mine_up');
												} else {
													if ($cmd == 'ups_list') {
														$start = $_GPC['start'];
														if (!isset($start) || empty($start) || intval($start <= 0)) {
															$start = 0;
														} else {
															$start = intval($start);
														}
														$limit = 20;
														$list = pdo_fetchall("select P.id,P.total_amount,P.content,P.publish_time,P.user_id,R.up_money,R.create_time from " . tablename('gandl_wall_rob') . " R INNER JOIN " . tablename('gandl_wall_piece') . " P ON R.piece_id=P.id  where R.uniacid=:uniacid  and R.wall_id=:wall_id and R.user_id=:user_id AND R.up_money>0 ORDER BY R.create_time DESC limit " . $start . "," . $limit . " ", array(':uniacid' => $_W['uniacid'], ':wall_id' => $wall['id'], ':user_id' => $user['uid']));
														$more = 1;
														if (empty($list) || count($list) < $limit) {
															$more = 0;
														}
														$start += count($list);
														if (!empty($list)) {
															for ($i = 0; $i < count($list); $i++) {
																$list[$i]['_url'] = $_W['siteroot'] . 'app/' . substr($this->createMobileUrl('piece', array('pid' => pencode($wall['id']), 'piid' => pencode($list[$i]['id']))), 2);
															}
														}
														returnSuccess('', array('start' => $start, 'more' => $more, 'list' => $list, 'now' => time()));
													} else {
														if ($cmd == 'down') {
															$down_total = pdo_fetchcolumn("select COUNT(id) from " . tablename('gandl_wall_user') . " where uniacid=:uniacid and wall_id=:wall_id and inviter_id=:inviter_id ", array(':uniacid' => $_W['uniacid'], ':wall_id' => $wall['id'], ':inviter_id' => $mine['id']));
															$down_money = pdo_fetchcolumn("select SUM(up_money) from " . tablename('gandl_wall_up_rob') . " where uniacid=:uniacid and wall_id=:wall_id and up_id=:up_id ", array(':uniacid' => $_W['uniacid'], ':wall_id' => $wall['id'], ':up_id' => $mine['id']));
															include $this->template('mine_down');
														} else {
															if ($cmd == 'downs_list') {
																$start = $_GPC['start'];
																if (!isset($start) || empty($start) || intval($start <= 0)) {
																	$start = 0;
																} else {
																	$start = intval($start);
																}
																$limit = 20;
																$list = pdo_fetchall(" SELECT A.user_id,B.sum_up_money FROM " . tablename('gandl_wall_user') . " A LEFT JOIN (select user_id,SUM(up_money) AS sum_up_money from " . tablename('gandl_wall_up_rob') . "  GROUP BY user_id ORDER BY sum_up_money DESC  ) B ON(A.user_id=B.user_id) where A.uniacid=:uniacid  and A.wall_id=:wall_id and A.inviter_id=:inviter_id limit " . $start . "," . $limit . " ", array(':uniacid' => $_W['uniacid'], ':wall_id' => $wall['id'], ':inviter_id' => $mine['id']));
																if (!empty($list)) {
																	$uids = array();
																	foreach ($list as $v) {
																		$uids[] = $v['user_id'];
																	}
																	$users = mc_fetch($uids, array('nickname', 'avatar'));
																	for ($i = 0; $i < count($list); $i++) {
																		$u = $users[$list[$i]['user_id']];
																		if (!empty($u['avatar'])) {
																			$u['avatar'] = VP_AVATAR($u['avatar'], 's');
																		}
																		$list[$i]['_user'] = $u;
																	}
																}
																$more = 1;
																if (empty($list) || count($list) < $limit) {
																	$more = 0;
																}
																$start += count($list);
																returnSuccess('', array('start' => $start, 'more' => $more, 'list' => $list, 'now' => time()));
															} else {
																if ($cmd == 'notify') {
																	$notify = $_GPC['notify'];
																	$status = $_GPC['status'];
																	if (empty($notify)) {
																		$this->returnError('缺少参数');
																	}
																	if ($status == 1 && $mine['follow'] != 1) {
																		$this->returnError('关注后才能收到通知', 'unfollow');
																	}
																	if ($notify == 'newpiece') {
																		pdo_query('UPDATE ' . tablename('gandl_wall_user') . ' SET notify_newpiece=' . ($status == 1 ? 1 : 0) . ' where uniacid=:uniacid and wall_id=:wall_id and id=:id', array(':uniacid' => $_W['uniacid'], ':wall_id' => $wall['id'], ':id' => $mine['id']));
																	}
																	$this->returnSuccess('操作成功');
																} else {
																	$mine_cold_time = $mine['follow'] == 1 ? $wall['cold_time'] - $wall['task_follow'] : $wall['cold_time'];
																	$mine_cold_time = $mine_cold_time - ($wall['task_invite'] == 0 ? 0 : ($mine['rob_fast'] > $wall['task_invite_max'] ? $wall['task_invite_max'] : $mine['rob_fast']));
																	if ($wall['up_rob_fee'] > 0) {
																		if ($mine['inviter_id'] > 0) {
																			$up_total = pdo_fetchcolumn("select SUM(up_money) from " . tablename('gandl_wall_up_rob') . " where uniacid=:uniacid and wall_id=:wall_id and mine_id=:mine_id and up_id=:up_id ", array(':uniacid' => $_W['uniacid'], ':wall_id' => $wall['id'], ':mine_id' => $mine['id'], ':up_id' => $mine['inviter_id']));
																		}
																		$down_total = pdo_fetchcolumn("select COUNT(id) from " . tablename('gandl_wall_user') . " where uniacid=:uniacid and wall_id=:wall_id and inviter_id=:inviter_id ", array(':uniacid' => $_W['uniacid'], ':wall_id' => $wall['id'], ':inviter_id' => $mine['id']));
																	}
																	include $this->template('mine');
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
}