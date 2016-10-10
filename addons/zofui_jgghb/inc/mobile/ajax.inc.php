<?php 
 	global $_W,$_GPC;
	
	$getuserinfo = pdo_fetch("SELECT * FROM " . tablename('zofui_jgghb_log') . " WHERE  uniacid = '{$_W['uniacid']}' AND openid = '{$_W['openid']}' AND date_format(from_UNIXTIME(`time`),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d') AND money != '' ");
	if($getuserinfo){
		exit;
	}
	$usertimes = pdo_fetchcolumn(" SELECT COUNT(id) FROM " . tablename('zofui_jgghb_log') . " WHERE uniacid ={$_W['uniacid']} AND openid = '{$_W['openid']}' AND date_format(from_UNIXTIME(`time`),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d')");
	if($usertimes>5){
		exit;
	}
	
	
	$prizeinfo = pdo_fetchall("SELECT * FROM " . tablename('zofui_jgghb_prize') . " WHERE  uniacid = '{$_W['uniacid']}'");
	
	foreach ($prizeinfo as $v) {
		$moneyarr[$v['pid']] = $v['money'];
	}
	
	foreach($prizeinfo as $k=>$v){
		$chancearr[$k+1] = $v['chance'];
	}
	$chancearr[8] = 100-array_sum($chancearr);
	if($chancearr[8]<0){$chancearr[8]=0;}
	$randPrize = get_rand($chancearr);
	
	$outArr = array(
		'status'    => 1,
        'prize'     => $randPrize
		
	);
	if($randPrize<8){	
		$fee = $moneyarr[$randPrize];//金额
	
		$arr['openid'] = $_GPC['openid'];
        $arr['hbname'] = '众惠幸运九宫格';
        $arr['body'] = "众惠幸运九宫格红包";
        $arr['fee'] = $fee;
		$res = $this->sendhongbaoto($arr);			
		if($res['result_code']=='SUCCESS'){
			$intodb = array();
			$intodb['uniacid'] = $_W['uniacid'];
			$intodb['openid'] = $_GPC['openid'];
			$intodb['time'] = time();
			$intodb['money'] = $moneyarr[$randPrize];
			$intodb['prizenum'] = $randPrize;
			pdo_insert('zofui_jgghb_log',$intodb);
		}	
	}elseif($randPrize==8){
		$intodb = array();
		$intodb['uniacid'] = $_W['uniacid'];
		$intodb['openid'] = $_GPC['openid'];
		$intodb['time'] = time();
		$intodb['money'] = 0;
		$intodb['prizenum'] = $randPrize;
		pdo_insert('zofui_jgghb_log',$intodb);		
	}
	$outArr = json_encode($outArr);
	echo $outArr;

	
/*  	if($outArr[prize]<8){
		$file = fopen(MB_ROOT.'/data.txt','a');
		fwrite($file,$outArr."aaaaaa-----中奖了");
		fopen($file);
	}  */
	
	
	function get_rand($proArr) {
		$result = '';
		 //概率数组的总概率精度
		$proSum = array_sum($proArr);   //100   
		//概率数组循环     
		foreach ($proArr as $key => $proCur) {       
		   $randNum = mt_rand(1, $proSum);      //1-100
		   if ($randNum <= $proCur) {          
				$result = $key;
				break;
		   } else {
				$proSum -= $proCur;
		   }
		}
		unset ($proArr);
		return $result;
   }