<?php

//decode by Zi
if (!defined('IN_IA')) {
	die('Access Denied');
}
if (!class_exists('PosterModel')) {
	class PosterModel extends PluginModel
	{
		public function checkScan()
		{
			global $_W, $_GPC;
			$_var_0 = m('user')->getOpenid();
			$_var_1 = intval($_GPC['posterid']);
			if (empty($_var_1)) {
				return;
			}
			$_var_2 = pdo_fetch('select id,times from ' . tablename('ewei_shop_poster') . ' where id=:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $_var_1));
			if (empty($_var_2)) {
				return;
			}
			$_var_3 = intval($_GPC['mid']);
			if (empty($_var_3)) {
				return;
			}
			$_var_4 = m('member')->getMember($_var_3);
			if (empty($_var_4)) {
				return;
			}
			$this->scanTime($_var_0, $_var_4['openid'], $_var_2);
		}
		public function scanTime($_var_0, $_var_5, $_var_2)
		{
			if ($_var_0 == $_var_5) {
				return;
			}
			global $_W, $_GPC;
			$_var_6 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_poster_scan') . ' where openid=:openid  and posterid=:posterid and uniacid=:uniacid limit 1', array(':openid' => $_var_0, ':posterid' => $_var_2['id'], ':uniacid' => $_W['uniacid']));
			if ($_var_6 <= 0) {
				$_var_7 = array('uniacid' => $_W['uniacid'], 'posterid' => $_var_2['id'], 'openid' => $_var_0, 'from_openid' => $_var_5, 'scantime' => time());
				pdo_insert('ewei_shop_poster_scan', $_var_7);
				pdo_update('ewei_shop_poster', array('times' => $_var_2['times'] + 1), array('id' => $_var_2['id']));
			}
		}
		public function createCommissionPoster($_var_0, $_var_8 = 0)
		{
			global $_W;
			$_var_9 = 2;
			if (!empty($_var_8)) {
				$_var_9 = 3;
			}
			$_var_2 = pdo_fetch('select * from ' . tablename('ewei_shop_poster') . ' where uniacid=:uniacid and type=:type and isdefault=1 limit 1', array(':uniacid' => $_W['uniacid'], ':type' => $_var_9));
			if (empty($_var_2)) {
				return '';
			}
			$_var_10 = m('member')->getMember($_var_0);
			if (empty($_var_2)) {
				return "";
			}
			$_var_11 = $this->getQR($_var_2, $_var_10, $_var_8);
			if (empty($_var_11)) {
				return "";
			}
			return $this->createPoster($_var_2, $_var_10, $_var_11, false);
		}
		public function getFixedTicket($_var_2, $_var_10, $_var_12)
		{
			global $_W, $_GPC;
			$_var_13 = md5("ewei_shop_poster:{$_W['uniacid']}:{$_var_10['openid']}:{$_var_2['id']}");
			$_var_14 = '{"action_info":{"scene":{"scene_str":"' . $_var_13 . '"} },"action_name":"QR_LIMIT_STR_SCENE"}';
			$_var_15 = $_var_12->fetch_token();
			$_var_16 = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $_var_15;
			$_var_17 = curl_init();
			curl_setopt($_var_17, CURLOPT_URL, $_var_16);
			curl_setopt($_var_17, CURLOPT_POST, 1);
			curl_setopt($_var_17, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($_var_17, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($_var_17, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($_var_17, CURLOPT_POSTFIELDS, $_var_14);
			$_var_18 = curl_exec($_var_17);
			$_var_19 = @json_decode($_var_18, true);
			if (!is_array($_var_19)) {
				return false;
			}
			if (!empty($_var_19['errcode'])) {
				return error(-1, $_var_19['errmsg']);
			}
			$_var_20 = $_var_19['ticket'];
			return array('barcode' => json_decode($_var_14, true), 'ticket' => $_var_20);
		}
		public function getQR($_var_2, $_var_10, $_var_8 = 0)
		{
			global $_W, $_GPC;
			$_var_21 = $_W['acid'];
			if ($_var_2['type'] == 1) {
				$_var_22 = m('qrcode')->createShopQrcode($_var_10['id'], $_var_2['id']);
				$_var_11 = pdo_fetch('select * from ' . tablename('ewei_shop_poster_qr') . ' where openid=:openid and acid=:acid and type=:type limit 1', array(':openid' => $_var_10['openid'], ':acid' => $_W['acid'], ':type' => 1));
				if (empty($_var_11)) {
					$_var_11 = array('acid' => $_var_21, 'openid' => $_var_10['openid'], 'type' => 1, 'qrimg' => $_var_22);
					pdo_insert('ewei_shop_poster_qr', $_var_11);
					$_var_11['id'] = pdo_insertid();
				}
				$_var_11['current_qrimg'] = $_var_22;
				return $_var_11;
			} else {
				if ($_var_2['type'] == 2) {
					$_var_23 = p('commission');
					if ($_var_23) {
						$_var_22 = $_var_23->createMyShopQrcode($_var_10['id'], $_var_2['id']);
						$_var_11 = pdo_fetch('select * from ' . tablename('ewei_shop_poster_qr') . ' where openid=:openid and acid=:acid and type=:type limit 1', array(':openid' => $_var_10['openid'], ':acid' => $_W['acid'], ':type' => 2));
						if (empty($_var_11)) {
							$_var_11 = array('acid' => $_var_21, 'openid' => $_var_10['openid'], 'type' => 2, 'qrimg' => $_var_22);
							pdo_insert('ewei_shop_poster_qr', $_var_11);
							$_var_11['id'] = pdo_insertid();
						}
						$_var_11['current_qrimg'] = $_var_22;
						return $_var_11;
					}
				} else {
					if ($_var_2['type'] == 3) {
						$_var_22 = m('qrcode')->createGoodsQrcode($_var_10['id'], $_var_8, $_var_2['id']);
						$_var_11 = pdo_fetch('select * from ' . tablename('ewei_shop_poster_qr') . ' where openid=:openid and acid=:acid and type=:type and goodsid=:goodsid limit 1', array(':openid' => $_var_10['openid'], ':acid' => $_W['acid'], ':type' => 3, ':goodsid' => $_var_8));
						if (empty($_var_11)) {
							$_var_11 = array('acid' => $_var_21, 'openid' => $_var_10['openid'], 'type' => 3, 'goodsid' => $_var_8, 'qrimg' => $_var_22);
							pdo_insert('ewei_shop_poster_qr', $_var_11);
							$_var_11['id'] = pdo_insertid();
						}
						$_var_11['current_qrimg'] = $_var_22;
						return $_var_11;
					} else {
						if ($_var_2['type'] == 4) {
							$_var_12 = WeAccount::create($_var_21);
							$_var_11 = pdo_fetch('select * from ' . tablename('ewei_shop_poster_qr') . ' where openid=:openid and acid=:acid and type=4 limit 1', array(':openid' => $_var_10['openid'], ':acid' => $_var_21));
							if (empty($_var_11)) {
								$_var_19 = $this->getFixedTicket($_var_2, $_var_10, $_var_12);
								if (is_error($_var_19)) {
									return $_var_19;
								}
								if (empty($_var_19)) {
									return error(-1, '生成二维码失败');
								}
								$_var_24 = $_var_19['barcode'];
								$_var_20 = $_var_19['ticket'];
								$_var_22 = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $_var_20;
								$_var_25 = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'scene_str' => $_var_24['action_info']['scene']['scene_str'], 'model' => 2, 'name' => 'EWEI_SHOP_POSTER_QRCODE', 'keyword' => 'EWEI_SHOP_POSTER', 'expire' => 0, 'createtime' => time(), 'status' => 1, 'url' => $_var_19['url'], 'ticket' => $_var_19['ticket']);
								pdo_insert('qrcode', $_var_25);
								$_var_11 = array('acid' => $_var_21, 'openid' => $_var_10['openid'], 'type' => 4, 'scenestr' => $_var_24['action_info']['scene']['scene_str'], 'ticket' => $_var_19['ticket'], 'qrimg' => $_var_22, 'url' => $_var_19['url']);
								pdo_insert('ewei_shop_poster_qr', $_var_11);
								$_var_11['id'] = pdo_insertid();
								$_var_11['current_qrimg'] = $_var_22;
							} else {
								$_var_11['current_qrimg'] = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $_var_11['ticket'];
							}
							return $_var_11;
						}
					}
				}
			}
		}
		public function getRealData($_var_26)
		{
			$_var_26['left'] = intval(str_replace('px', '', $_var_26['left'])) * 2;
			$_var_26['top'] = intval(str_replace('px', '', $_var_26['top'])) * 2;
			$_var_26['width'] = intval(str_replace('px', '', $_var_26['width'])) * 2;
			$_var_26['height'] = intval(str_replace('px', '', $_var_26['height'])) * 2;
			$_var_26['size'] = intval(str_replace('px', '', $_var_26['size'])) * 2;
			$_var_26['src'] = tomedia($_var_26['src']);
			return $_var_26;
		}
		public function createImage($_var_27)
		{
			load()->func('communication');
			$_var_28 = ihttp_request($_var_27);
			return imagecreatefromstring($_var_28['content']);
		}
		public function mergeImage($_var_29, $_var_26, $_var_27)
		{
			$_var_30 = $this->createImage($_var_27);
			$_var_31 = imagesx($_var_30);
			$_var_32 = imagesy($_var_30);
			imagecopyresized($_var_29, $_var_30, $_var_26['left'], $_var_26['top'], 0, 0, $_var_26['width'], $_var_26['height'], $_var_31, $_var_32);
			imagedestroy($_var_30);
			return $_var_29;
		}
		public function mergeText($_var_29, $_var_26, $_var_33)
		{
			$_var_34 = IA_ROOT . '/addons/ewei_shop/static/fonts/msyh.ttf';
			$_var_35 = $this->hex2rgb($_var_26['color']);
			$_var_36 = imagecolorallocate($_var_29, $_var_35['red'], $_var_35['green'], $_var_35['blue']);
			imagettftext($_var_29, $_var_26['size'], 0, $_var_26['left'], $_var_26['top'] + $_var_26['size'], $_var_36, $_var_34, $_var_33);
			return $_var_29;
		}
		function hex2rgb($_var_37)
		{
			if ($_var_37[0] == '#') {
				$_var_37 = substr($_var_37, 1);
			}
			if (strlen($_var_37) == 6) {
				list($_var_38, $_var_39, $_var_40) = array($_var_37[0] . $_var_37[1], $_var_37[2] . $_var_37[3], $_var_37[4] . $_var_37[5]);
			} elseif (strlen($_var_37) == 3) {
				list($_var_38, $_var_39, $_var_40) = array($_var_37[0] . $_var_37[0], $_var_37[1] . $_var_37[1], $_var_37[2] . $_var_37[2]);
			} else {
				return false;
			}
			$_var_38 = hexdec($_var_38);
			$_var_39 = hexdec($_var_39);
			$_var_40 = hexdec($_var_40);
			return array('red' => $_var_38, 'green' => $_var_39, 'blue' => $_var_40);
		}
		public function createPoster($_var_2, $_var_10, $_var_11, $_var_41 = true)
		{
			global $_W;
			$_var_42 = IA_ROOT . '/addons/ewei_shop/data/poster/' . $_W['uniacid'] . '/';
			if (!is_dir($_var_42)) {
				load()->func('file');
				mkdirs($_var_42);
			}
			if (!empty($_var_11['goodsid'])) {
				$_var_43 = pdo_fetch('select id,title,thumb,commission_thumb,marketprice,productprice from ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $_var_11['goodsid'], ':uniacid' => $_W['uniacid']));
				if (empty($_var_43)) {
					m('message')->sendCustomNotice($_var_10['openid'], '未找到商品，无法生成海报');
					die;
				}
			}
			$_var_44 = md5(json_encode(array('openid' => $_var_10['openid'], 'goodsid' => $_var_11['goodsid'], 'bg' => $_var_2['bg'], 'data' => $_var_2['data'], 'version' => 1)));
			$_var_45 = $_var_44 . '.png';
			if (!is_file($_var_42 . $_var_45) || $_var_11['qrimg'] != $_var_11['current_qrimg']) {
				set_time_limit(0);
				@ini_set('memory_limit', '256M');
				$_var_29 = imagecreatetruecolor(640, 1008);
				$_var_46 = $this->createImage(tomedia($_var_2['bg']));
				imagecopy($_var_29, $_var_46, 0, 0, 0, 0, 640, 1008);
				imagedestroy($_var_46);
				$_var_26 = json_decode(str_replace('&quot;', '\'', $_var_2['data']), true);
				foreach ($_var_26 as $_var_47) {
					$_var_47 = $this->getRealData($_var_47);
					if ($_var_47['type'] == 'head') {
						$_var_48 = preg_replace('/\\/0$/i', '/96', $_var_10['avatar']);
						$_var_29 = $this->mergeImage($_var_29, $_var_47, $_var_48);
					} else {
						if ($_var_47['type'] == 'img') {
							$_var_29 = $this->mergeImage($_var_29, $_var_47, $_var_47['src']);
						} else {
							if ($_var_47['type'] == 'qr') {
								$_var_29 = $this->mergeImage($_var_29, $_var_47, tomedia($_var_11['current_qrimg']));
							} else {
								if ($_var_47['type'] == 'nickname') {
									$_var_29 = $this->mergeText($_var_29, $_var_47, $_var_10['nickname']);
								} else {
									if (!empty($_var_43)) {
										if ($_var_47['type'] == 'title') {
											$_var_29 = $this->mergeText($_var_29, $_var_47, $_var_43['title']);
										} else {
											if ($_var_47['type'] == 'thumb') {
												$_var_49 = !empty($_var_43['commission_thumb']) ? tomedia($_var_43['commission_thumb']) : tomedia($_var_43['thumb']);
												$_var_29 = $this->mergeImage($_var_29, $_var_47, $_var_49);
											} else {
												if ($_var_47['type'] == 'marketprice') {
													$_var_29 = $this->mergeText($_var_29, $_var_47, $_var_43['marketprice']);
												} else {
													if ($_var_47['type'] == 'productprice') {
														$_var_29 = $this->mergeText($_var_29, $_var_47, $_var_43['productprice']);
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
				imagepng($_var_29, $_var_42 . $_var_45);
				imagedestroy($_var_29);
				if ($_var_11['qrimg'] != $_var_11['current_qrimg']) {
					pdo_update('ewei_shop_poster_qr', array('qrimg' => $_var_11['current_qrimg']), array('id' => $_var_11['id']));
				}
			}
			$_var_30 = $_W['siteroot'] . 'addons/ewei_shop/data/poster/' . $_W['uniacid'] . '/' . $_var_45;
			if (!$_var_41) {
				return $_var_30;
			}
			if ($_var_11['qrimg'] != $_var_11['current_qrimg'] || empty($_var_11['mediaid']) || empty($_var_11['createtime']) || $_var_11['createtime'] + 3600 * 24 * 3 - 7200 < time()) {
				$_var_50 = $this->uploadImage($_var_42 . $_var_45);
				$_var_11['mediaid'] = $_var_50;
				pdo_update('ewei_shop_poster_qr', array('mediaid' => $_var_50, 'createtime' => time()), array('id' => $_var_11['id']));
			}
			return array('img' => $_var_30, 'mediaid' => $_var_11['mediaid']);
		}
		public function uploadImage($_var_30)
		{
			load()->func('communication');
			$_var_51 = m('common')->getAccount();
			$_var_52 = $_var_51->fetch_token();
			$_var_16 = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token={$_var_52}&type=image";
			$_var_17 = curl_init();
			$_var_26 = array('media' => '@' . $_var_30);
			if (version_compare(PHP_VERSION, '5.5.0', '>')) {
				$_var_26 = array('media' => curl_file_create($_var_30));
			}
			curl_setopt($_var_17, CURLOPT_URL, $_var_16);
			curl_setopt($_var_17, CURLOPT_POST, 1);
			curl_setopt($_var_17, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($_var_17, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($_var_17, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($_var_17, CURLOPT_POSTFIELDS, $_var_26);
			$_var_53 = @json_decode(curl_exec($_var_17), true);
			if (!is_array($_var_53)) {
				$_var_53 = array('media_id' => '');
			}
			curl_close($_var_17);
			return $_var_53['media_id'];
		}
		public function getQRByTicket($_var_20 = '')
		{
			global $_W;
			if (empty($_var_20)) {
				return false;
			}
			$_var_54 = pdo_fetchall('select * from ' . tablename('ewei_shop_poster_qr') . ' where ticket=:ticket and acid=:acid and type=4 limit 1', array(':ticket' => $_var_20, ':acid' => $_W['acid']));
			$_var_55 = count($_var_54);
			if ($_var_55 <= 0) {
				return false;
			}
			if ($_var_55 == 1) {
				return $_var_54[0];
			}
			return false;
		}
		public function checkMember($_var_0 = '')
		{
			global $_W;
			$_var_56 = WeiXinAccount::create($_W['acid']);
			$_var_57 = $_var_56->fansQueryInfo($_var_0);
			$_var_57['avatar'] = $_var_57['headimgurl'];
			load()->model('mc');
			$_var_58 = mc_openid2uid($_var_0);
			if (!empty($_var_58)) {
				pdo_update('mc_members', array('nickname' => $_var_57['nickname'], 'gender' => $_var_57['sex'], 'nationality' => $_var_57['country'], 'resideprovince' => $_var_57['province'], 'residecity' => $_var_57['city'], 'avatar' => $_var_57['headimgurl']), array('uid' => $_var_58));
			}
			pdo_update('mc_mapping_fans', array('nickname' => $_var_57['nickname']), array('uniacid' => $_W['uniacid'], 'openid' => $_var_0));
			$_var_59 = m('member');
			$_var_10 = $_var_59->getMember($_var_0);
			if (empty($_var_10)) {
				$_var_60 = mc_fetch($_var_58, array('realname', 'nickname', 'mobile', 'avatar', 'resideprovince', 'residecity', 'residedist'));
				$_var_10 = array('uniacid' => $_W['uniacid'], 'uid' => $_var_58, 'openid' => $_var_0, 'realname' => $_var_60['realname'], 'mobile' => $_var_60['mobile'], 'nickname' => !empty($_var_60['nickname']) ? $_var_60['nickname'] : $_var_57['nickname'], 'avatar' => !empty($_var_60['avatar']) ? $_var_60['avatar'] : $_var_57['avatar'], 'gender' => !empty($_var_60['gender']) ? $_var_60['gender'] : $_var_57['sex'], 'province' => !empty($_var_60['resideprovince']) ? $_var_60['resideprovince'] : $_var_57['province'], 'city' => !empty($_var_60['residecity']) ? $_var_60['residecity'] : $_var_57['city'], 'area' => $_var_60['residedist'], 'createtime' => time(), 'status' => 0);
				pdo_insert('ewei_shop_member', $_var_10);
				$_var_10['id'] = pdo_insertid();
				$_var_10['isnew'] = true;
			} else {
				$_var_10['nickname'] = $_var_57['nickname'];
				$_var_10['avatar'] = $_var_57['headimgurl'];
				$_var_10['province'] = $_var_57['province'];
				$_var_10['city'] = $_var_57['city'];
				pdo_update('ewei_shop_member', $_var_10, array('id' => $_var_10['id']));
				$_var_10['isnew'] = false;
			}
			return $_var_10;
		}
		function perms()
		{
			return array('poster' => array('text' => $this->getName(), 'isplugin' => true, 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log', 'log' => '扫描记录', 'clear' => '清除缓存-log', 'setdefault' => '设置默认海报-log'));
		}
	}
}