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

namespace Web\Controller;
use W\Controller;

class NavController extends Controller{

    /**
     * 导航菜单
     */
    public function index(){
        global $_W;

        $module_name = $this->d;
        //获取当前模块的所有菜单
        $modules = M('modules_bindings')->field('eid,module,entry,call,title,do')->where([
            'module' => $module_name,
        ])->select();

        $nav_list = $this->get_nav_type();
        include $this->display();
    }

    /**
     * 添加道航
     */
    public function add(){
        global $_W;
        if(IS_POST){
            //检查
            if(!I('post.title'))
                JSON([1001,'名称不能为空']);
            if(!I('post.do_'))
                JSON([1003,'操作名不能为空']);
            else
                $_POST['do'] = I('post.do_');
            if(!I('post.entry'))
                JSON([1005,'请选择导航类型']);

            $_POST['module']  = $this->d;
            $_POST['call']  = I('post.call','');
            $_POST['state']  = I('post.state','');
            $_POST['direct']  = I('post.direct','');
            $_POST['url']  = I('post.url','');
            $_POST['icon']  = I('post.icon','');
            $_POST['displayorder']  = I('post.displayorder',0);
            if(M('modules_bindings')->where(['module'=>$this->d])->add(I('post.')))
                JSON([1000,'添加成功',['url'=>$this->createWebUrl('Nav',['op'=>'index'])]]);
            else
                JSON([1007,'添加失败']);
        }
        $nav_list = $this->get_nav_type();
        include $this->display();
    }
    /**
     * 编辑导航
     */
    public function edit(){
        global $_W;
        $id = I('get.id');
        if(!$id) return false;
        if(IS_POST){
            //检查
            if(!I('post.title'))
                JSON([1001,'名称不能为空']);
            if(!I('post.do_'))
                JSON([1003,'操作名不能为空']);
            else
                $_POST['do'] = I('post.do_');
            if(!I('post.entry'))
                JSON([1005,'请选择导航类型']);

            if(M('modules_bindings')->where(['module'=>$this->d,'eid'=>$id])->save(I('post.')))
                JSON([1000,'修改成功',['url'=>$this->createWebUrl('Nav',['op'=>'index'])]]);
            else
                JSON([1007,'修改失败']);
        }
        $info = M('modules_bindings')->field('eid,entry,title,module,do')->where([
            'module' => $this->d,
            'eid' => $id,
        ])->find();
        $nav_list = $this->get_nav_type();
        include $this->display();

    }

    /**
     * 删除导航
     */
    public function del(){
        global $_W;
        $id = I('get.id');
        if(!$id) return false;
        if(M('modules_bindings')->where(['module'=>$this->d,'eid'=>$id])->delete())
            JSON([1000,'删除成功',['url'=>$this->createWebUrl('Nav',['op'=>'index'])]]);
        else
            JSON([1001,'删除失败']);

    }
    /**
     * 获得导航类型
     *
     */
    public function get_nav_type(){
        $nav_arr = [
            'menu' => '业务菜单',
            'cover' => '封面入口',
            'rule' => '回复规则',
            'home' => '微站首页导航图标',
            'profile' => '微站个人中心导航',
            'shortcut' => '微站快捷功能导航',
            'function' => '微站独立功能',
        ];
        return $nav_arr;
    }
}
