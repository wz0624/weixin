(function($) {
	$.extend({
		tipsBox: function(options) {
			options = $.extend({
				obj: null,
				//jq对象，要在那个html标签上显示
				toobj: null,
				str: "+1",
				//字符串，要显示的内容;也可以传一段html，如: "<b style='font-family:Microsoft YaHei;'>+1</b>"
				startSize: "12px",
				//动画开始的文字大小
				endSize: "18px",
				//动画结束的文字大小
				interval: 1000,
				color: "red",
				//文字颜色
				callback: function() {} //回调函数
			},
			options);
			$("body").append("<span class='animit_num'>" + options.str + "</span>");
			
			var box = $(".animit_num");
			var left = 0,top= 0,to_top =0,to_left =0;
			
			if(options.obj !=null){
				left = options.obj.offset().left;
				top = options.obj.offset().top;
			}
				
			if(options.toobj !=null){
				to_top = options.toobj.offset().top;
				to_left = options.toobj.offset().left;
			}
			box.css({
				"position": "absolute",
				"left": left + "px",
				"top": top + "px",
				"z-index": 9999,
				"font-size": options.startSize,
				"line-height": options.endSize,
				"color": options.color
			});

			box.animate({
				"font-size": options.endSize,
				"opacity": "0",
				"top": to_top + "px",
				"left": to_left + "px"
			},
			options.interval,
			function() {
				box.remove();
				options.callback();
			});
			
		}
	});
})(jQuery);
function niceIn(prop) {
	prop.find('i').addClass('niceIn');
	setTimeout(function() {
		prop.find('i').removeClass('niceIn');
	},
	1000);
}
/**
 * ajax
 * */
(function (window,$,Meepo_tools) {
	Meepo_tools.post = function(d,data,call){
		var i = Meepo_tools.querystring('i');
		var j = Meepo_tools.querystring('j');
		
		var url = './index.php?i='+i+'&j='+j+'&c=entry&do='+d+'&m=meepo_voteplatform';
		$.post(url,data,call);
	}
	Meepo_tools.ajax = function(ajaxInfo){
		var i = Meepo_tools.querystring('i');
		var j = Meepo_tools.querystring('j');
		
		var url = './index.php?i='+i+'&j='+j+'&c=entry&do='+ajaxInfo.do_it+'&m=meepo_voteplatform';
		var defaultInfo = {
            type: "GET",                       
            dataType: 'JSON',      
            cache: false,
            urlPata: {},
            formPata: {},
            error: function() {
            },  
			beforeSend:function(){
			},
			complete:function(){
			},
            success: function() {
            } //成功后显示debug信息。也可以增加自己的处理程序
        };

        //补全ajaxInfo
        if (typeof ajaxInfo.dataType == "undefined") {
            ajaxInfo.dataType = defaultInfo.dataType;
        }
        
        if (typeof ajaxInfo.formPata == "undefined") {
            ajaxInfo.type = "GET";
        } else {
            if (ajaxInfo.dataType == "JSON") {
                ajaxInfo.type = "POST";
            } else {    //get或者jsonp
                ajaxInfo.type = "POST";
            }
            ajaxInfo.data = ajaxInfo.formPata;
        }

        

		$.ajax({
            type: ajaxInfo.type,
            dataType: ajaxInfo.dataType,
            cache: ajaxInfo.cache,
            xhrFields: {
                //允许跨域访问时添加cookie
                withCredentials: true
            },
            url: url,  
            data: ajaxInfo.data,
            beforeSend:function(){
				ajaxInfo.beforesend();
			},
            complete:function(){
				ajaxInfo.complete();
			},
            success: function (data) {
                  ajaxInfo.success(data);
            },
			error: function() { 
				ajaxInfo.error();
                alert("提交" + ajaxInfo.title + "的时候发生错误！"); 
            }
        });
    

	
	}
	Meepo_tools.querystring = function(name){ 
		var result = location.search.match(new RegExp("[\?\&]" + name+ "=([^\&]+)","i")); 
		if (result == null || result.length < 1){ 
			return "";
		}
		return result[1]; 
	}
    window.Meepo_tools = Meepo_tools;
    
    typeof define === 'function' && define('Meepo_tools',[],function(){return Meepo_tools});
})(window,window.jQuery,window.Meepo_tools || {});

