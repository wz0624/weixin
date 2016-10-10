/**
 * 微赞模块框架
 *
 * @author Jialin
 * @url http://www.012wz.com/thread-13093-1-1.html
 * @承接web网站定制化开发，微赞模块开发
 * @qq 77035993
 * @php开发学习，技术交流群70886552
 */
function CheckCode(code,type){
    type = type?type : 1;
    var status = false;
    if(code%2 == 0){
         status = true;
    }
    if(type == 1){
        return status ? 'Success' : 'Error';
    }else if(type ==2 ){
        return status ? 'success' : 'error';
    }
}