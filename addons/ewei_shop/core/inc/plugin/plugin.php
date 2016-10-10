<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
class Plugin extends Core{
    public $pluginname;
    public $model;
    public function __construct($weizan_0 = ''){
        parent :: __construct();
        $this -> modulename = 'ewei_shop';
        $this -> pluginname = $weizan_0;
        $this -> loadModel();
        if (strexists($_SERVER['REQUEST_URI'], '/web/')){
            cpa($this -> pluginname);
        }else if (strexists($_SERVER['REQUEST_URI'], '/app/')){
            $this -> setFooter();
        }
        $this -> module['title'] = pdo_fetchcolumn('select title from ' . tablename('modules') . ' where name=\'ewei_shop\' limit 1');
    }
    private function loadModel(){
        $weizan_1 = IA_ROOT . '/addons/' . $this -> modulename . '/plugin/' . $this -> pluginname . '/model.php';
        if (is_file($weizan_1)){
            $weizan_2 = ucfirst($this -> pluginname) . 'Model';
            require $weizan_1;
            $this -> model = new $weizan_2($this -> pluginname);
        }
    }
    public function getSet(){
        return $this -> model -> getSet();
    }
    public function updateSet($weizan_3 = array()){
        $this -> model -> updateSet($weizan_3);
    }
    public function template($weizan_4, $weizan_5 = TEMPLATE_INCLUDEPATH){
        global $_W;
        $weizan_6 = IA_ROOT . '/addons/ewei_shop/';
        if (defined('IN_SYS')){
            $weizan_7 = IA_ROOT . '/addons/ewei_shop/plugin/' . $this -> pluginname . "/template/{$weizan_4}.html";
            $weizan_8 = IA_ROOT . "/data/tpl/web/{$_W['template']}/ewei_shop/plugin/" . $this -> pluginname . "/{$weizan_4}.tpl.php";
            if (!is_file($weizan_7)){
                $weizan_7 = IA_ROOT . "/addons/ewei_shop/template/{$weizan_4}.html";
                $weizan_8 = IA_ROOT . "/data/tpl/web/{$_W['template']}/ewei_shop/{$weizan_4}.tpl.php";
            }
            if (!is_file($weizan_7)){
                $weizan_7 = IA_ROOT . "/web/themes/{$_W['template']}/{$weizan_4}.html";
                $weizan_8 = IA_ROOT . "/data/tpl/web/{$_W['template']}/{$weizan_4}.tpl.php";
            }
            if (!is_file($weizan_7)){
                $weizan_7 = IA_ROOT . "/web/themes/default/{$weizan_4}.html";
                $weizan_8 = IA_ROOT . "/data/tpl/web/default/{$weizan_4}.tpl.php";
            }
        }else{
            $weizan_9 = m('cache') -> getString('template_shop');
            if (empty($weizan_9)){
                $weizan_9 = 'default';
            }
            if (!is_dir(IA_ROOT . '/addons/ewei_shop/template/mobile/' . $weizan_9)){
                $weizan_9 = 'default';
            }
            $weizan_10 = m('cache') -> getString('template_' . $this -> pluginname);
            if (empty($weizan_10)){
                $weizan_10 = 'default';
            }
            if (!is_dir(IA_ROOT . '/addons/ewei_shop/plugin/' . $this -> pluginname . '/template/mobile/' . $weizan_10)){
                $weizan_10 = 'default';
            }
            $weizan_8 = IA_ROOT . '/data/app/ewei_shop/plugin/' . $this -> pluginname . "/{$weizan_10}/mobile/{$weizan_4}.tpl.php";
            $weizan_7 = $weizan_6 . '/plugin/' . $this -> pluginname . "/template/mobile/{$weizan_10}/{$weizan_4}.html";
            if (!is_file($weizan_7)){
                $weizan_7 = $weizan_6 . '/plugin/' . $this -> pluginname . "/template/mobile/default/{$weizan_4}.html";
                $weizan_8 = IA_ROOT . '/data/app/ewei_shop/plugin/' . $this -> pluginname . "/default/mobile/{$weizan_4}.tpl.php";
            }
            if (!is_file($weizan_7)){
                $weizan_7 = $weizan_6 . "/template/mobile/{$weizan_9}/{$weizan_4}.html";
                $weizan_8 = IA_ROOT . "/data/app/ewei_shop/{$weizan_9}/{$weizan_4}.tpl.php";
            }
            if (!is_file($weizan_7)){
                $weizan_7 = $weizan_6 . "/template/mobile/default/{$weizan_4}.html";
                $weizan_8 = IA_ROOT . "/data/app/ewei_shop/default/{$weizan_4}.tpl.php";
            }
            if (!is_file($weizan_7)){
                $weizan_7 = $weizan_6 . "/template/mobile/{$weizan_4}.html";
                $weizan_8 = IA_ROOT . "/data/app/ewei_shop/{$weizan_4}.tpl.php";
            }
            if (!is_file($weizan_7)){
                $weizan_11 = explode('/', $weizan_4);
                $weizan_12 = $weizan_11[0];
                $weizan_13 = m('cache') -> getString('template_' . $weizan_12);
                if (empty($weizan_13)){
                    $weizan_13 = 'default';
                }
                if (!is_dir(IA_ROOT . '/addons/ewei_shop/plugin/' . $weizan_12 . '/template/mobile/' . $weizan_13)){
                    $weizan_13 = 'default';
                }
                $weizan_14 = $weizan_11[1];
                $weizan_7 = IA_ROOT . '/addons/ewei_shop/plugin/' . $weizan_12 . '/template/mobile/' . $weizan_13 . "/{$weizan_14}.html";
            }
        }
        if (!is_file($weizan_7)){
            exit("Error: template source '{$weizan_4}' is not exist!");
        }
        if (DEVELOPMENT || !is_file($weizan_8) || filemtime($weizan_7) > filemtime($weizan_8)){
            shop_template_compile($weizan_7, $weizan_8, true);
        }
        return $weizan_8;
    }
    public function _exec_plugin($weizan_15, $weizan_16 = true){
        global $_GPC;
        if ($weizan_16){
            $weizan_17 = IA_ROOT . '/addons/ewei_shop/plugin/' . $this -> pluginname . '/core/web/' . $weizan_15 . '.php';
        }else{
            $weizan_17 = IA_ROOT . '/addons/ewei_shop/plugin/' . $this -> pluginname . '/core/mobile/' . $weizan_15 . '.php';
        }
        if (!is_file($weizan_17)){
            message("未找到控制器文件 : {$weizan_17}");
        }
        include $weizan_17;
        exit;
    }
}
