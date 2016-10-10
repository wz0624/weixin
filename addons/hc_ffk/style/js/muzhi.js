var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cspan id='cnzz_stat_icon_1255385093'%3E%3C/span%3E%3Cscript  src='" + cnzz_protocol + "s95.cnzz.com/z_stat.php%3Fid%3D1255385093' type='text/javascript'%3E%3C/script%3E"));
var _czc = _czc || [];
_czc.push(["_setAccount", "1255385093"]);
$('#cnzz_stat_icon_1255385093').hide();

$("body").append("<div class='shareDiv'></div>");
$(".shareDiv").on("click",function(){
	$(".shareDiv").hide();
});

var MUZHI = (function(muzhi){
	muzhi.gameDetail={};//游戏详细信息
	muzhi.code = null;//商户唯一编号
	muzhi.endPic = "../addons/hc_ffk/style/images/showimg.png";
	function getCode(){
	  if(this.code==null){
	   var url = location.search;
	   var reqParam = {};
	   if (url.indexOf("?") != -1) {
	      var _url = url.substr(1);
	      var params = _url.split("&");
	      for(var i = 0; i < params.length; i ++) {
	         reqParam[params[i].split("=")[0]]=unescape(params[i].split("=")[1]);
	      }
	   }//reParam end
	   this.code = reqParam['code'] ||'demo';
	 }
	 return this.code;
	}
    //请求游戏信息
	muzhi.get = function(){
		var code = getCode();
		if(!code){
			var def = $.Deferred();
			def.resolve();
			return def.promise();
		}else{
			return $.get( "http://www.muzhibuluo.com/api/gameDetail/"+code).promise();
		}
		
	};
	//设置游戏信息
	muzhi.setGameDetail = function(obj){
		if(obj&&obj.code=="00000"){
			muzhi.gameDetail = obj.data;
			document.title = obj.data.title;				
		}else{
			console.log("请求失败");
		}	
	}

    //获取游戏图片
	muzhi.getGameImg = function(id,src){
		var src = src;
		if(this.gameDetail.images){
			var images = this.gameDetail.images;
			for(var i in images){
				if(images[i].imageCode==id 
					&& typeof images[i].imageUrl =='string' 
					&& images[i].imageUrl!=''){
					src = images[i].imageUrl;
				}
			}
		}
		return src;
	}

	//结束游戏
	muzhi.endGame = function(score, againFunction, shareFunction, closeFuntion,defaultTxt){
		//alert(score);
		sendscore(score);
		if(!$("#muzhiEndDiv").length){//加入弹出div
			$("body").append("<div class='muzhiEndDivOut' id = 'muzhiEndDiv'><div class = 'muzhiEndDiv'>"+
		            "<div class = 'muzhiEndHead'>"+
		                "<div class='muzhiEndLogo'></div>"+
		                "<div class='muzhiScore' id='muzhiScore'></div>"+
		                "<div class='muzhiEndX' id='muzhiEndX'></div>"+
		            "</div>"+		            
		            "<div class = 'muzhiPrizeDiv' id = 'muzhiPrizeDiv'></div>"+
		            "<div class='muzhiAdvDiv' id='muzhiAdvDiv'></div>"+
		            "<div class='muzhiBtn'>"+
		                "<span><a class='muzhiShare' id='muzhiShare'>游戏分享</a></span>"+
		                "<span><a class='muzhiAgain' id='muzhiAgain'>再玩一次</a></span>"+
		            "</div>"+
		        "</div></div>");
				$(".muzhiAdvDiv").append('<img src="'+this.endPic+'" alt="showing" class="muzhishowing">');
			//$("body").append('<div class="popTip"></div>');
		}
		$('#muzhiScore').html(score+"<span>分</span>");
		
		//分享按钮
		$( "#muzhiShare" ).bind( "click", function() {
			_czc.push(['_trackEvent', code, 'shareBtn', '', '']);

			$(".shareDiv").show();
            //if (typeof shareFunction === "function") {
				//shareFunction();
            //}
		});
		//重玩按钮
		$( "#muzhiAgain" ).bind( "click", function() {
			_czc.push(['_trackEvent', code, 'againBtn', '', '']);
			$("#muzhiEndDiv").hide();
			if (typeof againFunction === "function") {
				againFunction();
            }
		});
		//关闭按钮
		$( "#muzhiEndX" ).bind( "click", function() {
			_czc.push(['_trackEvent', code, 'closeBtn', '', '']);
			$("#muzhiEndDiv").hide();
			if (typeof closeFuntion === "function") {
				closeFuntion();
            }
		});
		
		
		$("#muzhiEndDiv").show();
	}

	return muzhi;
})(MUZHI||{});