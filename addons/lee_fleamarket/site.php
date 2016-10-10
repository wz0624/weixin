<?php
/**
 */
defined('IN_IA') or exit('Access Denied');

class Lee_FleamarketModuleSite extends WeModuleSite {
	public $goods = 'lee_fleamarket_goods';
	
	public function getHomeTiles() {

		global $_W;

		$urls = array();

		$list = pdo_fetchall("SELECT name, id FROM ".tablename('rule')." WHERE uniacid = '{$_W['uniacid']}' AND module = 'lee_fleamarket'");

		if (!empty($list)) {

			foreach ($list as $row) {

				$urls[] = array('title'=>$row['name'], 'url'=> $this->createMobileUrl('list', array('rid' => $row['id'])));

			}

		}

		return $urls;

	}
	public function doMobileAdd(){
		global $_W,$_GPC;
		$categorys = pdo_fetchall("SELECT * FROM".tablename('lee_fleamarket_category')."WHERE uniacid='{$_W['uniacid']}'");
		$data = array(
			'uniacid'        => $_W['uniacid'],
			'openid'      => $_W['fans']['from_user'],
			'title'       => $_GPC['title'],
			'rolex'       => $_GPC['rolex'],
			'price'       => $_GPC['price'],
			'realname'    => $_GPC['realname'],
			'sex'         => $_GPC['sex'],
			'mobile'      => $_GPC['mobile'],
			'description' => $_GPC['description'],
			'thumb1' 	  => $_GPC['thumb1'],
			'thumb2' 	  => $_GPC['thumb3'],
			'thumb3' 	  => $_GPC['thumb3'],
			'thumb4' 	  => $_GPC['thumb4'],
			'createtime'  => TIMESTAMP,
			'pcate'       => $_GPC['pcate'],
			'status'      => 0,
		);
		if (!empty($_GPC['id'])) {
			$good = pdo_fetch("SELECT * FROM".tablename($this->goods)."WHERE id='{$_GPC['id']}'");
		}
		if ($_W['ispost']) {
			if (empty($_GPC['id'])) {
				pdo_insert($this->goods,$data);
				message('发布成功',$this->createMobileUrl('list'),'success');
			}else{
				pdo_update($this->goods,$data,array('id' => $_GPC['id']));
				message('更新成功',$this->createMobileUrl('list'),'success');
			}

		}
		
		load()->func('tpl');
		include $this->template('add');
	}
	public function doMobileList(){	
		global $_GPC,$_W;
		//必须关注
		$this->checkAuth();
		//必须关注
		$pcate = intval($_GPC['pcate']);
		//分类显示
		$categorys = pdo_fetchall("SELECT * FROM".tablename('lee_fleamarket_category')."WHERE uniacid='{$_W['uniacid']}' AND enabled='1'");
		//分享数据
		$rid = intval($_GPC['rid']);
		//if (!empty($rid)) {
		$reply = pdo_fetch("SELECT * FROM ".tablename('lee_fleamarket_reply')." WHERE rid = :rid ", array(':rid' => $rid));
		$sharepic = $_W['attachurl'].$reply['picture'];
		$description = $reply['description'];
		$title = $reply['title'];
		//}
		if(!empty($_GPC['keyword'])){
			$keyword = "%{$_GPC['keyword']}%";
			$condition = " AND title LIKE '{$keyword}'";
		}
		$st = '';
		if (!empty($this->module['config']['status'])) {
			$st = " AND status='1' ";
			
		}
		if(empty($pcate)) {
			$list = pdo_fetchall("SELECT * FROM ".tablename($this->goods)." WHERE uniacid='{$_W['uniacid']}' $st $condition");
			
		}else{
			$list = pdo_fetchall("SELECT * FROM".tablename($this->goods)."WHERE uniacid='{$_W['uniacid']}' AND pcate='{$pcate}' $st $condition");
		}
		$keyword = $_GPC['keyword'];
		include $this->template('list');
	}
	public function doMobileDetail(){
		global $_W,$_GPC;
		$id = intval($_GPC['id']);
		$detail = pdo_fetch("SELECT * FROM".tablename($this->goods)."WHERE id='{$id}'");
		//print_r($detail);
		$title = $detail['title'];
		$_share_img = $_W['attachurl'].$detail['thumb1'];
		include $this->template('detail');
	}
	public function doMobileMygoods(){
		global $_W,$_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize  = 10;
		$list = pdo_fetchall("SELECT * FROM".tablename($this->goods)."WHERE openid='{$_W['fans']['from_user']}' LIMIT ".($pindex - 1) * $psize.','.$psize);
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM".tablename($this->goods)."WHERE openid='{$_W['fans']['from_user']}'");
		$pager  = pagination($total, $pindex, $psize);
		if ($_GPC['op'] == 'delete') {
			pdo_delete($this->goods,array('id' => $_GPC['id']));
			message('删除成功',$this->createMobileUrl('mygoods', array('weid' => $_W['weid'])),'success');
		}	
		include $this->template('mygoods');
	}
	public function doWebGoods(){
		global $_GPC,$_W;
		$item = pdo_fetchall("SELECT * FROM".tablename($this->goods)."WHERE uniacid='{$_W['uniacid']}'");
		$goods = array();
		foreach ($item as $key => $value) {
			$category = pdo_fetch("SELECT * FROM".tablename('lee_fleamarket_category')."WHERE id='{$value['pcate']}'");
			$goods[] = array(
					'id' => $value['id'],
					'title' => $vlaue['title'],
					'rolex' => $value['rolex'],
					'pcate' => $value['pcate'],
					'price' => $value['price'],
					'realname' => $value['realname'],
					'sex' => $value['sex'],
					'mobile' => $value['mobile'],
					'name' => $category['name'],
					'createtime' => $value['createtime'],
					'status' => $value['status'],
					'uniacid' => $value['uniacid'],
				);
		}
		if ($_GPC['foo'] == 'delete') {
			pdo_delete($this->goods,array('id' => $_GPC['id']));
			message('删除成功',referer(),'success');
		}
		if ($_GPC['foo'] == 'update') {
			//echo $_GPC['id'].$_GPC['status'];exit;
			pdo_query("UPDATE ".tablename('lee_fleamarket_goods')." SET status='{$_GPC['status']}' WHERE id='{$_GPC['id']}'");
			message('更新成功',referer(),'success');
		}
		load()->func('tpl');
		include $this->template('goods');
	}
	//分类
	public function doWebCategory(){
		global $_GPC,$_W;
		$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		$id = intval($_GPC['id']);
		if ($op == 'post') {
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM".tablename('lee_fleamarket_category')."WHERE id='{$id}'");
			}
			if ($_W['ispost']) {
				$data = array(
					'uniacid'    => $_W['uniacid'],
					'name'    => $_GPC['name'],
					'classid'    => $_GPC['classid'],					
					'enabled' => $_GPC['enabled'],
					);
				if (empty($id)) {
					pdo_insert('lee_fleamarket_category',$data);
				}else{
					//print_r($data);exit;
					pdo_update('lee_fleamarket_category',$data,array('id' => $id));
				}
				message('更新成功',referer(),'success');
			}
		}elseif($op == 'display'){
			$row = pdo_fetchall("SELECT * FROM".tablename('lee_fleamarket_category')."WHERE uniacid='{$_W['uniacid']}'");
		}
		if(checksubmit('delete')){
			pdo_delete('lee_fleamarket_category', " id  IN  ('".implode("','", $_GPC['select'])."')");
			message('删除成功',referer(),'success');
		}
		load()->func('tpl');
		include $this->template('category');
	}
	 private function checkAuth() {
        global $_W;
        checkauth();
    }
}
