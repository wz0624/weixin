/**
 * Created by bookkilled on 15/10/2.
 */
(function(){
    $('#postrlt').on('click',postmsg);
    function postmsg(){
        var myname = $.trim($('#txtName').val());
        var myid = parseInt(Math.random()*4);
        var contant = $('#warper').html();
        if(myname == "" || myname == "请输入您的名字"){return false;}
        var json = {data:[
            {id:'1',name:'周金胜',tag:'雷神之锤',url:'images/1.jpg'},
            {id:'2',name:'周金胜',tag:'单身汪',url:'images/2.jpg'},
            {id:'3',name:'周金胜',tag:'程序猿',url:'images/3.jpg'},
            {id:'3',name:'周金胜',tag:'野生奥特曼',url:'images/4.jpg'},
            {id:'3',name:'周金胜',tag:'葫芦娃',url:'images/5.jpg'}
        ]};
        var reqhtml = '<p class="fff ft16"><span class="ft24">测试结果：</span><br><span class="fc636363">'+myname+'</span> : 恭喜您，测试结果显示你就是【'+json.data[myid].tag+'】图里面画的这个东西！哈哈！</p><p class="tc mtb20"><img src="'+json.data[myid].url+'" width="100%" alt=""></p><p class="mtb20"><a class="goback" href="javascript:window.location.reload();">再次测试</a></p>';
        $('#warper').html(reqhtml);
        //var optionMine = {
        //    type : 'POST',
        //    url : 'data.js',
        //    //dataType : 'json',
        //    data : {
        //        id : myid,
        //        name : myname
        //    }
        //}
        //var reqMine = $.ajax(optionMine);
        //reqMine.done(function(json){
        //    var json = eval(json);
        //    var reqhtml = '<p class="fff ft16"><span class="ft24">测试结果：</span><br><span class="fc636363">'+myname+'</span> : 恭喜您，测试结果显示你就是【'+json[myid].tag+'】图里面画的这个东西！哈哈！</p><p class="tc mtb20"><img src="'+json[myid].url+'" width="100%" alt=""></p><p class="mtb20"><a class="goback" href="javascript:window.location.reload();">再次测试</a></p>';
        //    $('#warper').html(reqhtml);
        //});
    }
})();



// 获取url参数
function urlPara(v){
    var url = window.location.search;
    if (url.indexOf(v) != -1){
        var start = url.indexOf(v)+v.length;
        var end = url.indexOf('&',start) == -1 ? url.length : url.indexOf('&',start);
        return url.substring(start,end);
    } else { return '';}
}