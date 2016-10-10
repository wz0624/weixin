<?php
/**
 * 微赞模块框架
 *
 * @author Jialin
 * @url http://www.012wz.com/thread-13093-1-1.html
 * @承接web网站定制化开发，微赞模块开发
 * @qq 77035993
 * @php开发学习，技术交流群70886552
 */

namespace Mobile\Controller;
use W\Controller;

class IndexController extends Controller{

    /**
     * 导航菜单
     */
    public function index(){
        global $_W;

        echo 'Mobile';
    }
}
