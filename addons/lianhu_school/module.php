<?php
/**
 * 家校通模块定义
 *
 * @author zhuhuan
 * @url http://bbs.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');
class Lianhu_schoolModule extends WeModule {

    public function __construct(){
        session_start();
//        if(!$_SESSION['school_id']){
//            message("检测到您没有选择学校kkkk",'','error');
//        }
    }
    public function fieldsFormDisplay($rid = 0) {
        //要嵌入规则编辑页的自定义内容，这里 $rid 为对应的规则编号，新增时为 0
    }

    public function fieldsFormValidate($rid = 0) {
        //规则编辑保存时，要进行的数据验证，返回空串表示验证无误，返回其他字符串将呈现为错误提示。这里 $rid 为对应的规则编号，新增时为 0
        return '';
    }

    public function fieldsFormSubmit($rid) {
        //规则验证无误保存入库时执行，这里应该进行自定义字段的保存。这里 $rid 为对应的规则编号
    }

    public function ruleDeleted($rid) {
        //删除规则时调用，这里 $rid 为对应的规则编号
    }   
	public function settingsDisplay($settings) {
        global $_GPC, $_W;
        $config=$settings;
        if (checksubmit()) {
            $config['on_school'][$_SESSION['school_id']]    =$_GPC['on_school'];
            $config['begin_course'][$_SESSION['school_id']] =$_GPC['begin_course'];
            $config['am_much'][$_SESSION['school_id']]      =$_GPC['am_much'];
            $config['pm_much'][$_SESSION['school_id']]      =$_GPC['pm_much'];
            $config['ye_much'][$_SESSION['school_id']]      =$_GPC['ye_much'];
            $config['sms_set'][$_SESSION['school_id']]      =$_GPC['sms_set'];
            $config['school_url'][$_SESSION['school_id']]   =$_GPC['school_url'];
            // $config['ad'][$_SESSION['school_id']]           =$_GPC['ad'];
            $config['line_type'][$_SESSION['school_id']]    =$_GPC['line_type'];
            $config['appointment'][$_SESSION['school_id']]  =$_GPC['appointment'];
            
            $cfg = array(
                'msg' => $_GPC['msg'],
                'msg1' => $_GPC['msg1'],
                // 'msg2' => $_GPC['msg2'],
                // 'msg3' => $_GPC['msg3'],
                // 'msg4' => $_GPC['msg4'],
                'version' => $_GPC['version'],
                'mylovekid' => $_GPC['mylovekid'],
                'family_set' => $_GPC['family_set'],
                'on_school'     => $config['on_school'] ,
                'begin_course'  => $config['begin_course'] ,
                'am_much'   => $config['am_much'],
                'pm_much'   => $config['pm_much'],
                'ye_much'   => $config['ye_much'],
                'sms_set'   => $config['sms_set'],
                'school_url'=> $config['school_url'],
                // 'ad'        => $config['ad'],  #教师端滚动
                'line_type' => $config['line_type'],
                'appointment'=>$config['appointment'],
                'qiniu'=>$_GPC['qiniu'],
                'qiniu_url'=>$_GPC['qiniu_url'],
                'qiniu_AccessKey'=>$_GPC['qiniu_AccessKey'],
                'qiniu_SecretKey'=>$_GPC['qiniu_SecretKey'],
                'qiniu_bucket'=>$_GPC['qiniu_bucket'],
                'pay_do'=>$_GPC['pay_do'],
                'pay_uniacid'=>$_GPC['pay_uniacid'],
            );
            if ($this->saveSettings($cfg)) {
                message('保存成功', 'refresh');
            }
        }
		load()->func('tpl');
        $uniacid_list=pdo_fetchall("select * from ".tablename('account_wechats')."  where 1=1");
		include $this->template('setting');
    }
}