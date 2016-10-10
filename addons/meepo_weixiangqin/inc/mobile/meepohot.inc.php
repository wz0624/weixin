<?php
include_once(MODULE_ROOT.'/func.php');
global $_GPC, $_W;
$weid = $_W['uniacid'];
$openid = $_W['openid'];
$man_status = intval($_GPC['man_status']);
$pindex = max(1, intval($_GPC['truepage']));
$psize =5;
$tablename = tablename("hnfans");
$result_str = '';
$julires = $this->getusers($weid,$openid);
if($man_status==0){
	$lists = pdo_fetchall("SELECT * FROM " . $tablename . " WHERE weid = :weid AND nickname!=:nickname AND isshow=:isshow AND yingcang=:yingcang AND gender=:gender  AND tuijian=:tuijian AND tj_over_time>=:tj_over_time ORDER BY love DESC,id DESC,time DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':weid' =>$weid,':nickname'=>'',':isshow'=>'1',':yingcang'=>'1',':gender'=>'1',':tuijian'=>'2',':tj_over_time'=>TIMESTAMP));
	if(!empty($lists)){
		foreach($lists as $row){
				if(!empty($row['lat']) && !empty($row['lng'])){
					if(!empty($julires['lat']) && !empty($julires['lng'])){
						 $juli[$row['id']]= "相距: ".getDistance($julires['lat'],$julires['lng'],$row['lat'],$row['lng'])."km";
					}else{
							$juli[$row['id']]= ""; 
					}
				}else{
					 $juli[$row['id']]= ""; 
				}
				$onclick2 = "'".$row['from_user']."'";
				$result_str .= '<li class="indexItem" >';
				if($row['tj_over_time']>=TIMESTAMP){
					$result_str .= '<div  class="tuijian"></div>';
				}
					$result_str .= '<span  class="linka" onclick="checkself('.$onclick2.')">';
				if(preg_match('/http:(.*)/',$row['avatar'])){
					$result_str .='<img src="'.$row['avatar'].'" alt="用户头像">';
				}elseif(preg_match('/images(.*)/',$row['avatar'])){
					$result_str .='<img src="'.$_W['attachurl'].$row['avatar'].'" alt="用户头像">';
				}else{
				 $result_str .='<img src="../addons/meepo_weixiangqin/template/mobile/tpl/static/friend/images/cdhn80.jpg" alt="用户头像">'; 
				}
				$result_str .='<div class="itemc"><p class="hcolor" style="font-size:13px;">'.cutstr($row['realname'],5,true);
				if($row['gender']=='1'){
					$result_str .="&nbsp;&nbsp;男";
				}elseif( $row['gender']=='2'){
					$result_str .="&nbsp;&nbsp;女";
				}else{
					$result_str .="&nbsp;&nbsp;保密";
				}
				$onclick = "'".$row['id']."','".$row['from_user']."'";
				$result_str .='<font id="shopspostion" style="color:red;font-size:12px;">&nbsp;&nbsp;'.$juli[$row['id']].'</font><p class="lcolor" style="font-size:13px;">&nbsp;&nbsp;微信: '.$row['nickname'].'</p>
					<p class="lcolor" style="font-size:13px;">&nbsp;&nbsp;'.$row['resideprovincecity'];
				if(strlen($row['mingzu'])==1){
						$result_str .='&nbsp;&nbsp;';
				}else{
						$result_str .='&nbsp;&nbsp;'.$row['mingzu'];
				}
				$result_str .=' </p></div>
				 </span>';
				$result_str .='<div class="likebox" style="margin-top:10px;">
						<div class="likeit2 likeit1  fleft "><span class="hitlike" onclick="hitlikeone('.$onclick.');" id="'.$row['from_user'].'">&nbsp;'.$row['love'].'</span></div>
						<div class="likeit2 letterit fleft"><a class="hitmail"  href="'.$this->createMobileUrl('hitmail',array('toname'=>$row['nickname'],'toopenid'=>$row['from_user'])).'" target="__blank" style="color:#fff">聊一聊</a></div>
						<div class="likeit2 sayhi fleft"><span class="saihello" onclick="sayhi('.$onclick.');" >打招呼</span></div>
					</div> </li><li class="dottedLine"></li>';  
		}
		if(count($lists)==5){
			die(json_encode(error(0,$result_str)));
		}else{
			die(json_encode(error(-2,$result_str)));
		}
	}else{//找系统推荐的
			$lists = pdo_fetchall("SELECT * FROM " . $tablename . " WHERE weid = :weid AND nickname!=:nickname AND isshow=:isshow AND yingcang=:yingcang AND gender=:gender  AND tj_over_time<:tj_over_time ORDER BY love DESC,id DESC,time DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':weid' =>$weid,':nickname'=>'',':isshow'=>'1',':yingcang'=>'1',':gender'=>'1',':tj_over_time'=>TIMESTAMP));
			if(!empty($lists)){
						foreach($lists as $row){
								if(!empty($row['lat']) && !empty($row['lng'])){
									if(!empty($julires['lat']) && !empty($julires['lng'])){
										 $juli[$row['id']]= "相距: ".getDistance($julires['lat'],$julires['lng'],$row['lat'],$row['lng'])."km";
									}else{
											$juli[$row['id']]= ""; 
									}
								}else{
									 $juli[$row['id']]= ""; 
								}
								$onclick2 = "'".$row['from_user']."'";
								$result_str .= '<li class="indexItem" >';
								if($row['tj_over_time']>=TIMESTAMP){
									$result_str .= '<div  class="tuijian"></div>';
								}
									$result_str .= '<span  class="linka" onclick="checkself('.$onclick2.')">';
								if(preg_match('/http:(.*)/',$row['avatar'])){
									$result_str .='<img src="'.$row['avatar'].'" alt="用户头像">';
								}elseif(preg_match('/images(.*)/',$row['avatar'])){
									$result_str .='<img src="'.$_W['attachurl'].$row['avatar'].'" alt="用户头像">';
								}else{
								 $result_str .='<img src="../addons/meepo_weixiangqin/template/mobile/tpl/static/friend/images/cdhn80.jpg" alt="用户头像">'; 
								}
								$result_str .='<div class="itemc"><p class="hcolor" style="font-size:13px;">'.cutstr($row['realname'],5,true);
								if($row['gender']=='1'){
									$result_str .="&nbsp;&nbsp;男";
								}elseif( $row['gender']=='2'){
									$result_str .="&nbsp;&nbsp;女";
								}else{
									$result_str .="&nbsp;&nbsp;保密";
								}
								$onclick = "'".$row['id']."','".$row['from_user']."'";
								$result_str .='<font id="shopspostion" style="color:red;font-size:12px;">&nbsp;&nbsp;'.$juli[$row['id']].'</font><p class="lcolor" style="font-size:13px;">&nbsp;&nbsp;微信: '.$row['nickname'].'</p>
									<p class="lcolor" style="font-size:13px;">&nbsp;&nbsp;'.$row['resideprovincecity'];
								if(strlen($row['mingzu'])==1){
										$result_str .='&nbsp;&nbsp;';
								}else{
										$result_str .='&nbsp;&nbsp;'.$row['mingzu'];
								}
								$result_str .=' </p></div>
								 </span>';
								$result_str .='<div class="likebox" style="margin-top:10px;">
										<div class="likeit2 likeit1  fleft "><span class="hitlike" onclick="hitlikeone('.$onclick.');" id="'.$row['from_user'].'">&nbsp;'.$row['love'].'</span></div>
										<div class="likeit2 letterit fleft"><a class="hitmail"  href="'.$this->createMobileUrl('hitmail',array('toname'=>$row['nickname'],'toopenid'=>$row['from_user'])).'" target="__blank" style="color:#fff">聊一聊</a></div>
										<div class="likeit2 sayhi fleft"><span class="saihello" onclick="sayhi('.$onclick.');" >打招呼</span></div>
									</div> </li><li class="dottedLine"></li>';  
						}
						die(json_encode(error(-1,$result_str)));
			}else{
				die(json_encode(error(-3,'over')));
			}
	}
}else{
		$lists = pdo_fetchall("SELECT * FROM " . $tablename . " WHERE weid = :weid AND nickname!=:nickname AND isshow=:isshow AND yingcang=:yingcang AND gender=:gender  AND tj_over_time<:tj_over_time ORDER BY love DESC,id DESC,time DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':weid' =>$weid,':nickname'=>'',':isshow'=>'1',':yingcang'=>'1',':gender'=>'1',':tj_over_time'=>TIMESTAMP));
			if(!empty($lists)){
						foreach($lists as $row){
								if(!empty($row['lat']) && !empty($row['lng'])){
									if(!empty($julires['lat']) && !empty($julires['lng'])){
										 $juli[$row['id']]= "相距: ".getDistance($julires['lat'],$julires['lng'],$row['lat'],$row['lng'])."km";
									}else{
											$juli[$row['id']]= ""; 
									}
								}else{
									 $juli[$row['id']]= ""; 
								}
								$onclick2 = "'".$row['from_user']."'";
								$result_str .= '<li class="indexItem" >';
								if($row['tj_over_time']>=TIMESTAMP){
									$result_str .= '<div  class="tuijian"></div>';
								}
									$result_str .= '<span  class="linka" onclick="checkself('.$onclick2.')">';
								if(preg_match('/http:(.*)/',$row['avatar'])){
									$result_str .='<img src="'.$row['avatar'].'" alt="用户头像">';
								}elseif(preg_match('/images(.*)/',$row['avatar'])){
									$result_str .='<img src="'.$_W['attachurl'].$row['avatar'].'" alt="用户头像">';
								}else{
								 $result_str .='<img src="../addons/meepo_weixiangqin/template/mobile/tpl/static/friend/images/cdhn80.jpg" alt="用户头像">'; 
								}
								$result_str .='<div class="itemc"><p class="hcolor" style="font-size:13px;">'.cutstr($row['realname'],5,true);
								if($row['gender']=='1'){
									$result_str .="&nbsp;&nbsp;男";
								}elseif( $row['gender']=='2'){
									$result_str .="&nbsp;&nbsp;女";
								}else{
									$result_str .="&nbsp;&nbsp;保密";
								}
								$onclick = "'".$row['id']."','".$row['from_user']."'";
								$result_str .='<font id="shopspostion" style="color:red;font-size:12px;">&nbsp;&nbsp;'.$juli[$row['id']].'</font><p class="lcolor" style="font-size:13px;">&nbsp;&nbsp;微信: '.$row['nickname'].'</p>
									<p class="lcolor" style="font-size:13px;">&nbsp;&nbsp;'.$row['resideprovincecity'];
								if(strlen($row['mingzu'])==1){
										$result_str .='&nbsp;&nbsp;';
								}else{
										$result_str .='&nbsp;&nbsp;'.$row['mingzu'];
								}
								$result_str .=' </p></div>
								 </span>';
								$result_str .='<div class="likebox" style="margin-top:10px;">
										<div class="likeit2 likeit1  fleft "><span class="hitlike" onclick="hitlikeone('.$onclick.');" id="'.$row['from_user'].'">&nbsp;'.$row['love'].'</span></div>
										<div class="likeit2 letterit fleft"><a class="hitmail"  href="'.$this->createMobileUrl('hitmail',array('toname'=>$row['nickname'],'toopenid'=>$row['from_user'])).'" target="__blank" style="color:#fff">聊一聊</a></div>
										<div class="likeit2 sayhi fleft"><span class="saihello" onclick="sayhi('.$onclick.');" >打招呼</span></div>
									</div> </li><li class="dottedLine"></li>';  
						}
						die(json_encode(error(-1,$result_str)));
			}else{
				die(json_encode(error(-3,'over')));
			}
}

	