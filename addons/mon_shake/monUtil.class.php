<?php

/**
 * Class MonUtil
 * 工具类
 */
class MonUtil
{

	public static $DEBUG = false;

	public static $IMG_TOP_BANNER = 1;

	public static $IMG_SECTION1_BG = 2;
	public static $IMG_SECTION1_LAYER5_1 = 3;
	public static $IMG_SECTION1_LAYER3_2 = 4;
	public static $IMG_SECTION1_LAYER6_3 = 5;
	public static $IMG_SECTION1_LAYER7_4 = 6;
	public static $IMG_SECTION1_LAYER9_5 = 7;

	public static $IMG_SECTION2_BG = 8;
	public static $IMG_SECTION2_LAYER16_1 = 9;
	public static $IMG_SECTION2_LAYER18_2 = 10;
	public static $IMG_SECTION2_LAYER31_3 = 11;
	public static $IMG_SECTION2_LAYER17_4 = 12;
	public static $IMG_GOOD_DLG_BG = 13;
	public static $IMG_BUY_BTN_URL = 14;

	public static $IMG_SECTION3_BG = 15;
	public static $IMG_SECTION3_LAYER15_1 = 16;
	public static $IMG_SECTION3_LAYER11_2 = 17;
	public static $IMG_SECTION3_LAYER14_3 = 18;

	/**
	 * author: codeMonkey QQ:631872807
	 * @param $url
	 * @return string
	 */
	public static function str_murl($url)
	{
		global $_W;

		return $_W['siteroot'] . 'app' . str_replace('./', '/', $url);

	}


	/**
	 * author: codeMonkey QQ:631872807
	 * 检查手机
	 */
	public static function  checkmobile()
	{

		if (!MonUtil::$DEBUG) {
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
			if (strpos($user_agent, 'MicroMessenger') === false) {
				echo "本页面仅支持微信访问!非微信浏览器禁止浏览!";
				exit();
			}
		}


	}

	/**
	 * author:codeMonkey QQ 631872807
	 * 获取哟规划信息
	 * @return array|mixed|stdClass
	 */
	public static function  getClientCookieUserInfo($cookieKey)
	{
		global $_GPC;
		$session = json_decode(base64_decode($_GPC[$cookieKey]), true);
		return $session;

	}


	/**
	 * author: codeMonkey QQ:631872807
	 * @param $openid
	 * @param $accessToken
	 * @return unknown
	 * cookie保存用户信息
	 */
	public static function setClientCookieUserInfo($userInfo = array(), $cookieKey)
	{

		if (!empty($userInfo) && !empty($userInfo['openid'])) {
			$cookie = array();
			foreach ($userInfo as $key => $value)
				$cookie[$key] = $value;
			$session = base64_encode(json_encode($cookie));

			isetcookie($cookieKey, $session, 1 * 3600 * 1);

		} else {

			message("获取用户信息错误");
		}


	}


	public static function getpicurl($url)
	{
		global $_W;
		return $_W ['attachurl'] . $url;

	}


	public static function  emtpyMsg($obj, $msg)
	{
		if (empty($obj)) {
			message($msg);
		}
	}

