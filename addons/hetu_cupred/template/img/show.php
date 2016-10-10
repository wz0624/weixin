<?php

require_once "jssdk.php";
$jssdk = new JSSDK("wx4a03901470498f7f", "6ddf6319f20cbd7ffd9b3386cb1086cf");		/*认证号开发者凭证*/
$signPackage = $jssdk->GetSignPackage();
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">
<meta name="viewport" content="width=320.1,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">
<meta name="apple-mobile-web-app-capable" content="yes">
<title>恭喜发财，大吉大利！</title>
<style>
body{
	font:normal 14px Tahoma,"Lucida Grande",Verdana,"Microsoft Yahei",STXihei,hei;
	background:#080808;
	background-size:100%;
	color:#353535;
}
body,div,a,ul,li,p,span{
	padding:0;
	margin:0;
}
a{
	text-decoration:none;
	color:#000;
}
.first-box{
	width:81.67%;
	position:absolute;
	left:9.165%;
	top:10%;
}
img.body{
	width:100%;
}
.user-headimg{
	position: absolute;
	border-radius: 10000px;
	left: 50%;
	top: 9.75%;
	width: 20.65%;
	margin-left: -10.5%;
}
.hot-click{
	position: absolute;
	border-radius: 10000px;
	left: 50%;
	top: 61%;
	width: 36%;
	height: 26%;
	margin-left: -18%;
}
.nickname{
	width: 100%;
	position: absolute;
	text-align: center;
	color: #FFF;
	top: 28.5%;
	font-size:1.15em;
}

.second-box{
	width:100%;
	height:100%;
	position:absolute;
	overflow:hidden;
	top:0;
	left:0;
}
.second-wrapper{
	overflow:hidden;
	width:auto;
	position:relative;
}
.second-bg{
	width:100%;
}
.second-headimg{
	position: absolute;
	border-radius: 10000px;
	left: 50%;
	top: 13%;
	width: 23%;
	margin-left: -11.5%;
}
.second-nickname{
	width: 100%;
	position: absolute;
	text-align: center;
	top: 26.8%;
	font-size:1.25em;
}
.money{
	position:absolute;
	text-align:center;
	width:100%;
	top:37%;
	color:#000;
}
.money-number{
	font-size:4.5em;
	padding-left:0.25em;
}
.yuan{
	font-size:1.1em;
	position:relative;
	top:-1px;
}




.tips-box{
	position:absolute;
	top:50%;
	width:100%;
	height:150px;
	margin-top:-50px;
	background:#fff;
	overflow:hidden;
	z-index:9999;
	text-align:center;
}
.explain{
	font-size:18px;
	padding:5px 8px;
	margin-top:4px;
	margin-bottom:8px;
}
.zhenggu-btn{
	background-color:#FE8007;
	color:#fff;
	padding:6px 14px;
	border-radius:3px;
}
.bg-cover{
	width:100%;
	height:100%;
	top:0;
	left:0;
	position:fixed;
	background:#000;
	opacity:0.6;
	z-index:999;
}


</style>
<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?c809a982796e417d8e31fb716969ebb2";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">
  wx.config({
    debug: false,
    appId: '<?php echo $signPackage["appId"];?>',
    timestamp: <?php echo $signPackage["timestamp"];?>,
    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
    signature: '<?php echo $signPackage["signature"];?>',
    jsApiList: [
      // 所有要调用的 API 都要加到这个列表中

	  'onMenuShareTimeline',
	  'onMenuShareAppMessage',

    ]
  });
wx.ready(function(){
	wx.onMenuShareTimeline({
			title: '恭喜发财，大吉大利！', // 分享标题
			link: window.location.href, // 分享链接
			imgUrl: 'http://m.ningj8818.com/zhengguhb/img/hongbao_icon.jpg', // 分享图标
			success: function () { 
					// 用户确认分享后执行的回调函数

			},
			cancel: function () { 
					// 用户取消分享后执行的回调函数

			}
	});
	wx.onMenuShareAppMessage({
			title: '恭喜发财，大吉大利！', // 分享标题
			desc: '点击领取红包', // 分享描述
			link: window.location.href, // 分享链接
			imgUrl: 'http://m.ningj8818.com/zhengguhb/img/hongbao_icon.jpg', // 分享图标
			type: '', // 分享类型,music、video或link，不填默认为link
			dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
			success: function () { 
					// 用户确认分享后执行的回调函数
			},
			cancel: function () { 
					// 用户取消分享后执行的回调函数
			}
	});
});
</script>
<script src="js/jquery.min.js"></script>
<script type="text/javascript">
var fools_show=function(){
	$(".bg-cover").show();
	$(".tips-box").show();
};
$(function(){
	$(".hot-click").click(function(){
		$(".first-box").hide();
		$(".second-box").show();
				var t=setTimeout('fools_show()',1750); 
			});
			$(".second-box").click(fools_show);
	});

</script>
</head>

<body>
<div style="display:none">
	<img src="img/hongbao_icon.jpg">
</div>


<div class="first-box">
	<img class="body" src="img/fools_1.png">
  <img class="user-headimg" src="<?php echo $_GET["usimg"];?>">
  <div class="hot-click"></div>
  <div class="nickname"><?php echo $_GET["usname"];?></div>
</div>
<div class="second-box" style="display:none">
	<div class="second-wrapper">
  	<img class="second-bg" src="img/fools_2.png">
  	<img class="second-headimg" src="<?php echo $_GET["usimg"];?>">
  	<div class="second-nickname"><?php echo $_GET["usname"];?>的红包</div>
    <div class="money">
    	<span class="money-number"><?php echo $_GET["money"];?></span>
      <span class="yuan">元</span>
    </div>
  </div>
</div>
<div class="tips-box" style="display:none">
	<div class="explain">别看了，没有天上掉下来的馅饼！<br>快去整回别人吧→_→
  </div>
  <a class="zhenggu-btn" href="http://mp.weixin.qq.com/s?__biz=MzA3NTQ1MzQ2MA==&mid=204436664&idx=1&sn=6f4375796d2f52f0e1279d6cc12a50ef#rd">我也要整蛊</a>
  <br/>
  <br/>
  <br/>
  <a class="zhenggu-btn" href="http://mp.weixin.qq.com/s?__biz=MjM5ODcwMjIzMA==&mid=202658927&idx=1&sn=8e01cf19e5607181d023ac3f29f62d9d#rd">Mr.V源码支持</a>
</div>
<div class="bg-cover" style="display:none"></div>
<div style="display:none"><script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1254723039'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s11.cnzz.com/stat.php%3Fid%3D1254723039' type='text/javascript'%3E%3C/script%3E"));</script></div>
</body>
</html>