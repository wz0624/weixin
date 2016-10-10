<?php
/**
 * 一秒模块微站定义
 *
 * @author dzduocai
 * @url http://www.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');
define('HT', '../addons/hetu_yimiao/template/');

class Hetu_yimiaoModuleSite extends WeModuleSite {
    public $yimiao = "hetu_yimiao";
	public function doMobileYimiaoru() {
		 global $_GPC, $_W;
            if(!$_W['fans']['from_user']){
           message('请在微信中打开');
        }
         load()->model('mc');
        $result = mc_fansinfo($_W['member']['uid'], $_W['acid'], $_W['uniacid']);
        
 
         include $this->template('yimiao');
	}
    
    public function doMobileYimiaoajax(){
         global $_GPC, $_W;
            
            

         $data = array(
            'uniacid' => $_GPC['uniacid'],
            'openid' => $_GPC['openid'],
            'nickname' => $_GPC['nickname'],
            'tishi' => $_GPC['tishi'],
            'time' => $_GPC['time'],
         );
         pdo_insert($this->yimiao, $data);
        
    }
	public function doWebYimiaoru() {
		 global $_GPC, $_W;
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;

        $yimiao_list = pdo_fetchall("SELECT * FROM " . tablename($this->yimiao) ." WHERE uniacid = '{$_W['uniacid']}' ORDER BY yi_id desc, add_time DESC LIMIT " .($pindex - 1) * $psize . ',' . $psize);
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->yimiao) ." WHERE uniacid = '{$_W['uniacid']}'");
        $pager = pagination($total, $pindex, $psize);
       
        
        
        include $this->template('yimiao');
	}

}