<?php
/**
 * 实时货币兑换模块处理程序
 *
 * @author Anson
 * @url http://www.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');

class Anson_exchange2ModuleProcessor extends WeModuleProcessor {
	public function respond() {
		$arr = array(
				array("name"=>"人民币","code"=>"CNY"),array("name"=>"美元","code"=>"USD"),array("name"=>"日元","code"=>"JPY"),array("name"=>"欧元","code"=>"EUR"),array("name"=>"英镑","code"=>"GBP"),array("name"=>"韩元","code"=>"KRW"),array("name"=>"港币","code"=>"HKD"),array("name"=>"澳大利亚元","code"=>"AUD"),array("name"=>"加拿大元","code"=>"CAD"),array("name"=>"菲律宾元","code"=>"PHP"),array("name"=>"阿尔及利亚第纳尔","code"=>"DZD"),array("name"=>"阿根廷比索","code"=>"ARS"),array("name"=>"阿联酋迪拉姆","code"=>"AED"),array("name"=>"阿曼里亚尔","code"=>"OMR"),array("name"=>"澳门元","code"=>"MOP"),array("name"=>"巴林第纳尔","code"=>"BHD"),array("name"=>"保加利亚列弗","code"=>"BGN"),array("name"=>"巴西雷亚尔","code"=>"BRL"),array("name"=>"冰岛克朗","code"=>"ISK"),array("name"=>"波兰兹罗提","code"=>"PLN"),array("name"=>"丹麦克朗","code"=>"DKK"),array("name"=>"俄罗斯卢布","code"=>"RUB"),array("name"=>"菲律宾比索","code"=>"PHP"),array("name"=>"哥伦比亚比索","code"=>"COP"),array("name"=>"捷克克朗","code"=>"CZK"),array("name"=>"卡塔尔里亚尔","code"=>"QAR"),array("name"=>"克罗地亚库纳","code"=>"HRK"),array("name"=>"肯尼亚先令","code"=>"KES"),array("name"=>"科威特第纳尔","code"=>"KWD"),array("name"=>"老挝基普","code"=>"LAK"),array("name"=>"黎巴嫩镑","code"=>"LBP"),array("name"=>"林吉特","code"=>"MYR"),array("name"=>"南非兰特","code"=>"ZAR"),array("name"=>"挪威克朗","code"=>"NOK"),array("name"=>"瑞典克朗","code"=>"SEK"),array("name"=>"瑞士法郎","code"=>"CHF"),array("name"=>"沙特里亚尔","code"=>"SAR"),array("name"=>"斯里兰卡卢比","code"=>"LKR"),array("name"=>"泰国铢","code"=>"THB"),array("name"=>"坦桑尼亚先令","code"=>"TZS"),array("name"=>"新加坡元","code"=>"SGD"),array("name"=>"新台币","code"=>"TWD"),array("name"=>"新西兰元","code"=>"NZD"),array("name"=>"匈牙利福林","code"=>"HUF"),array("name"=>"印度卢比","code"=>"INR"),array("name"=>"约旦第纳尔","code"=>"JOD"),array("name"=>"越南盾","code"=>"VND"),array("name"=>"智利比索","code"=>"CLP")
			);
		if(!$this->inContext) {
			//************货币列表************
	        foreach ($arr as $k => $v) {
	        	$t = $k+1;
	        	$reply .= $t.":".$v['name']."\n";
	        }
	        $reply .= "\n\n请根据上面的对照表输入 要兑换的货币id-金额-需兑换成的货币id,例如:\n".
	        			"1-10-2\n".
	        			"则计算 10元人民币能兑换成多少美元 ";
			$this->beginContext();
		} else {
			$b = strtolower($this->message['content']); 
			$b = explode("-", $b);
			if (isset($b[0]) && isset($b[1]) && isset($b[2])) {
				if (is_numeric($b[0]) && is_numeric($b[1]) && is_numeric($b[2])) {
					$appKey = $this->module['config']['exchange2_appKey'];
			        $url = "http://op.juhe.cn/onebox/exchange/currency";
					$params = array(
					      "from" => $arr[$b[0]-1]['code'],	//转换汇率前的货币代码
					      "to" => $arr[$b[2]-1]['code'],	//转换汇率成的货币代码
					      "key" => $appKey,					//应用APPKEY(应用详细页查询)
					);
					$paramstring = http_build_query($params);
					$content = $this->juhecurl($url,$paramstring);
					$result = json_decode($content,true);
					if($result){
					    if($result['error_code']=='0'){
					        $result1 = $result['result'][0];
					        $aaa = $b[1]*$result1['exchange'];
					        $reply = $b[1]." ".$result1['currencyF_Name']." = ".$aaa." ".$result1['currencyT_Name']."\n\n".
					        			"数据仅供参考，交易时以银行柜台成交价为准 更新时间:".$result1['updateTime'];
							$this->endContext();
					    }else{
	        				$reply = $result['error_code'].":".$result['reason'].",请重试.";
					    }
					}else{
	    				$reply = "请求失败,请重试";
					}
				}else{
					$reply = "您的输入有误, 请按照要求输入:要兑换的货币id-金额-需兑换成的货币id.例如: \n".
			        			"1-10-2 \n".
			        			"则计算 10元人民币能兑换成多少美元 ";
				}
			}else{
			    $reply = "您的输入有误, 请按照要求输入:要兑换的货币id-金额-需兑换成的货币id.例如: \n".
			        			"1-10-2 \n".
			        			"则计算 10元人民币能兑换成多少美元 ";
			}
			
		}
		// $reply = '123123';
		return $this->respText($reply);
	}





	/**
	 * 请求接口返回内容
	 * @param  string $url [请求的URL地址]
	 * @param  string $params [请求的参数]
	 * @param  int $ipost [是否采用POST形式]
	 * @return  string
	 */
	function juhecurl($url,$params=false,$ispost=0){
	    $httpInfo = array();
	    $ch = curl_init();
	 
	    curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
	    curl_setopt( $ch, CURLOPT_USERAGENT , 'JuheData' );
	    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 60 );
	    curl_setopt( $ch, CURLOPT_TIMEOUT , 60);
	    curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    if( $ispost )
	    {
	        curl_setopt( $ch , CURLOPT_POST , true );
	        curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
	        curl_setopt( $ch , CURLOPT_URL , $url );
	    }
	    else
	    {
	        if($params){
	            curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
	        }else{
	            curl_setopt( $ch , CURLOPT_URL , $url);
	        }
	    }
	    $response = curl_exec( $ch );
	    if ($response === FALSE) {
	        //echo "cURL Error: " . curl_error($ch);
	        return false;
	    }
	    $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
	    $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
	    curl_close( $ch );
	    return $response;
	}
}