<?php
/**
 * O2O预约模块定义
 *
 * @author 华轩科技
 * @url http://bbs.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');

class Hx_o2oModule extends WeModule {

	public function settingsDisplay($settings) {
		global $_W, $_GPC;
		load()->func('tpl');
        if (checksubmit()) {
            $cfg = array(
                'sitename' => $_GPC['sitename'],
                'sitelogo' => $_GPC['sitelogo'],
                'sitedescription' => $_GPC['sitedescription'],
            	'shoplist_thumb' => $_GPC['shoplist_thumb'],
            	'member_bg' => $_GPC['member_bg'],
            	'tel' => $_GPC['tel'],
                'noticeemail' => $_GPC['noticeemail'],
                'kfid' => $_GPC['kfid'],
                'k_templateid' => $_GPC['k_templateid'],
                'kfirst' => $_GPC['kfirst'],
                'kfoot' => $_GPC['kfoot'],
                'm_templateid' => $_GPC['m_templateid'],
                'mfirst' => $_GPC['mfirst'],
                'mfoot' => $_GPC['mfoot'],
            );
            if (!empty($_GPC['logo'])) {
                $cfg['logo'] = $_GPC['logo'];
            }
            if ($this->saveSettings($cfg)) {
                message('保存成功', 'refresh');
            }
        }
		include $this->template('setting');
	}

}