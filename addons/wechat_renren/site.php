<?php
defined('IN_IA') or exit('Access Denied');

class Wechat_renrenModuleSite extends WeModuleSite {

	public function doMobileFm() {
	global $_W,$_GPC;
	$renrenshopurl = $_W['siteroot']."addons/wechat_renren/";
	$topimg =tomedia($this->module['config']['img']);
	$ptitle = $this->module['config']['title'];
	$desc = $this->module['config']['desc'];
	$uuu = $this->module['config']['uuu'];
	$kefuq1 = $this->module['config']['kefuq1'];
	$kefuq2 = $this->module['config']['kefuq2'];
	$kefuq3 = $this->module['config']['kefuq3'];
	$kefutel = $this->module['config']['kefutel'];
	$weid = $_W['uniacid'];
	$pindex = max(1, intval($_GPC['page']));
			$psize =10;//每页面10条
			$condition = '';
			if (!empty($_GPC['keyword'])) {
	$condition .= " AND (title LIKE '%".$_GPC['keyword']."%' "." OR jianjie LIKE '%".$_GPC['keyword']."%') ";
			}
			$list = pdo_fetchall("SELECT *  FROM ".tablename('wechat_renren')." WHERE isok=1 and weid =".$weid.$condition." ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','.$psize);//分页
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('wechat_renren') . " WHERE isok=1  and  weid =".$weid.$condition);
			$pager = pagination($total, $pindex, $psize,$url = '', $context = array('before' =>0, 'after' =>0));
		$sql = "SELECT *  FROM ".tablename('wechat_renren')." WHERE isok=1 and weid =".$weid.$condition;
			$top = pdo_fetchall("SELECT *  FROM ".tablename('wechat_rentop')." WHERE isok=1 and weid =".$weid.$condition." ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','.$psize);//分页
			$topal = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('wechat_rentop') . " WHERE isok=1  and  weid =".$weid.$condition);
			$paper = pagination($total, $pindex, $psize,$url = '', $context = array('before' =>0, 'after' =>0));
		$sql = "SELECT *  FROM ".tablename('wechat_rentop')." WHERE isok=1 and weid =".$weid.$condition;
		include $this->template('index');	
	}	
	public function doWebRenrenshop() {
		global $_W,$_GPC;
		if(defined('SAE')){
	load()->func('filesae');
	}else{
	load()->func('file');
	}
		load()->func('tpl');
		$renrenshopurl = $_W['siteroot']."addons/wechat_renren/";
		$weid = $_W['uniacid'];
		$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		
		if('post' == $op){//添加或修改
			$id = intval($_GPC['id']);
			if(!empty($id)){
			$item = pdo_fetch("SELECT * FROM ".tablename('wechat_renren')." where id=$id");
			empty($item)?message('亲,数据不存在！', '', 'error'):"";	
			}
			
			
			if(checksubmit('submit')){
				empty ($_GPC['title'])?message('亲,名称必填'):$title =$_GPC['title'];
				empty ($_GPC['money'])?message('亲,价格必填'):$money =$_GPC['money'];
				empty ($_GPC['url'])?message('亲,链接必填'):$url =$_GPC['url'];
				$jianjie = $_GPC['jianjie'];
				$logo = $_GPC['logo'];
				$isok =1;
				if(empty($id)){
						pdo_insert('wechat_renren', array('title'=>$title,'money'=>$money,'url'=>$url,'jianjie'=>$jianjie,'logo'=>$logo,'isok'=>$isok,'weid'=>$weid));//添加数据
						message('添加成功！', $this->createWebUrl('renrenshop', array('op' => 'display')), 'success');
				}else{
						pdo_update('wechat_renren', array('title'=>$title,'money'=>$money,'url'=>$url,'jianjie'=>$jianjie,'logo'=>$logo,'isok'=>$isok,'weid'=>$weid), array('id' => $id));
						message('更新成功！', $this->createWebUrl('renrenshop', array('op' => 'display')), 'success');
				}
				
				
			}else{
				include $this->template('index');
			}
			
		}else if('del' == $op){//删除
		
		
			if(isset($_GPC['delete'])){
				$ids = implode(",",$_GPC['delete']);
				
				$row1 = pdo_fetchall("SELECT id,logo FROM ".tablename('wechat_renren')." WHERE id in(".$ids.")");
				if(!empty($row1)){
					foreach($row1 as $data1){
					if (!empty($data1['logo'])) {
			file_delete($data1['logo']);
		}	
					}
				}
				$sqls = "delete from  ".tablename('wechat_renren')."  where id in(".$ids.")"; 
				pdo_query($sqls);
				message('删除成功！', referer(), 'success');
			}
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT id FROM ".tablename('wechat_renren')." WHERE id = :id", array(':id' => $id));
			if (empty($row)) {
				message('抱歉，操作不存在或是已经被删除！', $this->createWebUrl('renrenshop', array('op' => 'display')), 'error');
			}
				if (!empty($row['logo'])) {
			file_delete($row['logo']);
		}
			pdo_delete('wechat_renren', array('id' => $id));
			message('删除成功！', referer(), 'success');
			
		}else if('display' == $op){//显示
			$pindex = max(1, intval($_GPC['page']));
			$psize =20;//每页显示
			$condition = '';
			if (!empty($_GPC['keyword'])) {
				$condition .= " AND (title LIKE '%".$_GPC['keyword']."%' "." OR jianjie LIKE '%".$_GPC['keyword']."%') ";
			}			
			$list = pdo_fetchall("SELECT *  FROM ".tablename('wechat_renren') ." WHERE weid =". $weid.$condition."  ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','.$psize);//分页
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('wechat_renren') ." WHERE weid =". $weid.$condition);
			$pager = pagination($total, $pindex, $psize);
			include $this->template('index');
		}else if('shenhe'==$op){
			
				$id = intval($_GPC['id']);
			$issend =( intval($_GPC['isok'])==1)?0:1;
			$data1 = array('isok'=>$issend,);
			pdo_update('wechat_renren', $data1, array('id' => $id));
			if($issend==1){
				echo json_encode(array('a'=>1));
			}else{
				echo json_encode(array('a'=>0));
			}
			
		}
	}
	public function doWebRenrentop() {
		global $_W,$_GPC;
		if(defined('SAE')){
	load()->func('filesae');
	}else{
	load()->func('file');
	}
		load()->func('tpl');
		$Renrentopurl = $_W['siteroot']."addons/wechat_renren/";
		$weid = $_W['uniacid'];
		$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		
		if('post' == $op){//添加或修改
			$id = intval($_GPC['id']);
			if(!empty($id)){
			$item = pdo_fetch("SELECT * FROM ".tablename('wechat_rentop')." where id=$id");
			empty($item)?message('亲,数据不存在！', '', 'error'):"";	
			}
			
			
			if(checksubmit('submit')){
				empty ($_GPC['title'])?message('亲,名称必填'):$title =$_GPC['title'];
				empty ($_GPC['money'])?message('亲,价格必填'):$money =$_GPC['money'];
				empty ($_GPC['sale'])?message('亲,特价必填'):$sale =$_GPC['sale'];
				empty ($_GPC['url'])?message('亲,链接必填'):$url =$_GPC['url'];
				$jianjie = $_GPC['jianjie'];
				$logo = $_GPC['logo'];
				$isok =1;
				if(empty($id)){
						pdo_insert('wechat_rentop', array('title'=>$title,'money'=>$money,'sale'=>$sale,'url'=>$url,'jianjie'=>$jianjie,'logo'=>$logo,'isok'=>$isok,'weid'=>$weid));//添加数据
						message('添加成功！', $this->createWebUrl('Renrentop', array('op' => 'display')), 'success');
				}else{
						pdo_update('wechat_rentop', array('title'=>$title,'money'=>$money,'sale'=>$sale,'url'=>$url,'jianjie'=>$jianjie,'logo'=>$logo,'isok'=>$isok,'weid'=>$weid), array('id' => $id));
						message('更新成功！', $this->createWebUrl('Renrentop', array('op' => 'display')), 'success');
				}
				
				
			}else{
				include $this->template('top');
			}
			
		}else if('del' == $op){//删除
		
		
			if(isset($_GPC['delete'])){
				$ids = implode(",",$_GPC['delete']);
				
				$row1 = pdo_fetchall("SELECT id,logo FROM ".tablename('wechat_rentop')." WHERE id in(".$ids.")");
				if(!empty($row1)){
					foreach($row1 as $data1){
					if (!empty($data1['logo'])) {
			file_delete($data1['logo']);
		}	
					}
				}
				$sqls = "delete from  ".tablename('wechat_rentop')."  where id in(".$ids.")"; 
				pdo_query($sqls);
				message('删除成功！', referer(), 'success');
			}
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT id FROM ".tablename('wechat_rentop')." WHERE id = :id", array(':id' => $id));
			if (empty($row)) {
				message('抱歉，操作不存在或是已经被删除！', $this->createWebUrl('Renrentop', array('op' => 'display')), 'error');
			}
				if (!empty($row['logo'])) {
			file_delete($row['logo']);
		}
			pdo_delete('wechat_rentop', array('id' => $id));
			message('删除成功！', referer(), 'success');
			
		}else if('display' == $op){//显示
			$pindex = max(1, intval($_GPC['page']));
			$psize =20;//每页显示
			$condition = '';
			if (!empty($_GPC['keyword'])) {
				$condition .= " AND (title LIKE '%".$_GPC['keyword']."%' "." OR jianjie LIKE '%".$_GPC['keyword']."%') ";
			}			
			$top = pdo_fetchall("SELECT *  FROM ".tablename('wechat_rentop') ." WHERE weid =". $weid.$condition."  ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','.$psize);//分页
			$topal = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('wechat_rentop') ." WHERE weid =". $weid.$condition);
			$paper = pagination($total, $pindex, $psize);
			include $this->template('top');
		}else if('shenhe'==$op){
			
				$id = intval($_GPC['id']);
			$issend =( intval($_GPC['isok'])==1)?0:1;
			$data1 = array('isok'=>$issend,);
			pdo_update('wechat_rentop', $data1, array('id' => $id));
			if($issend==1){
				echo json_encode(array('a'=>1));
			}else{
				echo json_encode(array('a'=>0));
			}
			
		}
	}
	public function doWebRenrenmob() {
		//这个操作被定义用来呈现 功能封面
		global $_GPC, $_W;
		include $this->template('mob');
	}
	public function doMobileDh() {

header("location:".$_W['siteroot'].$this->createMobileUrl('fm'));
	}

public function doMobileFabu() {
	global $_W,$_GPC;
	if(defined('SAE')){
	load()->func('filesae');
	}else{
	load()->func('file');
	}
	$renrenshopurl = $_W['siteroot']."addons/wechat_renren/";
	$weid = $_W['uniacid'];
	if(checksubmit('submit')){
	empty ($_GPC['title'])?message('亲,名称必填'):$title =$_GPC['title'];
				empty ($_GPC['url'])?message('亲,链接必填'):$url =$_GPC['url'];
				$des = $_GPC['des'];
				$isok =0;
			if (!empty($_FILES['pic']['tmp_name'])) {
					$upload = file_upload($_FILES['pic'],'image');
					if (is_error($upload)) {
						message('上传出错', '', 'error');
					}
					$img = $upload['url'];
				}

	$data = array(
	'weid'=>$weid,
	'title'=>$title,
	'url'=>$url,
	'jianjie'=>$des,
	'logo'=>$img,
	'isok'=>$isok,
	);
	 pdo_insert('wechat_renren', $data);
	die('<script>alert("申请成功,请等待审核!");location.href="'.$this->createMobileUrl('fm').'"</script>');
	}else{
	include $this->template('fabu');		
	}
	}

}
