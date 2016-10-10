<?php
defined('IN_IA') or exit('Access Denied');

require_once IA_ROOT . "/addons/" . maketeam_readmanage . "/dbutil.class.php";

class Maketeam_readmanageModule extends WeModule {
public $tablename_basic = 'basic_reply';//文字回复
public $tablename_news = 'news_reply';//图文回复
public $tablename_group = 'mc_groups';//会员等级
	private $replies = array();
	public function fieldsFormDisplay($rid = 0) {
		//要嵌入规则编辑页的自定义内容，这里 $rid 为对应的规则编号，新增时为 0
		global $_W;
		load()->func('tpl');
		if (!empty($rid) && $rid > 0) {
			$isexists = pdo_fetch("SELECT id FROM ".tablename('rule')." WHERE id = :id", array(':id' => $rid));
		}
		if(!empty($isexists)) {
			//让已经设置的值在后台显示
			$resdata = pdo_fetch("SELECT * FROM ".tablename('maketeam_readmanage')." WHERE rid = :rid ORDER BY `id`", array(':rid' => $rid));
			if($resdata['respond_type'] == '1'){
				$replies = pdo_fetchall("SELECT * FROM ".tablename($this->tablename_basic)." WHERE rid = :rid ORDER BY `id`", array(':rid' => $rid));
			}elseif($resdata['respond_type'] == '2'){
				$replies = pdo_fetchall("SELECT * FROM ".tablename($this->tablename_news)." WHERE rid = :rid ORDER BY `displayorder` DESC", array(':rid' => $rid));
				foreach($replies as &$reply) {
					if(!empty($reply['thumb'])) {
						$reply['src'] = tomedia($reply['thumb']);
					}
				}
			}
		}
		load()->model('mc');
		$groups = mc_groups($_W['uniacid']);
		$selected_groups = explode(',',$resdata['order_level']);
		include $this->template('form');
	}

	public function fieldsFormValidate($rid = 0) {
		global $_GPC;
		if($_GPC['respond_type']=='1'){
			$this->replies = @json_decode(htmlspecialchars_decode($_GPC['replies']), true);
			if(empty($this->replies)) {
				return '必须填写有效的文字回复内容.';
			}
		}elseif($_GPC['respond_type']=='2'){
			if(empty($_GPC['titles'])) {
				return '必须填写有效的图文标题.';
			}
			foreach($_GPC['titles'] as $k => $v) {
				$row = array();
				if(empty($v)) {
					continue;
				}
				$row['title'] = $v;
				$row['id'] = $_GPC['id'][$k];
				$row['author'] = $_GPC['authors'][$k];
				$row['displayorder'] = $_GPC['displayorder'][$k];
				$row['thumb'] = $_GPC['thumbs'][$k];
				$row['description'] = $_GPC['descriptions'][$k];
				$row['content'] = $_GPC['contents'][$k];
//				$row['url'] = $_GPC['urls'][$k];//图文原始链接
				$row['incontent'] = intval($_GPC['incontent'][$k]);
				$row['createtime'] = time();
				$this->replies[] = $row;
			}
			if(empty($this->replies)) {
				return '必须填写有效的回复内容.';
			}
			foreach($this->replies as &$r) {
				if(trim($r['title']) == '') {
					return '必须填写有效的标题.';
				}
				if (trim($r['author']) == '') {
					return '必须填写有效的作者名称.';
				}
				if (trim($r['thumb']) == '') {
					return '必须填写有效的封面链接地址.';
				}
				if (trim($r['description']) == '') {
					return '必须填写有效的图文描述.';
				}
				$r['content'] = htmlspecialchars_decode($r['content']);
			}
		}
		if($_GPC['read_type']=='1'){
			if(empty($_GPC['order_count'])) {
				return '必须填写会员积分.';
			}
		}elseif($_GPC['read_type']=='2'){
		if(empty($_GPC['order_level'])) {
				return '必须选择会员等级.';
			}
		}
		return '';
	}

