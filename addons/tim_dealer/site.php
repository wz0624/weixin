<?php
/**
 * Timfan design模块处理程序
 *
 * @author Tim Fan
 * QQ:1026073477
 * @url http://i-fanr.com/
 */
defined('IN_IA') or exit('Access Denied');

class Tim_dealerModuleSite extends WeModuleSite {

	public function doMobileIndex() {
		global $_W,$_GPC;
		$uniacid =$_W['uniacid'];
		$info = pdo_fetchall("SELECT *  FROM ".tablename('micro_eventsetting')." WHERE uniacid=$uniacid");
		
		include $this->template('index');
	}
	
	public function doMobileCover1() { 
		global $_W,$_GPC;
		$uniacid =$_W['uniacid'];
		$ppt_list = pdo_fetchall("SELECT *  FROM ".tablename('tim_dealerppt')." WHERE uniacid=$uniacid");
		$setting = pdo_fetchall("SELECT *  FROM ".tablename('tim_dealersetting')." WHERE uniacid=$uniacid");
		
		
		include $this->template('index');
	}
	
	public function doMobileSearch() { 
		global $_W,$_GPC;
		$uniacid = $_W['uniacid'];
		$province = $_GPC['province'];
		$city = $_GPC['city'];
		$ppt_list = pdo_fetchall("SELECT *  FROM ".tablename('tim_dealerppt')." WHERE uniacid=$uniacid");
		$setting = pdo_fetchall("SELECT *  FROM ".tablename('tim_dealersetting')." WHERE uniacid=$uniacid");
		$result = pdo_fetchall("SELECT * FROM ".tablename('tim_dealer'). " WHERE uniacid=:uniacid and province=:province and city=:city",array(':uniacid' => $uniacid, ':province' => $province, ':city' => $city));
		include $this->template('result');
	}

