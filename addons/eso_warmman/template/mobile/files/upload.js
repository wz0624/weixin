var stage = null;
var imagelayer = null;
var uploadimage = null;
var filepath = "";
var lastDist = 0;

$(document).ready(function(){
    var suneve = $(".loading_alert");
    if (suneve.length) {
        var ww = $(window).width() * 0.4,
            wh = $(window).width() * 0.4 * 0.937,
            wt = ($(window).height() - wh) / 2,
            wl = ($(window).width() - ww) / 2
        suneve.find(".sun").css({
            width:ww,
            height:wh,
            top:wt,
            left:wl
        });
        $(".loading_alert .nuan").css({
            width:ww,
            height:wh,
            top:wt,
            left:wl
        });
        $(".loading_alert .tips").css({
            width:ww * 0.826,
            height:wh * 0.826 * 0.18,
            top:(wt + wh)  * 1.01,
            left:wl + (ww - ww * 0.826) / 2
        });
        TweenLite.killTweensOf($(".sun"));
    }
    if ($("#canvas").length) {
        stage = new Kinetic.Stage({
            container: 'canvas',
            width: $(document).width() * 0.78,
            height: $(document).width() * 0.78 * 0.75,
            x: 0,
            y: 0
        });
        imagelayer = new Kinetic.Layer();
        var bgImageObject = new Image();
        bgImageObject.onload = function() {
            var background = new Kinetic.Image({
                x: 0,
                y: 0,
                image: bgImageObject,
                width: $(document).width() * 0.78,
                height: $(document).width() * 0.78 * 0.75,
                draggable: false
            });

            imagelayer.add(background);
            imagelayer.draw();
        };
        bgImageObject.src = upData.now_path + "files/empty_image.png";
        stage.add(imagelayer);
        stage.getContent().addEventListener('touchmove', function(evt) {
            //alert(uploadimage);
            //alert(filepath);
            if(uploadimage == null || filepath!=""){return false;}
            var touch1 = evt.touches[0];
            var touch2 = evt.touches[1];
            if(touch1 && touch2) {
                var dist = getDistance({
                    x: touch1.clientX,
                    y: touch1.clientY
                }, {
                    x: touch2.clientX,
                    y: touch2.clientY
                });
                if(lastDist==0) {
                    lastDist = dist;
                }
                var scale = uploadimage.scaleX() * dist / lastDist;
                uploadimage.scaleX(scale);
                uploadimage.scaleY(scale);
                imagelayer.draw();
                lastDist = dist;
            }
        }, false);
        stage.getContent().addEventListener('touchend', function() {
            if(uploadimage == null || filepath!=""){return false;}
            lastDist = 0;
        }, false);
        //
        setTimeout(function(){
            addImage();
        },100);
    }
});

function addImage(){
    if(uploadimage!=null){
        uploadimage.destroy();
    }
    var fileImageObject = new Image();
    fileImageObject.onload = function() {
        var rect = getSize(fileImageObject.width,fileImageObject.height);
        uploadimage = new Kinetic.Image({
            x: rect.x,
            y: rect.y,
            image: fileImageObject,
            width: rect.width,
            height: rect.height,
            draggable: true
        });
        imagelayer.add(uploadimage);
        imagelayer.draw();
    };
    fileImageObject.src = upData.img;
    $(".bao").css({backgroundColor:'#296C74'});
}
function _whirl() {
	alertsun(1);
	$.post(upData.flip,
			{},
			function(data) {
				var result = eval("("+data+")");
				if (!result.error) {
					if(uploadimage!=null){
						uploadimage.destroy();
					}
					var fileImageObject = new Image();
					fileImageObject.onload = function() {
						var rect = getSize(fileImageObject.width,fileImageObject.height);
						uploadimage = new Kinetic.Image({
							x: rect.x,
							y: rect.y,
							image: fileImageObject,
							width: rect.width,
							height: rect.height,
							draggable: true
						});
						imagelayer.add(uploadimage);
						imagelayer.draw();
						alertsun(0);
					};
					fileImageObject.src = result.message;
				}
			}
	);
}
function saveToSever(){
    var jpg;
    var b64;
    var c = $("#canvas canvas").get(0);

    if(navigator.userAgent.match(/Android/i) ) {
        var encoder = new JPEGEncoder();
        var ctx=c.getContext("2d");
        jpg = encoder.encode(ctx.getImageData(0,0,c.width,c.height),  80 );
    }else{
        jpg= imagelayer.toDataURL({mimeType:'image/jpeg', quality:0.8} );
    }

    b64= jpg.substring(23);

    var tag_text = $(".bao-tag div[data-y=1]").attr("data-text");
    if (!tag_text) {
        $.alertk("请选择标签");
        return;
    }
    alertsun(1);
    //
    $.post(upData.upfile,
        {image:b64,type:'next',text:tag_text},
        function(data){
            //alert(data);
            alertsun(0);
            //
            var result = eval("("+data+")");
            if (!result.error) {
                window.location.reload();
            }else{
                $.alert("数据出错！");
            }
        }
    );
}

function pluswen() {
    alertsun(1);
    if (!upData.uuid) {
        alertsun(0);
        $.alert("数据错误！");
    }
    $.post(upData.index,
        {plus:'1',uid:upData.uuid},
        function(data){
            alertsun(0);
            //
            var result = eval("("+data+")");
            if (!result.error) {
                $(".degree_text_b span").text(result.val);
                $(".degree_add_alert span").text(result.plus);
                $(".degree_text_b").addClass("aler");
                $(".degree_add_alert").fadeIn();
                $(".alert_mask").fadeIn();
                setTimeout(function(){
                    $(".degree_text_b").removeClass("aler");
                    $(".degree_add_alert").fadeOut();
                    $(".alert_mask").fadeOut();
                }, 1500);
            }else{
                if (!result.message) {
                    $.alert("数据出错！");
                }else{
                    $.alert(result.message);
                }
            }
        }
    );
}

