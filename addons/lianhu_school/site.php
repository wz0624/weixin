<?php
defined('IN_IA') or exit('Access Denied');
require('global.inc.php');
require('money.php');
include('emoji.php');
include('qiniu/autoload.php');
use Qiniu\Auth as QiniuAuth;
use Qiniu\Storage\UploadManager as QiniuUploadManager;
class Lianhu_schoolModuleSite extends WeModuleSite
{
    public $ac;
    public $op;
    public $where_uniacid_school;
    public $table_pe;
    public function __construct()
    {
        global $_W, $_GPC;
        @session_start();
        $table_pe       = tablename('lianhu');
        $table_pe       = trim($table_pe, '`');
        $table_pe       = str_ireplace('lianhu', '', $table_pe);
        $this->table_pe = $table_pe;
        load()->func('tpl');
        load()->func('file');
        load()->model('mc');
        $this->ac = $_GPC['ac'] ? $_GPC['ac'] : 'list';
        $this->op = $_GPC['op'] ? $_GPC['op'] : 'list';
        if ($_SESSION['school_id']) {
            if ($_SESSION['uniacid'] != $_W['uniacid'] && $_SESSION['uniacid']) {
                unset($_SESSION['school_id']);
            } else {
                $_SESSION['school_name']    = pdo_fetchcolumn("select school_name from {$table_pe}lianhu_school where school_id=:sid", array(
                    ':sid' => $_SESSION['school_id']
                ));
                $this->where_uniacid_school = " uniacid={$_W['uniacid']} and school_id={$_SESSION['school_id']} ";
            }
        }
        $_SESSION['uniacid'] = $_W['uniacid'];
    }
    public function __call($function_name, $args)
    {
        if (strstr($function_name, 'doWeb')) {
            $fname = str_ireplace('doWeb', '', $function_name);
            $this->__web($fname);
        }
        if (strstr($function_name, 'doMobile')) {
            $fname = str_ireplace('doMobile', '', $function_name);
            $this->__mobile($fname);
        }
    }
    public function selectTemplate($module)
    {
        $school_id = $_SESSION['school_id'];
        if ($school_id)
            $mu_str = pdo_fetchcolumn("select mu_str from " . $this->table_pe . "lianhu_school where school_id=:sid", array(
                ':sid' => $school_id
            ));
        if ($mu_str)
            $mu_str = "../{$mu_str}/";
        else
            $mu_str = '';
        $out = $mu_str . $module;
        return $out;
    }
    public function __mobile($module)
    {
        global $_GPC, $_W;
        $uid      = $_W['member']['uid'];
        $table_pe = $this->table_pe;
        if (empty($uid))
            $uid = $this->register_member();
        if (!$_W['member']['uid']) {
            exit("只能在微信里访问");
        }
        $ac        = $this->ac;
        $op        = $this->op;
        $total     = 10;
        $pagesize  = 20;
        $page      = $_GPC['page'] ? $_GPC['page'] : 1;
        $page      = (int) $page;
        $start_num = ($page - 1) * $pagesize;
        $sql_limit = "limit {$start_num},{$pagesize} ";
        if (!file_exists(MODULE_ROOT . '/module/mobile/' . $module . '.php')) {
            header('Location:' . $this->createMobileUrl('home'));
        }
        require('module/mobile/' . $module . '.php');
        $pager       = pagination($total, $page, $pagesize);
        $signPackage = $this->getSignPackage();
        $template    = $this->selectTemplate($module);
        include $this->template(strtolower($template));
    }
    public function __web($module)
    {
        global $_GPC, $_W;
        $table_pe = $this->table_pe;
        $this->controllerSchoolAdmin();
        $this->teacherLoginWeb();
        $this->modelBeginSet($module);
        $ac        = $this->ac;
        $op        = $this->op;
        $total     = 10;
        $pagesize  = 20;
        $page      = $_GPC['page'] ? $_GPC['page'] : 1;
        $page      = (int) $page;
        $start_num = ($page - 1) * $pagesize;
        $sql_limit = "limit {$start_num},{$pagesize} ";
        require('module/web/' . $module . '.php');
        $pager = pagination($total, $page, $pagesize);
        include $this->template(strtolower($module));
    }
    public function controllerSchoolAdmin()
    {
        global $_W;
        $uid    = $_W['uid'];
        $result = pdo_fetch("select * from " . $this->table_pe . "lianhu_school_admin where uid=:uid", array(
            ':uid' => $uid
        ));
        if ($result['status'] == 0 && $result) {
            exit("您无权限");
        } elseif ($result) {
            $_SESSION['school_id'] = $result['school_id'];
            $_SESSION['uniacid']   = $_W['uniacid'];
        }
    }
    public function teacherLoginWeb()
    {
        global $_W, $_GPC;
        load()->model('user');
        $uid          = $_W['uid'];
        $user_info    = user_single($uid);
        $group_id     = $user_info['groupid'];
        $group_id_tea = pdo_fetchcolumn("select id from " . tablename('users_group') . " where name='教师组' ");
        if ($group_id == $group_id_tea && !$_SESSION['school_id']) {
            $_SESSION['school_id'] = pdo_fetchcolumn("select school_id from " . $this->table_pe . "lianhu_teacher where fanid={$uid}");
            $_SESSION['uniacid']   = $_W['uniacid'];
        }
    }
    public function getWebAdminName()
    {
        $teacher = $this->teacher_qx('no');
        if ($teacher == 'no')
            $teacher_name = pdo_fetchcolumn("select teacher_realname from " . $this->table_pe . "lianhu_teacher where fanid={$uid}");
        else
            $teacher_name = '管理员';
        return $teacher_name;
    }
    public function modelBeginSet($module)
    {
        global $_W, $_GPC;
        $group_id_tea = pdo_fetchcolumn("select id from " . tablename('users_group') . " where name='教师组' ");
        if (!$group_id_tea)
            message('请先设置一个教师用户组哦(组名：教师组)', '', 'error');
        $school = pdo_fetch("select * from " . $this->table_pe . "lianhu_school where status=1");
        if (!$school)
            message('请先设置一个有效地学校', $this->createWebUrl('school'), 'error');
        if ($_SESSION['school_id'] || $_COOKIE['school_id']) {
            if (!$_SESSION['school_id'] && $_COOKIE['school_id']) {
                $_SESSION['school_id']      = $_COOKIE['school_id'];
                $this->where_uniacid_school = " uniacid={$_W['uniacid']} and school_id={$_SESSION['school_id']} ";
            }
            $config           = $this->module['config'];
            $need_arr         = array(
                'msg',
                'msg1',
                'version'
            );
            $need_session_arr = array(
                'am_much',
                'pm_much',
                'on_school'
            );
            foreach ($need_arr as $key => $value) {
                if (!$config[$value])
                    message('参数设置里有参数未设置(通知/在校时间/上下晚课时等)', 'error');
            }
            foreach ($need_session_arr as $key => $value) {
                if (!$config[$value][$_SESSION['school_id']])
                    message('参数设置里有参数未设置(通知/在校时间/上下晚课时等)', 'error');
            }
            if ($module == 'Grade')
                return true;
            $grade = pdo_fetch("select * from " . $this->table_pe . "lianhu_grade where status=1 and school_id={$_SESSION['school_id']}");
            if (!$grade)
                message('请先设置一个有效的年级', $this->createWebUrl('grade'), 'error');
            if ($module == 'Class')
                return true;
            $class = pdo_fetch("select * from " . $this->table_pe . "lianhu_class where status=1 and school_id={$_SESSION['school_id']}");
            if (!$class)
                message('请先设置一个有效的班级', $this->createWebUrl('Class'), 'error');
            if ($module == 'Course')
                return true;
            $course = pdo_fetch("select * from " . $this->table_pe . "lianhu_course where school_id={$_SESSION['school_id']}");
            if (!$course)
                message('请先设置一个有效的课程', $this->createWebUrl('course'), 'error');
        } else {
            message("请先选择登陆的学校", $this->createWebUrl('school'), 'error');
        }
    }
    public function classHead($teacher_id)
    {
        $where_uniacid_school = $this->where_uniacid_school;
        $list                 = pdo_fetchall("select * from " . $this->table_pe . "lianhu_class where 
                teacher_id=:tid and status=1  and  {$where_uniacid_school} ", array(
            ':tid' => $teacher_id
        ));
        if (!$list)
            return false;
        return $list;
    }
    public function teacherCourse($teacher_id, $world)
    {
        $result = pdo_fetch("select * from " . $this->table_pe . "lianhu_teacher where teacher_id=:tid", array(
            ':tid' => $teacher_id
        ));
        if (!$result['course_id'])
            return '';
        $course_list = pdo_fetchall("select * from " . $this->table_pe . "lianhu_course where course_id in ({$result['course_id']})  ");
        if ($world == 'echo') {
            foreach ($course_list as $key => $value) {
                $str .= $value['course_name'] . ',';
            }
            $str = trim($str, ',');
            return $str;
        }
        return $course_list;
    }
    public function returnAllEfficeCourse()
    {
        global $_W;
        $school_uniacid = "  uniacid={$_W['uniacid']} and school_id={$_SESSION['school_id']} ";
        $list           = pdo_fetchall("select * from  " . $this->table_pe . "lianhu_course where {$school_uniacid} ");
        return $list;
    }
    public function echoArrOne($arr, $name)
    {
        foreach ($arr as $key => $val) {
            $str .= $val[$name] . ',';
        }
        $str = trim($str, ',');
        return $str;
    }
    public function gradeName($grade_id)
    {
        if (empty($grade_id))
            return false;
        return pdo_fetchcolumn("select grade_name from " . $this->table_pe . "lianhu_grade where grade_id=:gid", array(
            ':gid' => $grade_id
        ));
    }
    public function className($class_id)
    {
        if (empty($class_id))
            return false;
        return pdo_fetchcolumn("select class_name from " . $this->table_pe . "lianhu_class where class_id=:cid ", array(
            ':cid' => $class_id
        ));
    }
    public function studentName($student_id)
    {
        return pdo_fetchcolumn("select student_name from " . $this->table_pe . "lianhu_student where student_id=:sid ", array(
            ":sid" => $student_id
        ));
    }
    public function memberNickName($uid)
    {
        return pdo_fetchcolumn("select nickname from " . tablename('mc_members') . " where uid=:uid ", array(
            ":uid" => $uid
        ));
    }
    public function teacherName($tid)
    {
        if (!$tid)
            return false;
        return pdo_fetchcolumn("select teacher_realname from " . $this->table_pe . "lianhu_teacher where teacher_id=:tid ", array(
            ":tid" => $tid
        ));
    }
    public function getTeacherImg($tid)
    {
        return pdo_fetchcolumn("select teacher_img from " . $this->table_pe . "lianhu_teacher where teacher_id=:tid ", array(
            ":tid" => $tid
        ));
    }
    public function zanLine($send_id)
    {
        global $_W;
        $uid   = $_W['member']['uid'];
        $count = pdo_fetchcolumn("select count(*) from " . $this->table_pe . "lianhu_send_like where uid=:uid and send_id=:send_id ", array(
            ':uid' => $uid,
            ':send_id' => $send_id
        ));
        return $count;
    }
    public function getTeacherClass($teacher_id, $get_all = false)
    {
        if (empty($teacher_id))
            return false;
        $list = pdo_fetchall("select class.*,grade.grade_name from " . $this->table_pe . "lianhu_class class 
                            left join " . $this->table_pe . "lianhu_grade grade on grade.grade_id=class.grade_id  
                            where class.status=1 and class.teacher_id=:tid", array(
            ':tid' => $teacher_id
        ));
        if ($get_all) {
            $class_ids = pdo_fetchcolumn("select teacher_other_power from  " . $this->table_pe . "lianhu_teacher where teacher_id=:tid ", array(
                ':tid' => $teacher_id
            ));
            if ($class_ids) {
                $cid_arr   = explode(',', $class_ids);
                $class_ids = implode(',', $cid_arr);
                $list_all  = pdo_fetchall("select class.*, grade.grade_name from " . $this->table_pe . "lianhu_class class 
                                        left join " . $this->table_pe . "lianhu_grade grade on grade.grade_id=class.grade_id  
                                        where class.status=1 and class.class_id in ({$class_ids})");
            }
        }
        return array(
            'list' => $list,
            'list_all' => $list_all
        );
    }
    public function upToQiniu($imgname)
    {
        global $_W, $_GPC;
        if (!$this->module['config']['qiniu'])
            return false;
        $accessKey = $this->module['config']['qiniu_AccessKey'];
        $secretKey = $this->module['config']['qiniu_SecretKey'];
        $auth      = new QiniuAuth($accessKey, $secretKey);
        $bucket    = $this->module['config']['qiniu_bucket'];
        $token     = $auth->uploadToken($bucket);
        $filePath  = ATTACHMENT_ROOT . $imgname;
        $key       = "qiniu" . $imgname;
        $uploadMgr = new QiniuUploadManager();
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        if ($err !== null)
            return false;
        else
            return $key;
    }
    public function imgFrom($imgname)
    {
        global $_W;
        if (stristr($imgname, "qiniu")) {
            return $this->module['config']['qiniu_url'] . $imgname;
        } else {
            return $_W['attachurl'] . $imgname;
        }
    }
    public function getSignPackage()
    {
        global $_W, $_GPC;
        load()->classs('weixin.account');
        $weixin      = new WeiXinAccount($_W['account']);
        $appid       = $_W['account']['key'];
        $protocol    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url         = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $jsapiTicket = $weixin->getJsApiTicket();
        $timestamp   = $_W['account']['jssdkconfig']['timestamp'];
        $nonceStr    = $_W['account']['jssdkconfig']['nonceStr'];
        $string      = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature   = sha1($string);
        $signPackage = array(
            "appId" => $appid,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }
    public function JsapiTicket()
    {
        global $_W;
        $result = pdo_fetch("select * from " . $this->table_pe . "lianhu_wechat where uniacid={$_W['uniacid']} and type=2 order by addtime desc ");
        if (!$result || (TIMESTAMP - $result['addtime']) > 7000) {
            $ticket = $this->getJsapiTicket();
        } else {
            $ticket = $result['code'];
        }
        return $ticket;
    }
    public function AccessToken()
    {
        global $_W;
        $acid = $_W['acid'];
        load()->classs('weixin.account');
        $accObj       = WeixinAccount::create($acid);
        $access_token = $accObj->fetch_token();
        return $access_token;
    }
    public function getAccessToken()
    {
        global $_W;
        load()->model('account');
        $accounts      = uni_accounts();
        $appid         = $accounts[$_W['acid']]['key'];
        $secret        = $accounts[$_W['acid']]['secret'];
        $url           = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
        $content       = file_get_contents($url);
        $arr           = json_decode($content, true);
        $access_token  = $arr['access_token'];
        $in['uniacid'] = $_W['uniacid'];
        $in['code']    = $access_token;
        $in['type']    = 1;
        $in['addtime'] = TIMESTAMP;
        pdo_insert('lianhu_wechat', $in);
        return $access_token;
    }
    public function getJsapiTicket()
    {
        global $_W;
        $access_token  = $this->AccessToken();
        $url           = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$access_token}&type=jsapi";
        $content       = file_get_contents($url);
        $arr           = json_decode($content, true);
        $ticket        = $arr['ticket'];
        $in['uniacid'] = $_W['uniacid'];
        $in['code']    = $ticket;
        $in['type']    = 2;
        $in['addtime'] = TIMESTAMP;
        pdo_insert('lianhu_wechat', $in);
        return $ticket;
    }
    public function topay($params)
    {
        global $_W;
        $config = $this->module['config'];
        if ($config['pay_do'] == 1) {
            header("Location:{$_W['siteroot']}app/index.php?i={$config['pay_uniacid']}&c=entry&do=topay&m=lianhu_school&from_uniacid={$_W['uniacid']}&order_id={$params['tid']}&limit_name={$params['title']}");
        }
        $this->pay($params);
    }
    public function doMobileTopay()
    {
        global $_W, $_GPC;
        $from_uniacid = $_GPC['from_uniacid'];
        $order_id     = $_GPC['order_id'];
        $limit_name   = $_GPC['limit_name'];
        $order_re     = pdo_fetch("select * from " . $this->table_pe . "lianhu_money_record where record_id=:rid", array(
            ':rid' => $order_id
        ));
        $params       = array(
            'tid' => $order_id,
            'ordersn' => "MMD" . $order_id,
            'title' => $limit_name,
            'fee' => $order_re['limit_much'],
            'user' => $_W['member']['uid']
        );
        $this->pay($params);
    }
    public function doWebSchool()
    {
        global $_GPC, $_W;
        $this->teacher_qx();
        $op = $this->op;
        if ($op == 'list') {
            $list = pdo_fetchall("select * from " . $this->table_pe . "lianhu_school where uniacid={$_W['uniacid']}");
        }
        if ($op == 'edit') {
            $result = pdo_fetch("select * from " . $this->table_pe . "lianhu_school where school_id=:sid ", array(
                ':sid' => $_GPC['sid']
            ));
            if ($_GPC['submit']) {
                $up                = "";
                $up['school_name'] = $_GPC['school_name'];
                $up['status']      = $_GPC['status'];
                $up['mu_str']      = $_GPC['mu_str'];
                pdo_update('lianhu_school', $up, array(
                    'school_id' => $_GPC['sid']
                ));
                message("更新成功", $this->createWebUrl('school'), 'success');
            }
        }
        if ($op == 'new') {
            if ($_GPC['submit']) {
                $up['school_name'] = $_GPC['school_name'];
                $up['status']      = $_GPC['status'];
                $up['uniacid']     = $_W['uniacid'];
                $up['addtime']     = TIMESTAMP;
                $up['status']      = $_GPC['status'];
                $up['mu_str']      = $_GPC['mu_str'];
                pdo_insert('lianhu_school', $up);
                message("新增成功", $this->createWebUrl('school'), 'success');
            }
        }
        if ($op == 'select') {
            $result = pdo_fetch("select * from " . $this->table_pe . "lianhu_school where school_id=:sid ", array(
                ':sid' => $_GPC['sid']
            ));
            if ($result) {
                $_SESSION['school_id'] = $result['school_id'];
                setcookie('school_id', $_SESSION['school_id'], TIMESTAMP + 3600 * 24 * 7, '/');
                message("切换成功", '', 'success');
            } else {
                message("切换失败", '', 'error');
            }
        }
        include $this->template('school');
    }
    public function getWeixinToken()
    {
        global $_W, $_GPC;
        $acid = pdo_fetchcolumn("select acid from " . tablename('account') . " where uniacid ={$_W['uniacid']} ");
        if ($acid) {
            load()->classs('weixin.account');
            $accObj       = WeixinAccount::create($acid);
            $access_token = $accObj->fetch_token();
            return $access_token;
        } else {
            exit('升级中。。。');
        }
    }
    public function returnEfficeOpenid($student, $num = 1)
    {
        $openid  = pdo_fetchcolumn("select openid from " . tablename('mc_mapping_fans') . " where fanid={$student['fanid']} ");
        $openid1 = pdo_fetchcolumn("select openid from " . tablename('mc_mapping_fans') . " where fanid={$student['fanid1']} ");
        $openid2 = pdo_fetchcolumn("select openid from " . tablename('mc_mapping_fans') . " where fanid={$student['fanid2']} ");
        if ($openid)
            $f_openid = $openid;
        if (!$openid && $openid1)
            $f_openid = $openid1;
        if (!$openid && !$openid1 && $openid2)
            $f_openid = $openid2;
        if ($openid && $openid1)
            $s_openid = $openid1;
        if (!$openid && $openid1)
            $s_openid = $openid1;
        if (!$openid && $openid1 && $openid2)
            $s_openid = $openid2;
        if ($openid && $openid1 && $openid2)
            $t_openid = $openid3;
        if ($num == 1)
            return $f_openid;
        if ($num == 2)
            return array(
                'f_openid' => $f_openid,
                's_openid' => $s_openid
            );
        if ($num == 3)
            return array(
                'f_openid' => $f_openid,
                's_openid' => $s_openid,
                't_openid' => $t_openid
            );
    }
    public function sendcustomMsg($from_user, $msg)
    {
        $access_token = $this->getWeixinToken();
        $url          = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";
        $msg          = str_replace('"', '\\"', $msg);
        $post         = '{"touser":"' . $from_user . '","msgtype":"text","text":{"content":"' . $msg . '"}}';
        $this->curlPost($url, $post);
    }
    public function curlPost($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $info = curl_exec($ch);
        curl_close($ch);
        return $info;
    }
    private function curlGet($url)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        $sContent = curl_exec($oCurl);
        $aStatus  = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }
    function getImg($url = "", $filename = "")
    {
        $hander = curl_init();
        $fp     = fopen($filename, 'wb');
        curl_setopt($hander, CURLOPT_URL, $url);
        curl_setopt($hander, CURLOPT_FILE, $fp);
        curl_setopt($hander, CURLOPT_HEADER, 0);
        curl_setopt($hander, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($hander, CURLOPT_TIMEOUT, 60);
        curl_exec($hander);
        curl_close($hander);
        fclose($fp);
        Return true;
    }
    public function sendSms($fanid)
    {
        global $_W;
        $result              = pdo_fetch("select * from " . tablename('mc_mapping_fans') . " where fanid={$fanid} ");
        $_W['member']['uid'] = $result['uid'];
        $_W['openid']        = $result['openid'];
        $class_money         = new money('sms', $this->table_pe);
        $not_need_to         = $class_money->money_judge();
        $_W['member']['uid'] = 0;
        $_W['openid']        = 0;
        if ($not_need_to) {
            $phone = pdo_fetchcolumn("select mobile from " . tablename('mc_members') . " where uid=:uid", array(
                ':uid' => $result['uid']
            ));
            return $phone;
        } else {
            return false;
        }
    }
    public function toSendCustomNotice($openid, $title, $content, $url)
    {
        $send_text .= "您好，您有一个学校通知\r\n";
        $send_text .= "标题：{$title}\r\n";
        $send_text .= "时间：" . date("Y-m-d H:i:s", time()) . "\r\n";
        $send_text .= "内容：{$content}\r\n";
        $send_text .= "<a href='" . $url . "'>点此查看详情>></a>";
        $this->sendcustomMsg($openid, $send_text);
    }
    public function courseName($course_id)
    {
        $course_name = pdo_fetchcolumn("select course_name from " . $this->table_pe . "lianhu_course where course_id=:cid", array(
            ":cid" => $course_id
        ));
        return $course_name;
    }
    public function classTeacher($class_id)
    {
        $result = pdo_fetchall("select * from " . $this->table_pe . "lianhu_teacher where 
                     teacher_other_power like :power and status=1", array(
            ':power' => "%{$class_id}%"
        ));
        return $result;
    }
    public function classCourse($class_id, $course_name)
    {
        $course_id = pdo_fetchcolumn("select course_id from " . $this->table_pe . "lianhu_course where course_name=:name", array(
            ':name' => $course_name
        ));
        if (!$course_id)
            return 'no';
        $teacher_name = pdo_fetchcolumn("select teacher_realname from " . $this->table_pe . "lianhu_teacher where 
                    course_id=:cid and teacher_other_power like :power limit 1", array(
            ':cid' => $course_id,
            ':power' => "%{$class_id}%"
        ));
        return $teacher_name;
    }
    public function classCouldCourse($class_id, $course_name)
    {
        $course_id = pdo_fetchcolumn("select course_id from " . $this->table_pe . "lianhu_course where course_name=:name", array(
            ':name' => $course_name
        ));
        if (!$course_id)
            return 'no';
        $teacher_list = pdo_fetchall("select * from " . $this->table_pe . "lianhu_teacher where 
                   (course_id ={$course_id} or course_id like '{$course_id},%' or course_id like '%,{$course_id},%' or course_id like '%,{$course_id}') 
                   and teacher_other_power like :power ", array(
            ':power' => "%{$class_id}%"
        ));
        return $teacher_list;
    }
    public function judePortType()
    {
        if ($_SESSION['student_mobile']) {
            $student_info = $this->mobile_from_find_student();
            return array(
                'type' => 'student',
                'info' => $student_info
            );
        }
        if ($_SESSION['teacher_mobile']) {
            $teacher_re = $this->teacher_mobile_qx(true);
            return array(
                'type' => 'teacher',
                'info' => $teacher_re
            );
        }
    }
    public function find_teacher_by_uid($uid, $ziduan = '', $course = false)
    {
        $result = pdo_fetch("select * from " . $this->table_pe . "lianhu_teacher where  fanid=:fanid ", array(
            ':fanid' => $uid
        ));
        if (!$result) {
            return '管理员测试';
        } else {
            if (!$course) {
                return $result[$ziduan];
            } else {
                if (!$result['course_id']) {
                    return '未绑定课程';
                }
                $course_name = pdo_fetchcolumn("select course_name from " . $this->table_pe . "lianhu_course where course_id=:cid ", array(
                    ':cid' => $result['course_id']
                ));
                return $course_name;
            }
        }
    }
    public function delete_course_teacher($cid, $model)
    {
        global $_W;
        if ($model == 'all') {
            $school_uniacid = " and uniacid={$_W['uniacid']} and school_id={$_SESSION['school_id']} ";
            $list           = pdo_fetchall("select * from " . $this->table_pe . "lianhu_teacher where 1=1 {$school_uniacid} ");
        } else {
            $list = pdo_fetch("select * from " . $this->table_pe . "lianhu_teacher where teacher_id=:teacher_id", array(
                ':teacher_id' => $model
            ));
        }
        foreach ($list as $key => $value) {
            if ($value['course_id'] == $cid) {
                $up['course_id'] = '';
                pdo_update('lianhu_teacher', $up, array(
                    ':teacher_id' => $value['teacher_id']
                ));
            } else {
                continue;
            }
        }
    }
    public function add_course_class($cid, $model)
    {
        global $_W;
        if ($model == 'all') {
            $school_uniacid = " and uniacid={$_W['uniacid']} and school_id={$_SESSION['school_id']} ";
            $list           = pdo_fetchall("select * from " . $this->table_pe . "lianhu_class where 1=1 {$school_uniacid} ");
        } else {
            $list = pdo_fetch("select * from " . $this->table_pe . "lianhu_class where class_id=:cid", array(
                ':cid' => $model
            ));
        }
        foreach ($list as $key => $value) {
            if ($value['course_ids']) {
                $class_course_id_arr = unserialize($value['course_ids']);
                $d                   = false;
                foreach ($class_course_id_arr as $k => $v) {
                    if ($v == $cid) {
                        $d = true;
                        break;
                    }
                }
                if (!$d) {
                    array_push($class_course_id_arr, $cid);
                }
            } else {
                $class_course_id_arr[0] = $cid;
            }
            $up['course_ids'] = serialize($class_course_id_arr);
            pdo_update('lianhu_class', $up, array(
                'class_id' => $value['class_id']
            ));
        }
    }
    public function delete_course_class()
    {
        global $_W;
        if ($model == 'all') {
            $school_uniacid = " and uniacid={$_W['uniacid']} and school_id={$_SESSION['school_id']} ";
            $list           = pdo_fetchall("select * from " . $this->table_pe . "lianhu_class where 1=1 {$school_uniacid}  ");
        } else {
            $list = pdo_fetch("select * from " . $this->table_pe . "lianhu_class where class_id=:cid", array(
                ':cid' => $model
            ));
        }
        foreach ($list as $key => $value) {
            if ($value['course_ids']) {
                $class_course_id_arr = unserialize($value['course_ids']);
                foreach ($class_course_id_arr as $k => $v) {
                    if ($v == $cid) {
                        unset($class_course_id_arr[$k]);
                        break;
                    }
                }
            } else {
                continue;
            }
            $up['course_ids'] = serialize($class_course_id_arr);
            pdo_update('lianhu_class', $up, array(
                'class_id' => $value['class_id']
            ));
        }
    }
    public function student_standard()
    {
        global $_GPC, $_W;
        load()->func('tpl');
        $quanxian = $this->teacher_standard();
        $model    = $_GPC['model'] ? $_GPC['model'] : "grade";
        $grade_id = $_GPC['gid'] ? $_GPC['gid'] : 0;
        if ($model == 'someone') {
            $student_id = intval($_GPC['sid']);
            if (!$student_id) {
                message('非法访问，没有学生id', '', 'error');
            }
            $student_result = pdo_fetch("select * from " . $this->table_pe . "lianhu_student where student_id={$student_id} ");
            if (!$student_result) {
                message('没有查到此学生', '', 'error');
            }
            $result = $student_result;
        } elseif ($model == 'student') {
            $class_id = intval($_GPC['cid']);
            $ff       = false;
            foreach ($quanxian as $key => $value) {
                foreach ($value as $k => $val) {
                    if ($val == $class_id) {
                        $ff = true;
                        break;
                    }
                }
            }
            if ($ff == false) {
                message('没有访问此班级的权限', '', 'error');
            }
            if (!$class_id) {
                message('非法访问，没有班级id', '', 'error');
            }
            $class_result = pdo_fetch("select * from " . $this->table_pe . "lianhu_class where class_id={$class_id} ");
            if (!$class_result) {
                message('没有查到此班级', '', 'error');
            }
            $student_list = pdo_fetchall("select * from " . $this->table_pe . "lianhu_student where class_id={$class_id} and status=1 ");
            $result       = $student_list;
        } elseif ($model == 'grade') {
            foreach ($quanxian as $key => $value) {
                $grades[] = $key;
            }
            $result = $grades;
        } elseif ($model == 'class') {
            $class_s = $quanxian[$grade_id];
            if (!$class_s) {
                message('非法访问', '', 'error');
            }
            $result = $class_s;
        }
        return $result;
    }
    public function teacher_standard($model = '')
    {
        global $_W, $_GPC;
        load()->model('user');
        $uid       = $_W['uid'];
        $user_info = user_single($uid);
        $group_id  = $user_info['groupid'];
        $do        = $this->teacher_qx('no');
        if ($do != 'teacher') {
            $school_uniacid = " and " . $this->where_uniacid_school;
            $class_all      = pdo_fetchall("select class_id from " . $this->table_pe . "lianhu_class where status=1 {$school_uniacid} ");
            return $this->juhe_class($class_all);
        }
        $group_name = pdo_fetchcolumn("select name from " . tablename('users_group') . " where id={$group_id}");
        if ($group_name == '教师组') {
            $teacher_result = pdo_fetch("select * from " . $this->table_pe . "lianhu_teacher where fanid={$uid} ");
            if ($teacher_result['status'] == 0) {
                message('该教师账号已经在家校通注销，不能登陆', '', 'error');
            }
            if (empty($teacher_result['teacher_other_power'])) {
                message('', '该账号没有管理权限，因此没有访问的必要', 'error');
            }
            $class_s = explode(',', $teacher_result['teacher_other_power']);
            if ($model == 'no') {
                return $class_s;
            }
            return $this->juhe_class($class_s);
        } else {
            message("该用户既不是超级管理员，也不是教师组成员，无法登陆此模块", "", "error");
        }
    }
    public function teacher_main($all_teacher = false)
    {
        global $_W;
        $teacher = $this->teacher_qx('no');
        if ($teacher == 'teacher') {
            $uid  = $_W['uid'];
            $t_id = pdo_fetchcolumn("select teacher_id from " . $this->table_pe . "lianhu_teacher where fanid={$uid}");
            $list = pdo_fetchall("select class.*,grade.grade_name from " . $this->table_pe . "lianhu_class class 
                         left join " . $this->table_pe . "lianhu_grade grade on grade.grade_id=class.grade_id  
                        where class.status=1 and class.teacher_id={$t_id}");
            if ($all_teacher) {
                $class_ids = pdo_fetchcolumn("select teacher_other_power from  " . $this->table_pe . "lianhu_teacher where teacher_id=:tid ", array(
                    ':tid' => $t_id
                ));
                if ($class_ids) {
                    $cid_arr   = explode(',', $class_ids);
                    $class_ids = implode(',', $cid_arr);
                    $list      = pdo_fetchall("select class.*, grade.grade_name from " . $this->table_pe . "lianhu_class class 
                                        left join " . $this->table_pe . "lianhu_grade grade on grade.grade_id=class.grade_id  
                                        where class.status=1 and class.class_id in ({$class_ids})");
                }
            }
        } else {
            $school_uniacid_class = " and class.uniacid={$_W['uniacid']} and class.school_id={$_SESSION['school_id']} ";
            $list                 = pdo_fetchall("select class.*, grade.grade_name from " . $this->table_pe . "lianhu_class class 
                                left join " . $this->table_pe . "lianhu_grade grade on grade.grade_id=class.grade_id  
                                where class.status=1 {$school_uniacid_class} ");
        }
        if (!$list) {
            message("您既不是班主任，也不是管理员，无法进入", '', 'error');
        }
        return $list;
    }
    public function teacher_class_list()
    {
    }
    public function teacher_qx($model = '')
    {
        global $_W, $_GPC;
        load()->model('user');
        $uid          = $_W['uid'];
        $user_info    = user_single($uid);
        $group_id     = $user_info['groupid'];
        $group_id_tea = pdo_fetchcolumn("select id from " . tablename('users_group') . " where name='教师组' ");
        if (!$group_id_tea) {
            message('请先设置一个教师用户组哦(组名：教师组)', '', 'error');
        }
        if ($group_id == $group_id_tea) {
            if ($model == 'no') {
                $_SESSION['school_id'] = pdo_fetchcolumn("select school_id from " . $this->table_pe . "lianhu_teacher where fanid={$uid}");
                return 'teacher';
            } else {
                message('只有管理员才能访问', '', 'error');
            }
        } else {
            if (!$_SESSION['school_id']) {
                if ($_COOKIE['school_id']) {
                    $_SESSION['school_id'] = $_COOKIE['school_id'];
                } else {
                    $_SESSION['school_id'] = pdo_fetchcolumn("select school_id from " . $this->table_pe . "lianhu_school where status=1 limit 1");
                    setcookie('school_id', $_SESSION['school_id'], TIMESTAMP + 3600 * 24 * 7, '/');
                }
            }
        }
    }
    public function juhe_class($class_s)
    {
        $quanxian = array(
            array()
        );
        foreach ($class_s as $key => $value) {
            if (is_array($value)) {
                $value = $value['class_id'];
            }
            if ($value) {
                $grade_id = pdo_fetchcolumn("select grade_id from " . $this->table_pe . "lianhu_class where class_id=" . $value);
                if ($quanxian[$grade_id]) {
                    array_push($quanxian[$grade_id], $value);
                } else {
                    $quanxian[$grade_id][0] = $value;
                }
            }
        }
        foreach ($quanxian as $key => $value) {
            if (!$value) {
                unset($quanxian[$key]);
            }
        }
        return $quanxian;
    }
    public function result_result($row, $model, $where, $url)
    {
        if ($model == 'grade') {
            $gid = intval($row);
            if ($where == 'name') {
                echo pdo_fetchcolumn("select grade_name from " . $this->table_pe . "lianhu_grade where grade_id={$gid} ");
            }
            if ($where == 'url') {
                if ($url == 'tea_jinbu_record' || $url == 'tea_error_record' || $url == 'tea_work_record') {
                    echo $this->createMobileUrl($url, array(
                        'model' => 'class',
                        'gid' => $gid
                    ));
                } elseif ($url == 'msg' || $url == 'test' || $url == 'score_list') {
                    echo $this->createWebUrl($url, array(
                        'model' => 'class',
                        'gid' => $gid
                    ));
                } else {
                    echo $this->createWebUrl('student_record', array(
                        'model' => 'class',
                        'gid' => $gid,
                        'ac' => $url
                    ));
                }
            }
        }
        if ($model == 'class') {
            $cid = intval($row);
            if ($where == 'name') {
                echo pdo_fetchcolumn("select class_name from " . $this->table_pe . "lianhu_class where class_id={$cid} ");
            }
            if ($where == 'url') {
                if ($url == 'tea_msg' || $url == 'tea_score_in' || $url == 'tea_jinbu_record' || $url == 'tea_error_record' || $url == 'tea_work_record') {
                    echo $this->createMobileUrl($url, array(
                        'model' => 'student',
                        'cid' => $cid
                    ));
                } elseif ($url == 'msg' || $url == 'test' || $url == 'score_list') {
                    echo $this->createWebUrl($url, array(
                        'model' => 'student',
                        'cid' => $cid
                    ));
                } else {
                    echo $this->createWebUrl('student_record', array(
                        'model' => 'student',
                        'cid' => $cid,
                        'ac' => $url
                    ));
                }
            }
        }
        if ($model == 'student') {
            if ($where == 'name') {
                echo $row['student_name'];
            }
            if ($where == 'url') {
                if ($url == 'tea_jinbu_record' || $url == 'tea_error_record' || $url == 'tea_work_record') {
                    echo $this->createMobileUrl($url, array(
                        'model' => 'someone',
                        'sid' => $row['student_id']
                    ));
                } elseif ($url == 'msg' || $url == 'test' || $url == 'score_list') {
                    echo $this->createWebUrl($url, array(
                        'model' => 'someone',
                        'sid' => $row['student_id']
                    ));
                } else {
                    echo $this->createWebUrl('student_record', array(
                        'model' => 'someone',
                        'sid' => $row['student_id'],
                        'ac' => $url
                    ));
                }
            }
        }
    }
    public function checkmobile($fanid)
    {
        $hav = pdo_fetch("select * from " . $this->table_pe . "lianhu_student where fanid={$fanid} or fanid1={$fanid} or fanid2={$fanid} ");
        if (!$hav) {
            message("您还未绑定学生账号", $this->createMobileUrl('bangding'), 'error');
        } else {
            $class_money = new money('bangding', $this->table_pe);
            $not_need_to = $class_money->money_judge();
            if (!$not_need_to) {
                $params = $class_money->money_to_order();
                $this->topay($params);
            }
        }
    }
    public function register_member()
    {
        global $_GPC, $_W;
        load()->model('mc');
        $profile = mc_oauth_userinfo($_W['acid']);
        $uid     = mc_openid2uid($profile['openid']);
        if (empty($uid)) {
            $row = array(
                'uniacid' => $_W['uniacid'],
                'nickname' => $profile['realname'],
                'realname' => $profile['realname'],
                'gender' => 0,
                'salt' => random(8),
                'createtime' => TIMESTAMP,
                'email' => time() . random(8) . '@012wz.com',
                'password' => random(8)
            );
            pdo_insert('mc_members', $row);
            $uid = pdo_insertid();
        }
        if (empty($uid))
            message('发生错误,请联系管理人员', '', 'error');
        $fans = pdo_fetchcolumn('select fanid from ' . tablename('mc_mapping_fans') . ' where openid=:openid and    uid=:uid ', array(
            ':openid' => $profile['openid'],
            ':uid' => $uid
        ));
        if (empty($fans)) {
            $row2 = array(
                'uid' => $uid,
                'openid' => $profile['openid'],
                'uniacid' => $_W['uniacid'],
                'acid' => $_W['acid'],
                'nickname' => $profile['realname'],
                'salt' => $row['salt']
            );
            pdo_insert('mc_mapping_fans', $row2);
        } else
            pdo_update('mc_mapping_fans', array(
                'uid' => $uid
            ), array(
                'openid' => $profile['openid'],
                'uniacid' => $_W['uniacid']
            ));
        $_W['member']['uid'] = $uid;
        return $uid;
    }
    public function doMobileBangding()
    {
        global $_GPC, $_W;
        $uid    = $_W['member']['uid'];
        $config = $this->module['config'];
        if (empty($uid)) {
            $uid = $this->register_member();
        }
        $fanid = pdo_fetchcolumn("select fanid from " . tablename('mc_mapping_fans') . " where uid={$uid} ");
        $hav   = pdo_fetch("select * from " . $this->table_pe . "lianhu_student where fanid={$fanid} or fanid1={$fanid} or fanid2={$fanid} ");
        if ($hav)
            header("Location:" . $this->createMobileUrl('home'));
        if ($_GPC['submit']) {
            $find[':student_passport'] = $_GPC['student_passport'];
            $find[':student_name']     = $_GPC['student_name'];
            if ($config['family_set'] == 'alone_school') {
                $student = pdo_fetch("select * from " . $this->table_pe . "lianhu_student where  xuehao=:student_passport and student_name=:student_name ", $find);
            } else
                $student = pdo_fetch("select * from " . $this->table_pe . "lianhu_student where  student_passport=:student_passport and student_name=:student_name ", $find);
            if ($student) {
                if ($student['fanid'] == $fanid || $student['fanid1'] == $fanid || $student['fanid2'] == $fanid) {
                    message('您已经绑定过此位学生了', $this->createMobileUrl('bangding'), 'error');
                }
                if (!$student['fanid']) {
                    $ziduan = 'fanid';
                } else if (!$student['fanid1']) {
                    $ziduan = 'fanid1';
                } else if (!$student['fanid2']) {
                    $ziduan = 'fanid2';
                } else {
                    message('该学生的三个账号已经被绑定了，无法再绑定', $this->createMobileUrl('bangding'), 'error');
                }
                pdo_update('lianhu_student', array(
                    $ziduan => $fanid
                ), array(
                    'student_id' => $student['student_id']
                ));
                message('绑定成功', $this->createMobileUrl('home'), 'success');
            } else {
                message('您提交的信息有误，无法绑定学生账号', $this->createMobileUrl('bangding'), 'error');
            }
        }
        include $this->template('bangding');
    }
    public function doMobileAdd_student()
    {
        global $_GPC, $_W;
        $uid = $_W['member']['uid'];
        if (empty($uid))
            $uid = $this->register_member();
        $result = $this->mobile_from_find_student();
        $config = $this->module['config'];
        $fanid  = pdo_fetchcolumn("select fanid from " . tablename('mc_mapping_fans') . " where uid={$uid} ");
        if ($_GPC['submit']) {
            $find[':student_passport'] = $_GPC['student_passport'];
            $find[':student_name']     = $_GPC['student_name'];
            if ($config['family_set'] == 'alone_school') {
                $student = pdo_fetch("select * from " . $this->table_pe . "lianhu_student where  xuehao=:student_passport and student_name=:student_name ", $find);
            } else
                $student = pdo_fetch("select * from " . $this->table_pe . "lianhu_student where  student_passport=:student_passport and student_name=:student_name ", $find);
            if ($student) {
                if ($student['fanid'] == $fanid || $student['fanid1'] == $fanid || $student['fanid2'] == $fanid) {
                    message('您已经绑定过此位学生了', $this->createMobileUrl('bangding'), 'error');
                }
                if (!$student['fanid']) {
                    $ziduan = 'fanid';
                } else if (!$student['fanid1']) {
                    $ziduan = 'fanid1';
                } else if (!$student['fanid2']) {
                    $ziduan = 'fanid2';
                } else {
                    message('该学生的三个账号已经被绑定了，无法再绑定', $this->createMobileUrl('bangding'), 'error');
                }
                pdo_update('lianhu_student', array(
                    $ziduan => $fanid
                ), array(
                    'student_id' => $student['student_id']
                ));
                message('绑定成功', $this->createMobileUrl('home'), 'success');
            } else {
                message('您提交的信息有误，无法绑定学生账号', $this->createMobileUrl('bangding'), 'error');
            }
        }
        $signPackage = $this->getSignPackage();
        $template    = $this->selectTemplate('Add_student');
        include $this->template($template);
    }
    public function domobileChange_student()
    {
        global $_W, $_GPC;
        $uid    = $_W['member']['uid'];
        $result = $this->mobile_from_find_student();
        if (empty($uid))
            $uid = $this->register_member();
        $list = $this->mobile_student_list();
        if ($_GPC['op'] == 'post' && $_GPC['sid']) {
            $_SESSION['student_id'] = (int) $_GPC['sid'];
            message("切换成功", $this->createMobileUrl('home'), 'success');
        }
        $signPackage = $this->getSignPackage();
        $template    = $this->selectTemplate('change_student');
        include $this->template($template);
    }
    public function doMobileTeaIn()
    {
        global $_W;
        $uid = $_W['member']['uid'];
        if (empty($uid)) {
            $uid = $this->register_member();
        }
        $result = $this->teacher_mobile_qx();
        if ($result) {
            header("Location:" . $this->createMobileUrl('teacenter'));
        } else {
            header("Location:" . $this->createMobileUrl('teacher'));
            exit();
        }
    }
    public function teacher_mobile_qx($no = false)
    {
        global $_GPC, $_W;
        $uid    = $_W['member']['uid'];
        $result = pdo_fetch("select * from " . $this->table_pe . "lianhu_teacher where uid={$uid} ");
        if ($no && !$result) {
            $_SESSION['teacher_mobile'] = false;
            return false;
        }
        if ($result) {
            $_SESSION['school_id']      = $result['school_id'];
            $_SESSION['teacher_id']     = $result['teacher_id'];
            $_SESSION['teacher_mobile'] = TRUE;
            $_SESSION['student_mobile'] = false;
            return $result;
        } else {
            $_SESSION['teacher_mobile'] = false;
            ;
            header("Location:" . $this->createMobileUrl('teacher'));
            exit();
        }
    }
    public function dealWithIdentity()
    {
        global $_W;
        $uid = $_W['member']['uid'];
    }
    public function doMobileTeacher()
    {
        global $_W, $_GPC;
        $uid = $_W['member']['uid'];
        if (empty($uid)) {
            $uid = $this->register_member();
        }
        load()->model('user');
        if ($_POST['submit'] == 1) {
            if (!$_GPC['passport'] || !$_GPC['password']) {
                message("请填写所有内容", $this->createMobileUrl('teacher'), 'error');
            }
            $result = pdo_fetch("select * from " . tablename('users') . " where username=:username", array(
                ':username' => $_GPC['passport']
            ));
            if (!$result) {
                message('系统中不存在此账号，请重新填写', $this->createMobileUrl('teacher'), 'error');
            }
            $tea_re = pdo_fetch("select uid from " . $this->table_pe . "lianhu_teacher where fanid={$result['uid']} ");
            if ($tea_re['uid'] > 0) {
                message('该老师账号已经被绑定了', $this->createMobileUrl('teacher'), 'error');
            }
            $password = user_hash($_GPC['password'], $result['salt']);
            if ($password == $result['password']) {
                $uid = $_W['member']['uid'];
                $re  = pdo_fetchall("select * from " . $this->table_pe . "lianhu_teacher where uid={$uid} ");
                if ($re) {
                    message('您已经绑定教师，后台可以解绑', $this->createMobileUrl('teacher'), 'error');
                }
                $up['uid'] = $uid;
                pdo_update('lianhu_teacher', $up, array(
                    'fanid' => $result['uid']
                ));
                message('绑定成功，跳转至教师个人中心', $this->createMobileUrl('teacenter'), 'success');
            } else {
                message('密码错误，请重新填写', $this->createMobileUrl('teacher'), 'error');
            }
        } else {
            $template = $this->selectTemplate('teacher');
            include $this->template($template);
        }
    }
    public function mobile_from_find_student($to_bd = true)
    {
        global $_W, $_GPC;
        $uid   = $_W['member']['uid'];
        $fanid = pdo_fetchcolumn("select fanid from " . tablename('mc_mapping_fans') . " where uid={$uid} ");
        if ($_SESSION['student_id'] || $_GPC['student_id'])
            if ($_GPC['student_id'])
                $where = " and  stu.student_id ={$_GPC['student_id']}";
            else
                $where = " and  stu.student_id ={$_SESSION['student_id']}";
        else
            $where = '';
        $result = pdo_fetch("select stu.*, class.class_name ,grade.grade_name, tea.teacher_realname,tea.teacher_id from " . $this->table_pe . "lianhu_student stu 
			left join " . $this->table_pe . "lianhu_class class on class.class_id=stu.class_id left join " . $this->table_pe . "lianhu_grade grade on grade.grade_id=class.grade_id
			left join  " . $this->table_pe . "lianhu_teacher tea on tea.teacher_id=class.teacher_id
			where (stu.fanid={$fanid} or stu.fanid1={$fanid} or stu.fanid2={$fanid}) {$where} ");
        if (empty($result) && $to_bd) {
            $_SESSION['student_mobile'] = FALSE;
            header("Location:" . $this->createMobileUrl('Bangding'));
        } elseif (empty($result) && !$to_bd) {
            $_SESSION['student_mobile'] = FALSE;
            return false;
        } else {
            $_SESSION['school_id'] = $result['school_id'];
            $class_money           = new money('bangding', $this->table_pe);
            $not_need_to           = $class_money->money_judge();
            if (!$not_need_to) {
                $params = $class_money->money_to_order();
                $this->topay($params);
                exit();
            }
            $_SESSION['student_mobile'] = true;
            $_SESSION['teacher_mobile'] = false;
            return $result;
        }
    }
    public function mobile_student_list()
    {
        global $_W, $_GPC;
        $uid   = $_W['member']['uid'];
        $fanid = pdo_fetchcolumn("select fanid from " . tablename('mc_mapping_fans') . " where uid={$uid} ");
        $list  = pdo_fetchall("select stu.*, class.class_name ,grade.grade_name, tea.teacher_realname,tea.teacher_id from " . $this->table_pe . "lianhu_student stu 
			left join " . $this->table_pe . "lianhu_class class on class.class_id=stu.class_id left join " . $this->table_pe . "lianhu_grade grade on grade.grade_id=class.grade_id
			left join  " . $this->table_pe . "lianhu_teacher tea on tea.teacher_id=class.teacher_id
			where stu.fanid={$fanid} or stu.fanid1={$fanid} or stu.fanid2={$fanid} ");
        if (empty($list))
            header("Location:" . $this->createMobileUrl('Bangding'));
        return $list;
    }
    public function doMobileGive_money_order()
    {
        global $_GPC, $_W;
        $module      = $_GPC['name'];
        $class_money = new money($module, $this->table_pe);
        $not_need_to = $class_money->money_judge();
        if (!$not_need_to) {
            $params = $class_money->money_to_order();
            $this->topay($params);
            exit();
        }
    }
    public function payResult($params)
    {
        if (($params['result'] == 'success' && $params['from'] == 'notify') || ($params['result'] == 'success' && $params['type'] == 'credit')) {
            if ($params['tid']) {
                $up['status'] = 1;
                $up['uid']    = $params['user'];
                pdo_update('lianhu_money_record', $up, array(
                    'record_id' => $params['tid']
                ));
            }
        }
        $result = pdo_fetch("select li.* from " . $this->table_pe . "lianhu_money_record left join " . $this->table_pe . "lianhu_money_limit li on li.limit_id=" . $this->table_pe . "lianhu_money_record.limit_id where record_id=:rid ", array(
            ':rid' => $params['tid']
        ));
        if ($params['from'] == 'return') {
            if ($params['result'] == 'success') {
                $url = "{$_W['siteroot']}/app/index.php?i={$result['uniacid']}&c=entry&do=home&m=lianhu_school";
                message('支付成功！', $url, 'success');
            } else {
                message('支付失败！', '', 'error');
            }
        }
    }
    public function MoneyGive($arr = false)
    {
        global $_GPC, $_W;
        $num             = 0;
        $need_money_list = pdo_fetchall("select * from " . $this->table_pe . "lianhu_money_limit where uniacid=:uniacid and school_id=:sid and status=1 ", array(
            ':uniacid' => $_W['uniacid'],
            ':sid' => $_SESSION['school_id']
        ));
        if ($need_money_list) {
            foreach ($need_money_list as $value) {
                $class_money = new money($value['limit_module'], $this->table_pe);
                $not_need_to = $class_money->money_judge();
                if (!$not_need_to) {
                    $out_list[$num]['name']         = $value['limit_name'];
                    $out_list[$num]['money']        = $value['limit_much'];
                    $out_list[$num]['limit_module'] = $value['limit_module'];
                    $num++;
                }
            }
        }
        if ($arr) {
            return $out_list;
        }
        return $num;
    }
    public function doMobileNeimsg()
    {
        global $_W, $_GPC;
        $uid = $_W['member']['uid'];
        if (empty($uid)) {
            $uid = $this->register_member();
        }
        $this->mobile_from_find_student();
        $uid   = $_W['member']['uid'];
        $fanid = pdo_fetchcolumn("select fanid from " . tablename('mc_mapping_fans') . " where uid={$uid} ");
        $this->mobile_from_find_student();
        $list = $this->web_msg();
        foreach ($list as $key => $value) {
            $msg_id_str3 .= "," . $value['msg_id'];
        }
        $msg_id_str  = pdo_fetchcolumn("select msg_id_str from " . $this->table_pe . "lianhu_student where fanid='{$fanid}' or fanid1={$fanid} or fanid2={$fanid}");
        $msg_id_str2 = $msg_id_str . $msg_id_str3;
        $msg_id_str  = trim($msg_id_str2, ',');
        pdo_query("update " . $this->table_pe . "lianhu_student set msg_id_str='{$msg_id_str}' where fanid='{$fanid}' or fanid1={$fanid} or fanid2={$fanid} ");
        $template = $this->selectTemplate('neimsg');
        include $this->template($template);
    }
    public function doMobileNeimsg_tea()
    {
        global $_W, $_GPC;
        $uid = $_W['member']['uid'];
        if (empty($uid)) {
            $uid = $this->register_member();
        }
        $teacher_info = $this->teacher_mobile_qx();
        $_W['uid']    = $teacher_info['uid'];
        $list         = $this->web_msg_tea();
        foreach ($list as $key => $value) {
            $msg_id_str3 .= "," . $value['msg_id'];
        }
        $msg_id_str  = pdo_fetchcolumn("select msg_id_str from " . $this->table_pe . "lianhu_teacher where uid='{$_W['uid']}'");
        $msg_id_str2 = $msg_id_str . $msg_id_str3;
        $msg_id_str  = trim($msg_id_str2, ',');
        pdo_query("update " . $this->table_pe . "lianhu_teacher set msg_id_str='{$msg_id_str}' where uid='{$_W['uid']}'");
        $template = $this->selectTemplate('neimsg');
        include $this->template($template);
    }
    public function doMobilePersoner()
    {
        global $_GPC, $_W;
        $uid = $_W['member']['uid'];
        if (empty($uid)) {
            $uid = $this->register_member();
        }
        if (!$_GPC['t_id']) {
            $uid          = $_W['member']['uid'];
            $fanid        = pdo_fetchcolumn("select fanid from " . tablename('mc_mapping_fans') . " where uid={$uid} ");
            $result       = pdo_fetch("select stu.*, class.class_name ,grade.grade_name, tea.teacher_id from " . $this->table_pe . "lianhu_student stu 
				left join " . $this->table_pe . "lianhu_class class on class.class_id=stu.class_id left join " . $this->table_pe . "lianhu_grade grade on grade.grade_id=class.grade_id
				left join  " . $this->table_pe . "lianhu_teacher tea on tea.teacher_id=class.teacher_id
				where stu.fanid={$fanid} or stu.fanid1={$fanid} or stu.fanid2={$fanid} ");
            $_GPC['t_id'] = $result['teacher_id'];
        }
        $result                = pdo_fetch("select *  from " . $this->table_pe . "lianhu_teacher  where teacher_id=:tid ", array(
            ':tid' => $_GPC['t_id']
        ));
        $_SESSION['school_id'] = $result['school_id'];
        $template              = $this->selectTemplate('personer');
        include $this->template($template);
    }
    public function get_fans_teacher()
    {
        global $_W;
        $school_uniacid = " and uniacid={$_W['uniacid']} and school_id={$_SESSION['school_id']} ";
        $list           = pdo_fetchall("select uid from  " . $this->table_pe . "lianhu_teacher where uid !=0 {$school_uniacid} ");
        if (!$list) {
            return 0;
        }
        foreach ($list as $key => $value) {
            $list2[$key] = $value['uid'];
        }
        return $list2;
    }
    public function get_fans_list($where = "")
    {
        global $_W;
        $fans = pdo_fetchall("select * from  " . tablename('mc_mapping_fans') . " where uniacid=:uniacid and nickname!='' {$where} order by fanid desc ", array(
            ':uniacid' => $_W['uniacid']
        ));
        return $fans;
    }
    public function get_info($table, $sid)
    {
        global $_W;
        $list = pdo_fetchall("select ta.*,tea.teacher_realname from " . $this->table_pe . "" . $table . " ta left join " . $this->table_pe . "lianhu_teacher tea on tea.teacher_id=ta.teacher_id where student_id={$sid} order by addtime desc");
        return $list;
    }
    public function find_user($fanid)
    {
        global $_W, $_GPC;
        $result = pdo_fetch("select nickname from " . tablename('mc_mapping_fans') . " where fanid=:fanid", array(
            ':fanid' => $fanid
        ));
        if (empty($result['nickname'])) {
            $result['nickname'] = $this->repair_info($fanid);
        }
        return $result['nickname'];
    }
    public function repair_info($fanid)
    {
        global $_W;
        $result = pdo_fetch("select * from " . tablename('mc_mapping_fans') . " where fanid=:fanid", array(
            ':fanid' => $fanid
        ));
        if (!$_W['account']['acid']) {
            $res  = pdo_fetch("select acid from " . tablename('account_wechats') . " where uniacid={$_W['uniacid']} ");
            $acid = $res['acid'];
        } else {
            $acid = $_W['account']['acid'];
        }
        load()->classs('weixin.account');
        $accObj           = WeixinAccount::create($acid);
        $access_token     = $accObj->fetch_token();
        $content          = file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$result['openid']}&lang=zh_CN");
        $info             = json_decode($content, true);
        $info['nickname'] = emoji_unified_to_html($info['nickname']);
        $info['nickname'] = strip_tags($info['nickname']);
        $info['nickname'] = str_replace("\\", "", $info['nickname']);
        $up_arr           = array(
            'nickname' => $info['nickname'],
            'realname' => $info['nickname'],
            'avatar' => $info['headimgurl'],
            'gender' => $info['sex'],
            'nationality' => $info['country'],
            'resideprovince' => $info['province'],
            'residecity' => $info['city'],
            'groupid' => $info['groupid']
        );
        $this->update_info('mc_members', $up_arr, array(
            'uid' => $result['uid']
        ));
        $this->update_info('mc_mapping_fans', array(
            'nickname' => $info['nickname']
        ), array(
            'fanid' => $result['fanid']
        ));
        return $info['nickname'];
    }
    private function update_info($table, $value_arr, $where)
    {
        if (!$where)
            message("必须传入更新条件", '', 'error');
        foreach ($value_arr as $key => $value) {
            if ($value) {
                $value = str_replace('\'', '', $value);
                $set_str .= "{$key}='{$value}',";
            }
        }
        if (!$set_str) {
            return false;
        }
        $set_str = trim($set_str, ',');
        foreach ($where as $key => $value) {
            $where_str .= "{$key}='{$value}' and ";
        }
        $where_str .= " 1=1";
        $sql    = "update " . tablename($table) . " set {$set_str} where {$where_str} ";
        $result = pdo_run($sql);
        return $result;
    }
    public function grade_class()
    {
        global $_W, $_GPC;
        $school_uniacid = " and uniacid={$_W['uniacid']} and school_id={$_SESSION['school_id']} ";
        $grades         = pdo_fetchall("select * from " . $this->table_pe . "lianhu_grade where status=1 {$school_uniacid} ");
        foreach ($grades as $key => $value) {
            $grades[$key]['second'] = pdo_fetchall("select * from " . $this->table_pe . "lianhu_class where grade_id={$value['grade_id']} and status=1");
        }
        return $grades;
    }
    public function send_record_msg($sid, $type, $intro = '', $url = false)
    {
        global $_W;
        $result  = pdo_fetch("select * from " . $this->table_pe . "lianhu_student where student_id=:sid ", array(
            ':sid' => $sid
        ));
        $openids = $this->returnEfficeOpenid($result, 3);
        $acid    = pdo_fetchcolumn("select acid from " . tablename('account') . " where uniacid={$_W['uniacid']}");
        load()->classs('weixin.account');
        $accObj = WeixinAccount::create($acid);
        $mu_id  = $this->module['config']['msg3'];
        foreach ($openids as $key => $value) {
            if (value) {
                $data = array(
                    'first' => array(
                        'value' => '家长您好，' . $type . '更新了，请查看'
                    ),
                    'keyword1' => array(
                        'value' => $_W['uniaccount']['name']
                    ),
                    'keyword2' => array(
                        'value' => '更新完成'
                    ),
                    'keyword3' => array(
                        'value' => date("Y-m-d", TIMESTAMP)
                    ),
                    'remark' => array(
                        'value' => '敬请留意'
                    )
                );
                $accObj->sendTplNotice($openid, $mu_id, $data);
            }
        }
    }
    public function homeWorkInfo($hid)
    {
        $result                 = pdo_fetch("select * from " . $this->table_pe . "lianhu_homework where homework_id=:hid", array(
            ":hid" => $hid
        ));
        $result['teacher_name'] = $this->teacherName($result['teacher_id']);
        $result['class_name']   = $this->className($result['class_id']);
        $result['course_name']  = $this->courseName($result['course_id']);
        return $result;
    }
    public function send_class_msg($hid, $que_num)
    {
        global $_W;
        $acid = pdo_fetchcolumn("select acid from " . tablename('account') . " where uniacid={$_W['uniacid']}");
        load()->classs('weixin.account');
        $accObj        = WeixinAccount::create($acid);
        $mu_id         = $this->module['config']['msg1'];
        $homework_info = $this->homeWorkInfo($hid);
        $student_list  = pdo_fetchall("select * from " . $this->table_pe . "lianhu_student where class_id={$homework_info['class_id']} and status=1 and (fanid !='' or fanid1 !='' or fanid2 !='')");
        foreach ($student_list as $key => $value) {
            $openid = $this->returnEfficeOpenid($value, 1);
            if (!$openid)
                continue;
            $homework_info['teacher_name'] = $homework_info['teacher_name'] ? $homework_info['teacher_name'] : '管理人员';
            $data                          = array(
                'first' => array(
                    'value' => $value['student_name'] . '的家长您好,发布了新的作业'
                ),
                'keyword1' => array(
                    'value' => $_SESSION['school_name']
                ),
                'keyword2' => array(
                    'value' => $homework_info['teacher_name']
                ),
                'keyword3' => array(
                    'value' => date("Y-m-d", TIMESTAMP)
                ),
                'keyword4' => array(
                    'value' => '课程：' . $homework_info['course_name'] . '的作业发布了'
                ),
                'remark' => array(
                    'value' => '请督促您的孩子完成作业！'
                )
            );
            $url                           = $_W['siteroot'] . $this->createMobileUrl('line_other', array(
                'op' => 'home_work',
                'student_id' => $value['student_id']
            ));
            $que_num                       = $this->insertMsgQueueMu($openid, $data, $mu_id, $url, $que_num);
        }
        return $que_num;
    }
    public function lib_replace_end_tag($str)
    {
        if (empty($str))
            return false;
        $str = htmlspecialchars($str);
        $str = str_replace('/', "", $str);
        $str = str_replace("\\", "", $str);
        $str = str_replace("&gt", "", $str);
        $str = str_replace("&lt", "", $str);
        $str = str_replace("<SCRIPT>", "", $str);
        $str = str_replace("</SCRIPT>", "", $str);
        $str = str_replace("<script>", "", $str);
        $str = str_replace("</script>", "", $str);
        $str = str_replace("select", "select", $str);
        $str = str_replace("join", "join", $str);
        $str = str_replace("union", "union", $str);
        $str = str_replace("where", "where", $str);
        $str = str_replace("insert", "insert", $str);
        $str = str_replace("delete", "delete", $str);
        $str = str_replace("update", "update", $str);
        $str = str_replace("like", "like", $str);
        $str = str_replace("drop", "drop", $str);
        $str = str_replace("create", "create", $str);
        $str = str_replace("modify", "modify", $str);
        $str = str_replace("rename", "rename", $str);
        $str = str_replace("alter", "alter", $str);
        $str = str_replace("cas", "cast", $str);
        $str = str_replace("&", "&", $str);
        $str = str_replace(">", ">", $str);
        $str = str_replace("<", "<", $str);
        $str = str_replace(" ", chr(32), $str);
        $str = str_replace(" ", chr(9), $str);
        $str = str_replace("    ", chr(9), $str);
        $str = str_replace("&", chr(34), $str);
        $str = str_replace("'", chr(39), $str);
        $str = str_replace("<br />", chr(13), $str);
        $str = str_replace("''", "'", $str);
        $str = str_replace("css", "'", $str);
        $str = str_replace("CSS", "'", $str);
        return $str;
    }
    public function web_msg($count = false)
    {
        global $_W;
        $school_uniacid = " and uniacid={$_W['uniacid']} and school_id={$_SESSION['school_id']}";
        $uid            = $_W['member']['uid'];
        $fanid          = pdo_fetchcolumn("select fanid from " . tablename('mc_mapping_fans') . " where uid={$uid} ");
        if ($fanid) {
            $msg_id_str = pdo_fetchcolumn("select msg_id_str from " . $this->table_pe . "lianhu_student where fanid='{$fanid}' or fanid1={$fanid} or fanid2={$fanid}");
            if ($msg_id_str) {
                $msg_id_str = trim($msg_id_str, ',');
                $list       = pdo_fetchall("select * from " . $this->table_pe . "lianhu_msg where status=1 and msg_id not in({$msg_id_str}) {$school_uniacid} order by msg_id desc");
            } else {
                $list = pdo_fetchall("select * from " . $this->table_pe . "lianhu_msg where status=1  {$school_uniacid} order by msg_id desc ");
            }
            if (!$count)
                $list = pdo_fetchall("select * from " . $this->table_pe . "lianhu_msg where status=1  {$school_uniacid} order by msg_id desc ");
        }
        return $list;
    }
    public function web_msg_tea()
    {
        global $_W;
        $school_uniacid = " and uniacid={$_W['uniacid']} and school_id={$_SESSION['school_id']}";
        $uid            = $_W['member']['uid'];
        if ($uid) {
            $msg_id_str = pdo_fetchcolumn("select msg_id_str from " . $this->table_pe . "lianhu_teacher where uid={$uid}");
            if ($msg_id_str) {
                $msg_id_str = trim($msg_id_str, ',');
                $list       = pdo_fetchall("select * from " . $this->table_pe . "lianhu_msg where status=1 and msg_id not in({$msg_id_str}) {$school_uniacid} order by msg_id desc");
            } else {
                $list = pdo_fetchall("select * from " . $this->table_pe . "lianhu_msg where status=1  {$school_uniacid} order by msg_id desc ");
            }
        }
        return $list;
    }
    public function file_upload($file, $type = 'image', $name = '')
    {
        global $_W;
        if (empty($file)) {
            return error(-1, '没有上传内容');
        }
        if (!in_array($type, array(
            'image',
            'audio',
            'application/vnd.ms-excel'
        ))) {
            return error(-1, '未知的上传类型');
        }
        global $_W;
        if (empty($_W['uploadsetting'][$type])) {
            $_W['uploadsetting']                      = array();
            $_W['uploadsetting'][$type]['folder']     = "{$type}s/{$_W['uniacid']}";
            $_W['uploadsetting'][$type]['extentions'] = $_W['config']['upload'][$type]['extentions'];
            $_W['uploadsetting'][$type]['limit']      = $_W['config']['upload'][$type]['limit'];
        }
        $settings  = $_W['uploadsetting'];
        $extention = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (!empty($settings[$type]['limit']) && $settings[$type]['limit'] * 1024 < filesize($file['tmp_name'])) {
            return error(-1, "上传的文件超过大小限制，请上传小于 {$settings[$type]['limit']}k 的文件");
        }
        $result = array();
        if (empty($name) || $name == 'auto') {
            $result['path'] = "{$settings[$type]['folder']}/" . date('Y/m/');
            mkdirs(ATTACHMENT_ROOT . '/' . $result['path']);
            do {
                $filename = random(30) . ".{$extention}";
            } while (file_exists(ATTACHMENT_ROOT . '/' . $result['path'] . $filename));
            $result['path'] .= $filename;
        } else {
            $result['path'] = $name . '.' . $extention;
        }
        if (!file_move($file['tmp_name'], ATTACHMENT_ROOT . '/' . $result['path'])) {
            return error(-1, '保存上传文件失败');
        }
        $result['success'] = true;
        return $result;
    }
    public function get_class_course($class_id)
    {
        global $_W;
        $teacher = $this->teacher_qx('no');
        $course  = pdo_fetchcolumn("select course_ids from " . $this->table_pe . "lianhu_class where class_id=:cid ", array(
            ':cid' => $class_id
        ));
        if ($course) {
            $course     = unserialize($course);
            $course_str = implode(',', $course);
            if ($teacher == 'teacher') {
                $uid  = $_W['uid'];
                $c_id = pdo_fetchcolumn("select course_id from " . $this->table_pe . "lianhu_teacher where fanid={$uid}");
                if ($c_id) {
                    $course_list = pdo_fetchall("select * from " . $this->table_pe . "lianhu_course  where course_id in ({$course_str}) and course_id in ({$c_id})  ");
                }
            } else {
                $course_list = pdo_fetchall("select * from " . $this->table_pe . "lianhu_course  where course_id in ({$course_str})  ");
            }
            return $course_list;
        }
    }
    public function get_grade_sroce_jilv($grade_id, $addtime)
    {
        $list = pdo_fetchall("select * from " . $this->table_pe . "lianhu_scorejilv  where 
                    grade_id=:gid and addtime>:add 
                    and status=1 
                    order by addtime desc", array(
            ':gid' => $grade_id,
            ':add' => $addtime
        ));
        return $list;
    }
    public function grade_class_num($gid, $num = true)
    {
        if ($num) {
            $class_num = pdo_fetchcolumn("select count(*) num from " . $this->table_pe . "lianhu_class where grade_id=:gid ", array(
                ':gid' => $gid
            ));
            return $class_num;
        } else {
            $class = pdo_fetchAll("select * from " . $this->table_pe . "lianhu_class where grade_id=:gid ", array(
                ':gid' => $gid
            ));
            return $class;
        }
    }
    public function classStudentNum($class_id, $num = true)
    {
        if ($num) {
            $student_num = pdo_fetchcolumn("select count(*) num from " . $this->table_pe . "lianhu_student where class_id=:cid ", array(
                ':cid' => $class_id
            ));
            return $student_num;
        } else {
            $list = pdo_fetchall("select * from " . $this->table_pe . "lianhu_class where class_id=:cid ", array(
                ':cid' => $class_id
            ));
            return $list;
        }
    }
    public function grade_student_num($gid)
    {
        $student_num = pdo_fetchcolumn("select count(*) num from " . $this->table_pe . "lianhu_student where grade_id=:gid", array(
            ':gid' => $gid
        ));
        return $student_num;
    }
    public function grade_teacher_num($gid)
    {
        $class_list = $this->grade_class_num($gid, false);
        foreach ($class_list as $key => $value) {
            $num += $this->class_teacher_num($value['class_id']);
        }
        return $num;
    }
    public function class_teacher_num($cid)
    {
        global $_W;
        $school_uniacid = " and uniacid={$_W['uniacid']} and school_id={$_SESSION['school_id']}";
        $num            = pdo_fetchcolumn("select count(*) from " . $this->table_pe . "lianhu_teacher where teacher_other_power like :power  {$school_uniacid} ", array(
            ':power' => "%{$cid}%"
        ));
        return $num;
    }
    public function class_student_num($cid, $num = true)
    {
        if ($num) {
            $student_num = pdo_fetchcolumn("select count(*) num from " . $this->table_pe . "lianhu_student where class_id=:cid", array(
                ':cid' => $cid
            ));
            return $student_num;
        } else {
            $student = pdo_fetchall("select * from " . $this->table_pe . "lianhu_student where class_id=:cid", array(
                ':cid' => $cid
            ));
            return $student;
        }
    }
    public function sort_arr($arr, $key, $model = 'max')
    {
        $num = count($arr);
        for ($g = 0; $g < $num; $g++) {
            foreach ($arr as $k => $value) {
                for ($i = 0; $i < $k; $i++) {
                    if ($value[$key] > $arr[$i][$key]) {
                        $zhongZhuang = $arr[$i];
                        $arr[$i]     = $value;
                        $arr[$k]     = $zhongZhuang;
                        break;
                    }
                }
            }
        }
        return $arr;
    }
    public function class_name_by_id($cid)
    {
        $class_name = pdo_fetchcolumn("select class_name from " . $this->table_pe . "lianhu_class where class_id=:cid", array(
            ':cid' => $cid
        ));
        return $class_name;
    }
    public function clear_html_short($content)
    {
        $content = htmlspecialchars_decode($content);
        $content = strip_tags($content);
        $content = mb_substr($content, 0, 42, 'utf-8');
        return $content;
    }
    public function money_people_num($limit_id)
    {
        $count = pdo_fetchcolumn("select count(*) num from " . $this->table_pe . "lianhu_money_record where limit_id=:lid and status=1 ", array(
            ':lid' => $limit_id
        ));
        return $count;
    }
    public function money_much($limit_id)
    {
        $count = pdo_fetchcolumn("select sum(limit_much) much from " . $this->table_pe . "lianhu_money_record where limit_id=:lid and status=1 ", array(
            ':lid' => $limit_id
        ));
        return $count;
    }
    public function sqlUpdate($table, $data = array(), $params = array(), $glue = 'AND')
    {
        $fields    = $this->implode($data, ',');
        $condition = $this->implode($params, $glue);
        $params    = array_merge($fields['params'], $condition['params']);
        $sql       = "UPDATE " . $this->table_pe . "{$table} SET {$fields['fields']}";
        $sql .= $condition['fields'] ? ' WHERE ' . $condition['fields'] : '';
        return pdo_query($sql, $params);
    }
    public function sqlInsert($table, $data, $replace = false)
    {
        $cmd       = $replace ? 'REPLACE INTO' : 'INSERT INTO';
        $condition = $this->implode($data, ',');
        return pdo_query("$cmd " . $this->table_pe . "{$table}  SET {$condition['fields']}", $condition['params']);
    }
    public function sqlDelete($table, $params = array(), $glue = 'AND')
    {
        $condition = $this->implode($params, $glue);
        $sql       = "DELETE FROM " . $this->table_pe . "" . $table;
        $sql .= $condition['fields'] ? ' WHERE ' . $condition['fields'] : '';
        return pdo_query($sql, $condition['params']);
    }
    private function sqlImplode($params, $glue = ',')
    {
        $result = array(
            'fields' => ' 1 ',
            'params' => array()
        );
        $split  = '';
        $suffix = '';
        if (in_array(strtolower($glue), array(
            'and',
            'or'
        ))) {
            $suffix = '__';
        }
        if (!is_array($params)) {
            $result['fields'] = $params;
            return $result;
        }
        if (is_array($params)) {
            $result['fields'] = '';
            foreach ($params as $fields => $value) {
                $result['fields'] .= $split . "`$fields` =  :{$suffix}$fields";
                $split                                 = ' ' . $glue . ' ';
                $result['params'][":{$suffix}$fields"] = is_null($value) ? '' : $value;
            }
        }
        return $result;
    }
    public function echoVoiceUrl($voice)
    {
        global $_W;
        $url  = $this->imgFrom($voice);
        $html = "<audio src='{$url}' controls='controls'></audio>";
        return $html;
    }
    public function getLineList($page = 1, $page_size = 10, $class_id)
    {
        $start = ($page - 1) * $page_size;
        $limit = " limit  {$start},{$page_size}";
        $list  = pdo_fetchall("select " . $this->table_pe . "lianhu_send.*,mc_members.nickname,mc_members.avatar from " . $this->table_pe . "lianhu_send left join " . tableName('mc_members') . " mc_members 
         on mc_members.uid=" . $this->table_pe . "lianhu_send.send_uid where send_status=1 and class_id=:cid order by add_time desc {$limit} ", array(
            ':cid' => $class_id
        ));
        return $list;
    }
    public function getLineZanName($send_id)
    {
        if (!$send_id)
            return false;
        $list = pdo_fetchall("select member.nickname from   " . $this->table_pe . "lianhu_send_like send_like
        left join " . $this->table_pe . "mc_members member  on member.uid=send_like.uid where send_id=:send_id", array(
            ":send_id" => $send_id
        ));
        if ($list) {
            foreach ($list as $key => $value) {
                if ($value['nickname'])
                    $str .= $value['nickname'] . ",";
            }
            $str = trim($str, ',');
        }
        if (empty($str))
            return "&nbsp";
        return $str;
    }
    public function getLineComplete($send_id)
    {
        if (!$send_id)
            return false;
        $table_pe = $this->table_pe;
        $list     = pdo_fetchall("select {$table_pe}lianhu_send_comment.*,mc_members.nickname from {$table_pe}lianhu_send_comment 
      left join " . tableName('mc_members') . " mc_members on mc_members.uid={$table_pe}lianhu_send_comment.comment_uid
       where send_id=:sid and comment_status=1", array(
            ":sid" => $send_id
        ));
        return $list;
    }
    public function decodeLineImgs($send_img, $no_display = false)
    {
        $arr   = unserialize($send_img);
        $count = count($arr);
        if (!$arr)
            return '';
        foreach ($arr as $key => $value) {
            $url = $this->imgFrom($value);
            if ($count == 1)
                $html .= '  <img class="lazy" src="' . $url . '"  data-original="' . $url . '" style="width:60%;margin-left:5%;margin-bottom:5px;">';
            else
                $html .= " <div data-img='" . $url . "' style='background-size:cover;background-image:url(" . $url . ");width:31%; height:120px;float:left;margin-left:2%;overflow: hidden; margin-bottom:5px;'></div>";
        }
        if (!$no_display)
            echo $html;
        else
            return $html;
    }
    public function getWechatMedia($media_id, $img_voice = 1, $qiniu = true)
    {
        if ($img_voice == 1)
            $exe = '.jpg';
        else
            $exe = '.amr';
        $base_dir     = $this->insertDir();
        $access_token = $this->AccessToken();
        $url          = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=' . $access_token . '&media_id=' . $media_id;
        $file_name    = $base_dir . time() . rand(111111, 999999) . $exe;
        $this->getImg($url, $file_name);
        $up_file_name = str_ireplace(ATTACHMENT_ROOT, '', $file_name);
        if ($qiniu)
            $img = $this->upToQiniu($up_file_name);
        if ($img)
            return $img;
        else
            return $up_file_name;
    }
    public function insertDir()
    {
        $base_dir = ATTACHMENT_ROOT . 'images/' . date("Y/m/d", time()) . '/';
        if (!file_exists($base_dir))
            mkdirs($base_dir);
        return $base_dir;
    }
    public function createQueueNum()
    {
        do {
            $num    = "QUE" . random('29');
            $result = pdo_fetch("select * from {$this->table_pe}lianhu_msg_queue where queue_num=:num", array(
                ":num" => $num
            ));
        } while ($result);
        return $num;
    }
    public function insertMsgQueueMu($openid, $data, $mu_id, $url, $queue_num = false)
    {
        $in['openid']        = $openid;
        $in['queue_content'] = serialize($data);
        $in['url']           = $url;
        $in['mu_id']         = $mu_id;
        $in['add_time']      = time();
        $in['queue_type']    = 1;
        if (!$queue_num)
            $in['queue_num'] = $this->createQueueNum();
        else
            $in['queue_num'] = $queue_num;
        pdo_insert('lianhu_msg_queue', $in);
        return $in['queue_num'];
    }
    public function insertMsgQueueKe($openid, $content, $queue_num = false)
    {
        $in['openid']        = $openid;
        $in['queue_content'] = serialize($content);
        $in['add_time']      = time();
        $in['queue_type']    = 2;
        if (!$queue_num)
            $in['queue_num'] = $this->createQueueNum();
        else
            $in['queue_num'] = $queue_num;
        pdo_insert('lianhu_msg_queue', $in);
        return $in['queue_num'];
    }
    public function insertMsgQueueSms($mobile, $content, $queue_num = false)
    {
        $in['mobile']        = $mobile;
        $in['queue_content'] = serialize($content);
        $in['add_time']      = time();
        $in['queue_type']    = 3;
        if (!$queue_num)
            $in['queue_num'] = $this->createQueueNum();
        else
            $in['queue_num'] = $queue_num;
        pdo_insert('lianhu_msg_queue', $in);
        return $in['queue_num'];
    }
    public function sendAllMsg($queue_id)
    {
        global $_W;
        load()->classs('weixin.account');
        $accObj = WeixinAccount::create($_W['acid']);
        if (!$queue_id)
            return false;
        $result = pdo_fetch("select * from {$this->table_pe}lianhu_msg_queue 
        where queue_id=:qid and end_time=0 and queue_status=1  ", array(
            ':qid' => $queue_id
        ));
        $data   = unserialize($result['queue_content']);
        if ($result['queue_type'] == 1) {
            $accObj->sendTplNotice($result['openid'], $result['mu_id'], $data, $result['url']);
        }
        if ($result['queue_type'] == 2) {
            $this->toSendCustomNotice($result['openid'], $data['title'], $data['content'], $data['url']);
        }
        if ($result['queue_type'] == 3) {
            $api_url = $this->module['config']['sms_set'][$_SESSION['school_id']];
            $api_url = str_replace("CONTENT", urlencode("【{$data['head']}】{$data['content']}"), $api_url);
            $api_url = str_replace("PHONE", $result['mobile'], $api_url);
            file_get_contents($api_url);
        }
        pdo_update("lianhu_msg_queue", array(
            'end_time' => TIMESTAMP,
            'queue_status' => 2
        ), array(
            "queue_id" => $queue_id
        ));
    }
}
?>