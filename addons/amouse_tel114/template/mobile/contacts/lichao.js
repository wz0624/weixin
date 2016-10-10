//index.html
//var urlPre="http://localhost/weiphp/";
var urlPre="http://dianhuaben.chaoyuanwuxian.com/";
var _url=urlPre+"index.php?s=/home/app/";
function getQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]); return null;
}
var listarr;
var typeId;
var count=0;
function gettitle(){
    var _data={};
    _data.token = getQueryString("token");
    $.get(_url+"getTitle", _data,
        function (data) {
            //alert(data);
            document.title =  data;
        });
};
function getInfo(){
    typeId = $(this).attr("data-no");
    if(typeId){
        count=0;
        $("#list").html("");
        var token=getQueryString("token");
        var _data={};
        _data.typeId = typeId;
        _data.token = token;
        _data.openid = getCookie(token);
        $.get(_url+"getList", _data,
            function (data) {
                var jsonDataTel = eval(data);
                listarr=jsonDataTel;
                var _arr = [];
                $.each(jsonDataTel, function(i, item) {
                    _arr.push('<li data-icon="phone" id="phone"><a href="#detail" data-transition="none" style="padding-top:3px;padding-bottom:3px" data-no="'+i+'"><h2>'+item.name+item.renzheng+item.shangmen+'</h2><p>'+item.address+'</p></a><a href="tel:'+item.telnum+'" data-icon="phone"></a></li>' );
                });


                if (_arr.length > 0) {
                    $("#list").html(_arr.join(""));
                    $("#list").listview("refresh");
                    if(_arr.length == 20){
                        var _arrloadmore = [];
                        _arrloadmore.push('<a  data-role="button" data-transition="none" onclick="getMore()">显示更多</a>');
                        $("#loadmore").html(_arrloadmore.join(""));
                        $("#loadmore").trigger( "create" );
                    }else{
                        $("#loadmore").html("");
                    }

                }

            });
        getAdimg("列表页顶部广告","lbad",typeId);

    }else{
        //alert('缺少参数！');
    }
};
function getMore(){

    if(typeId){
        count=count+1;
        var token=getQueryString("token");
        var _data={};
        _data.typeId = typeId;
        _data.token = token;
        _data.count = count;
        _data.openid = getCookie(token);
        $.get(_url+"getList", _data,
            function (data) {
                var jsonDataTel = eval(data);
                //listarr=jsonDataTel;
                listarr=listarr.concat(jsonDataTel);
                //alert(jsonDataTel);
                var _arr = [];
                $.each(jsonDataTel, function(i, item) {
                    _arr.push('<li data-icon="phone" id="phone"><a href="#detail" data-transition="none" style="padding-top:3px;padding-bottom:3px" data-no="'+(i+count*20)+'"><h2>'+item.name+item.renzheng+item.shangmen+'</h2><p>'+item.address+'</p></a><a href="tel:'+item.telnum+'" data-icon="phone"></a></li>' );
                });
                if (_arr.length > 0) {
                    $("#list").append(_arr).listview('refresh');
                    $("#list").listview("refresh");
                    if(_arr.length == 20){
                        var _arrloadmore = [];
                        _arrloadmore.push('<a  data-role="button" data-transition="none" onclick="getMore()">显示更多</a>');
                        $("#loadmore").html(_arrloadmore.join(""));
                        $("#loadmore").trigger( "create" );
                    }else{
                        $("#loadmore").html("");
                        $("#loadmore").trigger( "create" );
                    }
                }

            });
        //getAdimg("列表页顶部广告","lbad");

    }else{
        //alert('缺少参数！');
    }
};
function setlikes(token){
    var id = $(this).attr("data-no");
    var token=getQueryString("token");
    var cookieopenid=getCookie(token);
    if(cookieopenid!==null){
        var _data={};
        _data.id = id;
        _data.token = token;
        _data.openid = cookieopenid;
        $.get(_url+"setlikes", _data,
            function (data) {
                if(data == 1){
                    alert("收藏成功");
                }else{
                    alert("取消收藏");
                }
            });
    }else{
        //alert('缺少参数！');
    }
};

