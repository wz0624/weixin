<?php
/**
 * 集阅读模块
 *
 * @author Toddy
 */
defined ( 'IN_IA' ) or exit ( 'Access Denied' );
class Jy_readsModule extends WeModule {
	
	// ===============================================
	public $m = 'jy_reads';
	public $table_reply = 'jy_reads_reply';
	public $table_prize = 'jy_reads_prize';
	public $table_user = 'jy_reads_user';
	public $table_info = 'jy_reads_user_info';
	public $table_property = 'jy_reads_user_property';
	public $table_verifier = 'jy_reads_verifier';
	public $table_log = 'jy_reads_log';
	public $table_bonus = 'jy_reads_bonus';

	// ===============================================
	
	public function settingsDisplay($settings) {
		// 声明为全局才可以访问到.
		global $_W, $_GPC;

        if(checksubmit()) {

        	// 获取时间戳
        	$config = $settings['bonus'];
        	if($config['time']){
        		$dat['time'] = $config['time'];
        		$time = $config['time'];
        	}else{
        		$dat['time'] = time();
        		$time = $dat['time'];
        	}

            load()->func('file');
            $certdir = MODULE_ROOT . '/cert/' . $_W['uniacid'];
            mkdirs($certdir);

            $mark = true;
            if(!empty($_GPC['cert'])) {
                $ret = file_put_contents($certdir . '/' . MD5($time.'apiclient_cert') . '.pem', trim($_GPC['cert']));
                $mark = $mark && $ret;
            }
            if(!empty($_GPC['key'])) {
                $ret = file_put_contents($certdir . '/' . MD5($time.'apiclient_key') . '.pem' , trim($_GPC['key']));
                $mark = $mark && $ret;
            }
            if(!empty($_GPC['ca'])) {
                $ret = file_put_contents($certdir . '/' . MD5($time.'ca') . '.pem' , trim($_GPC['ca']));
                $mark = $mark && $ret;
            }
            if(!$mark) {
                message('证书保存失败, 请保证当前模块下 Cert 目录可写');
            }

            $dat['appid'] = trim($_GPC['appid']);
            $dat['appsecret'] = trim($_GPC['appsecret']);
            $dat['mchid'] = trim($_GPC['mchid']);
            $dat['mchkey'] = trim($_GPC['mchkey']);
            $dat['ip'] = trim($_GPC['ip']);
            $settings['bonus'] = $dat;
            if($this->saveSettings($settings)) {
                message('保存参数成功', 'refresh');
            }
        }
        // 获取当期公众号设置
		$sql = "SELECT * FROM ".tablename('uni_settings')." WHERE `uniacid`=:uniacid";
		$unisetting  =  pdo_fetch($sql,array(':uniacid'=>$_W['uniacid']));

		// 获取粉丝公众号ID
		if(!empty($unisetting['oauth'])) {
			$temp = unserialize($unisetting['oauth']);
			$weid = empty($temp['account']) ? $_W['uniacid'] : $temp['account'];
		} else {
			$weid = $_W['uniacid'];
		}

		$sql = "SELECT * FROM ".tablename('account_wechats')." WHERE `uniacid`=:uniacid";
		$oauthsetting  =  pdo_fetch($sql,array(':uniacid'=>$weid));

		// 获取粉丝公众号ID
        $config = $settings['bonus'];
        if(empty($config['ip'])) {
            $config['ip'] = $_SERVER['SERVER_ADDR'];
        }
        if(empty($config['appid'])) {
            $config['appid'] = $oauthsetting['key'];
        }
        if(empty($config['appsecret'])) {
            $config['appsecret'] = $oauthsetting['secret'];
        }
		include $this->template ( 'web/setting' );
		// unset( $unisetting );
		// unset( $oauthsetting );

	}

