<?php
/**
 * 生命计算器模块定义
 *
 * @author chroisen
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Lee_lifeModule extends WeModule {

    public function settingsDisplay($settings) {
        global $_GPC, $_W;
		load()->func('tpl');
		$uniacid = $_W['uniacid'];
		$data = pdo_fetch("SELECT * FROM".tablename('lee_life')."WHERE uniacid=:uniacid",array(':uniacid'=>$uniacid));
        if (checksubmit()) {
            $cfg = array(
			 'link' => $_GPC['link'],
			 'title'=> $_GPC['title'],
			 'desc' => $_GPC['desc'],
			 'imgurl' => $_GPC['imgurl']			 
			 );
            if ($this->saveSettings($cfg)) {
                message('保存成功', 'refresh');
            }
        }
        include $this->template('setting');			 
			 
	} 

}