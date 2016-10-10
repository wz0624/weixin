<?php


defined('IN_IA') or exit('Access Denied');
define('RES', '../addons/hc_mynzj/template/');
class Hc_mynzjModuleSite extends WeModuleSite
{
    public function doWebsetting()
    {
        global $_W, $_GPC;
        $weid = $_W['uniacid'];
        load()->func('tpl');
        $subject                = pdo_fetch("SELECT * FROM " . tablename(hc_mynzj_setting) . " WHERE weid = '{$weid}' ORDER BY id DESC LIMIT 1");
        $item['hc_mynzj_title'] = empty($item['hc_mynzj_title']) ? '我的年终奖？' : $item['hc_mynzj_title'];
        $item['share_desc']     = empty($item['share_desc']) ? '老板~我的年终奖呢！赶紧打我卡上~' : $item['share_desc'];
        $item['share_title']    = empty($item['share_title']) ? '@老板，给我出来！' : $item['share_title'];
        $item['hc_mynzj_url']   = empty($item['hc_mynzj_url']) ? 'http://mp.weixin.qq.com/s?__biz=MzAwNjI1MTQwNg==&mid=409164202&idx=1&sn=d714f4c647f81ce4b66dd21e0c5a64c9#rd' : $item['hc_mynzj_url'];
        if (checksubmit()) {
            $data = array(
                'hc_mynzj_title' => $_GPC['hc_mynzj_title'],
                'hc_mynzj_url' => $_GPC['hc_mynzj_url'],
                'share_desc' => $_GPC['share_desc'],
                'share_title' => $_GPC['share_title'],
                'loading' => $_GPC['loading'],
                'share_pic' => $_GPC['share_pic'],
                'hc_bg' => $_GPC['hc_bg'],
                'hc_bg2' => $_GPC['hc_bg2'],
                'hc_nt1' => $_GPC['hc_nt1'],
                'hc_nt2' => $_GPC['hc_nt2'],
                'hc_tips' => $_GPC['hc_tips'],
                'hc_top' => $_GPC['hc_top']
            );
            if (empty($subject)) {
                $data['weid'] = $weid;
                pdo_insert(hc_mynzj_setting, $data);
            } else {
                pdo_update(hc_mynzj_setting, $data, array(
                    'weid' => $weid
                ));
            }
            message('欧了！欧了！更新完毕！', referer(), 'success');
        }
        if (!$subject['share_pic']) {
            $subject['share_pic'] = "../addons/hc_mynzj/template/mobile/hcnzj/share_pic.png";
        }
        if (!$subject['loading']) {
            $subject['loading'] = "../addons/hc_mynzj/template/mobile/hcnzj/loading.png";
        }
        if (!$subject['hc_bg']) {
            $subject['hc_bg'] = "../addons/hc_mynzj/template/mobile/hcnzj/bd.jpg";
        }
        if (!$subject['hc_bg2']) {
            $subject['hc_bg2'] = "../addons/hc_mynzj/template/mobile/hcnzj/second.jpg";
        }
        if (!$subject['hc_nt1']) {
            $subject['hc_nt1'] = "../addons/hc_mynzj/template/mobile/hcnzj/btn1.png";
        }
        if (!$subject['hc_nt2']) {
            $subject['hc_nt2'] = "../addons/hc_mynzj/template/mobile/hcnzj/btn2.png";
        }
        if (!$subject['hc_tips']) {
            $subject['hc_tips'] = "../addons/hc_mynzj/template/mobile/hcnzj/share.png";
        }
        if (!$subject['hc_top']) {
            $subject['hc_top'] = "../addons/hc_mynzj/template/mobile/hcnzj/text_wrap.png";
        }
        include $this->template('setting');
    }
    public function doWebsettings()
    {
        global $_W, $_GPC;
        $hidden = $_GPC['hidden'];
        echo $hidden;
        load()->func('tpl');
        if ($hidden == "yes") {
            $data0  = array(
                'name' => $_GPC['sql_name0'],
                'remarks' => $_GPC['sql_remarks0']
            );
            $data1  = array(
                'name' => $_GPC['sql_name1'],
                'remarks' => $_GPC['sql_remarks1']
            );
            $data2  = array(
                'name' => $_GPC['sql_name2'],
                'remarks' => $_GPC['sql_remarks2']
            );
            $data3  = array(
                'name' => $_GPC['sql_name3'],
                'remarks' => $_GPC['sql_remarks3']
            );
            $data4  = array(
                'name' => $_GPC['sql_name4'],
                'remarks' => $_GPC['sql_remarks4']
            );
            $data5  = array(
                'name' => $_GPC['sql_name5'],
                'remarks' => $_GPC['sql_remarks5']
            );
            $data6  = array(
                'name' => $_GPC['sql_name6'],
                'remarks' => $_GPC['sql_remarks6']
            );
            $data7  = array(
                'name' => $_GPC['sql_name7'],
                'remarks' => $_GPC['sql_remarks7']
            );
            $data8  = array(
                'name' => $_GPC['sql_name8'],
                'remarks' => $_GPC['sql_remarks8']
            );
            $data9  = array(
                'name' => $_GPC['sql_name9'],
                'remarks' => $_GPC['sql_remarks9']
            );
            $data10 = array(
                'name' => $_GPC['sql_name10'],
                'remarks' => $_GPC['sql_remarks10']
            );
            $data11 = array(
                'name' => $_GPC['sql_name11'],
                'remarks' => $_GPC['sql_remarks11']
            );
            pdo_update(hc_mynzj_table1, $data0, array(
                'id' => $_GPC['sql_hidden0']
            ));
            pdo_update(hc_mynzj_table1, $data1, array(
                'id' => $_GPC['sql_hidden1']
            ));
            pdo_update(hc_mynzj_table1, $data2, array(
                'id' => $_GPC['sql_hidden2']
            ));
            pdo_update(hc_mynzj_table1, $data3, array(
                'id' => $_GPC['sql_hidden3']
            ));
            pdo_update(hc_mynzj_table1, $data4, array(
                'id' => $_GPC['sql_hidden4']
            ));
            pdo_update(hc_mynzj_table1, $data5, array(
                'id' => $_GPC['sql_hidden5']
            ));
            pdo_update(hc_mynzj_table1, $data6, array(
                'id' => $_GPC['sql_hidden6']
            ));
            pdo_update(hc_mynzj_table1, $data7, array(
                'id' => $_GPC['sql_hidden7']
            ));
            pdo_update(hc_mynzj_table1, $data8, array(
                'id' => $_GPC['sql_hidden8']
            ));
            pdo_update(hc_mynzj_table1, $data9, array(
                'id' => $_GPC['sql_hidden9']
            ));
            pdo_update(hc_mynzj_table1, $data10, array(
                'id' => $_GPC['sql_hidden10']
            ));
            pdo_update(hc_mynzj_table1, $data11, array(
                'id' => $_GPC['sql_hidden11']
            ));
            message('欧了！欧了！更新完毕！', referer(), 'success');
            $hidden = "NO";
        }
        $sql1 = pdo_fetchall("SELECT * FROM " . tablename(hc_mynzj_table1) . " ORDER BY id  LIMIT 12");
        if ($hidden == "yesyes") {
            $dota0 = array(
                'name' => $_GPC['sql_name0']
            );
            $dota1 = array(
                'name' => $_GPC['sql_name1']
            );
            $dota2 = array(
                'name' => $_GPC['sql_name2']
            );
            $dota3 = array(
                'name' => $_GPC['sql_name3']
            );
            pdo_update(hc_mynzj_table2, $dota0, array(
                'id' => $_GPC['sql_hidden0']
            ));
            pdo_update(hc_mynzj_table2, $dota1, array(
                'id' => $_GPC['sql_hidden1']
            ));
            pdo_update(hc_mynzj_table2, $dota2, array(
                'id' => $_GPC['sql_hidden2']
            ));
            pdo_update(hc_mynzj_table2, $dota3, array(
                'id' => $_GPC['sql_hidden3']
            ));
            message('欧了！欧了！更新完毕！', referer(), 'success');
            $hidden = "NO";
        }
        $sql2 = pdo_fetchall("SELECT * FROM " . tablename(hc_mynzj_table2) . " ORDER BY id  LIMIT 4");
        include $this->template('settings');
    }
    public function doMobileLink()
    {
        global $_W, $_GPC;
        load()->func('tpl');
        $sql            = "SELECT * FROM " . tablename(hc_mynzj_setting) . " WHERE weid = '{$_W['uniacid']}'";
        $arr            = pdo_fetchall($sql);
        $hc_mynzj_title = $arr['0']['hc_mynzj_title'];
        $hc_mynzj_url   = $arr['0']['hc_mynzj_url'];
        $share_desc     = $arr['0']['share_desc'];
        $share_title    = $arr['0']['share_title'];
        $loading        = $arr['0']['loading'];
        $share_pic      = $arr['0']['share_pic'];
        $hc_bg          = $arr['0']['hc_bg'];
        $hc_bg2         = $arr['0']['hc_bg2'];
        $hc_nt1         = $arr['0']['hc_nt1'];
        $hc_nt2         = $arr['0']['hc_nt2'];
        $hc_tips        = $arr['0']['hc_tips'];
        $hc_top         = $arr['0']['hc_top'];
        $weid           = $_W['uniacid'];
        $homeurl        = empty($reply['homeurl']) ? $_W['siteroot'] . 'app/' . $this->createMobileUrl('link', array(
            'id' => $id
        ), true) : $reply['homeurl'];
        $arr1           = pdo_fetchall("SELECT * FROM " . tablename(hc_mynzj_table1) . " ORDER BY id  LIMIT 12");
        $name0          = $arr1['0']['name'];
        $remarks0       = $arr1['0']['remarks'];
        $name1          = $arr1['1']['name'];
        $remarks1       = $arr1['1']['remarks'];
        $name2          = $arr1['2']['name'];
        $remarks2       = $arr1['2']['remarks'];
        $name3          = $arr1['3']['name'];
        $remarks3       = $arr1['3']['remarks'];
        $name4          = $arr1['4']['name'];
        $remarks4       = $arr1['4']['remarks'];
        $name5          = $arr1['5']['name'];
        $remarks5       = $arr1['5']['remarks'];
        $name6          = $arr1['6']['name'];
        $remarks6       = $arr1['6']['remarks'];
        $name7          = $arr1['7']['name'];
        $remarks7       = $arr1['7']['remarks'];
        $name8          = $arr1['8']['name'];
        $remarks8       = $arr1['8']['remarks'];
        $name9          = $arr1['9']['name'];
        $remarks9       = $arr1['9']['remarks'];
        $name10         = $arr1['10']['name'];
        $remarks10      = $arr1['10']['remarks'];
        $name11         = $arr1['11']['name'];
        $remarks11      = $arr1['11']['remarks'];
        $sql2           = pdo_fetchall("SELECT * FROM " . tablename(hc_mynzj_table2) . " ORDER BY id LIMIT 4");
        include $this->template('link');
    }
}