function getDetail(){
    var j = $(this).attr("data-no");
    var jsonDataTel = listarr;
    var _arr = [];
    $.each(jsonDataTel, function(i, item) {
        if(i==j){
            if(item.renzheng && item.shangmen){
                _arr.push('<h4>'+item.name+'</h4>'+item.renzheng+'认证'+item.shangmen+'上门'+'<span class="imgright-1"><a href="tel:'+item.telnum+'" ><img width="35px"src="./css/images/phone.png"/></a> <a id="likes"  data-no="'+item.id+'"><img width="43px"src="./css/images/like.png"/></a></span><br /><br /><p>电话：'+item.telnum+'<br />地址：'+item.address+'</p>'+item.info+item.img);
            }else if(item.renzheng){
                _arr.push('<h4>'+item.name+'</h4>'+item.renzheng+'认证'+'<span class="imgright-1"><a href="tel:'+item.telnum+'" ><img width="35px"src="./css/images/phone.png"/></a> <a id="likes"  data-no="'+item.id+'"><img width="43px"src="./css/images/like.png"/></a></span><br /><br /><p>电话：'+item.telnum+'<br />地址：'+item.address+'</p>'+item.info+item.img);
            }else if(item.shangmen){
                _arr.push('<h4>'+item.name+'</h4>'+item.shangmen+'上门'+'<span class="imgright-1"><a href="tel:'+item.telnum+'" ><img width="35px"src="./css/images/phone.png"/></a> <a id="likes"  data-no="'+item.id+'"><img width="43px"src="./css/images/like.png"/></a></span><br /><br /><p>电话：'+item.telnum+'<br />地址：'+item.address+'</p>'+item.info+item.img);
            }else{
                _arr.push('<h4>'+item.name+'</h4>'+item.renzheng+item.shangmen+'<span class="imgright-1"><a href="tel:'+item.telnum+'" ><img width="35px"src="./css/images/phone.png"/></a> <a id="likes"  data-no="'+item.id+'"><img width="43px"src="./css/images/like.png"/></a></span><br /><br /><p>电话：'+item.telnum+'<br />地址：'+item.address+'</p>'+item.info+item.img);
            }
            /*_arr.push('<h4>'+item.name+'</h4>'+item.renzheng+'认证'+item.shangmen+'上门'+'<span class="imgright-1"><a href="tel:'+item.telnum+'" ><img width="35px"src="./css/images/phone.png"/></a> <a id="likes"  data-no="'+item.id+'"><img width="43px"src="./css/images/like.png"/></a></span><br /><br /><p>地址：'+item.address+'</p>'+item.info);*/
        }
        if (_arr.length > 0) {
            $("#telDetail").html(_arr.join(""));
            $("#telDetail").trigger( "create" );
            $("#likes").on("click", setlikes);
        }
    });
};
var searchlist;
function getSearchDetail(){
    var j = $(this).attr("data-no");
    var jsonDataTel = searchlist;
    var _arr = [];
    $.each(jsonDataTel, function(i, item) {
        if(i==j){
            if(item.renzheng && item.shangmen){
                _arr.push('<h4>'+item.name+'</h4>'+item.renzheng+'认证'+item.shangmen+'上门'+'<span class="imgright-1"><a href="tel:'+item.telnum+'" ><img width="35px"src="./css/images/phone.png"/></a> <a id="likes"  data-no="'+item.id+'"><img width="43px"src="./css/images/like.png"/></a></span><br /><br /><p>电话：'+item.telnum+'<br />地址：'+item.address+'</p>'+item.info);
            }else if(item.renzheng){
                _arr.push('<h4>'+item.name+'</h4>'+item.renzheng+'认证'+'<span class="imgright-1"><a href="tel:'+item.telnum+'" ><img width="35px"src="./css/images/phone.png"/></a> <a id="likes"  data-no="'+item.id+'"><img width="43px"src="./css/images/like.png"/></a></span><br /><br /><p>电话：'+item.telnum+'<br />地址：'+item.address+'</p>'+item.info);
            }else if(item.shangmen){
                _arr.push('<h4>'+item.name+'</h4>'+item.shangmen+'上门'+'<span class="imgright-1"><a href="tel:'+item.telnum+'" ><img width="35px"src="./css/images/phone.png"/></a> <a id="likes"  data-no="'+item.id+'"><img width="43px"src="./css/images/like.png"/></a></span><br /><br /><p>电话：'+item.telnum+'<br />地址：'+item.address+'</p>'+item.info);
            }else{
                _arr.push('<h4>'+item.name+'</h4>'+item.renzheng+item.shangmen+'<span class="imgright-1"><a href="tel:'+item.telnum+'" ><img width="35px"src="./css/images/phone.png"/></a> <a id="likes"  data-no="'+item.id+'"><img width="43px"src="./css/images/like.png"/></a></span><br /><br /><p>电话：'+item.telnum+'<br />地址：'+item.address+'</p>'+item.info);
            }
            /*_arr.push('<h4>'+item.name+'</h4>'+item.renzheng+item.shangmen+'<span class="imgright-1"><a href="tel:'+item.telnum+'" ><img width="35px"src="./css/images/phone.png"/></a> <a id="likes"  data-no="'+item.id+'"><img width="43px"src="./css/images/like.png"/></a></span><br /><br /><p>地址：'+item.address+'</p>'+item.info);*/
        }
        if (_arr.length > 0) {
            $("#telDetail").html(_arr.join(""));
            $("#telDetail").trigger( "create" );
            $("#likes").on("click", setlikes);
        }
    });
};
function getSearch(){
    var token=getQueryString("token");
    var keyword=document.getElementById('keyword').value;
    var keyword1=document.getElementById('keyword1').value;
    var keyword=keyword1?keyword1:keyword;
    if(keyword){
        var _data={};
        _data.keyword = keyword;
        _data.token=token;
        _data.openid = getCookie(token);
        $.get(_url+"getSearch", _data,
            function (data) {
                $("#searchlist").html("");
                var jsonDataTel = eval(data);
                searchlist=jsonDataTel;
                var _arr = [];
                $.each(jsonDataTel, function(i, item) {
                    _arr.push('<li data-icon="phone" id="phone"><a href="#detail" data-transition="none" style="padding-top:0px;padding-bottom:0px" data-no="'+i+'"><h2>'+item.name+item.renzheng+item.shangmen+'</h2><p>'+item.address+'</p></a><a href="tel:'+item.telnum+'" data-icon="phone"></a></li>' );
                });
                if (_arr.length > 0) {

                    $("#searchlist").html(_arr.join(""));
                    $("#searchlist").listview("refresh");
                }
            });

    }else{
        //alert('缺少参数！');
    }
};
function getAdimg(type,id,typeid){
    //广告图片
    var token=getQueryString("token");
    var _data={};
    _data.type = type;
    _data.typeid = typeid;
    _data.token = token;
    $.get(_url+"getAdimg", _data,
        function (data) {
            if(id==="lbad"){
                document.getElementById('lbad').innerHTML=data;
            }else if(id==="syad"){
                document.getElementById('syad').innerHTML=data;
            }else if(id==="ssad"){
                document.getElementById('ssad').innerHTML=data;
            }

        });
};
function getAdimg2(type,id,typeid){
    //广告图片
    var token=getQueryString("token");
    var _data={};
    _data.type = type;
    _data.typeid = typeid;
    _data.token = token;
    $.get(_url+"getAdimg2", _data,
        function (data) {
            //alert(data);
            document.getElementById('syad').innerHTML='<div class="slider" ><ul><li><a href="http://www.internetke.com" target="_blank"><img src="images/1.jpg" alt="科e互联网站建设团队"></a></li><li><a href="http://www.internetke.com" target="_blank"><img src="images/2.jpg" alt="网页特效集锦"></a></li><li><a href="http://www.internetke.com" target="_blank"><img src="images/3.jpg" alt="JS代码素材库"></a></li><li><a href="http://www.internetke.com" target="_blank"><img src="images/4.jpg" alt="用心建站用心服务"></a></li><li><a href="http://www.internetke.com" target="_blank"><img src="images/5.jpg" alt="学会用才能学会写"></a></li></ul></div>';
            $(".slider").yxMobileSlider({width:1024,height:320,during:3000});
            $("#syad").trigger( "create" );
        });
};
function setCookie(name,value){
    var Days = 30; //此 cookie 将被保存 30 天
    var exp  = new Date();
    exp.setTime(exp.getTime() + Days*24*60*60*1000);
    document.cookie = name + "="+ escape(value) +";expires="+ exp.toGMTString();
}
function getCookie(name){
    var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
    if(arr != null) return unescape(arr[2]); return null;
}
//my.html
function sendInfo(token){
    var token=getQueryString("token");
    var cookieopenid=getCookie(token);
    if(cookieopenid!==null){
        if($("#name").val() && $("#telnum").val() && $("#address").val() && $("#type2").val()){
            var _data={};
            _data.name = $("#name").val();
            _data.telnum = $("#telnum").val();
            _data.address = $("#address").val();
            _data.type = $("#type2").val();
            _data.info = $("#info").val();
            _data.token = token;
            _data.openid = cookieopenid;
            $("#mypubliced").html();
            $.get(_url+"sendUserInfo", _data,
                function (data) {
                    var _arr = [];
                    if(data == 0){
                        _arr.push('<h1>号码已存在!</h1>');
                    }else{
                        var jsonData = eval(data);
                        $.each(jsonData, function(i, item) {
                            _arr.push('<h1>发布成功 </br><div id="chakandiv" >赶快邀请好友收藏你的店铺吧，收藏数越多排名越靠前！<a data-role="button" data-transition="none" id="chakan" data-inline="true" href="">立即点击查看</a></div></h1><div data-role="fieldcontain"><label for="name">机构名称：</label><input type="text" name="name" id="name" value='+item.name+'><label for="telnum">电话号码：</label><input type="text" name="telnum" id="telnum"  value='+item.telnum+'><label for="address">机构地址：</label><input type="text" name="address" id="address" value='+item.address+'>  <label for="info">最新公告：</label><input type="text" name="info" id="info"  value='+item.info+'>  <label for="type">选择分类：</label><input type="text" name="type" id="type" value='+item.type+'></div>');
                        });
                    }
                    $("#mypubliced").html(_arr.join(""));
                    $("#mypublic").innerHTML = "";
                    $("#chakandiv").trigger( "create" );
                    $("#chakan").on("click", function(){
                        window.location.href="http://dianhuaben.chaoyuanwuxian.com/webapp/index.html?token="+token;
                    });
                });
        }else{
            //alert("信息不完整！填写完整后再发布！");
            var _arr = [];
            _arr.push('<h1>信息不完整！填写完整后再发布！</h1>');
            $("#mypubliced").html(_arr.join(""));
        }
    }else{
        //alert('缺少参数！');
    }
};

