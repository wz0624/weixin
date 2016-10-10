<?php
/**
 * 捷讯求缘分模块定义
 *
 * @author 捷讯设计
 * @url http://bbs.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');

class J_shakeluckyModule extends WeModule {
	public $tablename = 'j_shakelucky_reply';
	public function fieldsFormDisplay($rid = 0) {
		//要嵌入规则编辑页的自定义内容，这里 $rid 为对应的规则编号，新增时为 0
		global $_W;
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->tablename)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			$list = pdo_fetchall("SELECT * FROM ".tablename('j_shakelucky_award')." WHERE rid = '".$rid."' order by id asc");
		}else{
			$reply=array(
				'starttime'=>strtotime(date("Y-m-d H:i")),
				'endtime'=>strtotime(date("Y-m-d H:i")),
			);
		}
		$mune_category=pdo_fetchcolumn("select count(*) from ".tablename('modules_bindings')." where module='j_activity'");
		if($mune_category){
			$actList = pdo_fetchall("SELECT * FROM ".tablename('j_activity_reply')." WHERE weid = '{$_W['uniacid']}' order by id desc");
		}
		load()->func('tpl');
		include $this->template('form');
	}

	public function fieldsFormValidate($rid = 0) {
		//规则编辑保存时，要进行的数据验证，返回空串表示验证无误，返回其他字符串将呈现为错误提示。这里 $rid 为对应的规则编号，新增时为 0
		return true;
	}

	public function fieldsFormSubmit($rid) {
		//规则验证无误保存入库时执行，这里应该进行自定义字段的保存。这里 $rid 为对应的规则编号
		global $_GPC, $_W;
		$id = intval($_GPC['reply_id']);
		$insert = array(
			'rid' => $rid,
			'weid'=> $_W['uniacid'],
			'picture' => $_GPC['picture'],
			'qrcode' => $_GPC['qrcode'],
			'clientpic' => $_GPC['clientpic'],
			'title' => $_GPC['title'],
			'fid' => intval($_GPC['fid']),
			'fstatus' => intval($_GPC['fstatus']),
			'fattend' => intval($_GPC['fattend']),
			'description' => $_GPC['description'],
			'rule' => htmlspecialchars_decode($_GPC['rule']),
			'content' => htmlspecialchars_decode($_GPC['content']),
			'gamecontent' => htmlspecialchars_decode($_GPC['gamecontent']),
			'quota' => intval($_GPC['quota']),
			'starttime' => strtotime($_GPC['acttime']['start']),
			'endtime' => strtotime($_GPC['acttime']['end']),
			'ruletype'=>intval($_GPC['ruletype']),
			'status'=>intval($_GPC['status']),
			'maxlottery'=>intval($_GPC['maxlottery']),
			'sharehelp'=>intval($_GPC['sharehelp']),
			'onlyone'=>intval($_GPC['onlyone']),
			'titleimg' => $_GPC['titleimg'],
			'bodycolor' => $_GPC['bodycolor'],
			'gamestarttime' => intval($_GPC['gamestarttime_h']).":".$_GPC['gamestarttime_m'],
			'gameendtime' => intval($_GPC['gameendtime_h']).":".$_GPC['gameendtime_m'],
			'code' => $_GPC['code'],
		);
		if (empty($id)) {
			$insert['status']=1;
			pdo_insert($this->tablename, $insert);
		} else {
			pdo_update($this->tablename, $insert, array('id' => $id));
		}
		
		foreach ($_GPC['award-title-new'] as $index => $row) {
			$data = array(
				'rid' => $rid,
				'title' => $_GPC['award-title-new'][$index],
				'isprize' => isset($_GPC['award-isprize-new'][$index]) ? 1:0,
				'total' => intval($_GPC['award-total-new'][$index]),
				'remain' => intval($_GPC['award-total-new'][$index]),
				'sponsor' => $_GPC['award-sponsor-new'][$index],
				'probalilty' => $_GPC['award-probalilty-new'][$index],
				'othernum' => intval($_GPC['award-othernum-new'][$index]),
				'absolute' => isset($_GPC['award-absolute-new'][$index]) ? 1:0,
			);
			pdo_insert('j_shakelucky_award', $data);
		}
		foreach ($_GPC['award-title'] as $index => $row) {
			$data = array(
				'title' => $_GPC['award-title'][$index],
				'isprize' => isset($_GPC['award-isprize'][$index]) ? 1:0,
				'total' => intval($_GPC['award-total'][$index]),
				'remain' => intval($_GPC['award-remain'][$index]),
				'probalilty' => $_GPC['award-probalilty'][$index],
				'sponsor' => $_GPC['award-sponsor'][$index],
				'othernum' => intval($_GPC['award-othernum'][$index]),
				
				'absolute' => isset($_GPC['award-absolute'][$index]) ? 1:0,
			);
			pdo_update('j_shakelucky_award', $data, array('id' => $index));
		}
	}

	public function ruleDeleted($rid) {
		global $_W;
		$replies = pdo_fetchall("SELECT id, picture,qrcode,clientpic FROM ".tablename($this->tablename)." WHERE rid = '$rid'");
		$deleteid = array();
		load()->func('file');
		if (!empty($replies)) {
			foreach ($replies as $index => $row) {
				file_delete($row['picture']);
				file_delete($row['qrcode']);
				file_delete($row['clientpic']);
				file_delete($row['titleimg']);
				$deleteid[] = $row['id'];
				pdo_delete("j_shakelucky_winner",array('aid'=>$row['id']));
				pdo_delete("j_shakelucky_award",array('rid'=>$rid));
			}
		}
		pdo_delete($this->tablename, "id IN ('".implode("','", $deleteid)."')");
		return true;
	}
	
	public function settingsDisplay($settings) {
        global $_GPC, $_W;
        if (checksubmit()) {
            $cfg = array(
                'need_info' => intval($_GPC['need_info']),
				'auto_appid' => $_GPC['auto_appid'],
				'auto_appsecret' => $_GPC['auto_appsecret'],
				'is_sharehelp' => intval($_GPC['is_sharehelp']),
				
            );
            if ($this->saveSettings($cfg)) {
                message('保存成功', 'refresh');
            }
        }
		load()->func('tpl');
		include $this->template('setting');
    }

}