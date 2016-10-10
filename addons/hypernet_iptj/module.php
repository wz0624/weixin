<?php
defined('IN_IA') or exit('Access Denied');
class hypernet_iptjModule extends WeModule
{
    public function fieldsFormDisplay($rid = 0)
    {
    }
    public function fieldsFormValidate($rid = 0)
    {
        return '';
    }
    public function fieldsFormSubmit($rid)
    {
    }
    public function ruleDeleted($rid)
    {
    }
    public function settingsDisplay($settings)
    {
        global $_W, $_GPC;
        require_once 'template_conf_ap.php';
        require_once 'template_conf_auth.php';
        require_once 'template_conf_re.php';
        require_once 'template_conf_cd.php';
        require_once 'template_conf_fl.php';
        require_once 'template_conf_bottom.php';
        require_once 'template_conf_pic.php';
        $apply  = json_decode($apply, true);
        $auth   = json_decode($auth, true);
        $refu   = json_decode($refu, true);
        $credit = json_decode($credit, true);
        $full   = json_decode($full, true);
        $bottom = json_decode($bottom, true);
        $proot  = $_W['attachurl'];
        load()->func('tpl');
        $src1 = $this->search_other(1);
        $src2 = $this->search_other(2);
        $src3 = $this->search_other(3);
        $src4 = $this->search_other(4);
        $src5 = $this->search_other(5);
        if ($_GPC['type'] == 'pic1') {
            $dat1 = $_GPC['ImageUrl1'];
            if ($dat1) {
                $this->setpic(1, $_GPC['priority_f'], $dat1, $proot, $_GPC['ison_f'], $_GPC['arti_f']);
                $this->we7_refresh();
            } else {
                message('别忘了 插入图片哦', 'refresh');
            }
        }
        if ($_GPC['type'] == 'pic2') {
            $dat2 = $_GPC['ImageUrl2'];
            if ($dat2) {
                $this->setpic(2, $_GPC['priority_s'], $dat2, $proot, $_GPC['ison_s'], $_GPC['arti_s']);
                $this->we7_refresh();
            } else {
                message('别忘了 插入图片哦', 'refresh');
            }
        }
        if ($_GPC['type'] == 'pic3') {
            $dat3 = $_GPC['ImageUrl3'];
            if ($dat3) {
                $this->setpic(3, $_GPC['priority_t'], $dat3, $proot, $_GPC['ison_t'], $_GPC['arti_t']);
                $this->we7_refresh();
            } else {
                message('别忘了 插入图片哦', 'refresh');
            }
        }
        if ($_GPC['type'] == 'pic4') {
            $dat4 = $_GPC['ImageUrl4'];
            if ($dat4) {
                $this->setpic(4, $_GPC['priority_fo'], $dat4, $proot, $_GPC['ison_fo'], $_GPC['arti_fo']);
                $this->we7_refresh();
            } else {
                message('别忘了 插入图片哦', 'refresh');
            }
        }
        if ($_GPC['type'] == 'pic5') {
            $dat5 = $_GPC['ImageUrl5'];
            if ($dat5) {
                $this->setpic(5, $_GPC['priority_fi'], $dat5, $proot, $_GPC['ison_fi'], $_GPC['arti_fi']);
                $this->we7_refresh();
            } else {
                message('别忘了 插入图片哦', 'refresh');
            }
        }
        if ($_GPC['type'] == 'apply') {
            self::applyInfo($_GPC);
            $href = wurl('profile/module/setting', array(
                'm' => $this->modulename
            ));
            echo "<script>location.href='$href';</script>";
            $_GPC['type'] = NULL;
        } else if ($_GPC['type'] == 'auth') {
            self::authInfo($_GPC);
            $href = wurl('profile/module/setting', array(
                'm' => $this->modulename
            ));
            echo "<script>location.href='$href';</script>";
            $_GPC['type'] = NULL;
        } else if ($_GPC['type'] == 'refu') {
            self::refuInfo($_GPC);
            $href = wurl('profile/module/setting', array(
                'm' => $this->modulename
            ));
            echo "<script>location.href='$href';</script>";
            $_GPC['type'] = NULL;
        } else if ($_GPC['type'] == 'credit') {
            self::creditInfo($_GPC);
            $href = wurl('profile/module/setting', array(
                'm' => $this->modulename
            ));
            echo "<script>location.href='$href';</script>";
            $_GPC['type'] = NULL;
        } else if ($_GPC['type'] == 'full') {
            self::fullInfo($_GPC);
            $href = wurl('profile/module/setting', array(
                'm' => $this->modulename
            ));
            echo "<script>location.href='$href';</script>";
            $_GPC['type'] = NULL;
        } else if ($_GPC['type'] == 'bottom') {
            self::set_bottom($_GPC);
            $href = wurl('profile/module/setting', array(
                'm' => $this->modulename
            ));
            echo "<script>location.href='$href';</script>";
            $_GPC['type'] = NULL;
        }
        include $this->template('setting');
    }
    private function img_type_change($img, $proot)
    {
        if (strpos($img, 'http') !== FALSE) {
            return $img;
        } else {
            return $proot . $img;
        }
    }
    private function setpic($pid, $priority, $img, $proot, $button, $arti)
    {
        $img = self::img_type_change($img, $proot);
        if ($this->search_pid($pid)) {
            pdo_update('ptj_pic', array(
                'priority' => $priority,
                'imgurl' => $img,
                'ison' => $button,
                'arti' => $arti
            ), array(
                'pid' => $pid
            ));
        } else {
            pdo_insert('ptj_pic', array(
                'priority' => $priority,
                'imgurl' => $img,
                'ison' => $button,
                'arti' => $arti
            ));
        }
    }
    private function search_pid($pid)
    {
        $id = pdo_fetch("SELECT * FROM " . tablename('ptj_pic') . "WHERE pid=:pid", array(
            ':pid' => $pid
        ));
        if ($id) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    private function search_other($pid)
    {
        $info = pdo_fetch("SELECT * FROM " . tablename('ptj_pic') . "WHERE pid=:pid", array(
            ':pid' => $pid
        ));
        return $info;
    }
    private function fileClean($model, $arr)
    {
        $flag   = 0;
        $length = count($arr);
        foreach ($model as $k => $v) {
            for ($i = 0; $i < $length; ++$i) {
                if (strpos($k, $arr[$i]) === false) {
                    ++$flag;
                }
            }
            if ($flag == $length) {
                unset($k);
            }
        }
    }
    private function fileInit($fname)
    {
        $inithead = "<?php\n";
        $filelocale = fopen(dirname(preg_replace('@\(.*\(.*$@', '', __FILE__)) . "/template_conf_" . $fname . ".php", "w") or die('上传失败');
        fwrite($filelocale, $inithead);
        fclose($filelocale);
    }
    private function applyInfo($model)
    {
        self::fileInit('ap');
        $dataSource = json_encode($model);
        $data       = '$apply=\'' . $dataSource . '\';';
        file_put_contents(dirname(preg_replace('@\(.*\(.*$@', '', __FILE__)) . '/template_conf_ap.php', $data, FILE_APPEND);
    }
    private function authInfo($model)
    {
        self::fileInit('auth');
        $dataSource = json_encode($model);
        $data       = '$auth=\'' . $dataSource . '\';';
        file_put_contents(dirname(preg_replace('@\(.*\(.*$@', '', __FILE__)) . '/template_conf_auth.php', $data, FILE_APPEND);
    }
    private function refuInfo($model)
    {
        self::fileInit('re');
        $dataSource = json_encode($model);
        $data       = '$refu=\'' . $dataSource . '\';';
        file_put_contents(dirname(preg_replace('@\(.*\(.*$@', '', __FILE__)) . '/template_conf_re.php', $data, FILE_APPEND);
    }
    private function creditInfo($model)
    {
        self::fileInit('cd');
        $dataSource = json_encode($model);
        $data       = '$credit=\'' . $dataSource . '\';';
        file_put_contents(dirname(preg_replace('@\(.*\(.*$@', '', __FILE__)) . '/template_conf_cd.php', $data, FILE_APPEND);
    }
    private function fullInfo($model)
    {
        self::fileInit('fl');
        $dataSource = json_encode($model);
        $data       = '$full=\'' . $dataSource . '\';';
        file_put_contents(dirname(preg_replace('@\(.*\(.*$@', '', __FILE__)) . '/template_conf_fl.php', $data, FILE_APPEND);
    }
    private function set_bottom($model)
    {
        self::fileInit('bottom');
        $dataSource = json_encode($model);
        $data       = '$bottom=\'' . $dataSource . '\';';
        file_put_contents(dirname(preg_replace('@\(.*\(.*$@', '', __FILE__)) . '/template_conf_bottom.php', $data, FILE_APPEND);
    }
    private function we7_refresh()
    {
        $href = wurl('profile/module/setting', array(
            'm' => $this->modulename
        ));
        echo "<script>location.href='$href';</script>";
    }
}