	public function doWebDealer_manage() {
		global $_W,$_GPC;
		$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		load()->func('tpl');
		$member['reside'] = array(
    		'province' => $member['province'],
    		'city'     => $member['city'],
		);
		if('post' == $op){//添加或修改
			$id = intval($_GPC['id']);
			if(!empty($id)){
			$item = pdo_fetch("SELECT * FROM ".tablename('tim_dealer')." where id=$id");
			empty($item)?message('亲,数据不存在！', '', 'error'):"";	
			}
			
			if(checksubmit('submit')){
				empty ($_GPC["province"])?message('亲,地址不能为空'):$province=$_GPC['province'];
			$city =$_GPC['city'];
			$company=$_GPC['company'];
			$address =$_GPC['address'];
			$telephone =$_GPC['telephone'];
			$uniacid =$_W['uniacid'];
				$data = array(
					'id'=>$id,
					'uniacid'=>$uniacid,
					'company'=>$company,
					'province' =>$province,
					'city'=>$city,
					'address'=>$address,
					'telephone' =>$telephone
				);
				
				if(empty($id)){
						pdo_insert('tim_dealer', $data);//添加数据
						message('数据添加成功！', $this->createWebUrl('dealer_manage', array('op' => 'display')), 'success');
				}else{
						pdo_update('tim_dealer', $data, array('id' => $id));
						message('数据更新成功！', $this->createWebUrl('dealer_manage', array('op' => 'display')), 'success');
				}
				
				
			}else{
				include $this->template('dealer_manage');
			}
			
			
		}else if('del' == $op){//删除
		
			if(isset($_GPC['delete'])){
				$ids = implode(",",$_GPC['delete']);
				$sqls = "delete from  ".tablename('tim_dealer')."  where id in(".$ids.")"; 
				pdo_query($sqls);
				message('删除成功！', referer(), 'success');
			}
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT id FROM ".tablename('tim_dealer')." WHERE id = :id", array(':id' => $id));
			if (empty($row)) {
				//dump($_GPC);
				message('抱歉，数据不存在或是已经被删除！', $this->createWebUrl('dealer_manage', array('op' => 'display')), 'error');
			}
			pdo_delete('tim_dealer', array('id' => $id));
			message('删除成功！', referer(), 'success');
			
		}else if('display' == $op){//显示
			$pindex = max(1, intval($_GPC['page']));
			$psize =20;//每页显示
			$condition = '';
			if (!empty($_GPC['keyword'])) {
				$condition .= " and  picture LIKE '%".$_GPC['keyword']."%'  ";
			}
			$list = pdo_fetchall("SELECT *  FROM ".tablename('tim_dealer') ." $condition  ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','.$psize);//分页
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tim_dealer')." $condition" );
			$pager = pagination($total, $pindex, $psize);
			include $this->template('dealer_manage');
		}
	}

	public function doWebDealer_ppt() {
		global $_W,$_GPC;
		$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		load()->func('tpl');
		if('post' == $op){//添加或修改
			$id = intval($_GPC['id']);
			if(!empty($id)){
			$item = pdo_fetch("SELECT * FROM ".tablename('tim_dealerppt')." where id=$id");
			empty($item)?message('亲,数据不存在！', '', 'error'):"";	
			}
			if(checksubmit('submit')){
				empty ($_GPC['picture'])?message('亲,标题不能为空'):$picture=$_GPC['picture'];
			$pic_intro =$_GPC['pic_intro'];$url =$_GPC['url'];
			$uniacid =$_W['uniacid'];
				$data = array(
					'id'=>$id,
					'uniacid'=>$uniacid,
					'picture' =>$picture,
					'pic_intro'=>$pic_intro,
					'url'=>$url,
				);
				if(empty($id)){
						pdo_insert('tim_dealerppt', $data);//添加数据
						message('数据添加成功！', $this->createWebUrl('dealer_ppt', array('op' => 'display')), 'success');
				}else{
						pdo_update('tim_dealerppt', $data, array('id' => $id));
						message('数据更新成功！', $this->createWebUrl('dealer_ppt', array('op' => 'display')), 'success');
				}
				
			}else{
				include $this->template('dealer_ppt');
			}
		}else if('del' == $op){//删除
			if(isset($_GPC['delete'])){
				$ids = implode(",",$_GPC['delete']);
				$sqls = "delete from  ".tablename('tim_dealerppt')."  where id in(".$ids.")"; 
				pdo_query($sqls);
				message('删除成功！', referer(), 'success');
			}
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT id FROM ".tablename('tim_dealerppt')." WHERE id = :id", array(':id' => $id));
			if (empty($row)) {
				//dump($_GPC);
				message('抱歉，数据不存在或是已经被删除！', $this->createWebUrl('dealer_ppt', array('op' => 'display')), 'error');
			}
			pdo_delete('tim_dealerppt', array('id' => $id));
			message('删除成功！', referer(), 'success');
			
		}else if('display' == $op){//显示
			$pindex = max(1, intval($_GPC['page']));
			$psize =20;//每页显示
			$condition = '';
			if (!empty($_GPC['keyword'])) {
				$condition .= " and  picture LIKE '%".$_GPC['keyword']."%'  ";
			}
			$list = pdo_fetchall("SELECT *  FROM ".tablename('tim_dealerppt') ." $condition  ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','.$psize);//分页
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tim_dealerppt')." $condition" );
			$pager = pagination($total, $pindex, $psize);
			include $this->template('dealer_ppt');
		}
	}

	public function doWebDealer_param() { 
		global $_W,$_GPC;
		$uniacid = $_W['uniacid'];
		load()->func('tpl');
		$info = pdo_fetch("SELECT * FROM ".tablename('tim_dealersetting'). " WHERE uniacid = :uniacid", array(':uniacid' => $uniacid));
		if(checksubmit('submit')){ 
				$title = $_GPC['title'];
				$copyright = $_GPC['copyright'];
				$share_content = $_GPC['share_content'];
				$share_title = $_GPC['share_title'];
				$page_cover = $_GPC['page_cover'];
				$share_icon = $_GPC['share_icon'];
				$infos = array(
					'uniacid' => $uniacid,
					'title' => $title,
					'copyright' => $copyright,
					'page_cover' => $page_cover,
					'share_content' => $share_content, 
					'share_title' => $share_title, 
					'share_icon' => $share_icon
				);
				if(empty($info)){
						pdo_insert('tim_dealersetting', $infos);//添加数据
						message('数据添加成功！', $this->createWebUrl('dealer_param'), 'success');
				}else{
						$id = $info['id'];
						pdo_update('tim_dealersetting', $infos, array('id' => $id));
						message('数据更新成功！', $this->createWebUrl('dealer_param'), 'success');
				}	
		}
		include $this->template('dealer_param');
	}


}