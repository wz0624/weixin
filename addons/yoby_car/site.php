<?php
/**
 * 拼车一族模块微站定义
 *
 * @author Yoby
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Yoby_carModuleSite extends WeModuleSite {

	public function doMobileFm() {//乘客或车主报名
		global $_W,$_GPC;
		$yobyurl = $_W['siteroot']."addons/yoby_car/";
		$weid = $_W['uniacid'];
		$openid = $_W['openid'];
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('yoby_car')."  where weid=$weid ");
		if(checksubmit('submit')){
 			empty ($_GPC['num'])?message('亲,人数不能为空'):$num = $_GPC['num'];
			empty ($_GPC['title'])?message('亲,称呼不能为空'):$title =$_GPC['title'];
			empty ($_GPC['createtime'])?message('亲,乘车时间不能为空'):$createtime = $_GPC['createtime'];
			empty ($_GPC['address1'])?message('亲,出发地址不能为空'):$address1 = $_GPC['address1'];
			empty ($_GPC['address2'])?message('亲,终点地址不能为空'):$address2 =$_GPC['address2'];
			$type = $_GPC['type'];
			$beizhu = $_GPC['beizhu'];
			
				$data = array(
	'weid'=>$weid,
	'num'=>$num,
	'type'=>$type,
	'title'=>$title,
	'createtime'=>strtotime($createtime),
	'address'=>$address1.', '.$address2,
	'beizhu'=>$beizhu,
	'openid'=>$openid,
	);
	
		if(!empty($openid)){
		pdo_insert('yoby_car',$data);//添加数据
		die('<script>alert("报名成功");location.href="'.$this->createMobileUrl('fm').'";</script>');
	}else{
		die('<script>alert("报名失败,请关注后从微信端进入!");location.href="'.$this->createMobileUrl('fm').'";</script>');
	}
			
		}else{
		include $this->template('baoming');	
		}
		
	}

	public function doMobileBaoming1() {
		global $_W,$_GPC;
			load()->func('file');
		$yobyurl = $_W['siteroot']."addons/yoby_car/";
		$weid = $_W['uniacid'];
		$openid = $_W['openid'];
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('yoby_car_zanzhu')."  where weid=$weid ");
		if(checksubmit('submit')){
				
				
 			
			empty ($_GPC['title'])?message('赞助商简介不能为空'):$title =$_GPC['title'];
			
			$url = $_GPC['url'];
				if (!empty($_FILES['logo']['tmp_name'])) {
					$upload = file_upload($_FILES['logo'],'image');
					if (is_error($upload)) {
						message('上传出错', '', 'error');
					}
					
				}
				$data = array(
	'weid'=>$weid,
	'num'=>0,
	'title'=>$title,
	'createtime'=>time(),
	'url'=>$url,
	'logo'=>$upload['path'],
	'openid'=>$openid,
	'isok'=>0,
	);
	
		if(!empty($openid)){
		pdo_insert('yoby_car_zanzhu',$data);//添加数据
		die('<script>alert("赞助商报名成功,管理员审核通过后才能显示");location.href="'.$this->createMobileUrl('baoming1').'";</script>');
	}else{
		die('<script>alert("报名失败,请关注后从微信端进入!");location.href="'.$this->createMobileUrl('baoming1').'";</script>');
	}
			
		}else{
		include $this->template('baoming1');	
		}
		
	}

	public function doMobileCar1() {//车主展示
		global $_W,$_GPC;
		$yobyurl = $_W['siteroot']."addons/yoby_car/";
		$weid = $_W['uniacid'];
		//$openid = $_W['openid'];
		$today = strtotime(date('Y-m-d'));//今天日期
		
		$pindex = max(1, intval($_GPC['page']));
			$psize =10;//每页显示
			$condition =" and type=1 and  createtime>= $today  ";
			
			$list = pdo_fetchall("SELECT * FROM ".tablename('yoby_car')." where weid=$weid  $condition ORDER   BY  createtime  DESC LIMIT ".($pindex - 1) * $psize.','.$psize);//分页
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('yoby_car')."  where weid=$weid ".$condition);
			$pager = pagination($total, $pindex, $psize);
		include $this->template('car1');
		
		}

	public function doMobileCar2() {//乘客展示
		global $_W,$_GPC;
		$yobyurl = $_W['siteroot']."addons/yoby_car/";
		$weid = $_W['uniacid'];
		//$openid = $_W['openid'];
		$today = strtotime(date('Y-m-d'));//今天日期
		
		$pindex = max(1, intval($_GPC['page']));
			$psize =10;//每页显示
			$condition =" and type=0 and  createtime>= $today  ";
			
			$list = pdo_fetchall("SELECT * FROM ".tablename('yoby_car')." where weid=$weid  $condition ORDER   BY  createtime  DESC LIMIT ".($pindex - 1) * $psize.','.$psize);//分页
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('yoby_car')."  where weid=$weid ".$condition);
			$pager = pagination($total, $pindex, $psize);
		include $this->template('car2');
		
		}

	public function doMobileCar3() {//赞助商展示
		global $_W,$_GPC;
		$yobyurl = $_W['siteroot']."addons/yoby_car/";
		$weid = $_W['uniacid'];
		//$openid = $_W['openid'];
		$today = strtotime(date('Y-m-d'));//今天日期
		
		$pindex = max(1, intval($_GPC['page']));
			$psize =5;//每页显示
			$condition ="   and isok=1   ";
			
			$list = pdo_fetchall("SELECT * FROM ".tablename('yoby_car_zanzhu')." where weid=$weid  $condition ORDER   BY  createtime  DESC LIMIT ".($pindex - 1) * $psize.','.$psize);//分页
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('yoby_car_zanzhu')."  where weid=$weid ".$condition);
			$pager = pagination($total, $pindex, $psize);
		include $this->template('car3');
		
		}
	public function doMobileView() {//详情信息
		global $_W,$_GPC;
		$yobyurl = $_W['siteroot']."addons/yoby_car/";
		$weid = $_W['uniacid'];
		$id = intval($_GPC['id']);
			if(!empty($id)){
			$item = pdo_fetch("SELECT * FROM ".tablename('yoby_car')." where id=$id");
			empty($item)?message('亲,数据不存在！', '', 'error'):"";	
			}
		include $this->template('view');
		
		}
	public function doMobileView1() {//详情信息
		global $_W,$_GPC;
		$yobyurl = $_W['siteroot']."addons/yoby_car/";
		$weid = $_W['uniacid'];
		$id = intval($_GPC['id']);
			if(!empty($id)){
			$item = pdo_fetch("SELECT * FROM ".tablename('yoby_car_zanzhu')." where id=$id");
			empty($item)?message('亲,数据不存在！', '', 'error'):"";	
			}
		include $this->template('view1');
		
		}
	public function doMobileSo() {//详情信息
		global $_W,$_GPC;
		$yobyurl = $_W['siteroot']."addons/yoby_car/";
		$weid = $_W['uniacid'];
		
		if(checksubmit('submit')){
	$pindex = max(1, intval($_GPC['page']));
			$psize =10;//每页显示
			$today = strtotime(date('Y-m-d'));
			
			$riqi =strtotime($_GPC['riqi']);
			$keyword =$_GPC['keyword'];
			
			if(!empty($riqi) && !empty($keyword)){
				$condition ="and createtime>= $today  and   (createtime  ={$riqi}  and  title like '%{$keyword}%'  or  address  like '%{$keyword}%')  ";
			}elseif(empty($riqi) && !empty($keyword)){
				$condition ="and createtime>= $today  and   (title like '%{$keyword}%'  or  address  like '%{$keyword}%')  ";
			}elseif(!empty($riqi) && empty($keyword)){
				$condition ="and createtime>= $today  and   createtime  ={$riqi} ";
			}elseif(empty($riqi) && empty($keyword)){
				$condition ="and createtime>= $today  ";
			}
			
			$list = pdo_fetchall("SELECT * FROM ".tablename('yoby_car')." where weid=$weid  $condition ORDER BY createtime DESC LIMIT ".($pindex - 1) * $psize.','.$psize);//分页
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('yoby_car')."  where weid=$weid ".$condition);
			$pager = pagination($total, $pindex, $psize);
		
include $this->template('so');
}else{
	include $this->template('so');
}
		
		}		
	public function doMobileSend() {//已发消息
		global $_W,$_GPC;
		$yobyurl = $_W['siteroot']."addons/yoby_car/";
		$weid = $_W['uniacid'];
		$openid = $_W['openid'];
		//$today = strtotime(date('Y-m-d'));//今天日期
		
		$pindex = max(1, intval($_GPC['page']));
			$psize =10;//每页显示
			$condition =" and openid='$openid'   ";
			
			$list = pdo_fetchall("SELECT * FROM ".tablename('yoby_car')." where weid=$weid  $condition ORDER   BY  createtime  DESC LIMIT ".($pindex - 1) * $psize.','.$psize);//分页
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('yoby_car')."  where weid=$weid ".$condition);
			$pager = pagination($total, $pindex, $psize);
		include $this->template('send');
		
		}		
public function doMobileDelsend(){//删除一条消息
	global $_W,$_GPC;
	$id = intval($_GPC['id']);
	pdo_delete('yoby_car', array('id' => $id));
	message('删除成功！', $this->createMobileUrl('send'), 'success');
}

	public function doMobileSaysend() {
		global $_W,$_GPC;
		$yobyurl = $_W['siteroot']."addons/yoby_car/";
		$weid = $_W['uniacid'];
		$openid = $_W['openid'];
		$to_openid = $_GPC["to_openid"];
		if(checksubmit('submit')){
				
				
 			
			empty ($_GPC['content'])?message('留言不能为空'):$content =$_GPC['content'];
			
				$data = array(
	'weid'=>$weid,
	'to_openid'=>$to_openid,
	'content'=>$content,
	'from_openid'=>$openid,
	'createtime'=>time(),
	);
	
		if(!empty($openid)){
		pdo_insert('yoby_car_say',$data);//添加数据
		die('<script>alert("留言成功");location.href="'.$this->createMobileUrl('car1').'";</script>');
	}else{
		die('<script>alert("留言失败,请关注后从微信端进入!");location.href="'.$this->createMobileUrl('car1').'";</script>');
	}
			
		}else{
		include $this->template('saysend');	
		}
		
	}
	public function doMobileSay() {//已发消息
		global $_W,$_GPC;
		$yobyurl = $_W['siteroot']."addons/yoby_car/";
		$weid = $_W['uniacid'];
		$openid = $_W['openid'];
		//$today = strtotime(date('Y-m-d'));//今天日期
		
		$pindex = max(1, intval($_GPC['page']));
			$psize =10;//每页显示
			$condition =" and to_openid='$openid'   ";
			
			$list = pdo_fetchall("SELECT * FROM ".tablename('yoby_car_say')." where weid=$weid  $condition ORDER   BY  createtime  DESC LIMIT ".($pindex - 1) * $psize.','.$psize);//分页
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('yoby_car_say')."  where weid=$weid ".$condition);
			$pager = pagination($total, $pindex, $psize);
		include $this->template('say');
		
		}		
public function doMobileDelsay(){//删除一条消息
	global $_W,$_GPC;
	$id = intval($_GPC['id']);
	pdo_delete('yoby_car_say', array('id' => $id));
	message('删除成功！', $this->createMobileUrl('say'), 'success');
}	
	public function doMobileView2() {//详情信息
		global $_W,$_GPC;
		$yobyurl = $_W['siteroot']."addons/yoby_car/";
		$weid = $_W['uniacid'];
		$id = intval($_GPC['id']);
			if(!empty($id)){
			$item = pdo_fetch("SELECT * FROM ".tablename('yoby_car_say')." where id=$id");
			empty($item)?message('亲,数据不存在！', '', 'error'):"";	
			}
		include $this->template('view2');
		
		}							
	public function doWebBaoming() {
		global $_W,$_GPC;
		$yobyurl = $_W['siteroot']."addons/yoby_car/";
		$weid = $_W['uniacid'];
		$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if('del' == $op){
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT id FROM ".tablename('yoby_car')." WHERE id = :id", array(':id' => $id));
			if (empty($row)) {
				message('抱歉，数据不存在或是已经被删除！', $this->createWebUrl('baoming', array('op' => 'display')), 'error');
			}
			pdo_delete('yoby_car', array('id' => $id));
			message('删除成功！', referer(), 'success');	
		}elseif('display' == $op){
				$pindex = max(1, intval($_GPC['page']));
			$psize =20;
			$condition = '';
			if (!empty($_GPC['keyword'])) {
				$condition .= " AND (title LIKE '%".$_GPC['keyword']."%' "." OR  address  LIKE '%".$_GPC['keyword']."%' "." OR beizhu LIKE '%".$_GPC['keyword']."%' )  ";
			}
			
			$list = pdo_fetchall("SELECT * FROM ".tablename('yoby_car')." WHERE weid = '{$weid}' $condition ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','.$psize);//分页
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('yoby_car') . " WHERE weid = '{$weid}'");
			$pager = pagination($total, $pindex, $psize);
		
			include $this->template('baoming');
		}
	}
	public function doWebLiuyan() {
		global $_W,$_GPC;
		$yobyurl = $_W['siteroot']."addons/yoby_car/";
		$weid = $_W['uniacid'];
		$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if('del' == $op){
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT id FROM ".tablename('yoby_car_say')." WHERE id = :id", array(':id' => $id));
			if (empty($row)) {
				message('抱歉，数据不存在或是已经被删除！', $this->createWebUrl('liuyan', array('op' => 'display')), 'error');
			}
			pdo_delete('yoby_car_say', array('id' => $id));
			message('删除成功！', referer(), 'success');	
		}elseif('display' == $op){
				$pindex = max(1, intval($_GPC['page']));
			$psize =20;
			$condition = '';
			if (!empty($_GPC['keyword'])) {
				$condition .= " AND content LIKE '%".$_GPC['keyword']."%'    ";
			}
			
			$list = pdo_fetchall("SELECT * FROM ".tablename('yoby_car_say')." WHERE weid = '{$weid}' $condition ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','.$psize);//分页
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('yoby_car_say') . " WHERE weid = '{$weid}'");
			$pager = pagination($total, $pindex, $psize);
		
			include $this->template('say');
		}
	}
	public function doWebHuodong() {
		global $_W,$_GPC;
		$yobyurl = $_W['siteroot']."addons/yoby_car/";
		$weid = $_W['uniacid'];
		$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if('post' == $op){
			$id = intval($_GPC['id']);
			if(!empty($id)){
			$item = pdo_fetch("SELECT * FROM ".tablename('yoby_car_huodong')." where id=$id");
			empty($item)?message('亲,数据不存在！', '', 'error'):"";	
			}
			
			if(checksubmit('submit')){
				empty ($_GPC['title'])?message('亲,标题不能为空'):$title =$_GPC['title'];
				empty ($_GPC['url'])?message('亲,网址不能为空'):$url =$_GPC['url'];
				empty ($_GPC['orderby'])?$orderby = 0 : $orderby =$_GPC['orderby'];
				$data = array(
					'weid'=>$weid,
					'title' =>$title,
					'url' =>$url,
					'createtime'=>time(),
					'orderby' =>$orderby,
					
				);
				if(empty($id)){
						pdo_insert('yoby_car_huodong', $data);//添加数据
						message('数据添加成功！', $this->createWebUrl('huodong', array('op' => 'display')), 'success');
				}else{
						pdo_update('yoby_car_huodong', $data, array('id' => $id));
						message('数据更新成功！', $this->createWebUrl('huodong', array('op' => 'display')), 'success');
				}		
			}else{
			include $this->template('huodong');	
			}	
		}elseif('del' == $op){
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT id FROM ".tablename('yoby_car_huodong')." WHERE id = :id", array(':id' => $id));
			if (empty($row)) {
				message('抱歉，数据不存在或是已经被删除！', $this->createWebUrl('huodong', array('op' => 'display')), 'error');
			}
			pdo_delete('yoby_car_huodong', array('id' => $id));
			message('删除成功！', referer(), 'success');	
		}elseif('display' == $op){
			$list = pdo_fetchall("SELECT *  FROM  ".tablename('yoby_car_huodong') ." where weid={$weid} order by orderby desc  limit 4");
			include $this->template('huodong');
		}
	}
	public function doWebZanzhu() {
		global $_W,$_GPC;
		$yobyurl = $_W['siteroot']."addons/yoby_car/";
		$weid = $_W['uniacid'];
		$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if('del' == $op){
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT id FROM ".tablename('yoby_car_zanzhu')." WHERE id = :id", array(':id' => $id));
			if (empty($row)) {
				message('抱歉，数据不存在或是已经被删除！', $this->createWebUrl('liuyan', array('op' => 'display')), 'error');
			}
			pdo_delete('yoby_car_zanzhu', array('id' => $id));
			message('删除成功！', referer(), 'success');	
		}elseif('display' == $op){
				$pindex = max(1, intval($_GPC['page']));
			$psize =20;
			$condition = '';
			if (!empty($_GPC['keyword'])) {
				$condition .= " AND title LIKE '%".$_GPC['keyword']."%'    ";
			}
			
			$list = pdo_fetchall("SELECT * FROM ".tablename('yoby_car_zanzhu')." WHERE weid = '{$weid}' $condition ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','.$psize);//分页
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('yoby_car_zanzhu') . " WHERE weid = '{$weid}'");
			$pager = pagination($total, $pindex, $psize);
		
			include $this->template('zanzhu');
		}else if('shenhe'==$op){
			
				$id = intval($_GPC['id']);
			$issend =( intval($_GPC['isok'])==1)?0:1;
			$data1 = array('isok'=>$issend,);
			pdo_update('yoby_car_zanzhu', $data1, array('id' => $id));
			if($issend==1){
				echo json_encode(array('a'=>1));
			}else{
				echo json_encode(array('a'=>0));
			}
			
		}
	}

}