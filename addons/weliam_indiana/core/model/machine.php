<?php 
	// 
	//  machine.php
	//  <project>
	//  机器人自动设置时间执行
	//  Created by Administrator on 2016-03-17.
	//  Copyright 2016 Administrator. All rights reserved.
	// 
	if (!defined('IN_IA')) {
		exit('Access Denied');
	} 
	class Welian_Indiana_Machine {
		public function marchine_cir($period_number = '',$timebucket = '',$code_num = 0){
			//根据具体期号，机器人购买时间和随机个数购买
			global $_W;
			ignore_user_abort(TRUE);
			set_time_limit(0);
			$periods = pdo_fetch("select id , period_number , shengyu_codes , machine_time from".tablename('weliam_indiana_period')."where uniacid = '{$_W['uniacid']}' and period_number = '{$period_number}'");
			$machines = pdo_fetch("select * from ".tablename('weliam_indiana_machineset')."where period_number = '{$period_number}'");
			if($periods['shengyu_codes'] > 0 && $machines['status'] == 1 && $machines['max_num'] != 0 ){
				$number = rand(1, $code_num);			 //随机选取范围中的夺宝码个数
				$min = $periods['shengyu_codes'] > $machines['max_num']?$machines['max_num']:$periods['shengyu_codes'];
				$min = $min==-1?$periods['shengyu_codes']:$min;
				if($number > $min){
					$number = $min;
				}

				$left_num = $machines['max_num'];
				$stime = ($machines['start_time']+28800) % 86400;
				$etime = ($machines['end_time']+28800) % 86400;
				$ntime = (time()+28800) % 86400;
				
				if($ntime > $stime && $ntime < $etime){
					$person = self::get_Machines(1);					//随机取一个机器人
					$api = "http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip={$person[0]['ip']}";
					$json = @file_get_contents($api);//调用新浪IP地址库 
					$arr = json_decode($json,true);
					pdo_insert('weliam_indiana_cart',array('num'=>$number,'uniacid'=>$_W['uniacid'],'period_number'=>$period_number,'openid'=>$person[0]['openid'],'ip'=>$person[0]['ip'],'ipaddress'=>$arr['province'].$arr['city']));
					m('codes')->code($person[0]['openid'],'machine',$_W['uniacid'],time());
					if($left_num > 0 ){
						$left_num = $left_num - $number;
					}
				}			 						
				$time_bucket = rand(1,$timebucket);
				$next_time = $time_bucket + time();
				if($left_num == 0){
					pdo_update("weliam_indiana_machineset",array('next_time'=>$next_time , 'max_num' => $left_num , 'status' => 0),array('id'=>$machines['id']));
					if($machines['is_followed'] == 1){
						//判断是否连期
						$Nperiod_number = self::get_NextPeriodNumber($period_number);
						if(!empty($Nperiod_number)){
							pdo_update("weliam_indiana_machineset",array('next_time'=>$next_time , 'max_num' => -1 , 'status' => 1 ,'period_number'=>$Nperiod_number),array('id'=>$machines['id']));
							$period_number = $Nperiod_number;
						}
					}
				}else{
					pdo_update("weliam_indiana_machineset",array('next_time'=>$next_time , 'max_num' => $left_num),array('id'=>$machines['id']));
				}
				sleep($time_bucket);
				m('machine')->marchine_cir($period_number , $timebucket , $code_num);
			}else{
				if($machines['is_followed'] == 1){
						//判断是否连期
						$Nperiod_number = self::get_NextPeriodNumber($period_number);
						if(!empty($Nperiod_number)){
							pdo_update("weliam_indiana_machineset",array('next_time'=>$next_time , 'max_num' => -1 , 'status' => 1 ,'period_number'=>$Nperiod_number),array('id'=>$machines['id']));
							$period_number = $Nperiod_number;
							m('machine')->marchine_cir($period_number , $timebucket , $code_num);
						}
					}
			}
		}
		public function get_Machines($num = 0){
			//查出member表中多少条数据
			global $_W;
			$info = pdo_fetchall("select * from".tablename('weliam_indiana_member')."where uniacid = '{$_W['uniacid']}' and type = '-1' and ip != '' order by rand() limit  ".$num);
			return $info;
		}
		
		public function get_MachinesInfoByPeriodnNumber($period_number = ''){
			//通过期号获取机器人运行情况
			global $_W;
			$machine_info = pdo_fetch("select * from".tablename('weliam_indiana_machineset')."where uniacid ='{$_W['uniacid']}' and period_number = '{$period_number}'");
			return $machine_info;
		}
		
		public function get_NextPeriodNumber($period_number = ''){
			//获取下一期的商品的period_number
			global $_W;
			$sql_g = "select goodsid from".tablename('weliam_indiana_period')."where uniacid=:uniacid and period_number=:period_number";
			$data_g = array(
				':uniacid'=>$_W['uniacid'],
				':period_number'=>$period_number
			);
			$goodsid = pdo_fetchcolumn($sql_g,$data_g);
			$sql = "select period_number from".tablename('weliam_indiana_period')."where uniacid=:uniacid and goodsid=:goodsid and status=:status";
			$data = array(
				':uniacid'=>$_W['uniacid'],
				':goodsid'=>$goodsid,
				':status'=>1
			);
			$Nperiod_number = pdo_fetchcolumn($sql,$data);
			return $Nperiod_number;
		}
		
	}
	
	?>