	public function fieldsFormSubmit($rid = 0) {
		//规则验证无误保存入库时执行，这里应该进行自定义字段的保存。这里 $rid 为对应的规则编号
		global $_GPC, $_W;
		$sid = $_GPC['sid'];
		//会员等级
		$o_level = $_GPC['order_level'];
		$order_level = '';
		foreach( $o_level as $key => $var )
		{
			$order_level .= $var.',';
		}
		if($_GPC['read_type'] == '1'){//积分模式
			$data = array(
					'rid' => $rid,
					'uniacid' => $_W['account']['uniacid'],
					'read_type' => $_GPC['read_type'],
					'respond_type' => $_GPC['respond_type'],
					'order_count' => $_GPC['order_count'],
					'order_level' => '',
					'order_money' => $_GPC['order_money'],
					'norule' => $_GPC['norule'],
					'follow_url' => $_GPC['follow_url']
			);
		}elseif($_GPC['read_type'] == '2'){
		$data = array(
				'rid' => $rid,
				'uniacid' => $_W['account']['uniacid'],
				'read_type' => $_GPC['read_type'],
				'respond_type' => $_GPC['respond_type'],
				'order_count' => '',
				'order_level' => $order_level,
				'order_money' => $_GPC['order_money'],
				'norule' => $_GPC['norule'],
				'follow_url' => $_GPC['follow_url']
		);}
		if (empty($sid)) {
			DBUtil::create(DBUtil::$TABLE_READMANAGE, $data);
			$sid = pdo_insertid();
		} else {
			DBUtil::updateById(DBUtil::$TABLE_READMANAGE, $data, $sid);
		}
		if($_GPC['respond_type'] == '1'){//文字回复类型,将回复存储到基础表
			$sql = 'DELETE FROM '. tablename($this->tablename_basic) . ' WHERE `rid`=:rid';
			$pars = array();
			$pars[':rid'] = $rid;
			pdo_query($sql, $pars);
			foreach($this->replies as $reply) {
				pdo_insert($this->tablename_basic, array('rid' => $rid, 'content' => $reply['content']));
			}
		}elseif($_GPC['respond_type'] == '2'){//图文回复类型,将回复存储到基础表
			$sql = 'SELECT `id` FROM ' . tablename($this->tablename_news) . " WHERE `rid` = :rid";
			$replies = pdo_fetchall($sql, array(':rid' => $rid), 'id');
			$replyids = array_keys($replies);
			foreach($this->replies as $reply) {
				if (in_array($reply['id'], $replyids)) {
					pdo_update($this->tablename_news, $reply, array('id' => $reply['id']));
				} else {
					$reply['rid'] = $rid;
					pdo_insert($this->tablename_news, $reply);
				}
				unset($replies[$reply['id']]);
			}
			if (!empty($replies)) {
				$replies = array_keys($replies);
				$replies = implode(',', $replies);
				$sql = 'DELETE FROM '. tablename($this->tablename_news) . " WHERE `id` IN ({$replies})";
				pdo_query($sql);
			}
		}
		return true;
	}

	public function ruleDeleted($rid = 0) {
		//删除规则时调用，这里 $rid 为对应的规则编号
		pdo_delete($this->tablename_basic, array('rid' => $rid));
		pdo_delete($this->tablename_news, array('rid' => $rid));
	}

	public function settingsDisplay($settings) {
		global $_W, $_GPC;
		//点击模块设置时将调用此方法呈现模块设置页面，$settings 为模块设置参数, 结构为数组。这个参数系统针对不同公众账号独立保存。
		//在此呈现页面中自行处理post请求并保存设置参数（通过使用$this->saveSettings()来实现）
		if(checksubmit()) {
			//字段验证, 并获得正确的数据$dat
			$this->saveSettings($dat);
		}
		//这里来展示设置项表单
		include $this->template('setting');
	}

}