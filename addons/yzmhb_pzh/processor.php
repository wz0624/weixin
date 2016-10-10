<?php
/**
 * 验证码红包模块处理程序
 *
 * @author pzh
 * @url http://bbs.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');
include_once("PZHSendMoney.php");
class Yzmhb_pzhModuleProcessor extends WeModuleProcessor {
	public function respond() {
		$content = $this->message['content'];
		global $_GPC,$_W;
		//判断是否在时间内
            $time = date('Y-m-d H:i:s',time());
            if($time< $this->module['config']['kouling']['begin_time'] )
            {
            	if(empty($this->module['config']['kouling']['nobegin']))
            	{
            		$errorMsg='活动未开始';
            	}
            	else 
            	{
            		$errorMsg = $this->module['config']['kouling']['nobegin'];
            	}
            	 return $this->respText($errorMsg);
            	
            }

            if($time> $this->module['config']['kouling']['end_time'] )
            {
            	if(empty($this->module['config']['kouling']['haveend']))
            	{
            		$errorMsg='活动已结束';
            	}
            	else 
            	{
            		$errorMsg = $this->module['config']['kouling']['haveend'];
            	}
            	 return $this->respText($errorMsg);
            }
         

         $nick_name   =  $this->module['config']['kouling']['nick_name'];
	    $send_name   =  $this->module['config']['kouling']['send_name'];
	    $wishing     =  $this->module['config']['kouling']['wishing'];
	    $remark      =  $this->module['config']['kouling']['remark'];
	    $act_name    =  $this->module['config']['kouling']['act_name'];
	    $maxCount    =  $this->module['config']['kouling']['maxCount'];
	    $maxRedCount =  $this->module['config']['kouling']['maxRedCount'];
	    $rand_list   =  $this->module['config']['kouling']['rand_list'];
	    $money_list  =  $this->module['config']['kouling']['money_list'];
	    $small       =  $this->module['config']['kouling']['small'];
           	if($maxRedCount <= 0)
	    {
	    	//红包已领完
	    	if(!empty($this->module['config']['kouling']['sendAllMsg']))
	    	{
	    		$errorMsg = $this->module['config']['kouling']['sendAllMsg'];
	    	}
	    	else 
	    	{
	    		$errorMsg='红包已领完';
	    	}
	    	 return $this->respText($errorMsg);
	    }
           $re_openid   =  $this->message['from'];
			 //判断用户操作是否过于频繁
               session_start(); 

            	
            		if($_W['timestamp']-$_SESSION[$re_openid]<$this->module['config']['kouling']['cooling'])
            		{
            			if(!empty($this->module['config']['kouling']['havegetMsg']))
            			{
            				$errorMsg = $this->module['config']['kouling']['havegetMsg'];
            			}
            			else 
            			{
            				$errorMsg='您的操作过于频繁';
            			}
            			$_SESSION[$re_openid] = $_W['timestamp'];
            			 return $this->respText($errorMsg);
            		}
            		else
            		{
            			
            				$_SESSION[$re_openid] =$_W['timestamp'];

            		}

            	
    
		    $this ->init();
		 

		//查询口令是否存在
	    $sql = 'SELECT beginer,moneyCount,count FROM ' . tablename('pzh_kouling4') . ' WHERE `uniacid` = :uniacid  and `kouling` = :kouling';
	    $params = array(':uniacid' => $_W['uniacid'] , ':kouling' => $content);
	   // return $this->respText($_W['acid']);
	    $result = pdo_fetch($sql, $params);
        $kouling_count=$result['count'];
	    if(!$result||$result['count']<=0)
	    {
	    	
	    	if(!empty($this->module['config']['kouling']['misMsg']))
	    	{
	    	$errorMsg = $this->module['config']['kouling']['misMsg'];
	        }
	        else
	        {
	        	$errorMsg='没有查到该口令';
	        }
	    	 return $this->respText($errorMsg);
	    }
         $beginer = $result['beginer'];
         $moneyCount = $result['moneyCount'];
	    //*************************************************************************************
	
	    $nick_name   =  $this->module['config']['kouling']['nick_name'];
	    $send_name   =  $this->module['config']['kouling']['send_name'];
	    $wishing     =  $this->module['config']['kouling']['wishing'];
	    $remark      =  $this->module['config']['kouling']['remark'];
	    $act_name    =  $this->module['config']['kouling']['act_name'];
	    $maxCount    =  $this->module['config']['kouling']['maxCount'];
	    $maxRedCount =  $this->module['config']['kouling']['maxRedCount'];
	    $rand_list   =  $this->module['config']['kouling']['rand_list'];
	    $money_list  =  $this->module['config']['kouling']['money_list'];
	    $small       =  $this->module['config']['kouling']['small'];
         if(empty($moneyCount) )
        {
              return $this->respText('金额不能为空');
        }
       if(strstr($moneyCount,'-'))
       {
         $money_list = explode('-',$moneyCount); 
         $total_amount = (rand($money_list[0]*100,$money_list[1]*100));

       }
        else
        {
             $total_amount = $moneyCount*100;
        }
	    
	      
       
	  
       
       


	    $sql = 'SELECT redPackCount,lastTime FROM ' . tablename('pzh_packet2') . ' WHERE `uniacid` = :uniacid and `type` = :type and `openid` = :openid';
	    $params = array(':uniacid' => $_W['uniacid'],':type' => 'kouling' , ':openid' => $re_openid);
	    $account = pdo_fetch($sql, $params);
         
       
	    if(!$account)
	    {
	        //如果查询不到该用户
	    	$sql = 'INSERT INTO'.tablename('pzh_packet2') .' (`uniacid`,`openid`,`redPackCount`,`lastTime`,`type`) values ('.
	    		strval($_W['uniacid']).',\''.$re_openid.'\',0,'.strval($_W['timestamp']).',\'kouling\')'; 
			$result = pdo_query($sql);
	         // return $this->respText($sql);
		}
		else
		{
			
			 if($account['redPackCount']>=$maxCount)
			{
	          //红包个数超过设定值
	          // return $this->respText('您的红包已领完~');
		        //用户红包个数超过设定值
				if(!empty($this->module['config']['kouling']['limitMsg']))
				{
					$errorMsg = $this->module['config']['kouling']['limitMsg'];
				}
				else 
				{
					$errorMsg='您的红包领取达到限制';
				}
				  return $this->respText($errorMsg);
			}
		}  
	
        //减去口令个数
        $sql = 'SELECT beginer,moneyCount,count FROM ' . tablename('pzh_kouling4') . ' WHERE `uniacid` = :uniacid  and `kouling` = :kouling';
        $params = array(':uniacid' => $_W['uniacid'] , ':kouling' => $content);
        $result = pdo_fetch($sql, $params);
        $kouling_count=$result['count'];
        $sql = 'update '.tablename('pzh_kouling4') .' set `count` = '.strval($kouling_count-1).'  WHERE `uniacid` = '.$_W['uniacid'].' and `kouling` = \''.$content.'\''; 
        pdo_query($sql);
       
		$packet = new  PZHSend();
	     $weid = $_W['uniacid'];
        
		$result = $packet->pay($re_openid,$nick_name,$send_name,$total_amount,$wishing,$act_name,$remark,$this->module['config']['mchid'],$this->module['config']['appid'],$this->module['config']['password']);
       
		if($result->return_code == 'FAIL' || $result ==  'fail'||!$result->return_code )
		{
           
        //加回口令个数
        $sql = 'SELECT beginer,moneyCount,count FROM ' . tablename('pzh_kouling4') . ' WHERE `uniacid` = :uniacid  and `kouling` = :kouling';
        $params = array(':uniacid' => $_W['uniacid'] , ':kouling' => $content);
      
        $result2 = pdo_fetch($sql, $params);
        $kouling_count=$result2['count'];
        $sql = 'update '.tablename('pzh_kouling4') .' set `count` = '.strval($kouling_count+1).'  WHERE `uniacid` = '.$_W['uniacid'].' and `kouling` = \''.$content.'\''; 
        pdo_query($sql);
  
        if (!$result->return_code) {
         	 return $this->respText('系统错误，请稍后再试');
         }
             //领取失败
			if(!empty($this->module['config']['kouling']['errorMsg']))
			{
				$errorMsg = $this->module['config']['kouling']['errorMsg'];
			}
			else 
			{
				$errorMsg=$result->return_msg;
				// $errorMsg = '123';
			}
			 return $this->respText($errorMsg);
		}
		else
		{
			//发送成功  
			$this ->module['config']['kouling']['maxRedCount']  =$maxRedCount - 1 ;
			$this->saveSettings($this->module['config']);
			$sql = 'update '.tablename('pzh_packet2') .'   set `redPackCount` = ' .strval($account['redPackCount']+1) . 
			' ,`lastTime`= ' . strval($_W['timestamp']). ' WHERE `uniacid` = '.strval($_W['uniacid']).' and `type` = \'kouling\' and `openid` = \''.$re_openid.'\'  ';
			$result2 = pdo_query($sql);
            //随机红包记录数据
			$time = date('Y-m-d H:i:s',time());
			$sql = 'INSERT INTO'.tablename('pzh_record') .' (`uniacid`,`openid`,`moneyCount`,`time`,`type`,`state`) values ('.
				strval($_W['uniacid']).',\''.$re_openid.'\','.strval($total_amount/100.0).',\''.$time.'\',\'kouling\',\'success\')'; 
			pdo_query($sql);

              $sql = 'update  '.tablename('pzh_excelinfo').' set `state` = \'已领取\' , `receivetime` = \''.$time.'\' where `uniacid` = '.strval($_W['uniacid']).' and `idcard` = \''.$content.'\''; 
			pdo_query($sql);
			
			if(!empty($this->module['config']['kouling']['successMsg']))
			{
				$successMsg = $this->module['config']['kouling']['successMsg'];
			}
			else 
			{
				$successMsg = '恭喜你获得一个红包~';
			}
			// if(!empty($this->module['config']['kouling']['sharemoney'])&&$this->module['config']['kouling']['sharemoney']>=100&&$re_openid!=$beginer&&!empty($beginer))
			// {
			// 	//奖励分享者
   //           $result=  $packet->pay($beginer,$nick_name,$send_name,$this->module['config']['kouling']['sharemoney'],'您的好友成功猜对了口令~',$act_name,$remark,$this->module['config']['mchid'],$this->module['config']['appid'],$this->module['config']['password']);
			// 	if($result->return_code == 'FAIL' || $result ==  'fail')
			// 	{

			// 	}
			// 	else
			// 	{
			// 		$sql = 'INSERT INTO'.tablename('pzh_record') .' (`uniacid`,`openid`,`moneyCount`,`time`,`type`,`state`) values ('.
			// 	  strval($_W['uniacid']).',\''.$beginer.'\','.strval($total_amount/100.0).',\''.$time.'\',\'kouling\',\'share\')'; 
			//       pdo_query($sql);
			// 	}
			// }

			   return $this->respText($successMsg);
	    	
		}
	}
	//初始化数据库
	function init()
	{
	      //查看关注数据库是否存在
		global $_W;
      
           $this->module['config']['kouling']['init']='yes';
           $this->saveSettings($this->module['config']);
     
		
		$tableName = $_W['config']['db']['tablepre'].'pzh_packet2';
		$exists= pdo_tableexists('pzh_packet2');
		if(!$exists)
		{
			$sql = 'CREATE TABLE '.$tableName.' (
				`uniacid` int(10)  NOT NULL,
				`openid` varchar(35) NOT NULL,
				`redPackCount` int(10) NOT NULL,
				`lastTime` int(50) ,
				`type`  varchar(50),
				`remark`   varchar(50)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;';

		pdo_run($sql);
		}
		$tableName = $_W['config']['db']['tablepre'].'pzh_record';
		$exists= pdo_tableexists('pzh_record');
		if(!$exists)
		{
			$sql = 'CREATE TABLE '.$tableName.' (
				`uniacid` int(10)  NOT NULL,
				`openid` varchar(35) NOT NULL,
				`moneyCount` float(10) NOT NULL,
				`time` varchar(50) ,
				`type`  varchar(50),
				`state`  varchar(50),
				`remark`   varchar(50)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;';

		pdo_run($sql);
		}
			$tableName = $_W['config']['db']['tablepre'].'pzh_kouling4';
		$exists= pdo_tableexists('pzh_kouling4');
		if(!$exists)
		{
			$sql = 'CREATE TABLE '.$tableName.' (
				`uniacid` int(10)  NOT NULL,
				`acid` int(10) NOT NULL,
				`moneyCount` varchar(50),
				`kouling` varchar(50) ,
				`createtime` varchar(50) ,
				`state`  varchar(50),
				`usetime` varchar(50),
				`count`   int(10),
				`beginer` varchar(50),
				`remark`   varchar(50)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;';

		pdo_run($sql);
		}
		$tableName = $_W['config']['db']['tablepre'].'pzh_sharekouling2';
		$exists= pdo_tableexists('pzh_sharekouling2');
		if(!$exists)
		{
			$sql = 'CREATE TABLE '.$tableName.' (
				`uniacid` int(10)  NOT NULL,
				`acid` int(10) NOT NULL,
				`kouling` varchar(250),
				`openid` varchar(50),
				`createtime` varchar(50) ,
				`remark`   varchar(50)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
		pdo_run($sql);
		}

		$tableName = $_W['config']['db']['tablepre'].'pzh_excelinfo';
		$exists= pdo_tableexists('pzh_excelinfo');
		if(!$exists)
		{
			$sql = 'CREATE TABLE '.$tableName.' (
				`uniacid` int(10)  NOT NULL,
				`createtime` varchar(35) ,
				`username` varchar(35) ,
				`userphone` varchar(35) ,
				`idcard` varchar(35) ,
				`money` varchar(35) ,
				`openacounttime`  varchar(50),
				`state`  varchar(50),
				`receivetime` varchar(35) ,
				`remark`   varchar(50)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;';

		pdo_run($sql);
		}
	}
}