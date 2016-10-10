<?php
/**
 * 生命计算器模块微站定义
 *
 * @author dzduocai
 * @url http://www.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');
define('DZ', '../addons/dzduocai_life/template/');

class Dzduocai_lifeModuleSite extends WeModuleSite {
    
     public $life = "dz_life";
	public function doMobileLiferukou() {
	   
	
         global $_GPC, $_W;
          if(!$_W['fans']['from_user']){
           message('请在微信中打开');
        }
         
         include $this->template('liferukou');
        
	}
    public function doMobileView(){
         global $_GPC, $_W;
    
         
        if(!$_W['fans']['from_user']){
          message('请在微信中打开');
        }
         $url = $_W['siteroot']."app/index.php?i=".$_W['uniacid']."&c=entry&do=liferukou&m=dzduocai_life&wxref=mp.weixin.qq.com";
         if (isset($_GPC['date_time'])) {
           $date_time=$_GPC['date_time'];
        }
        
        
        
        $dob = strtotime($date_time);
        if(!$dob){
             message('请填写规范的日期格式');
        }
       
        $y = date('Y', $dob);
         if (($m = (date('m') - date('m', $dob))) < 0) {
          $y++;
         } elseif ($m == 0 && date('d') - date('d', $dob) < 0) {
          $y++;
         }
        $nian= date('Y') - $y;
        
        $d1=strtotime ("now");
        $d2=strtotime($date_time);
        //$nian=round(($d1-$d2)/3600/24/365);
        $nian2=($d1-$d2)/3600/24/365;
        $Days=round(($d1-$d2)/3600/24);
        $yue=round(($d1-$d2)/3600/24/30);
        $zhou=round(($d1-$d2)/3600/24/7);
        $xiaoshi=round(($d1-$d2)/3600);
        $fen=round(($d1-$d2)/60);
        $miao=round(($d1-$d2));
       
        
        
        $data['date_time'] = $date_time;
        $data['uniacid'] = $_W['uniacid'];
        $data['openid'] = $_W['fans']['from_user'];
        load()->model('mc');
        $result = mc_fansinfo($_W['member']['uid'], $_W['acid'], $_W['uniacid']);
        $data['nickname'] = $result['nickname'];
        pdo_insert($this->life, $data);
      include $this->template('view');
          
        
    }
    public function doMobileFuture(){
        global $_GPC, $_W;
         if(!$_W['fans']['from_user']){
           message('请在微信中打开');
        }
         $url = $_W['siteroot']."app/index.php?i=".$_W['uniacid']."&c=entry&do=liferukou&m=dzduocai_life&wxref=mp.weixin.qq.com";
        if (isset($_GPC['date_time'])) {
           $date_time=$_GPC['date_time'];
        }
        date_default_timezone_set('PRC'); 
        $dob = strtotime($date_time);
        $y = date('Y', $dob);
         if (($m = (date('m') - date('m', $dob))) < 0) {
          $y++;
         } elseif ($m == 0 && date('d') - date('d', $dob) < 0) {
          $y++;
         }
        $nian= date('Y') - $y;
        
        $d1=strtotime ("now");
        $d2=strtotime($date_time);
        $nian3=round(($d1-$d2)/3600/24/365);
        $nian2=($d1-$d2)/3600/24/365;
        $Days=round(($d1-$d2)/3600/24);
        $yue=round(($d1-$d2)/3600/24/30);
        $zhou=round(($d1-$d2)/3600/24/7);
        $xiaoshi=round(($d1-$d2)/3600);
        $fen=round(($d1-$d2)/60);
        $miao=round(($d1-$d2));
        $s_Days=36500-$Days;
        $s_fan=$s_Days * 3;
        $s_zao=$s_Days / 2;
        $s_bing=100 - $nian3;
        $s_zhou=round($s_Days / 7);
        $s_pi=$s_Days * 5;
        $s_meng=$s_Days * 10;
        $s_chaojia=round($s_Days / 60);
        $s_zhayan=$s_Days * 100;
        $s_lei=round($s_Days / 15);
        $s_hang=round($s_Days / 7);
        $s_zhijia=round($s_Days / 20);
        $s_shui=$s_Days * 8;
        $s_lanyao=round($s_Days * 5.2);
        $s_haqian=round($s_Days * 7.4);
        include $this->template('future');
        
    }
	public function doWebLifecanjia() {
		//这个操作被定义用来呈现 管理中心导航菜单
        global $_GPC, $_W;
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;

        $lift_list = pdo_fetchall("SELECT * FROM " . tablename($this->life) .
            " WHERE uniacid = '{$_W['uniacid']}' ORDER BY life_id desc, add_time DESC LIMIT " .
            ($pindex - 1) * $psize . ',' . $psize);
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->life) .
            " WHERE uniacid = '{$_W['uniacid']}'");
        $pager = pagination($total, $pindex, $psize);
        
        include $this->template('lifecanjia');
	}

}