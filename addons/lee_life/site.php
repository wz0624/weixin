<?php
/**
 * 生命计算器模块微站定义
 *
 * @author chroisen
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Lee_lifeModuleSite extends WeModuleSite {

	public function doMobileindex() {
		global $_W,$_GPC;
		$uniacid = $_W['uniacid'];
		$data = pdo_fetch("SELECT * FROM".tablename('lee_life')."WHERE uniacid=:uniacid",array(':uniacid'=>$uniacid));
		$update = array(
			'uniacid'=>$uniacid,
			'scannum'=>$data['scannum']+1,
			'createtime'=>TIMESTAMP		
		);
		if(empty($data)){
			pdo_insert('lee_life',$update);
			
		}
		else{
			pdo_update('lee_life',$update,array('uniacid'=>$uniacid));
		}
		
		$title = $this->module['config']['title'];
		$title = str_replace('[day]',$day,$title);
		$desc = $this->module['config']['desc'];
		$link = $this->module['config']['link'];
		$imgurl = $_W['attachurl'].$this->module['config']['imgurl'];		
		include $this->template('index');
	}
	public function doMobileview() {
		global $_W,$_GPC;
		$uniacid = $_W['uniacid'];

		$time = $_GPC['totime'];
		$sort = strtotime($time);
		$nowtime = time();
		$time = $nowtime - $sort;
		$age = $time / 60 / 60 / 24 / 365;
	    $year   = floor($time / 60 / 60 / 24 / 365);
	    $month  = floor($time / 60 / 60 / 24 / 30);
	    $week   = floor($time / 60 / 60 / 24 / 7);
	    $day    = floor($time / 60 / 60 / 24);
	    $hour   = floor($time / 60 / 60);
	    $minute = floor($time / 60);
	    $second = $time;
		$title = $this->module['config']['title'];
		$title = str_replace('[day]',$day,$title);
		$desc = $this->module['config']['desc'];
		$link = $this->module['config']['link'];
		$imgurl = $_W['attachurl'].$this->module['config']['imgurl'];		
		include $this->template('view');
	}
	
	
	public function doMobilefuture() {
		global $_W,$_GPC;
		$uniacid = $_W['uniacid'];
		$time = $_GPC['totime'];
		$nowtime = time();
		$ages = $nowtime - $time;
		$hadday = floor($ages / 60 / 60 / 24);
		$sort = time();
		$time = $time + 100*366*24*60*60 - $sort;
	    $day    = floor($time / 60 / 60 / 24);
		$sleep = $day;
		$eat = $day * 3;
		$shower = floor($day/2);
		$sick = floor($time / 60 / 60 / 24 / 365);
		$week   = floor($time / 60 / 60 / 24 / 7);
		$pi = floor($day / 5);
		$dream = $day * 10;
		$chaojia = floor($day / 60);
		$zhanyan = $day * 12;
		$tears = floor($day / 30);
		$lie =  floor($day / 15);
		$zhijia = floor($day / 30);
		$water = floor($day / 6);
	    $minute = floor($time / 60);
		$title = $this->module['config']['title'];
		$title = str_replace('[day]',$hadday,$title);
		$desc = $this->module['config']['desc'];
		$link = $this->module['config']['link'];
		$imgurl = $_W['attachurl'].$this->module['config']['imgurl'];		
		include $this->template('future');
	}
	
	public function doMobileshare() {
		global $_W,$_GPC;
		$uniacid = $_W['uniacid'];
		$link = $this->module['config']['link'];
		$data = pdo_fetch("SELECT * FROM".tablename('lee_life')."WHERE uniacid=:uniacid",array(':uniacid'=>$uniacid));
		$update = array(
			'uniacid'=>$uniacid,
			'sharenum'=>$data['sharenum']+1,
			'createtime'=>TIMESTAMP		
		);
		if(empty($data)){
			pdo_insert('lee_life',$update);
			
		}
		else{
			pdo_update('lee_life',$update,array('uniacid'=>$uniacid));
		}
		message('',$link);
	}	
}