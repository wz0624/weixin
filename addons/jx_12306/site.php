<?php
/**
 * 12306验证码模块微站定义
 *
 * @author QQ1500158347
 * @url http://www.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');
define('RES', '../addons/jx_12306/template/');
class Jx_12306ModuleSite extends WeModuleSite {
	public function doWebsetting() {
		global $_W,$_GPC;
		$weid = $_W['uniacid'];
		load()->func('tpl');
		$subject = pdo_fetch("SELECT * FROM ".tablename(jx_12306_setting)." WHERE weid = '{$weid}' ORDER BY id DESC LIMIT 1");

	/* 	$item['photo']=empty($item['photo'])?'./addons/jx_12306/template/mobile/b.gif':$item['photo']; */
		$item['jx_12306_title']=empty($item['jx_12306_title'])?'想回家过年？先打败12306再说！':$item['jx_12306_title'];		
		$item['share_desc']=empty($item['share_desc'])?'还想PK验证码？看看我如何轻松拿到票~':$item['share_desc'];		
		$item['share_title']=empty($item['share_title'])?'又一个抢票神器出现啦！安全保障、稳稳哒~':$item['share_title'];		
		$item['jx_12306_url']=empty($item['jx_12306_url'])?'http://www.jiaowx.com':$item['jx_12306_url'];		

		if (checksubmit()) {
			$data = array(
				'jx_12306_title' => $_GPC['jx_12306_title'],
				'jx_12306_url' => $_GPC['jx_12306_url'],
				'share_desc' => $_GPC['share_desc'],
				'share_title' => $_GPC['share_title'],
				'photo' => $_GPC['photo'],
				'photoss' => $_GPC['photoss']
			);
			if(empty($subject)){
				$data['weid'] = $weid;
				pdo_insert(jx_12306_setting, $data);	
			}else{
              pdo_update(jx_12306_setting, $data, array('weid' => $weid));
			}
            message('欧了！欧了！更新完毕！', referer(), 'success');
		}
		if (!$subject['photo']){
			$subject['photo'] = "../addons/jx_12306/template/mobile/b.jpg";
		}

		if (!$subject['photoss']){
			$subject['photoss'] = "../addons/jx_12306/template/mobile/images/top_11bfa29.png";
		}
		include $this->template('setting');
	}
	public function doMobileLink() {
		//这个操作被定义用来呈现 功能封面
		global $_W, $_GPC;
		load()->func('tpl');
			$sql="SELECT * FROM ".tablename(jx_12306_setting)." WHERE weid = '{$_W['uniacid']}'";
			$arr = pdo_fetchall($sql);
			$jx_12306_title=$arr['0']['jx_12306_title'];
			$jx_12306_url=$arr['0']['jx_12306_url'];
			$share_desc=$arr['0']['share_desc'];
			$share_title=$arr['0']['share_title']; 
			$photo=$arr['0']['photo']; 
			$photoss=$arr['0']['photoss']; 
			$weid=$_W['uniacid'];
			$homeurl = empty($reply['homeurl']) ? $_W['siteroot'] . 'app/' . $this->createMobileUrl('link', array('id' => $id), true) : $reply['homeurl'];
		
		include $this->template('link');
	}

}