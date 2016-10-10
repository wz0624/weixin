/**
 * Created by Administrator on 15-3-26.
 */
var val=0; //放置输入的值
var xval=0;//保存转换Number类型的值
var temp=0; //保存第一次输入的值
var errEle=$(".err");
var _interval='';
/*********************************************************/
function dot(){
    var str=document.getElementById("money").value;
    if(str===''){
    	return false;
    }
    for(i=0; i<=str.length;i++){ //判断是否已经有一个点号
        if(str.substr(i,1)==".") return false; //如果有则不再插入
    }
    str=str + ".";
    document.getElementById("money").value=str;
}
/*获取输入数字*/
function inputEvent(e){
    val=e;
    var xsval=document.getElementById("money");
    if(xsval.value==='0'){
    	return false;
    }
    if(xsval.value==='' && val==='00'){
            return false;
    }

    var str=xsval.value;
    var lan=str.length;
    var weizhi=str.indexOf(".");
    if(weizhi>0){
    	if((lan-weizhi)>2){
    		return false;
    	}
        if(val==='00'){
                return false;
        }
    }
    xsval.value+=val; //连续输入数字(String类型)
    //转换Number类型
}
function del(){ //退格
    var str=document.getElementById("money").value;
    str=(str!="0") ? str : "";
    str=str.substr(0,str.length-1);
    str=(str!="") ? str : "0";
    if(str==='0'){
    	str='';
    }
    document.getElementById("money").value=str;
}



