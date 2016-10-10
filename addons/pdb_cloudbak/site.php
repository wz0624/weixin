<?php
/**
 * 云备份模块微站定义
 *
 * @author PHP大巴
 * @url http://www.phpdb.net/
 */
defined('IN_IA') or exit('Access Denied');
require_once (__DIR__ . '/kuaipan/core.php');

class Pdb_cloudbakModuleSite extends Pdb_cloudbakModuleCore {

	public function doWebStart() {
		global $_W,$_GPC;
		
		$in_bak = $_GPC['in_bak'];
		if(checksubmit()){
			// print_r($_GPC);exit;
			$data = array();
			if (!$_GPC['name']){
				$name = date("Y-m-d H:i:s").'备份';
			}else{
				$name = trim($_GPC['name']);
			}
			$data['name'] = $name;
			$data['weid'] = trim($_W['uniacid']);
			$data['create_time'] = date("Y-m-d H:i:s");
			// print_r($data);exit;
			pdo_insert('pdb_cloudbak_record',$data);
			$id = pdo_insertid();
			
			//跳转到备份页：
			$url = $this->createWebUrl('start', array('id' => $id,'in_bak'=>'1'));
			// echo $id;exit;
			// $in_bak = 1;
			header('location:'.$url);
			exit;
			
		}
		$id = $_GPC['id'];
		if ($id){
			$sql = "select * from ".tablename('pdb_cloudbak_record').
					" where weid = '{$_W['uniacid']}' and id='{$id}' ";
			$record = pdo_fetch($sql);
			//print_r($record);exit;
			//echo $sql;exit;
		}
		// print_r($_GPC);exit;
		//开始备份：
		include $this->template('start');
	}
	public function doWebList() {
		global $_GPC, $_W;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		
		if ($operation == 'display') {
			//显示资源列表；
			$pindex = max(1, intval($_GPC['page']));
			$psize = 15;
			$condition = '';
			if (!empty($_GPC['keyword'])) {
				$condition .= " AND name LIKE '%{$_GPC['keyword']}%'";
			}
			$list = pdo_fetchall("SELECT * FROM " . tablename('pdb_cloudbak_record') . " WHERE weid = '{$_W['uniacid']}' $condition ORDER BY status DESC,id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('pdb_cloudbak_record') . " WHERE weid = '{$_W['uniacid']}' $condition");
			$pager = pagination($total, $pindex, $psize);
		} elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT id FROM " . tablename('pdb_cloudbak_record') . " WHERE id = :id", array(':id' => $id));
			if (empty($row)) {
				message('抱歉，记录不存在或是已经被删除！');
			}
			pdo_delete("pdb_cloudbak_record", array("id" => $id));
			message('删除成功！', $this->createWebUrl('list', array('op' => 'display')), 'success');
		} 
		include $this->template('list');
	}
	
	

}