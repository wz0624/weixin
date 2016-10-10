<?php
/**
 * 捷讯派红包模块定义
 *
 * @author 捷讯设计
 * @url http://bbs.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');

class J_pocketmoneyModule extends WeModule {
	public function fieldsFormDisplay($rid = 0) {
		global $_W, $_GPC;
		
		$reply = pdo_fetch("SELECT * FROM ".tablename('j_pocketmoney_reply')." WHERE rid = :rid", array(':rid' => $rid));
		load()->func('tpl');
		$list = pdo_fetchall("SELECT * FROM ".tablename('j_pocketmoney_ad')." WHERE weid = :weid", array(':weid' => $_W['uniacid']));
		$adlist=@explode(',',$reply['adlist']);
		
		//------------
		$credit=pdo_fetchcolumn("SELECT creditnames FROM ".tablename('uni_settings')." WHERE uniacid = :weid", array(':weid' => $_W['uniacid']));
		$creditlist=iunserializer($credit);
		$creditary=array();
		foreach($creditlist as $key=>$val){
			if($val['enabled']){
				$creditary[]=array(
					'name'=>$key,
					'title'=>$val['title'],
				);
			}
		}
		//------------
		$groupslist=pdo_fetchall("SELECT * FROM ".tablename('mc_groups')." WHERE uniacid = :weid order by groupid asc", array(':weid' => $_W['uniacid']));
		
		include $this->template('form');
	}

	public function fieldsFormValidate($rid = 0) {
		//规则编辑保存时，要进行的数据验证，返回空串表示验证无误，返回其他字符串将呈现为错误提示。这里 $rid 为对应的规则编号，新增时为 0
		return '';
	}

	public function fieldsFormSubmit($rid) {
		global $_W, $_GPC;
		$reid = intval($_GPC['reply_id']);
		load()->func('file');
		$dir_url=IA_ROOT . '/addons/j_pocketmoney/cert_2/'.$rid;
		mkdirs($dir_url);
		$data = array(
			'rid' => $rid,
			'weid' => $_W['uniacid'],
			'cover' => $_GPC['cover'],
			'title' => $_GPC['title'],
			'description' => $_GPC['description'],
			'info' => htmlspecialchars_decode($_GPC['info']),
			'arealimit' => trim($_GPC['arealimit']),
			'gzurl' => trim($_GPC['gzurl']),
			'smurl' => trim($_GPC['smurl']),
			'neednums' => intval($_GPC['neednums']),
			'firstmin' => intval($_GPC['firstmin']),
			'firstmax' => intval($_GPC['firstmax']),
			'starttime' => strtotime($_GPC['starttime']),
			'endtime' => strtotime($_GPC['endtime']),
			'appid' => trim($_GPC['appid']),
			'secret' => trim($_GPC['secret']),
			'mchid' => trim($_GPC['mchid']),
			'signkey' => trim($_GPC['signkey']),
			'ip' => trim($_GPC['ip']),
			'pageback' => trim($_GPC['pageback']),
			'shareimg' => trim($_GPC['shareimg']),
			'packremark' => trim($_GPC['packremark']) ? trim($_GPC['packremark']) :"关注我们，好事早知道",
			
			'secondmin' => intval($_GPC['secondmin']),
			'secondmax' => intval($_GPC['secondmax']),
			'gametype' => intval($_GPC['gametype']),
			'totalfee' =>intval($_GPC['totalfee']),
			'remainfee' => intval($_GPC['remainfee']),
			'showfee' => intval($_GPC['showfee']),
			'maxnums' => intval($_GPC['maxnums']) ? intval($_GPC['maxnums']) : 1,
			'sharetofriend' => intval($_GPC['sharetofriend']),
			'sharetotimeline' => intval($_GPC['sharetotimeline']),
			'adlist' => implode(',',$_GPC['adlist']),
			'comefrom' => trim($_GPC['comefrom']),
			'credittype' => trim($_GPC['credittype']),
			'credit' => intval($_GPC['credit']),
			'groupid' => intval($_GPC['groupid']),
		);
		if ($_FILES["rootca"]["name"]){
			if(file_exists($dir_url."/rootca.pem"))@unlink ($dir_url."/rootca.pem");
			move_uploaded_file($_FILES["rootca"]["tmp_name"],$dir_url."/rootca.pem");
			$data['rootca']=1;
		}
		if ($_FILES["apiclient_cert"]["name"]){
			if(file_exists($dir_url."/apiclient_cert.pem"))@unlink ($dir_url."/apiclient_cert.pem");
			move_uploaded_file($_FILES["apiclient_cert"]["tmp_name"],$dir_url."/apiclient_cert.pem");
			$data['apiclient_cert']=1;
		}
		if ($_FILES["apiclient_key"]["name"]){
			if(file_exists($dir_url."/apiclient_key.pem"))@unlink ($dir_url."/apiclient_key.pem");
			move_uploaded_file($_FILES["apiclient_key"]["tmp_name"],$dir_url."/apiclient_key.pem");
			$data['apiclient_key']=1;
		}
		if($data['neednums']==0){
			$data['neednums']=1;
		}
		if($data['firstmin']==0){
			$data['firstmin']=1;
		}
		if($data['firstmax']<2){
			$data['firstmax']=2;
		}
		if($data['firstmin']>=$data['firstmax']){
			$data['firstmax']=$data['firstmin']+1;
		}
		
		if($data['secondmin']==0){
			$data['secondmin']=1;
		}
		if($data['secondmax']<2){
			$data['secondmax']=2;
		}
		if($data['secondmin']>=$data['secondmax']){
			$data['secondmax']=$data['secondmin']+1;
		}
		if (empty($reid)) {
			$data['remainfee']=$data['totalfee'];
			pdo_insert('j_pocketmoney_reply', $data);
		} else {
			pdo_update('j_pocketmoney_reply', $data, array('id' => $reid));
		}
	}

	public function ruleDeleted($rid) {
		global $_GPC, $_W;
		pdo_delete('j_pocketmoney_reply', array('rid' => $rid));
		pdo_delete('j_pocketmoney_records', array('rid' => $rid));
		pdo_delete('j_pocketmoney_fans', array('rid' => $rid));
		load()->func('file');
		$dir_url=IA_ROOT . '/addons/j_pocketmoney/cert_2/'.$rid;
		rmdirs($dir_url,false);
		return true;
	}

	public function settingsDisplay($settings) {
		global $_GPC, $_W;
        if (checksubmit()) {
            $cfg = array(
                'share_img' => intval($_GPC['share_img']),
				'show_money' => intval($_GPC['show_money']),
				'get_msg' => trim($_GPC['get_msg']),
				
				'key_word' => $_GPC['key_word'],
				'key_kouhao' => str_replace(array("\r\n", "\r", "\n"), "|$|",trim($_GPC['key_kouhao'])),
				'key_wordtime' => intval($_GPC['key_wordtime']),
            );
            if ($this->saveSettings($cfg)) {
                message('保存成功', 'refresh');
            }
        }
		load()->func('tpl');
		include $this->template('setting');
	}

}