	public function fieldsFormDisplay($rid = 0) {
		// 要嵌入规则编辑页的自定义内容，这里 $rid 为对应的规则编号，新增时为 0
		global $_W;
		if ($rid == 0) {
			$reply = array (
					'name' => '集阅读',
					'share_title' => '集阅读开始啦！',
					'share_description' => '收集阅读，有机会获红包哟！',
					'share_thumb' => MODULE_URL . 'public/mobile/images/default.jpg',
					'loading' => MODULE_URL . 'public/mobile/images/loading.png',
					'arrow' => MODULE_URL . 'public/mobile/images/index_arrow.png',
					'top' => MODULE_URL . 'public/mobile/images/top.jpg',
					'bgcolor' => '#33cccc',
					'starttime' => time (),
					'endtime' => time () + 10 * 84400,
					'status' => 1,
					'tips' => '这是活动tips。',
					'share' => 0,
					'alias' => '阅读',
					'start' => MODULE_URL . 'public/mobile/images/start.png'
			);
		} else {
			$reply = pdo_fetch ( "SELECT * FROM " . tablename ( $this->table_reply ) . " WHERE rid = :rid ORDER BY `id` DESC", array (
					':rid' => $rid 
			) );
			$prizes = pdo_fetchall ( "SELECT * FROM " . tablename ( $this->table_prize ) . " WHERE rid = :rid and status=:status ORDER BY `displayorder` desc,`id` DESC", array (
					':rid' => $rid,
					':status' => 1 
			) );
		}
		$properties = pdo_fetchall('SELECT * FROM '.tablename($this->table_property));
		
		load ()->func ( 'tpl' );
		include $this->template ( 'web/form' );
	}
	public function fieldsFormValidate($rid = 0) {
		// 规则编辑保存时，要进行的数据验证，返回空串表示验证无误，返回其他字符串将呈现为错误提示。这里 $rid 为对应的规则编号，新增时为 0
		return '';
	}
	public function fieldsFormSubmit($rid) {
		// 规则验证无误保存入库时执行，这里应该进行自定义字段的保存。这里 $rid 为对应的规则编号
		global $_GPC, $_W;
		$data = array (
				'status' => intval ( $_GPC ['status'] ),
				'rid' => $rid,
				'name' => $_GPC ['activity'],
				'uniacid' => $_W ['uniacid'],
				'share_title' => $_GPC ['share_title'],
				'share_thumb' => $_GPC ['share_thumb'],
				'share_description' => $_GPC ['share_description'],
				'link' => $_GPC ['link'],
				'loading' => $_GPC ['loading'],
				'arrow' => $_GPC ['arrow'],
				'top' => $_GPC ['top'],
				'bottom' => $_GPC ['bottom'],
				'telephone' => $_GPC ['telephone'],
				'bgcolor' => $_GPC ['bgcolor'],
				'content' => htmlspecialchars_decode ( $_GPC ['content'] ),
				'rule' => htmlspecialchars_decode ( $_GPC ['rule'] ),
				'tips' => $_GPC ['tips'],
				'ad' => $_GPC ['ad'],
				'ad_url' => $_GPC ['ad_url'],
				'starttime' => strtotime ( $_GPC ['time'] [start] ),
				'endtime' => strtotime ( $_GPC ['time'] [end] ),
				'follow' => intval ( $_GPC ['follow'] ),
				'mutual' => intval ( $_GPC ['mutual'] ),
				'location' => intval ( $_GPC ['location'] ),
				'area' => $_GPC ['area'],
				'copyright' => $_GPC ['copyright'] ,
				'share' => $_GPC['rshare'],
				'alias' => $_GPC['alias'],
				'start' => $_GPC['start']
		);
		$replyid = intval($_GPC['replyid']);
		if (empty ( $replyid )) {
			pdo_insert ( $this->table_reply, $data );
			$replyid = pdo_insertid ();
		} else {
			pdo_update ( $this->table_reply, $data, array (
					'id' => $replyid 
			) );
		}
		if ($_GPC ['nprizename']) {
			foreach ( $_GPC ['nprizename'] as $k => $v ) {
				$info = empty($_GPC ['nproperty'] [$k])?'':json_encode($_GPC ['nproperty'] [$k]);
				$data = array (
						'rid' => $rid,
						'uniacid' => $_W ['uniacid'],
						'replyid' => $replyid,
						'prizename' => $_GPC ['nprizename'] [$k],
						'prizeurl' => $_GPC ['nprizeurl'] [$k],
						'prizethumb' => $_GPC ['nprizethumb'] [$k],
						'prizecount' => intval ( $_GPC ['nprizecount'] [$k] ),
						'prizerest' => intval ( $_GPC ['nprizerest'] [$k] ),
						'prizeneed' => intval ( $_GPC ['nprizeneed'] [$k] ),
						'displayorder' => intval ( $_GPC ['ndisplayorder'] [$k] ) ,
						'info' => $info,
						'share' => $_GPC['nshare'][$k],
				);
				pdo_insert ( $this->table_prize, $data );
			}
		}
		if ($_GPC ['prizename']) {
			foreach ( $_GPC ['prizename'] as $k => $v ) {
				$info = empty($_GPC ['property'] [$k])?'':json_encode($_GPC ['property'] [$k]);
				$data = array (
						'prizename' => $_GPC ['prizename'] [$k],
						'prizeurl' => $_GPC ['prizeurl'] [$k],
						'prizethumb' => $_GPC ['prizethumb'] [$k],
						'prizecount' => intval ( $_GPC ['prizecount'] [$k] ),
						'prizerest' => intval ( $_GPC ['prizerest'] [$k] ),
						'prizeneed' => intval ( $_GPC ['prizeneed'] [$k] ),
						'displayorder' => intval ( $_GPC ['displayorder'] [$k] ) ,
						'info' => $info,
						'share' => $_GPC['share'][$k],
				);
				pdo_update ( $this->table_prize, $data, array (
						'id' => $k 
				) );
			}
		}
	}
	public function ruleDeleted($rid) {
		// 删除规则时调用，这里 $rid 为对应的规则编号
		pdo_delete ( $this->table_reply, array (
				'rid' => $rid 
		) );
		pdo_update ( $this->table_prize, array (
				'status' => 0 
		), array (
				'rid' => $rid 
		) );
	}
}
