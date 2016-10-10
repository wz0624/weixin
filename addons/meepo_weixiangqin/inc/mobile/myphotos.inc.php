<?php
     global $_W,$_GPC;
	   if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
			
	    }else{
		    $url=$this->createMobileUrl('Errorjoin');			
			header("location:$url");
			exit;										
		}	
    $weid = $_W['uniacid'];
		$openid = $_W['openid'];
		$settings = pdo_fetch("SELECT * FROM ".tablename('meepo_hongniangset')." WHERE weid=:weid",array(':weid'=>$_W['uniacid']));
		$cfg = $this->module['config'];	
		if(empty($openid)){
		  message('请重新从微信进入');
		}
		$photocfg = $this->module['config'];
		$photos = $this->getallphotos($openid);//取得所有照片
	  include $this->template('myphotos');