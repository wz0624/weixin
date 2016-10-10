<?php
/**
 * 弄死七夕微站定义
 *
 * @author  n1ce 
 * @url http://www.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');
define('RES', '../addons/n1ce_biubiu/template/mobile/');
class N1ce_biubiuModuleSite extends WeModuleSite {

	public function doMobileindex() {
		//这个操作被定义用来呈现 功能封面
		global $_W,$_GPC;
		$title = isset($this->module['config']['title']) ? $this->module['config']['title'] : "我在弄死牛郎织女游戏中得了"+scores+"分，不服来战吧！";
		$desc = isset($this->module['config']['desc']) ?$this->module['config']['desc'] : '';
		$pic = isset($this->module['config']['pic']) ? $this->module['config']['pic'] : $_W['siteroot'].'addons/n1ce_love/template/mobile/images/answer.png';
		$pic = tomedia($pic);
		$s_url = isset($this->module['config']['s_url']) ? $this->module['config']['s_url'] : '';
		$pageurl = $_W['siteroot'].'app/'.$this->createMobileUrl('index');
		/*if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) {
			include $this->template('no_sub');
			exit();
		}*/
		include $this->template('index');
	}



}