function _share(t) {
    if (t) {
        $("#boy_share").show();
        $("#degree_share").show();
        $("#boy_next").hide();
    }else{
        $("#boy_share").hide();
        $("#degree_share").hide();
        $("#boy_next").show();
    }
}

function getSize(width,height){
    var MAX_WIDTH = 760;
    var MAX_HEIGHT = 680;
    var x=0,y=0;
    if (width/MAX_WIDTH < height/MAX_HEIGHT) {
        height *= MAX_WIDTH / width;
        width = MAX_WIDTH;
        x = 0;
        y =  Math.ceil((MAX_HEIGHT-height)/2);
    } else {
        width *= MAX_HEIGHT / height;
        height = MAX_HEIGHT;
        y = 0;
        x =  Math.ceil((MAX_WIDTH-width)/2);
    }
    width = Math.ceil(width);
    height = Math.ceil(height);
    return {x:x,y:y,width:width,height:height};
}

function getDistance(p1, p2) {
    return Math.sqrt(Math.pow((p2.x - p1.x), 2) + Math.pow((p2.y - p1.y), 2));
}

function _back(t) {
    if (t) {
        if (confirm("确定返回重新上传评分？")) {
            $.alert("正在返回...");
            setTimeout(function(){
                window.location.href = upData.unupfile;
            }, 100);
        }
    }else{
        $.alert("正在返回...");
        setTimeout(function(){
            window.location.href = upData.unupfile;
        }, 100);
    }
}
function _receive(t) {
    if (t) {
        $(".alert_mask").show();
        var lpost = $(".ylpost");
        lpost.show();
        lpost.find(".bk").css({"margin-top":parseInt(($(window).height()-lpost.find(".bk").height())/2 - 20)});
    }else{
        $(".alert_mask").hide();
        $(".ylpost").hide();
    }
}
function _button() {
    $.alert("正在提交...");
    $.post(upData.index,
        {mobile:$("#mobilenumber").val(),realname:$("#realname").val()},
        function(data) {
            $.alert(0);
            //
            var result = eval("("+data+")");
            if (result.success) {
                _receive(0);
                _form(1);
            }else{
                if (!result.message) {
                    $.alert("提交出错！");
                }else{
                    $.alert(result.message);
                }
            }
        }
    );
}
function _form(t) {
    if (t) {
        $("#boy_form").show();
        $("#degree_form").show();
        $("#boy_next").hide();
    }else{
        $("#boy_form").hide();
        $("#degree_form").hide();
        $("#boy_next").show();
    }
}
function _tag(obj) {
    var tthis = $(obj);
    $("div.tag").each(function(){
        if ($(this).attr("data-y")) {
            $(this).attr("data-y","0");
            $(this).find("img").attr("src", upData.now_path+"files/g/"+$(this).attr("data-l")+".png");
        }
    });
    tthis.attr("data-y", "1");
    tthis.find("img").attr("src", upData.now_path+"files/g/"+tthis.attr("data-l")+"s.png");
}
function _upload(t) {
    $("#up_images").attr("data-sex", t).click();
}
function _upfile(obj){
    alertsun(1);
    //
    $.ajaxFileUpload({
        url: upData.upfile + "&sex="+$(obj).attr("data-sex")+"&upname="+$(obj).attr("id"),
        secureuri: false,
        fileElementId: obj.id,
        dataType: 'json',
        success: function (data, status) {
            alertsun(0);
            //
            if (data.message) {
                $.alert(data.message);
            } else {
                window.location.reload();
            }
        },error: function (data, status, e) {
            alertsun(0);
            //
            $.alert("上传失败");
        }
    })
}

function alertsun(show) {
    if (show) {
        $(".loading_alert").show();
        $(".alert_mask").show();
        TweenMax.to($(".loading_alert .sun"), 15, {rotation:360, ease:Linear.easeNone, repeat:-1});
    }else{
        TweenLite.killTweensOf($(".sun"));
        $(".loading_alert").hide();
        $(".alert_mask").hide();
    }
}

if (typeof(wx) != "undefined") wx = null;
$.getScript("http://res.wx.qq.com/open/js/jweixin-1.0.0.js", function(){
    if (shareData.imgUrl && (shareData.imgUrl.substring(0,1) == '.' || shareData.imgUrl.substring(0,1) == '#')) {
        if ($(shareData.imgUrl).find("img:eq(0)").attr("src")) {
            shareData.imgUrl = $(shareData.imgUrl).find("img:eq(0)").attr("src");
        }
    }
    if (!shareData.link) {
        shareData.link = document.URL;
    }
    if (!shareData.title) {
        shareData.title = $("title:eq(0)").text();
    }
    if (!shareData.desc) {
        shareData.desc = $("body").text();
    }
    if (shareData.desc && (shareData.desc.substring(0,1) == '.' || shareData.desc.substring(0,1) == '#')) {
        if ($(shareData.desc).text()) {
            shareData.desc = $(shareData.desc).text().replace(/^\s+|\s+$/g,"");
        }
    }
    // 是否启用调试
    jssdkconfig.debug = false;
    //
    jssdkconfig.jsApiList = [
        'checkJsApi',
        'onMenuShareTimeline',
        'onMenuShareAppMessage',
        'onMenuShareQQ',
        'onMenuShareWeibo'
    ];
    wx.config(jssdkconfig);
    wx.ready(function () {
        wx.onMenuShareAppMessage(shareData);
        wx.onMenuShareTimeline(shareData);
        wx.onMenuShareQQ(shareData);
        wx.onMenuShareWeibo(shareData);
    });
});