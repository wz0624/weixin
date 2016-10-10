<?php
/**
 * 微赞模块框架
 *
 * @author Jialin
 * @url http://www.012wz.com/thread-13093-1-1.html
 * @承接web网站定制化开发，微赞模块开发
 * @qq 77035993
 */

class W {

    /**
     * 开始程序
     */
    public static function Start($name,$argu=''){
        //定义参数
        self::define_conf();
        // 注册AUTOLOAD方法
        spl_autoload_register('W::autoload');
        //加载底层函数文件
        include W_DIR . '/Common/' . 'functions.php';
        //加载文件
        self::require_file();//加载框架Common文件
        self::require_file('',true);//加载应用Common文件
        //加载框架控制器
        self::require_w();
        //路由器分配
        self::Route($name,$argu);

    }


    /**
     * @param $class
     * @param string $map
     */
    protected static function define_conf(){
        //定义框架目录
        defined('W_DIR') or define('W_DIR',__DIR__);

        // 定义当前请求的系统常量
        define('NOW_TIME',      $_SERVER['REQUEST_TIME']);
        define('REQUEST_METHOD',$_SERVER['REQUEST_METHOD']);
        define('IS_GET',        REQUEST_METHOD =='GET' ? true : false);
        define('IS_POST',       REQUEST_METHOD =='POST' ? true : false);
        define('IS_PUT',        REQUEST_METHOD =='PUT' ? true : false);
        define('IS_DELETE',     REQUEST_METHOD =='DELETE' ? true : false);

    }
    /**
     * 加载Common下的文件
     */
    protected static function require_file($app_name = '',$app=false){
        $file_list = [
            'config',
            'functions',
        ];
        foreach($file_list as $v ){
            if($app_name){
                $file_dir = APP_PATH .'/' .$app_name . '/Common/' . $v . '.php';
            }else{
                $file_dir = W_DIR .'/Common/' . $v . '.php';
            }
            if($app){
                $file_dir = APP_PATH . '/Common/' . $v . '.php';
            }
            if(file_exists($file_dir)){
                if($v == 'config'){
                    C($file_dir);
                    continue;
                }
                include_once $file_dir;
            }
        }
    }

    /**
     * 加载底层框架文件
     */
    protected static function require_w(){
        global $_W;
        //实例化Db类
        $db_info = $_W['config']['db']['host'] ?$_W['config']['db'] :$_W['config']['db']['master']  ;
        $db_conf = [
            'db_type'          =>  'mysql',
            'db_user'      =>  $db_info['username'],
            'db_pwd'      =>   $db_info['password'],
            'db_host'      =>  $db_info['host'],
            'db_port'      =>  $db_info['port'],
            'db_name'      =>  $db_info['database'],
            'db_prefix'      =>  $db_info['tablepre'],
        ];
        C('DB_CONF',$db_conf);
    }

    /**
     * 路由处理
     */
    protected static function Route($name,$argu=''){
        $isWeb = stripos($name, 'doWeb') === 0;
        $isMobile = stripos($name, 'doMobile') === 0;
        if($isWeb || $isMobile) {
            if ($isWeb) {
                $app_name = 'Web';
                $model_name = substr($name, 5);
                defined('APP_TYPE') or define('APP_TYPE','web');
            }else if ($isMobile){
                $app_name = 'Mobile';
                $model_name = substr($name, 8);
                defined('APP_TYPE') or define('APP_TYPE','app');
            }
            defined('APP_NAME') or define('APP_NAME',$app_name);
            defined('MODEL_NAME') or define('MODEL_NAME',$model_name);
            $action_name = I('get.op','index');
            defined('ACTION_NAME') or define('ACTION_NAME',$action_name);
            $_ins = A($app_name . "\\" . $model_name);
            if($_ins){
                self::require_file($app_name);
                $_ins->$action_name();
            }
        }
        return false;
    }


    /**
     * 类库自动加载
     * @param string $class 对象类名
     * @return void
     */
    public static function autoload($name) {
        $action_names = explode("\\",$name);
        if(empty($action_names))
            return false;
        $name = implode('/',$action_names);
        if(in_array($action_names[0],['W']))
            $action_dir = W_DIR . '/'. $name . '.class.php';
        else
            $action_dir = APP_PATH . '/' . $name . C('CLASS_FILE_EXT');
        if(file_exists($action_dir)){
            include $action_dir;
        }
    }
}