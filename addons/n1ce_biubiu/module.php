<?php
/**
 * 弄死牛郎织女模块定义
 *
 * @author n1ce   QQ：541535641
 * @url http://www.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');

class N1ce_biubiuModule extends WeModule {
	public function settingsDisplay($settings) {
		global $_W, $_GPC;
		if(checksubmit()) {
			$cfg = array(
                'title' => $_GPC['title'],
                'pic' => $_GPC['pic'],
                's_url' => $_GPC['s_url'],
				'desc' => $_GPC['desc'],
            );
            if ($this->saveSettings($cfg)) {
                message('保存成功', 'refresh');
            }
		}
		
		if (empty($settings['s_url'])) {
            $settings['s_url'] = 'http://www.dwz.cn/1dJaTb';
        }
		
    
		include $this->template('setting');
	}

}