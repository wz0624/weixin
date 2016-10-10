<?php
global $_W,$_GPC;
    $weid = $_W['uniacid'];
		$openid = $_W['openid'];
		$cfg = $this->module['config'];
		$page = intval($_GPC['page']);
		$tuijiannum = empty($cfg['tuijiannum']) ? 10 : intval($cfg['tuijiannum']);
			if(!empty($_GPC['id'])){
				  $id = intval($_GPC['id']);
				  $tuijian = pdo_fetch("SELECT `tuijian`,`gender`,`tj_over_time`,`nickname`,`avatar` FROM".tablename('hnfans')." WHERE weid=:weid AND id=:id",array(':weid'=>$weid,':id'=>$id));
				  if($tuijian['tuijian'] == '1'){
						  if($tuijian['gender'] == '0'){
							   message('此人性别保密，不可推荐','referer','error');
						  }
				  }
					$tj_over_time = empty($tuijian['tj_over_time'])? date('Y-m-d H:i:s',time()):date('Y-m-d H:i:s',$tuijian['tj_over_time']);
					if(checksubmit('submit')){
							$new_tj_over_time = strtotime($_GPC['tj_over_time']);
							if($tuijian['tuijian'] == '1'){
									pdo_update('hnfans',array('tj_over_time'=>$new_tj_over_time,'tuijian'=>2),array('id'=>$id,'weid'=>$weid));
									message('推荐成功',$this->createWebUrl('list',array('isshow'=>1,'page'=>$page)),'success');
							}elseif($tuijian['tuijian'] == '2'){
								if($new_tj_over_time < $tuijian['tj_over_time']){
										pdo_update('hnfans',array('tj_over_time'=>0,'tuijian'=>1),array('id'=>$id,'weid'=>$weid));
										message('取消推荐成功',$this->createWebUrl('list',array('isshow'=>1,'page'=>$page)),'success');
								}else{
										pdo_update('hnfans',array('tj_over_time'=>$new_tj_over_time,'tuijian'=>2),array('id'=>$id,'weid'=>$weid));
										message('修改推荐时间成功',$this->createWebUrl('list',array('isshow'=>1,'page'=>$page)),'success');
								}
							}
					}
			}else{
			  message('参数错误','referer','error');
			}

			include $this->template('tuijian');
