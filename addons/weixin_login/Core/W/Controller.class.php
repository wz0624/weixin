<?php
namespace W;

class Controller {
    public $W; //微赞全局参数
    public $d; //当前模块名

    public function __construct()
    {
        //获得全局$W
        global $_W;
        $this->W = $_W;
        //设置当前模块名
        $this->d = $this->get_this_model_name();
    }

    /**
     * @param string $name
     * @return bool|string
     * 显示模板
     */
    public function display($name=''){
        $name = explode('/',$name);
        $name_count = count($name);
        $app_name = $name_count == 3 ? $name[0] : APP_NAME;
        $model_name = $name_count == 3 ? $name[1] : $name_count == 2 ? $name[0] : MODEL_NAME;
        $action_name = $name_count ==3 ? $name[2] : ($name_count == 2 ? $name[1] : (!empty($name[0])?$name[0]:ACTION_NAME));
        $tmp_dir = APP_PATH . $app_name . '/View/' . $model_name . '/' . $action_name . C('TMP_FILE_EXT');
        if(!is_file($tmp_dir)) {
            //微赞目录查找
            $tmp_dir = dirname(dirname(SITE_PATH)) . '/' . APP_TYPE . '/themes/' . $this->W['template'] .'/'. $model_name . '/' . $action_name . '.html';
        }
        if(file_exists($tmp_dir)){
            $content = file_get_contents($tmp_dir);
            if($content){
                $_content = template_xr($content);
                $cache_dir = APP_PATH . '/Runtime/Cache/' . $app_name . '/' . $model_name . '/' . $action_name . C('TMP_FILE_EXT');
                if(!is_dir(dirname($cache_dir))){
                    if(!mkdir(dirname($cache_dir),0777,true)){
                        echo '目录' . $this->d . '不可写' . '<br />';
                    }
                }
                if(file_put_contents($cache_dir,$_content))
                    return $cache_dir;
                else{
                    echo '目录' . $this->d . '不可写';
                }
            }
        }
        return false;
    }

    /**
     * 获得当前模块名
     */
    public function get_this_model_name(){
        if($model_name = I('get.m'))
            return  $model_name;
        if($eid = I('get.eid')){
            $model_res = M('modules_bindings')->field('module')->where(array(
                'eid' =>  $eid,
            ))->find();
            if($model_res)
                return $model_res['module'];
        }
        return false;
    }

    protected function createMobileUrl($do, $query = array(), $noredirect = true) {
        global $_W;
        $query['do'] = $do;
        $query['m'] = strtolower($this->d);
        return murl('entry', $query, $noredirect);
    }


    protected function createWebUrl($do, $query = array()) {
        $query['do'] = $do;
        $query['m'] = strtolower($this->d);
        return wurl('site/entry', $query);
    }
}