	public static function defaultImg($img_type,$shake='')
	{
		switch ($img_type) {
			//首页
			case MonUtil::$IMG_TOP_BANNER:
				if (!empty($shake)&&!empty($shake['top_banner'])) {
					return MonUtil::getpicurl($shake['top_banner']);
				}
				$img_name = "14309075753903.png";
				break;
			case MonUtil::$IMG_SECTION1_BG:
				if (!empty($shake) && !empty($shake['section1_bg'])) {
					return MonUtil::getpicurl($shake['section1_bg']);
				}
				$img_name = "14309073223522.png";
				break;
			case MonUtil::$IMG_SECTION1_LAYER5_1:
				if (!empty($shake) && !empty($shake['section1_layer5_1'])) {
					return MonUtil::getpicurl($shake['section1_layer5_1']);
				}
				$img_name = "14308134964560.png";
				break;
			case MonUtil::$IMG_SECTION1_LAYER3_2:
				if (!empty($shake) && !empty($shake['section1_layer3_2'])) {
					return MonUtil::getpicurl($shake['section1_layer3_2']);
				}
				$img_name = "14308134933519.png";
				break;
			case MonUtil::$IMG_SECTION1_LAYER6_3:
				if (!empty($shake) && !empty($shake['section1_layer6_3'])) {
					return MonUtil::getpicurl($shake['section1_layer6_3']);
				}
				$img_name = "14308135063561.png";
				break;
			case MonUtil::$IMG_SECTION1_LAYER7_4:
				if (!empty($shake) && !empty($shake['section1_layer7_4'])) {
					return MonUtil::getpicurl($shake['section1_layer7_4']);
				}
				$img_name = "14308135097500.png";
				break;
			case MonUtil::$IMG_SECTION1_LAYER9_5:
				if (!empty($shake) && !empty($shake['section1_layer9_5'])) {
					return MonUtil::getpicurl($shake['section1_layer9_5']);
				}
				$img_name = "14308135129754.png";
				break;
			//要一摇
			case MonUtil::$IMG_SECTION2_BG:
				if (!empty($shake) && !empty($shake['section2_bg'])) {
					return MonUtil::getpicurl($shake['section2_bg']);
				}
				$img_name = "14309073259702.png";
				break;
			case MonUtil::$IMG_SECTION2_LAYER16_1:
				if (!empty($shake) && !empty($shake['section2_layer16_1'])) {
					return MonUtil::getpicurl($shake['section2_layer16_1']);
				}
				$img_name = "14308135373386.png";
				break;
			case MonUtil::$IMG_SECTION2_LAYER18_2:
				if (!empty($shake) && !empty($shake['section2_layer18_2'])) {
					return MonUtil::getpicurl($shake['section2_layer18_2']);
				}
				$img_name = "14308135459137.png";
				break;
			case MonUtil::$IMG_SECTION2_LAYER31_3:
				if (!empty($shake) && !empty($shake['section2_layer31_3'])) {
					return MonUtil::getpicurl($shake['section2_layer31_3']);
				}
				$img_name = "14308136007866.png";
				break;
			case MonUtil::$IMG_SECTION2_LAYER17_4:
				if (!empty($shake) && !empty($shake['section2_layer17_4'])) {
					return MonUtil::getpicurl($shake['section2_layer17_4']);
				}
				$img_name = "14308135422880.png";
				break;
			case MonUtil::$IMG_GOOD_DLG_BG:
				if (!empty($shake) && !empty($shake['good_dlg_bg'])) {
					return MonUtil::getpicurl($shake['good_dlg_bg']);
				}
				$img_name = "14308135568314.png";
				break;
			case MonUtil::$IMG_BUY_BTN_URL:
				if (!empty($shake) && !empty($shake['buy_btn_url'])) {
					return MonUtil::getpicurl($shake['buy_btn_url']);
				}
				$img_name = "14308136061480.png";
				break;
			//尾页
			case MonUtil::$IMG_SECTION3_BG:
				if (!empty($shake) && !empty($shake['section3_bg'])) {
					return MonUtil::getpicurl($shake['section3_bg']);
				}
				$img_name = "14309073223522.png";
				break;
			case MonUtil::$IMG_SECTION3_LAYER15_1:
				if (!empty($shake) && !empty($shake['section3_layer15_1'])) {
					return MonUtil::getpicurl($shake['section3_layer15_1']);
				}
				$img_name = "14308135343285.png";
				break;
			case MonUtil::$IMG_SECTION3_LAYER11_2:
				if (!empty($shake) && !empty($shake['section3_layer11_2'])) {
					return MonUtil::getpicurl($shake['section3_layer11_2']);
				}
				$img_name = "14308135186754.png";
				break;
			case MonUtil::$IMG_SECTION3_LAYER14_3:
				if (!empty($shake) && !empty($shake['section3_layer14_3'])) {
					return MonUtil::getpicurl($shake['section3_layer14_3']);
				}
				$img_name = "14308135317650.png";
		}

		return "../addons/mon_shake/images/" . $img_name;

	}


}