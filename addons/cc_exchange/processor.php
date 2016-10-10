<?php
/**
 * 实时汇率查询模块处理程序
 *
 * @author cc
 * @url http://www.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');

class Cc_exchangeModuleProcessor extends WeModuleProcessor {
	public function respond() {
		if(!$this->inContext) {
			$reply = '请输入需要查询币种代码'."\n\n".'a:美元'."\n".'b:欧元'."\n".'c:港币'."\n".'d:日元'."\n".'e:英镑'."\n".'f:澳大利亚元'."\n".'g:加拿大元'."\n".'h:泰国铢'."\n".'i:新加坡元'."\n".'j:挪威克朗'."\n".'k:林吉特'."\n".'l:澳门元'."\n".'m:韩国元'."\n".'n:瑞士法郎'."\n".'o:丹麦克朗'."\n".'p:瑞典克朗'."\n".'q:卢布'."\n".'r:新西兰元'."\n".'s:菲律宾比索'."\n".'t:新台币'."\n\n".'例:输入 a 将查询 美元实时汇率';
			$this->beginContext();
		} else {
			global $_W;
			$role = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t');
			$b = strtolower($this->message['content']); 
			$type = substr($b,0,1);
			if (in_array($type, $role)) {
				$appKey = $this->module['config']['exchange_appKey'];
				$url = "http://web.juhe.cn:8080/finance/exchange/rmbquot?key=$appKey";
				$express = new Express();
				$arr = $express->getcontent($url);
				if ($arr['resultcode']==200) {
					$all = $arr['result'][0];
					$i = 0;
					foreach ($all as $k => $v) {
						$all[$k]['key'] = $role[$i];
						$i++;
					}
					foreach ($all as $k => $v) {
						if($type == $v['key']){
							$reply = "=== ".$v['name']."实时汇率 ===\n".
									"汇买价: ".$v['fBuyPri']."\n".
									"钞买价: ".$v['mBuyPri']."\n".
									"钞/汇卖价: ".$v['fSellPri']."\n".
									"中间价: ".$v['bankConversionPri']."\n\n".
									"更新时间: ".$v['date']." ".$v['time'];
							// $reply = json_encode($v);
						}
					}
					$this->endContext();
				}else{
					$reply = "暂时无法查询,请重新输入币种代码";
				}
			}else{
				$reply = '请输入正确的币种代码!'."\n\n";
				$reply .= '请输入需要查询币种代码'."\n\n".'a:美元'."\n".'b:欧元'."\n".'c:港币'."\n".'d:日元'."\n".'e:英镑'."\n".'f:澳大利亚元'."\n".'g:加拿大元'."\n".'h:泰国铢'."\n".'i:新加坡元'."\n".'j:挪威克朗'."\n".'k:林吉特'."\n".'l:澳门元'."\n".'m:韩国元'."\n".'n:瑞士法郎'."\n".'o:丹麦克朗'."\n".'p:瑞典克朗'."\n".'q:卢布'."\n".'r:新西兰元'."\n".'s:菲律宾比索'."\n".'t:新台币'."\n\n".'例:输入 a 将查询 美元实时汇率';
			}
			
		}
 
		return $this->respText($reply);
	}
}


class Express
{
    /*
     * 网页内容获取方法
    */
    public function getcontent($url)
    {
        if (function_exists("file_get_contents")) {
            $file_contents = file_get_contents($url);
        } else {
            $ch      = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $file_contents = curl_exec($ch);
            curl_close($ch);
        }
        $file_contents   = json_decode($file_contents, true);
        return $file_contents;
    }
}