function getPublic(token){
    if(token){
        var _data={};
        _data.token = token;
        $.get(_url+"getallType", _data,
            function (data) {
                $("#mypublic").html("");
                var jsonData = eval(data);
                var _arr = [];
                _arr.push('<div data-role="header"><h1>填写立即发布</h1></div><div data-role="content" style="padding:15px;"><div data-role="fieldcontain"><label for="name">机构名称：</label><input type="text" name="name" id="name"><label for="telnum">电话号码：</label><input type="text" name="telnum" id="telnum"><label for="address">机构地址：</label><input type="text" name="address" id="address"><label for="info">最新公告：</label><input type="text" name="info" id="info"><label for="type">分类：(<font color="red">请认真选择</font>)</label><div  style="width:140px;float:left;">一级分类:<select  name="type" id="type" >' );
                /*$.each(jsonData, function(i, item) {
                 _arr.push('<optgroup label="' + item.title + '">' );
                 $.each(item.type2, function(j, item2) {
                 _arr.push('<option value="'+item2.id+'">'+item2.title+'</option>' );
                 });
                 _arr.push('</optgroup>' );
                 });*/
                $.each(jsonData, function(i, item) {
                    _arr.push('<option value="'+item.id+'">'+item.title+'</option>' );

                });
                _arr.push('</select></div><div style="width:140px;float:left;margin-left:10px;">二级分类:<select  name="type2" id="type2">' );

                _arr.push('</select></div><br/><a data-role="button" data-transition="none" id="submit" data-inline="true" href="#detail">立即发布</a></div><div data-role="footer" data-position="fixed"><h5>潮源无线</h5></div></div>' );
                /*_arr.push('</select></div><br/><a data-role="button" data-transition="none" id="submit" data-inline="true" href="#detail">立即发布</a></div><div data-role="footer" data-position="fixed"><h5>潮源无线</h5></div></div>' );*/
                if (_arr.length > 0) {
                    $("#mypublic").html(_arr.join(""));
                    $("#mypublic").trigger( "create" );
                    $("#submit").on("click", sendInfo);
                    //
                    var typeid=0;
                    $("#type").change(function(){
                        typeid=$("#type").val();
                        var type2arr=[];
                        $.each(jsonData, function(i, item) {
                            $.each(item.type2, function(j, item2) {
                                if(typeid == item2.pid){
                                    type2arr.push('<option value="'+item2.id+'">'+item2.title+'</option>' );
                                }
                            });
                        });
                        $("#type2").html(type2arr.join(""));
                    });
                    if(typeid == 0 ){
                        var type2arr=[];
                        $.each(jsonData, function(i, item) {
                            $.each(item.type2, function(j, item2) {
                                if(item2.pid == 5 ){
                                    type2arr.push('<option value="'+item2.id+'">'+item2.title+'</option>' );
                                }
                            });
                        });
                        $("#type2").html(type2arr.join(""));
                    }
                }
            });
        //getAdimg("搜索页顶部广告","ssad");
    }else{
        alert('缺少参数！');
    }
};
function getPubliced(token){
    if(token){
        var _data={};
        var cookieopenid=getCookie(token);
        _data.token = token;
        _data.openid = cookieopenid;
        $.get(_url+"getPubliced", _data,
            function (data) {
                $("#mypubliced").html("");
                var jsonData = eval(data);
                var _arr = [];
                $.each(jsonData, function(i, item) {
                    _arr.push('<h1>发布成功</h1><div data-role="fieldcontain"><label for="name">机构名称：</label><input type="text" name="name" id="name" value='+item.name+'><label for="telnum">电话号码：</label><input type="text" name="telnum" id="telnum"  value='+item.telnum+'><label for="address">机构地址：</label><input type="text" name="address" id="address" value='+item.address+'> <label for="info">最新公告：</label><input type="text" name="info" id="info"  value='+item.info+'>  <label for="type">分类：</label><input type="text" name="type2" id="type2" disabled="disabled" value='+item.type+'><br /><a data-role="button" data-transition="none" id="editsubmit" data-inline="true" href="#detail">编辑发布</a></div>');
                });
                $("#mypubliced").html(_arr.join(""));
                $("#mypubliced").trigger( "create" );
                $("#editsubmit").on("click", sendInfo);
            });
        //getAdimg("搜索页顶部广告","ssad");
    }else{
        alert('缺少参数！');
    }
};
//mylikes.html
var likesarr;
function getMylikesList(){
    var token=getQueryString("token");
    var openid=getCookie(token);
    if(token){
        $("#mylikeslist").html("");

        var _data={};
        _data.openid = openid;
        _data.token = token;
        $.get(_url+"getMylikesList", _data,
            function (data) {
                var jsonDataTel = eval(data);
                likesarr=jsonDataTel;
                var _arr = [];
                $.each(jsonDataTel, function(i, item) {
                    _arr.push('<li data-icon="phone" id="phone"><a href="#detail" data-transition="none" style="padding-top:3px;padding-bottom:3px" data-no="'+i+'"><h2>'+item.name+item.renzheng+item.shangmen+'</h2><p>'+item.address+'</p></a><a href="tel:'+item.telnum+'" data-icon="phone"></a></li>' );

                });
                if (_arr.length > 0) {
                    $("#mylikeslist").html(_arr.join(""));
                    $("#mylikeslist").listview("refresh");
                    $("#mylikeslist").on("click", "a", getlikeDetail);
                }
            });
    }else{
        //alert('缺少参数！');
    }
};
function getlikeDetail(){
    var j= $(this).attr("data-no");
    var jsonDataTel = likesarr;
    var _arr = [];
    $.each(jsonDataTel, function(i, item) {
        if(i==j){
            if(item.renzheng && item.shangmen){
                _arr.push('<h4>'+item.name+'</h4>'+item.renzheng+'认证'+item.shangmen+'上门'+'<span class="imgright-1"><a href="tel:'+item.telnum+'" ><img width="35px"src="./css/images/phone.png"/></a> <a id="likes"  data-no="'+item.id+'"><img width="43px"src="./css/images/like.png"/></a></span><br /><br /><p>电话：'+item.telnum+'<br />地址：'+item.address+'</p>'+item.info);
            }else if(item.renzheng){
                _arr.push('<h4>'+item.name+'</h4>'+item.renzheng+'认证'+'<span class="imgright-1"><a href="tel:'+item.telnum+'" ><img width="35px"src="./css/images/phone.png"/></a> <a id="likes"  data-no="'+item.id+'"><img width="43px"src="./css/images/like.png"/></a></span><br /><br /><p>电话：'+item.telnum+'<br />地址：'+item.address+'</p>'+item.info);
            }else if(item.shangmen){
                _arr.push('<h4>'+item.name+'</h4>'+item.shangmen+'上门'+'<span class="imgright-1"><a href="tel:'+item.telnum+'" ><img width="35px"src="./css/images/phone.png"/></a> <a id="likes"  data-no="'+item.id+'"><img width="43px"src="./css/images/like.png"/></a></span><br /><br /><p>电话：'+item.telnum+'<br />地址：'+item.address+'</p>'+item.info);
            }else{
                _arr.push('<h4>'+item.name+'</h4>'+item.renzheng+item.shangmen+'<span class="imgright-1"><a href="tel:'+item.telnum+'" ><img width="35px"src="./css/images/phone.png"/></a> <a id="likes"  data-no="'+item.id+'"><img width="43px"src="./css/images/like.png"/></a></span><br /><br /><p>电话：'+item.telnum+'<br />地址：'+item.address+'</p>'+item.info);
            }
            /*_arr.push('<h4>'+item.name+'</h4>'+item.renzheng+item.shangmen+'<span class="imgright-1"><a href="tel:'+item.telnum+'" ><img width="35px"src="./css/images/phone.png"/></a> <a id="likes"  data-no="'+item.id+'"><img width="43px"src="./css/images/like.png"/></a></span><br /><br /><p>地址：'+item.address+'</p>'+item.info);*/

        }
    });
    if (_arr.length > 0) {
        $("#mylikesdetail").html(_arr.join(""));
        $("#mylikesdetail").trigger( "create" );
        $("#likes").on("click", setlikes);
    }
};