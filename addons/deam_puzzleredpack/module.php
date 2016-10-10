<?php
/**
 * 【点沐】拼图红包模块定义
 *
 * @author deam
 * @url http://bbs.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');
define('DM_ROOT', IA_ROOT . '/addons/deam_puzzleredpack');
class Deam_puzzleredpackModule extends WeModule {
	public function fieldsFormDisplay($rid = 0) {
		global $_W;
		load()->func('tpl');
		if (!empty($rid)) {
			$activity = pdo_fetch("SELECT * FROM " . tablename('deam_puzzleredpack_report') . " WHERE rid = :rid AND uniacid = :uniacid ORDER BY `id` DESC", array(':rid' => $rid , ':uniacid' =>$_W['uniacid']));
		}
		$getact = pdo_fetchall("SELECT * FROM " . tablename('deam_puzzleredpack_packetset') . " WHERE uniacid = :uniacid ORDER BY `id` DESC", array(':uniacid' => $_W['uniacid']));
		
		include $this->template('form');
	}

	public function fieldsFormValidate($rid = 0) {
		//规则编辑保存时，要进行的数据验证，返回空串表示验证无误，返回其他字符串将呈现为错误提示。这里 $rid 为对应的规则编号，新增时为 0
		return '';
	}

	public function fieldsFormSubmit($rid) {
		global $_GPC,$_W;
		$id = intval($_GPC['reply_id']);
		$actid = intval($_GPC['actid']);
		$record['rid'] = $rid;
		$record['actid'] = $actid;
		$record['uniacid'] = $_W['uniacid'];
		$record['title'] = $_GPC['actitle'];
		$record['image'] = $_GPC['img'];
		if (empty($id)) {
			$id = pdo_insert('deam_puzzleredpack_report', $record);
		}else{
			pdo_update('deam_puzzleredpack_report', $record, array('id' => $id));
		}
	}

	public function ruleDeleted($rid) {
		//删除规则时调用，这里 $rid 为对应的规则编号
	}

	public function settingsDisplay($settings) {
		global $_W, $_GPC;
		if(checksubmit()) {
			load()->func('file');
            $r = mkdirs(DM_ROOT . '/tmpdata/cert/'.$_W['uniacid']);
			if(!empty($_GPC['cert'])) {
                $ret = file_put_contents(DM_ROOT.'/tmpdata/cert/'.$_W['uniacid'].'/apiclient_cert.pem', trim($_GPC['cert']));
                $r = $r && $ret;
            }
            if(!empty($_GPC['key'])) {
                $ret = file_put_contents(DM_ROOT.'/tmpdata/cert/'.$_W['uniacid'].'/apiclient_key.pem', trim($_GPC['key']));
                $r = $r && $ret;
            }
            if(!empty($_GPC['rootca'])) {
                $ret = file_put_contents(DM_ROOT.'/tmpdata/cert/'.$_W['uniacid'].'/rootca.pem', trim($_GPC['rootca']));
                $r = $r && $ret;
            }
			if(!$r) {
                message('证书保存失败, 请保证 /addons/deam_puzzleredpack/tmpdata/cert 目录可写');
            }			
			$settings['appid'] = $_GPC['appid'];
			$settings['mch_id'] = $_GPC['mch_id'];
			$settings['getip'] = $_GPC['getip'];
			$settings['partnerkey'] = $_GPC['partnerkey'];
			if($this->saveSettings($settings)){
				message('保存成功', 'refresh');
			}
			
		}
		$fileCert = file_exists(DM_ROOT.'/tmpdata/cert/'.$_W['uniacid'].'/apiclient_cert.pem');
		$fileKey = file_exists(DM_ROOT.'/tmpdata/cert/'.$_W['uniacid'].'/apiclient_key.pem');
		$fileRootca = file_exists(DM_ROOT.'/tmpdata/cert/'.$_W['uniacid'].'/rootca.pem');
		
		include $this->template('setting');
	}

}