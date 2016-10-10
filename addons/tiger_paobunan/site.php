<?php
/**
 * 是男人就动起来!模块微站定义
 *
 * @author 老虎
 * @url http://www.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');

class Tiger_paobunanModuleSite extends WeModuleSite {

	public function doMobileImport() {
		//这个操作被定义用来呈现 功能封面
		global $_GPC,$_W;
		
	    include $this->template('index');
	}

}