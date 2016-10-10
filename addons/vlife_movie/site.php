<?php
/**
 * 微生活影讯模块微站定义
 *
 */
defined('IN_IA') or exit('Access Denied');

class Vlife_movieModuleSite extends WeModuleSite {

	// public function doMobileCover() {
		// //这个操作被定义用来呈现 功能封面
	// }
	public function doWebRule() {
		//这个操作被定义用来呈现 规则列表
	}
	public function doWebMovie() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_W, $_GPC;
		$settings=$this->module['config'];
		if(checksubmit(submit)) {
			//字段验证, 并获得正确的数据$dat
			$data = array(
				'movie_city'	=>	$_GPC['movie_city'],
				'movie_api'	=>	$_GPC['movie_api'],
				'movie_ad'	=>	$_GPC['movie_ad'],
				'movie_banner'	=>	$_GPC['movie_banner']
			);
			if($this->saveSettings($data)){
				message('保存成功', 'refresh');
			}
		}
			load()->func('tpl');
        include $this->template('setting');
	}
	// public function doMobileIndex() {
		// //这个操作被定义用来呈现 微站首页导航图标
	// }
	// public function doMobilePerson() {
		// //这个操作被定义用来呈现 微站个人中心导航
	// }
	// public function doMobileFuntction() {
		// //这个操作被定义用来呈现 微站快捷功能导航
	//}

}