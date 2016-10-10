<?php
defined('IN_IA') or exit('Access Denied');
define('TEMPLATE_PATH', '../../addons/dayu_form/template/style/');
class dayu_formModuleSite extends WeModuleSite
{
    private static $COOKIE_DAYS = 7;
    public function getMenus()
    {
        $menus = array(
            array(
                'title' => '新建表单',
                'url' => $this->createWebUrl('post'),
                'icon' => 'fa fa-plus-square-o'
            )
        );
        return $menus;
    }
    public function getHomeTiles()
    {
        global $_W;
        $urls = array();
        $list = pdo_fetchall("SELECT title, reid FROM " . tablename('dayu_form') . " WHERE weid = '{$_W['uniacid']}'");
        if (!empty($list)) {
            foreach ($list as $row) {
                $urls[] = array(
                    'title' => $row['title'],
                    'url' => $_W['siteroot'] . "app/" . $this->createMobileUrl('dayu_form', array(
                        'id' => $row['reid']
                    ))
                );
            }
        }
        return $urls;
    }
    public function doWebQuery()
    {
        global $_W, $_GPC;
        $kwd              = $_GPC['keyword'];
        $sql              = 'SELECT * FROM ' . tablename('dayu_form') . ' WHERE `weid`=:weid AND `title` LIKE :title ORDER BY reid DESC LIMIT 0,8';
        $params           = array();
        $params[':weid']  = $_W['uniacid'];
        $params[':title'] = "%{$kwd}%";
        $ds               = pdo_fetchall($sql, $params);
        foreach ($ds as &$row) {
            $r                = array();
            $r['title']       = $row['title'];
            $r['description'] = cutstr(strip_tags($row['description']), 50);
            $r['thumb']       = $row['thumb'];
            $r['reid']        = $row['reid'];
            $row['entry']     = $r;
        }
        include $this->template('query');
    }
    public function doWebStaff()
    {
        global $_W, $_GPC;
        $op       = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
        $weid     = $_W['uniacid'];
        $reid     = intval($_GPC['reid']);
        $activity = pdo_fetch('SELECT reid,title,kfid FROM ' . tablename('dayu_form') . ' WHERE weid = :weid AND reid = :reid', array(
            ':weid' => $weid,
            ':reid' => $reid
        ));
        if (empty($activity)) {
            message('表单不存在或已删除', $this->createWebUrl('display'), 'error');
        }
        if ($op == 'list') {
            $where           = ' reid = :reid';
            $params[':reid'] = $reid;
            if (!empty($_GPC['keyword'])) {
                $where .= " AND nickname LIKE '%{$_GPC['keyword']}%'";
            }
            $pindex = max(1, intval($_GPC['page']));
            $psize  = 20;
            $total  = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('dayu_form_staff') . ' WHERE ' . $where, $params);
            $lists  = pdo_fetchall('SELECT * FROM ' . tablename('dayu_form_staff') . ' WHERE ' . $where . ' ORDER BY createtime DESC,id ASC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params, 'id');
            $pager  = pagination($total, $pindex, $psize);
            if (checksubmit('submit')) {
                if (!empty($_GPC['ids'])) {
                    foreach ($_GPC['ids'] as $k => $v) {
                        $data = array(
                            'nickname' => trim($_GPC['nickname'][$k]),
                            'openid' => trim($_GPC['openid'][$k]),
                            'weid' => trim($_GPC['weid'][$k])
                        );
                        pdo_update('dayu_form_staff', $data, array(
                            'reid' => $reid,
                            'id' => intval($v)
                        ));
                    }
                    message('编辑成功', $this->createWebUrl('staff', array(
                        'op' => 'list',
                        'reid' => $reid
                    )), 'success');
                }
            }
            include $this->template('staff');
        } elseif ($op == 'post') {
            if (checksubmit('submit')) {
                if (!empty($_GPC['nickname'])) {
                    foreach ($_GPC['nickname'] as $k => $v) {
                        $v = trim($v);
                        if (empty($v))
                            continue;
                        $data['reid']       = $reid;
                        $data['nickname']   = $v;
                        $data['nickname']   = $_GPC['nickname'][$k];
                        $data['openid']     = $_GPC['openid'][$k];
                        $data['weid']       = $_GPC['weid'][$k];
                        $data['createtime'] = time();
                        pdo_insert('dayu_form_staff', $data);
                    }
                }
                message('添加客服成功', $this->createWebUrl('staff', array(
                    'reid' => $reid,
                    'op' => 'list'
                )), 'success');
            }
            include $this->template('staff');
        } elseif ($op == 'staffdel') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                pdo_delete('dayu_form_staff', array(
                    'id' => $id
                ));
            }
            message('删除成功.', referer());
        }
    }
    public function doWebchangecheckedAjax()
    {
        global $_W, $_GPC;
        $id   = $_GPC['id'];
        $kfid = $_GPC['kfid'];
        if (false !== pdo_update('dayu_form', array(
            'kfid' => $kfid
        ), array(
            'reid' => intval($id),
            'weid' => $_W['uniacid']
        ))) {
            exit('1');
        } else {
            exit('0');
        }
    }
    public function doWebEditkf()
    {
        global $_W, $_GPC;
        if ($_GPC['dopost'] == "update") {
            $reid     = $_GPC['reid'];
            $nickname = $_GPC['nickname'];
            $openid   = $_GPC['openid'];
            if (is_array($reid)) {
                foreach ($reid as $k => $v) {
                    $actid = $v . ",";
                }
            }
            $actid = substr($actid, 0, strlen($actid) - 1);
            $a     = pdo_update('dayu_form_staff', array(
                'reid' => $actid,
                'nickname' => $nickname,
                'openid' => $openid
            ), array(
                'id' => $_GPC['id']
            ));
            message("更改成功!", referer());
            exit;
        }
        $fff    = pdo_fetchall("SELECT reid,title FROM " . tablename('dayu_form'));
        $config = pdo_fetch("SELECT * from " . tablename('dayu_form_staff') . " where id=" . $_GPC['id']);
        $fun    = explode(',', $config['reid']);
        include $this->template('kf_edit');
    }
    public function doWebDetail()
    {
        global $_W, $_GPC;
        $rerid            = intval($_GPC['id']);
        $sql              = 'SELECT * FROM ' . tablename('dayu_form_info') . " WHERE `rerid`=:rerid";
        $params           = array();
        $params[':rerid'] = $rerid;
        $row              = pdo_fetch($sql, $params);
        if (empty($row)) {
            message('访问非法.');
        }
        $sql             = 'SELECT * FROM ' . tablename('dayu_form') . ' WHERE `weid`=:weid AND `reid`=:reid';
        $params          = array();
        $params[':weid'] = $_W['uniacid'];
        $params[':reid'] = $row['reid'];
        $activity        = pdo_fetch($sql, $params);
        if (empty($activity)) {
            message('非法访问.');
        }
        $sql             = 'SELECT * FROM ' . tablename('dayu_form_fields') . ' WHERE `reid`=:reid ORDER BY `refid`';
        $params          = array();
        $params[':reid'] = $row['reid'];
        $fields          = pdo_fetchall($sql, $params);
        if (empty($fields)) {
            message('非法访问.');
        }
        $ds = $fids = array();
        foreach ($fields as $f) {
            $ds[$f['refid']]['fid']   = $f['title'];
            $ds[$f['refid']]['type']  = $f['type'];
            $ds[$f['refid']]['refid'] = $f['refid'];
            $fids[]                   = $f['refid'];
        }
        $fids          = implode(',', $fids);
        $row['fields'] = array();
        $sql           = 'SELECT * FROM ' . tablename('dayu_form_data') . " WHERE `reid`=:reid AND `rerid`='{$row['rerid']}' AND `refid` IN ({$fids})";
        $fdatas        = pdo_fetchall($sql, $params);
        foreach ($fdatas as $fd) {
            $row['fields'][$fd['refid']] = $fd['data'];
        }
        foreach ($ds as $value) {
            if ($value['type'] == 'reside') {
                $row['fields'][$value['refid']] = '';
                foreach ($fdatas as $fdata) {
                    if ($fdata['refid'] == $value['refid']) {
                        $row['fields'][$value['refid']] .= $fdata['data'];
                    }
                }
                break;
            }
        }
        $record              = array();
        $record['status']    = intval($_GPC['status']);
        $record['yuyuetime'] = TIMESTAMP;
        $record['kfinfo']    = $_GPC['kfinfo'];
        if ($_GPC['status'] == '0') {
            $huifu = '等待客服确认（答复：' . $_GPC['kfinfo'] . '）';
        } elseif ($_GPC['status'] == '1') {
            $huifu = '已确认，客服受理中（答复：' . $_GPC['kfinfo'] . '）';
        } elseif ($_GPC['status'] == '2') {
            $huifu = '客服拒绝受理（答复：' . $_GPC['kfinfo'] . '）';
        } elseif ($_GPC['status'] == '3') {
            $huifu = '完成（答复：' . $_GPC['kfinfo'] . '）';
        }
        $ymember  = $row['member'];
        $ymobile  = $row['mobile'];
        $ytime    = date('Y-m-d H:i:s', TIMESTAMP);
        $mfirst   = $activity['mfirst'];
        $mfoot    = $activity['mfoot'];
        $template = array(
            "touser" => $row['openid'],
            "template_id" => $activity['m_templateid'],
            "url" => $_W['siteroot'] . 'app/' . $this->createMobileUrl('mydayu_form', array(
                'name' => 'dayu_form',
                'weid' => $row['weid'],
                'id' => $row['reid']
            )),
            "topcolor" => "#FF0000",
            "data" => array(
                'first' => array(
                    'value' => urlencode($mfirst),
                    'color' => "#743A3A"
                ),
                'keyword1' => array(
                    'value' => urlencode($ymember),
                    'color' => '#000000'
                ),
                'keyword2' => array(
                    'value' => urlencode($ymobile),
                    'color' => '#000000'
                ),
                'keyword3' => array(
                    'value' => urlencode($_GPC['yuyuetime']),
                    'color' => '#000000'
                ),
                'keyword4' => array(
                    'value' => urlencode($huifu),
                    'color' => "#FF0000"
                ),
                'remark' => array(
                    'value' => urlencode("\\n" . $mfoot),
                    'color' => "#008000"
                )
            )
        );
        if ($_W['ispost'] && $activity['custom_status'] == 1) {
            load()->model('mc');
            $acc = notice_init();
            if (is_error($acc)) {
                return error(-1, $acc['message']);
            }
            $url  = $_W['siteroot'] . 'app/' . $this->createMobileUrl('mydayu_form', array(
                'name' => 'dayu_form',
                'weid' => $row['weid'],
                'id' => $row['reid']
            ));
            $info = "【您好，受理结果通知】\n\n";
            $info .= "姓名：{$ymember}\n手机：{$ymobile}\n受理结果：{$huifu}\n\n";
            $info .= "<a href='{$url}'>点击查看详情</a>";
            $custom = array(
                'msgtype' => 'text',
                'text' => array(
                    'content' => urlencode($info)
                ),
                'touser' => $row['openid']
            );
            $acc->sendCustomNotice($custom);
            pdo_update('dayu_form_info', $record, array(
                'rerid' => $rerid
            ));
            message('修改成功', referer(), 'success');
        }
        if ($_W['ispost'] && $activity['custom_status'] == 0) {
            load()->func('communication');
            $this->send_template_message(urldecode(json_encode($template)));
            pdo_update('dayu_form_info', $record, array(
                'rerid' => $rerid
            ));
            message('修改成功', referer(), 'success');
        }
        $row['yuyuetime'] && $row['yuyuetime'] = date('Y-m-d H:i:s', $row['yuyuetime']);
        load()->func('tpl');
        include $this->template('detail');
    }
    public function doWebManage()
    {
        global $_W, $_GPC;
        $_accounts = $accounts = uni_accounts();
        load()->model('mc');
        if (empty($accounts) || !is_array($accounts) || count($accounts) == 0) {
            message('请指定公众号');
        }
        if (!isset($_GPC['acid'])) {
            $account = array_shift($_accounts);
            if ($account !== false) {
                $acid = intval($account['acid']);
            }
        } else {
            $acid = intval($_GPC['acid']);
            if (!empty($acid) && !empty($accounts[$acid])) {
                $account = $accounts[$acid];
            }
        }
        reset($accounts);
        $reid            = intval($_GPC['id']);
        $sql             = 'SELECT * FROM ' . tablename('dayu_form') . ' WHERE `weid`=:weid AND `reid`=:reid';
        $params          = array();
        $params[':weid'] = $_W['uniacid'];
        $params[':reid'] = $reid;
        $activity        = pdo_fetch($sql, $params);
        if (empty($activity)) {
            message('非法访问.');
        }
        $sql             = 'SELECT * FROM ' . tablename('dayu_form_fields') . ' WHERE `reid`=:reid ORDER BY `refid`';
        $params          = array();
        $params[':reid'] = $reid;
        $fields          = pdo_fetchall($sql, $params);
        if (empty($fields)) {
            message('非法访问.');
        }
        $ds = array();
        foreach ($fields as $f) {
            $ds[$f['refid']] = $f['title'];
        }
        $select = array();
        if (!empty($_GPC['select'])) {
            foreach ($_GPC['select'] as $field) {
                if (isset($ds[$field])) {
                    $select[] = $field;
                }
            }
        }
        $pindex    = max(1, intval($_GPC['page']));
        $psize     = 20;
        $starttime = empty($_GPC['time']['start']) ? strtotime('-1 month') : strtotime($_GPC['time']['start']);
        $endtime   = empty($_GPC['time']['end']) ? TIMESTAMP : strtotime($_GPC['time']['end']) + 86399;
        $where .= 'reid=:reid AND `createtime` > :starttime AND `createtime` < :endtime';
        $params               = array();
        $params[':reid']      = $reid;
        $params[':starttime'] = $starttime;
        $params[':endtime']   = $endtime;
        $status               = $_GPC['status'];
        if (!empty($_GPC['keywords'])) {
            $where .= ' and (member like :member or mobile like :mobile)';
            $params[':member'] = "%{$_GPC['keywords']}%";
            $params[':mobile'] = "%{$_GPC['keywords']}%";
        }
        if ($status != '') {
            $allstatus .= " and status='{$status}'";
        }
        $sql   = 'SELECT * FROM ' . tablename('dayu_form_info') . " WHERE $where $allstatus ORDER BY `createtime` DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
        $list  = pdo_fetchall($sql, $params);
        $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('dayu_form_info') . " WHERE $where $allstatus", $params);
        $pager = pagination($total, $pindex, $psize);
        foreach ($list as $index => $row) {
            $list[$index]['user'] = mc_fansinfo($row['openid'], $acid, $_W['uniacid']);
        }
        if (!empty($_GPC['export'])) {
            $sql            = 'SELECT title FROM ' . tablename('dayu_form_fields') . " AS f JOIN " . tablename('dayu_form_info') . " AS r ON f.reid='{$reid}' GROUP BY title ORDER BY refid";
            $tableheader    = pdo_fetchall($sql, $params);
            $tablelength    = count($tableheader);
            $tableheaders[] = array(
                'title' => '姓名'
            );
            $tableheaders[] = array(
                'title' => '手机'
            );
            $tableheaders[] = array(
                'title' => '提交时间'
            );
            $tableheaders[] = array(
                'title' => '状态'
            );
            $sql            = 'SELECT * FROM ' . tablename('dayu_form_info') . " WHERE $where $allstatus ORDER BY createtime DESC";
            $list           = pdo_fetchall($sql, $params);
            if (empty($list)) {
                message('暂时没有数据');
            }
            foreach ($list as &$r) {
                $sql              = 'SELECT data, refid FROM ' . tablename('dayu_form_data') . " WHERE `reid`=:reid AND `rerid`='{$r['rerid']}' ORDER BY redid";
                $paramss          = array();
                $paramss[':reid'] = $r['reid'];
                $r['fields']      = array();
                $fdatas           = pdo_fetchall($sql, $paramss);
                foreach ($fdatas as $fd) {
                    if (false == array_key_exists($fd['refid'], $r['fields'])) {
                        $r['fields'][$fd['refid']] = $fd['data'];
                    } else {
                        $r['fields'][$fd['refid']] .= '-' . $fd['data'];
                    }
                }
            }
            $data = array();
            foreach ($list as $key => $value) {
                $data[$key]['member']     = $value['member'];
                $data[$key]['mobile']     = $value['mobile'];
                $data[$key]['createtime'] = date('Y-m-d H:i:s', $value['createtime']);
                $data[$key]['status']     = $value['status'];
                if (!empty($value['fields'])) {
                    foreach ($value['fields'] as $field) {
                        if (substr($field, 0, 6) == 'images') {
                            $data[$key][] = str_replace(array(
                                "\n",
                                "\r",
                                "\t"
                            ), '', $_W['attachurl'] . $field);
                        } else {
                            $data[$key][] = str_replace(array(
                                "\n",
                                "\r",
                                "\t"
                            ), '', $field);
                        }
                    }
                }
            }
            $html = "\xEF\xBB\xBF";
            foreach ($tableheaders as $value) {
                $html .= $value['title'] . "\t ,";
            }
            foreach ($tableheader as $value) {
                $html .= $value['title'] . "\t ,";
            }
            $html .= "\n";
            foreach ($data as $value) {
                if ($value['status'] == '0') {
                    $huifu = '等待确认';
                } elseif ($value['status'] == '1') {
                    $huifu = '已确认';
                } elseif ($value['status'] == '2') {
                    $huifu = '已拒绝';
                } elseif ($value['status'] == '3') {
                    $huifu = '已完成';
                } elseif ($value['status'] == '-1') {
                    $huifu = '用户取消';
                }
                $html .= $value['member'] . "\t ,";
                $html .= $value['mobile'] . "\t ,";
                $html .= $value['createtime'] . "\t ,";
                $html .= $huifu . "\t ,";
                if (empty($value)) {
                    $html .= "&nbsp;\n ,";
                } else {
                    for ($i = 0; $i < $tablelength; $i++) {
                        $html .= $value[$i] . "\t ,";
                    }
                    $html .= "\n";
                }
            }
            $stime = date('Ymd', $starttime);
            $etime = date('Ymd', $endtime);
            header("Content-type:text/csv");
            header("Content-Disposition:attachment; filename=全部数据$stime-$etime.csv");
            echo $html;
            exit();
        }
        include $this->template('manage');
    }
    public function doWebbatchrecord()
    {
        global $_GPC, $_W;
        $reid  = intval($_GPC['reid']);
        $reply = pdo_fetch("select reid from " . tablename('dayu_form') . " where reid = :reid", array(
            ':reid' => $reid
        ));
        if (empty($reply)) {
            $this->webmessage('抱歉，表单主题不存在或是已经被删除！');
        }
        foreach ($_GPC['idArr'] as $k => $rerid) {
            $rerid = intval($rerid);
            pdo_delete('dayu_form_info', array(
                'rerid' => $rerid,
                'reid' => $reid
            ));
            pdo_delete('dayu_form_data', array(
                'rerid' => $rerid,
                'reid' => $reid
            ));
        }
        $this->webmessage('记录删除成功！', '', 0);
    }
    public function webmessage($error, $url = '', $errno = -1)
    {
        $data          = array();
        $data['errno'] = $errno;
        if (!empty($url)) {
            $data['url'] = $url;
        }
        $data['error'] = $error;
        echo json_encode($data);
        exit;
    }
    public function doWebDisplay()
    {
        global $_W, $_GPC;
        if ($_W['ispost']) {
            $reid              = intval($_GPC['reid']);
            $switch            = intval($_GPC['switch']);
            $sql               = 'UPDATE ' . tablename('dayu_form') . ' SET `status`=:status WHERE `reid`=:reid';
            $params            = array();
            $params[':status'] = $switch;
            $params[':reid']   = $reid;
            pdo_query($sql, $params);
            exit();
        }
        $sql    = 'SELECT * FROM ' . tablename('dayu_form') . ' WHERE `weid`=:weid';
        $status = $_GPC['status'];
        if ($status != '') {
            $sql .= " and status=" . intval($status);
        }
        $ds = pdo_fetchall($sql, array(
            ':weid' => $_W['uniacid']
        ));
        foreach ($ds as &$item) {
            $item['isstart'] = $item['starttime'] > 0;
            $item['switch']  = $item['status'];
            $item['link']    = murl('entry', array(
                'do' => 'dayu_form',
                'id' => $item['reid'],
                'm' => 'dayu_form'
            ), true, true);
        }
        include $this->template('display');
    }
    public function doWebDelete()
    {
        global $_W, $_GPC;
        $reid = intval($_GPC['id']);
        if ($reid > 0) {
            $params          = array();
            $params[':reid'] = $reid;
            $sql             = 'DELETE FROM ' . tablename('dayu_form') . ' WHERE `reid`=:reid';
            pdo_query($sql, $params);
            $sql = 'DELETE FROM ' . tablename('dayu_form_info') . ' WHERE `reid`=:reid';
            pdo_query($sql, $params);
            $sql = 'DELETE FROM ' . tablename('dayu_form_fields') . ' WHERE `reid`=:reid';
            pdo_query($sql, $params);
            $sql = 'DELETE FROM ' . tablename('dayu_form_data') . ' WHERE `reid`=:reid';
            pdo_query($sql, $params);
            $sql = 'DELETE FROM ' . tablename('dayu_form_staff') . ' WHERE `reid`=:reid';
            pdo_query($sql, $params);
            message('操作成功.', referer());
        }
        message('非法访问.');
    }
    public function doWebdayu_formDelete()
    {
        global $_W, $_GPC;
        $id = intval($_GPC['id']);
        if (!empty($id)) {
            pdo_delete('dayu_form_info', array(
                'rerid' => $id
            ));
            pdo_delete('dayu_form_data', array(
                'rerid' => $id
            ));
        }
        message('操作成功.', referer());
    }
    public function doMobiledayu_formDelete()
    {
        global $_W, $_GPC;
        $id     = intval($_GPC['id']);
        $openid = intval($_GPC['openid']);
        $reid   = intval($_GPC['reid']);
        $form   = pdo_fetch("SELECT rerid, openid FROM " . tablename('dayu_form_info') . " WHERE rerid = '$id'");
        if (!empty($id) && $openid == $form['openid']) {
            pdo_delete('dayu_form_info', array(
                'rerid' => $id
            ));
            pdo_delete('dayu_form_data', array(
                'rerid' => $id
            ));
            message('删除成功.', $this->createMobileUrl('mydayu_form', array(
                'weid' => $_W['uniacid'],
                'id' => $reid
            )));
        } else {
            message('删除失败，原因：该记录不在您的名下.', $this->createMobileUrl('mydayu_form', array(
                'weid' => $_W['uniacid'],
                'id' => $reid
            )));
        }
    }
    public function doWebPost()
    {
        global $_W, $_GPC;
        $reid    = intval($_GPC['id']);
        $hasData = false;
        if ($reid) {
            $sql = 'SELECT COUNT(*) FROM ' . tablename('dayu_form_info') . ' WHERE `reid`=' . $reid;
            if (pdo_fetchcolumn($sql) > 0) {
                $hasData = true;
            }
        }
        load()->model('mc');
        $groups = mc_groups();
        if (checksubmit()) {
            $record                = array();
            $record['title']       = trim($_GPC['activity']);
            $record['weid']        = $_W['uniacid'];
            $record['description'] = trim($_GPC['description']);
            $record['content']     = trim($_GPC['content']);
            $record['information'] = trim($_GPC['information']);
            if (!empty($_GPC['thumb'])) {
                $record['thumb'] = $_GPC['thumb'];
                load()->func('file');
                file_delete($_GPC['thumb-old']);
            }
            $record['status']        = intval($_GPC['status']);
            $record['custom_status'] = intval($_GPC['custom_status']);
            $record['inhome']        = intval($_GPC['inhome']);
            $record['filesize']      = intval($_GPC['filesize']);
            $record['upsize']        = intval($_GPC['upsize']);
            $record['pretotal']      = intval($_GPC['pretotal']);
            $record['starttime']     = strtotime($_GPC['starttime']);
            $record['endtime']       = strtotime($_GPC['endtime']);
            $record['noticeemail']   = trim($_GPC['noticeemail']);
            $record['k_templateid']  = trim($_GPC['k_templateid']);
            $record['kfirst']        = trim($_GPC['kfirst']);
            $record['kfoot']         = trim($_GPC['kfoot']);
            $record['mfirst']        = trim($_GPC['mfirst']);
            $record['mfoot']         = trim($_GPC['mfoot']);
            $record['m_templateid']  = trim($_GPC['m_templateid']);
            $record['mobile']        = trim($_GPC['mobile']);
            $record['accountsid']    = trim($_GPC['accountsid']);
            $record['tokenid']       = trim($_GPC['tokenid']);
            $record['appId']         = trim($_GPC['appId']);
            $record['templateId']    = trim($_GPC['templateId']);
            $record['kaishi']        = intval($_GPC['kaishi']);
            $record['jieshu']        = intval($_GPC['jieshu']);
            $record['tianshu']       = intval($_GPC['tianshu']);
            $record['mname']         = trim($_GPC['mname']);
            $record['skins']         = trim($_GPC['skins']);
            $record['mbgroup']       = $_GPC['mbgroup'];
            $record['follow']        = intval($_GPC['follow']);
            $record['isdel']         = intval($_GPC['isdel']);
            if (empty($reid)) {
                $record['status']     = 1;
                $record['createtime'] = TIMESTAMP;
                pdo_insert('dayu_form', $record);
                $reid = pdo_insertid();
                if (!$reid) {
                    message('保存表单失败, 请稍后重试.');
                }
            } else {
                if (pdo_update('dayu_form', $record, array(
                    'reid' => $reid
                )) === false) {
                    message('保存表单失败, 请稍后重试.');
                }
            }
            if (!$hasData) {
                $sql             = 'DELETE FROM ' . tablename('dayu_form_fields') . ' WHERE `reid`=:reid';
                $params          = array();
                $params[':reid'] = $reid;
                pdo_query($sql, $params);
                foreach ($_GPC['title'] as $k => $v) {
                    $field                 = array();
                    $field['reid']         = $reid;
                    $field['title']        = trim($v);
                    $field['displayorder'] = range_limit($_GPC['displayorder'][$k], 0, 254);
                    $field['type']         = $_GPC['type'][$k];
                    $field['essential']    = $_GPC['essentialvalue'][$k] == 'true' ? 1 : 0;
                    $field['bind']         = $_GPC['bind'][$k];
                    $field['value']        = $_GPC['value'][$k];
                    $field['value']        = urldecode($field['value']);
                    $field['description']  = $_GPC['desc'][$k];
                    pdo_insert('dayu_form_fields', $field);
                }
            }
            message('保存表单成功.', 'refresh');
        }
        $types             = array();
        $types['number']   = '数字(number)';
        $types['text']     = '字串(text)';
        $types['textarea'] = '文本(textarea)';
        $types['radio']    = '单选(radio)';
        $types['checkbox'] = '多选(checkbox)';
        $types['select']   = '选择(select)';
        $types['calendar'] = '日历(calendar)';
        $types['email']    = '电子邮件(email)';
        $types['image']    = '上传图片(image)';
        $types['range']    = '时间(range)';
        $types['reside']   = '居住地(reside)';
        $fields            = fans_fields();
        if ($reid) {
            $sql             = 'SELECT * FROM ' . tablename('dayu_form') . ' WHERE `weid`=:weid AND `reid`=:reid';
            $params          = array();
            $params[':weid'] = $_W['uniacid'];
            $params[':reid'] = $reid;
            $activity        = pdo_fetch($sql, $params);
            $activity['starttime'] && $activity['starttime'] = date('Y-m-d H:i:s', $activity['starttime']);
            $activity['endtime'] && $activity['endtime'] = date('Y-m-d H:i:s', $activity['endtime']);
            if ($activity) {
                $sql             = 'SELECT * FROM ' . tablename('dayu_form_fields') . ' WHERE `reid`=:reid ORDER BY `refid`';
                $params          = array();
                $params[':reid'] = $reid;
                $ds              = pdo_fetchall($sql, $params);
            }
        }
        $sql             = 'SELECT * FROM ' . tablename('dayu_form') . ' WHERE `weid`=:weid AND `reid`=:reid';
        $params          = array();
        $params[':weid'] = $_W['uniacid'];
        $params[':reid'] = $reid;
        $reply           = pdo_fetch($sql, $params);
        if (!$reply) {
            $activity = array(
                "mname" => "我的表单",
                "kfirst" => "有新的客户提交表单，请及时确认",
                "kfoot" => "点击处理客户提交的表单。",
                "mfirst" => "受理结果通知",
                "mfoot" => "如有疑问，请致电联系我们。",
                "information" => "您的预约申请我们已经收到, 请等待客服确认.",
                "kaishi" => 1,
                "jieshu" => 22,
                "tianshu" => 15,
                "filesize" => 12,
                "upsize" => 640,
                "status" => 1,
                "follow" => 1,
                "pretotal" => "100",
                "endtime" => date('Y-m-d H:i:s', strtotime('+30 day'))
            );
        }
        include $this->template('post');
    }
    public function doMobiledayu_form()
    {
        global $_W, $_GPC;
        include 'plugin/sms.php';
        require 'fans.mobile.php';
        $reid            = intval($_GPC['id']);
        $sql             = 'SELECT * FROM ' . tablename('dayu_form') . ' WHERE `weid`=:weid AND `reid`=:reid';
        $params          = array();
        $params[':weid'] = $weid;
        $params[':reid'] = $reid;
        $activity        = pdo_fetch($sql, $params);
        if ($activity['status'] == 0) {
            message('当前表单已经停止.');
        }
        if (!$activity) {
            message('非法访问.');
        }
        if ($activity['starttime'] > TIMESTAMP) {
            message('当前表单还未开始！');
        }
        if ($activity['endtime'] < TIMESTAMP) {
            message('当前表单已经结束！');
        }
        if ($activity['follow'] == 1) {
            $oauth_openid = "dayu_form_" . $weid;
            if (empty($_COOKIE[$oauth_openid])) {
                $this->getCode($reid);
            }
            $this->getFollow();
        }
        $userinfo = mc_fetch($uid, array(
            'realname',
            'mobile',
            'resideprovince',
            'residecity'
        ));
        $acc      = notice_init();
        if (is_error($acc)) {
            return error(-1, $acc['message']);
        }
        $setting           = uni_setting($_W['uniacid'], array(
            'creditnames',
            'creditbehaviors',
            'uc',
            'payment',
            'passport'
        ));
        $behavior          = $setting['creditbehaviors'];
        $creditnames       = $setting['creditnames'];
        $credits           = mc_credit_fetch($_W['member']['uid'], '*');
        $activity['thumb'] = tomedia($activity['thumb']);
        $title             = $activity['title'];
        $yuyuetime         = date('Y-m-d H:i', time() + 3600);
        $group             = pdo_fetch("SELECT * FROM " . tablename('mc_members') . " WHERE uniacid = '{$weid}' AND uid = '{$uid}'");
        $groupid           = $group['groupid'];
        if ($activity['mbgroup'] != 0) {
            if ($groupid != $activity['mbgroup']) {
                message('您所在会员组没有相关的操作权限！', '', 'error');
            }
        }
        $sql             = 'SELECT * FROM ' . tablename('dayu_form_fields') . ' WHERE `reid` = :reid ORDER BY `displayorder` DESC';
        $params          = array();
        $params[':reid'] = $reid;
        $ds              = pdo_fetchall($sql, $params);
        if (!$ds) {
            message('非法访问.');
        }
        $initRange = $initCalendar = false;
        $binds     = array();
        foreach ($ds as &$r) {
            if ($r['type'] == 'range') {
                $initRange = true;
            }
            if ($r['type'] == 'calendar') {
                $initCalendar = true;
            }
            if ($r['value']) {
                $r['options'] = explode(',', $r['value']);
            }
            if ($r['bind']) {
                $binds[$r['type']] = $r['bind'];
            }
            if ($r['type'] == 'reside') {
                $reside = $r;
            }
        }
        $resideprovince = !empty($userinfo['resideprovince']) ? $userinfo['resideprovince'] : '广东省';
        $residecity     = !empty($userinfo['residecity']) ? $userinfo['residecity'] : '广州市';
        if (checksubmit('submit')) {
            $pretotal = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('dayu_form_info') . " WHERE reid = :reid AND openid = :openid", array(
                ':reid' => $reid,
                ':openid' => $_W['openid']
            ));
            if ($pretotal >= $activity['pretotal']) {
                message('抱歉,每人只能提交' . $activity['pretotal'] . "次！", referer(), 'error');
            }
            $row               = array();
            $row['reid']       = $reid;
            $row['member']     = $_GPC['member'];
            $row['mobile']     = $_GPC['mobile'];
            $row['openid']     = $_W['openid'];
            $row['createtime'] = TIMESTAMP;
            $datas             = $fields = $update = array();
            foreach ($ds as $value) {
                $fields[$value['refid']] = $value;
            }
            foreach ($_GPC as $key => $value) {
                if (strexists($key, 'field_')) {
                    $bindFiled = substr(strrchr($key, '_'), 1);
                    if (!empty($bindFiled)) {
                        $update[$bindFiled] = $value;
                    }
                    $refid = intval(str_replace('field_', '', $key));
                    $field = $fields[$refid];
                    if ($refid && $field) {
                        $entry          = array();
                        $entry['reid']  = $reid;
                        $entry['rerid'] = 0;
                        $entry['refid'] = $refid;
                        if (in_array($field['type'], array(
                            'number',
                            'text',
                            'calendar',
                            'email',
                            'textarea',
                            'radio',
                            'range',
                            'select',
                            'image'
                        ))) {
                            $entry['data'] = strval($value);
                        }
                        if (in_array($field['type'], array(
                            'checkbox'
                        ))) {
                            if (!is_array($value))
                                continue;
                            $entry['data'] = implode(';', $value);
                        }
                        $datas[] = $entry;
                    }
                }
            }
            if ($_FILES) {
                foreach ($_FILES as $key => $file) {
                    if (strexists($key, 'field_')) {
                        $refid = intval(str_replace('field_', '', $key));
                        $field = $fields[$refid];
                        if ($refid && $field && $file['name'] && $field['type'] == 'image') {
                            $upfile      = $file;
                            $name        = $upfile['name'];
                            $type        = $upfile['type'];
                            $size        = $upfile['size'];
                            $tmp_name    = $upfile['tmp_name'];
                            $error       = $upfile['error'];
                            $upload_path = "../attachment/dayu_form/" . $weid . "/";
                            load()->func('file');
                            @mkdirs($upload_path);
                            if (intval($error) > 0) {
                                message('上传错误：错误代码：' . $error, referer(), 'error');
                            } else {
                                $upfilesize  = !empty($activity['filesize']) ? $activity['filesize'] : 12;
                                $maxfilesize = $upfilesize;
                                if ($maxfilesize > 0) {
                                    if ($size > $maxfilesize * 1024 * 1024) {
                                        message('上传文件过大' . $_FILES["file"]["error"], referer(), 'error');
                                    }
                                }
                                $uptypes = array(
                                    'image/jpg',
                                    'image/png',
                                    'image/jpeg'
                                );
                                if (!in_array($type, $uptypes)) {
                                    message('上传文件类型不符：' . $type, referer(), 'error');
                                }
                                if (!file_exists($upload_path)) {
                                    mkdir($upload_path);
                                }
                                $source_filename = 'form' . $reid . '_' . date("YmdHis") . mt_rand(10, 99);
                                $target_filename = 'form' . $reid . '_' . date("YmdHis") . mt_rand(10, 99) . '.thumb.jpg';
                                if (!move_uploaded_file($tmp_name, $upload_path . $source_filename)) {
                                    message('移动文件失败，请检查服务器权限', referer(), 'error');
                                }
                                $srcfile        = $upload_path . $source_filename;
                                $desfile        = $upload_path . $target_filename;
                                $avatarsize     = !empty($activity['upsize']) ? $activity['upsize'] : 640;
                                $ret            = file_image_thumb($srcfile, $desfile, $avatarsize);
                                $entry          = array();
                                $entry['reid']  = $reid;
                                $entry['rerid'] = 0;
                                $entry['refid'] = $refid;
                                if (!is_array($ret)) {
                                    $entry['data'] = $upload_path . $target_filename;
                                }
                                $datas[] = $entry;
                            }
                        }
                    }
                    unlink($srcfile);
                }
            }
            if (!empty($_GPC['reside'])) {
                if (in_array('reside', $binds)) {
                    $update['resideprovince'] = $_GPC['reside']['province'];
                    $update['residecity']     = $_GPC['reside']['city'];
                    $update['residedist']     = $_GPC['reside']['district'];
                }
                foreach ($_GPC['reside'] as $key => $value) {
                    $resideData          = array(
                        'reid' => $reside['reid']
                    );
                    $resideData['rerid'] = 0;
                    $resideData['refid'] = $reside['refid'];
                    $resideData['data']  = $value;
                    $datas[]             = $resideData;
                }
            }
            if (!empty($update)) {
                load()->model('mc');
                mc_update($_W['member']['uid'], $update);
            }
            if (empty($datas)) {
                message('非法访问.', '', 'error');
            }
            if (pdo_insert('dayu_form_info', $row) != 1) {
                message('保存失败.');
            }
            $rerid = pdo_insertid();
            if (empty($rerid)) {
                message('保存失败.');
            }
            foreach ($datas as &$r) {
                $r['rerid'] = $rerid;
                pdo_insert('dayu_form_data', $r);
            }
            if (empty($activity['starttime'])) {
                $record              = array();
                $record['starttime'] = TIMESTAMP;
                pdo_update('dayu_form', $record, array(
                    'reid' => $reid
                ));
            }
            if (!empty($datas)) {
                foreach ($datas as $row) {
                    $img = "<img src='{$_W['attachurl']}";
                    if (substr($row['data'], 0, 6) == 'images') {
                        $body = $fields[$row['refid']]['title'] . ':' . $img . $row['data'] . " ' width='90';height='120'/>";
                    }
                    $body .= '<h4>' . $fields[$row['refid']]['title'] . ':' . $row['data'] . '</h4>';
                    $smsbody .= '(' . $fields[$row['refid']]['title'] . ':' . $row['data'] . ')';
                    $bodym .= $fields[$row['refid']]['title'] . ':' . $row['data'] . ',';
                }
                if (!empty($activity['noticeemail'])) {
                    load()->func('communication');
                    ihttp_email($activity['noticeemail'], $activity['title'] . '的表单提醒', '<h4>姓名：' . $_GPC['member'] . '</h4><h4>手机：' . $_GPC['mobile'] . '</h4>' . $body);
                }
                if (!empty($activity['mobile'])) {
                    $content = $smsbody;
                    $this->SendSms($activity['mobile'], $_GPC['member'], $_GPC['mobile'], $smsbody, $activity['accountsid'], $activity['tokenid'], $activity['appId'], $activity['templateId']);
                }
                $ymember = $_GPC['member'];
                $ymobile = $_GPC['mobile'];
                $ytime   = date('Y-m-d H:i:s', TIMESTAMP);
                $kfirst  = $activity['kfirst'];
                $kfoot   = $activity['kfoot'];
                $staff   = pdo_fetchall("SELECT `openid` FROM " . tablename('dayu_form_staff') . " WHERE reid=:reid AND weid=:weid", array(
                    ':weid' => $_W['uniacid'],
                    ':reid' => $row['reid']
                ));
                if ($activity['custom_status'] == 0 && $staff) {
                    if (is_array($staff)) {
                        foreach ($staff as $s) {
                            $template = array(
                                "touser" => $s['openid'],
                                "template_id" => $activity['k_templateid'],
                                "url" => $_W['siteroot'] . 'app/' . $this->createMobileUrl('manageform', array(
                                    'name' => 'dayu_form',
                                    'weid' => $row['weid'],
                                    'id' => $row['reid']
                                )),
                                "topcolor" => "#FF0000",
                                "data" => array(
                                    'first' => array(
                                        'value' => urlencode($kfirst),
                                        'color' => "#743A3A"
                                    ),
                                    'keyword1' => array(
                                        'value' => urlencode($ymember),
                                        'color' => '#000000'
                                    ),
                                    'keyword2' => array(
                                        'value' => urlencode($ymobile),
                                        'color' => '#000000'
                                    ),
                                    'keyword3' => array(
                                        'value' => urlencode($ytime),
                                        'color' => '#000000'
                                    ),
                                    'keyword4' => array(
                                        'value' => urlencode($bodym),
                                        'color' => "#FF0000"
                                    ),
                                    'remark' => array(
                                        'value' => urlencode($kfoot),
                                        'color' => "#008000"
                                    )
                                )
                            );
                            $this->send_template_message(urldecode(json_encode($template)));
                        }
                    }
                } else {
                    $staff = pdo_fetchall("SELECT `openid` FROM " . tablename('dayu_form_staff') . " WHERE reid=" . $row['reid'] . " AND weid=" . $_W['uniacid']);
                    if (is_array($staff)) {
                        foreach ($staff as $s) {
                            $url  = $_W['siteroot'] . 'app/' . $this->createMobileUrl('manageform', array(
                                'name' => 'dayu_form',
                                'weid' => $row['weid'],
                                'id' => $row['reid']
                            ));
                            $info = "【您好，有新的订单】\n\n";
                            $info .= "姓名：{$ymember}\n手机：{$ymobile}\n内容：{$bodym}\n\n";
                            $info .= "<a href='{$url}'>点击查看详情</a>";
                            $custom = array(
                                'msgtype' => 'text',
                                'text' => array(
                                    'content' => urlencode($info)
                                ),
                                'touser' => $s['openid']
                            );
                            $acc->sendCustomNotice($custom);
                        }
                    }
                }
            }
            message($activity['information'], $this->createMobileUrl('mydayu_form', array(
                'name' => 'dayu_form',
                'weid' => $row['weid'],
                'id' => $row['reid']
            )));
        }
        foreach ($binds as $key => $value) {
            if ($value == 'reside') {
                unset($binds[$key]);
                $binds[] = 'resideprovince';
                $binds[] = 'residecity';
                $binds[] = 'residedist';
                break;
            }
        }
        if (!empty($_W['openid']) && !empty($binds)) {
            $profile = fans_search($_W['openid'], $binds);
            if ($profile['gender']) {
                if ($profile['gender'] == '0')
                    $profile['gender'] = '保密';
                if ($profile['gender'] == '1')
                    $profile['gender'] = '男';
                if ($profile['gender'] == '2')
                    $profile['gender'] = '女';
            }
            foreach ($ds as &$r) {
                if ($profile[$r['bind']]) {
                    $r['default'] = $profile[$r['bind']];
                }
            }
        }
        load()->func('tpl');
        $_share['title']   = $activity['title'];
        $_share['content'] = $activity['description'];
        $_share['imgUrl']  = tomedia($activity['thumb']);
        include $this->template($activity['skins']);
    }
    public function SendSms($telephone, $member, $mobile, $title, $accountsid, $tokenid, $appId, $templateId)
    {
        $result['state']       = 0;
        $options['accountsid'] = $accountsid;
        $options['token']      = $tokenid;
        $ucpass                = new Ucpaas($options);
        $appId                 = $appId;
        $to                    = $telephone;
        $templateId            = $templateId;
        $member                = $member;
        $mobile                = $mobile;
        $title                 = $title;
        $param                 = "{$member},{$mobile},{$title}";
        $iscg                  = $ucpass->templateSMS($appId, $to, $templateId, $param);
    }
    public function doMobileMydayu_form()
    {
        global $_W, $_GPC;
        require 'fans.mobile.php';
        $operation       = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        $reid            = intval($_GPC['id']);
        $sql             = 'SELECT * FROM ' . tablename('dayu_form') . ' WHERE `weid`=:weid AND `reid`=:reid';
        $params          = array();
        $params[':weid'] = $_W['uniacid'];
        $params[':reid'] = $reid;
        $activity        = pdo_fetch($sql, $params);
        if ($operation == 'display') {
            $pindex = max(1, intval($_GPC['page']));
            $psize  = 10;
            $status = intval($_GPC['status']);
            if ($_GPC['status'] != '') {
                if ($status == 2) {
                    $where .= " and ( status=2 or status=-1 )";
                } else {
                    $where .= " and status=$status";
                }
            }
            if ($reid) {
                $rows = pdo_fetchall("SELECT * FROM " . tablename('dayu_form_info') . " WHERE openid = :openid and reid = :reid $where ORDER BY rerid DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(
                    ':openid' => $_W['openid'],
                    ':reid' => $reid
                ));
            } else {
                $rows = pdo_fetchall("SELECT * FROM " . tablename('dayu_form_info') . " WHERE openid = :openid $where ORDER BY rerid DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(
                    ':openid' => $_W['openid']
                ));
            }
            $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('dayu_form_info') . " WHERE openid = :openid $where ", array(
                ':openid' => $_W['openid']
            ));
            $pager = dayupagination($total, $pindex, $psize);
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $reids[$row['reid']] = $row['reid'];
                }
                $dayu_form = pdo_fetchall("SELECT * FROM " . tablename('dayu_form') . " WHERE reid IN (" . implode(',', $reids) . ")", array(), 'reid');
            }
        } elseif ($operation == 'detail') {
            $id  = intval($_GPC['id']);
            $row = pdo_fetch("SELECT * FROM " . tablename('dayu_form_info') . " WHERE openid = :openid AND rerid = :rerid", array(
                ':openid' => $_W['openid'],
                ':rerid' => $id
            ));
            if (empty($row)) {
                message('我的表单不存在或是已经被删除！');
            }
            $row['createtime'] = !empty($row['createtime']) ? date('Y年m月d日 H:i', $row['createtime']) : '时间丢失';
            $row['yuyuetime']  = !empty($row['yuyuetime']) ? date('Y年m月d日 H:i', $row['yuyuetime']) : '客服尚未受理';
            $dayu_form         = pdo_fetch("SELECT * FROM " . tablename('dayu_form') . " WHERE reid = :reid", array(
                ':reid' => $row['reid']
            ));
            $sql               = 'SELECT * FROM ' . tablename('dayu_form_fields') . ' WHERE `reid`=:reid ORDER BY `refid`';
            $params            = array();
            $params[':reid']   = $row['reid'];
            $fields            = pdo_fetchall($sql, $params);
            if (empty($fields)) {
                message('非法访问.');
            }
            $ds = $fids = array();
            foreach ($fields as $f) {
                $ds[$f['refid']]['fid']   = $f['title'];
                $ds[$f['refid']]['type']  = $f['type'];
                $ds[$f['refid']]['refid'] = $f['refid'];
                $fids[]                   = $f['refid'];
            }
            $fids          = implode(',', $fids);
            $row['fields'] = array();
            $sql           = 'SELECT * FROM ' . tablename('dayu_form_data') . " WHERE `reid`=:reid AND `rerid`='{$row['rerid']}' AND `refid` IN ({$fids})";
            $fdatas        = pdo_fetchall($sql, $params);
            foreach ($fdatas as $fd) {
                $row['fields'][$fd['refid']] = $fd['data'];
            }
            foreach ($ds as $value) {
                if ($value['type'] == 'reside') {
                    $row['fields'][$value['refid']] = '';
                    foreach ($fdatas as $fdata) {
                        if ($fdata['refid'] == $value['refid']) {
                            $row['fields'][$value['refid']] .= $fdata['data'];
                        }
                    }
                    break;
                }
            }
            $dayu_form['content'] = htmlspecialchars_decode($dayu_form['content']);
        }
        include $this->template('dayu_form');
    }
    public function doMobilemanageform()
    {
        global $_W, $_GPC;
        require 'fans.mobile.php';
        load()->func('tpl');
        $operation       = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        $reid            = intval($_GPC['id']);
        $sql             = 'SELECT * FROM ' . tablename('dayu_form') . ' WHERE `weid`=:weid AND `reid`=:reid';
        $params          = array();
        $params[':weid'] = $_W['uniacid'];
        $params[':reid'] = $reid;
        $activity        = pdo_fetch($sql, $params);
        if (!empty($reid)) {
            if ($openid != $activity['kfid']) {
                message('非法访问！你不是管理员。');
            }
        }
        $list = pdo_fetchall("SELECT * FROM " . tablename('dayu_form') . " WHERE weid = '{$weid}' and status = 1 ORDER BY reid DESC", array(), 'reid');
        if ($operation == 'display' && $_W['openid'] == $activity['kfid']) {
            $pindex = max(1, intval($_GPC['page']));
            $psize  = 10;
            $status = intval($_GPC['status']);
            if ($status != '') {
                if ($status == 2) {
                    $where .= " and ( status=2 or status=-1 )";
                } else {
                    $where .= " and status=$status";
                }
            }
            $rows  = pdo_fetchall("SELECT * FROM " . tablename('dayu_form_info') . " WHERE reid = :reid $where ORDER BY rerid DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(
                ':reid' => $reid
            ));
            $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('dayu_form_info') . " WHERE reid = :reid $where ", array(
                ':reid' => $reid
            ));
            $pager = dayupagination($total, $pindex, $psize);
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $reids[$row['reid']] = $row['reid'];
                }
                $dayu_form = pdo_fetchall("SELECT * FROM " . tablename('dayu_form') . " WHERE reid IN (" . implode(',', $reids) . ") $where", array(), 'reid');
            }
        } elseif ($operation == 'detail') {
            $rerid = intval($_GPC['rerid']);
            $row   = pdo_fetch("SELECT * FROM " . tablename('dayu_form_info') . " WHERE rerid = :rerid", array(
                ':rerid' => $rerid
            ));
            if (empty($row)) {
                message('表单不存在或是已经被删除！');
            }
            $dayu_form            = pdo_fetch("SELECT * FROM " . tablename('dayu_form') . " WHERE reid = :reid", array(
                ':reid' => $row['reid']
            ));
            $dayu_form['content'] = htmlspecialchars_decode($dayu_form['content']);
            $sql                  = 'SELECT * FROM ' . tablename('dayu_form_fields') . ' WHERE `reid`=:reid ORDER BY `refid`';
            $params               = array();
            $params[':reid']      = $row['reid'];
            $fields               = pdo_fetchall($sql, $params);
            if (empty($fields)) {
                message('非法访问.');
            }
            $ds = $fids = array();
            foreach ($fields as $f) {
                $ds[$f['refid']]['fid']   = $f['title'];
                $ds[$f['refid']]['type']  = $f['type'];
                $ds[$f['refid']]['refid'] = $f['refid'];
                $fids[]                   = $f['refid'];
            }
            $fids          = implode(',', $fids);
            $row['fields'] = array();
            $sql           = 'SELECT * FROM ' . tablename('dayu_form_data') . " WHERE `reid`=:reid AND `rerid`='{$row['rerid']}' AND `refid` IN ({$fids})";
            $fdatas        = pdo_fetchall($sql, $params);
            foreach ($fdatas as $fd) {
                $row['fields'][$fd['refid']] = $fd['data'];
            }
            foreach ($ds as $value) {
                if ($value['type'] == 'reside') {
                    $row['fields'][$value['refid']] = '';
                    foreach ($fdatas as $fdata) {
                        if ($fdata['refid'] == $value['refid']) {
                            $row['fields'][$value['refid']] .= $fdata['data'];
                        }
                    }
                    break;
                }
            }
        }
        $row['createtime']   = !empty($row['createtime']) ? date('Y年m月d日 H:i', $row['createtime']) : '时间丢失';
        $row['yuyuetime']    = !empty($row['yuyuetime']) ? date('Y年m月d日 H:i', $row['yuyuetime']) : '客服尚未受理';
        $yuyuetime           = date('Y-m-d H:i', TIMESTAMP);
        $record              = array();
        $record['status']    = intval($_GPC['status']);
        $record['yuyuetime'] = strtotime($_GPC['yuyuetime']);
        $record['kfinfo']    = $_GPC['kfinfo'];
        if ($_GPC['status'] == '0') {
            $huifu = '等待客服确认（答复：' . $_GPC['kfinfo'] . '）';
        } elseif ($_GPC['status'] == '1') {
            $huifu = '已确认，客服受理中（答复：' . $_GPC['kfinfo'] . '）';
        } elseif ($_GPC['status'] == '2') {
            $huifu = '客服拒绝受理（答复：' . $_GPC['kfinfo'] . '）';
        } elseif ($_GPC['status'] == '3') {
            $huifu = '已完成（答复：' . $_GPC['kfinfo'] . '）';
        }
        $ymember  = $row['member'];
        $ymobile  = $row['mobile'];
        $ytime    = date('Y-m-d H:i:s', $yuyuetime);
        $mfirst   = $dayu_form['mfirst'];
        $mfoot    = $dayu_form['mfoot'];
        $template = array(
            "touser" => $row['openid'],
            "template_id" => $dayu_form['m_templateid'],
            "url" => $_W['siteroot'] . 'app/' . $this->createMobileUrl('mydayu_form', array(
                'name' => 'dayu_form',
                'weid' => $row['weid'],
                'id' => $row['reid']
            )),
            "topcolor" => "#FF0000",
            "data" => array(
                'first' => array(
                    'value' => urlencode($mfirst),
                    'color' => "#743A3A"
                ),
                'keyword1' => array(
                    'value' => urlencode($ymember),
                    'color' => '#000000'
                ),
                'keyword2' => array(
                    'value' => urlencode($ymobile),
                    'color' => '#000000'
                ),
                'keyword3' => array(
                    'value' => urlencode($_GPC['yuyuetime']),
                    'color' => '#000000'
                ),
                'keyword4' => array(
                    'value' => urlencode($huifu),
                    'color' => "#FF0000"
                ),
                'remark' => array(
                    'value' => urlencode($mfoot),
                    'color' => "#008000"
                )
            )
        );
        if ($_W['ispost'] && $dayu_form['custom_status'] == 1) {
            load()->model('mc');
            $acc = notice_init();
            if (is_error($acc)) {
                return error(-1, $acc['message']);
            }
            $url  = $_W['siteroot'] . 'app/' . $this->createMobileUrl('mydayu_form', array(
                'name' => 'dayu_form',
                'weid' => $row['weid'],
                'id' => $row['reid']
            ));
            $info = "【您好，受理结果通知】\n\n";
            $info .= "姓名：{$ymember}\n手机：{$ymobile}\n受理结果：{$huifu}\n\n";
            $info .= "<a href='{$url}'>点击查看详情</a>";
            $custom = array(
                'msgtype' => 'text',
                'text' => array(
                    'content' => urlencode($info)
                ),
                'touser' => $row['openid']
            );
            $acc->sendCustomNotice($custom);
            pdo_update('dayu_form_info', $record, array(
                'rerid' => $rerid
            ));
            message('修改成功', referer(), 'success');
        }
        if ($_W['ispost'] && $dayu_form['custom_status'] == 0) {
            load()->func('communication');
            $this->send_template_message(urldecode(json_encode($template)));
            pdo_update('dayu_form_info', $record, array(
                'rerid' => $rerid
            ));
            message('修改成功', referer(), 'success');
        }
        include $this->template('manage_form');
    }
    public function send_template_message($data)
    {
        global $_W, $_GPC;
        $atype        = 'weixin';
        $account_code = "account_weixin_code";
        load()->classs('weixin.account');
        $access_token = WeAccount::token();
        $url          = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $access_token;
        $response     = ihttp_request($url, $data);
        if (is_error($response)) {
            return error(-1, "访问公众平台接口失败, 错误: {$response['message']}");
        }
        $result = @json_decode($response['content'], true);
        if (empty($result)) {
            return error(-1, "接口调用失败, 原数据: {$response['meta']}");
        } elseif (!empty($result['errcode'])) {
            return error(-1, "访问微信接口错误, 错误代码: {$result['errcode']}, 错误信息: {$result['errmsg']},信息详情：{$this->error_code($result['errcode'])}");
        }
        return true;
    }
    public function doMobileFansUs()
    {
        global $_W, $_GPC;
        $qrcodesrc = tomedia('qrcode_' . $_W['acid'] . '.jpg');
        include $this->template('fans_us');
    }
    public function get_curl($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        return json_decode($data, 1);
    }
    public function post_curl($url, $post = '')
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        return json_decode($data, 1);
    }
    private function getCode($id)
    {
        global $_GPC, $_W;
        $appid  = $_W['account']['key'];
        $secret = $_W['account']['secret'];
        $level  = $_W['account']['level'];
        if ($level == 4) {
            $oauth_openid = "dayu_form_" . $_W['uniacid'];
            if (empty($_COOKIE[$oauth_openid])) {
                $redirect_uri = url('entry&do=GetToken&m=dayu_form&id=' . $id, '', true);
                $redirect_uri = $_W['siteroot'] . 'app/' . $redirect_uri;
                $url          = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . urlencode($redirect_uri) . '&response_type=code&scope=snsapi_base&state=0#wechat_redirewct';
                header('Location: ' . $url, true, 301);
            }
        } else {
            return '';
        }
    }
    public function doMobileGetToken()
    {
        global $_GPC, $_W;
        $appid  = $_W['account']['key'];
        $secret = $_W['account']['secret'];
        $url    = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $secret . '&code=' . $_GPC['code'] . '&grant_type=authorization_code';
        $data   = $this->get_curl($url);
        if (empty($data)) {
            $data = file_get_contents($url);
            $data = json_decode($data, 1);
        }
        $oauth_openid = "dayu_form_" . $_W['uniacid'];
        setcookie($oauth_openid, $data['openid'], time() + self::$COOKIE_DAYS * 24 * 60 * 60);
        header('Location:' . $this->createMobileUrl('dayu_form', array(
            'id' => $_GPC['id'],
            'weid' => $_W['uniacid']
        )), true, 301);
    }
    public function getFollow()
    {
        global $_GPC, $_W;
        $p = pdo_fetch("SELECT follow FROM " . tablename('mc_mapping_fans') . " WHERE uniacid = :weid AND openid = :openid LIMIT 1", array(
            ":weid" => $_W['uniacid'],
            ":openid" => $_W['openid']
        ));
        if (intval($p['follow']) == 0) {
            header('Location: ' . $this->createMobileUrl('FansUs'), true, 301);
        } else {
            return true;
        }
    }
}
function tpl_form_field_dateyy($name, $value = array(), $ishour = false)
{
    $s = '';
    if (!defined('INCLUDE_DATE')) {
        $s = '
		<link type="text/css" rel="stylesheet" href="/addons/dayu_form/template/mobile/datetimepicker.css" />
		<script type="text/javascript" src="/addons/dayu_form/template/mobile/datetimepicker.js"></script>';
    }
    define('INCLUDE_DATE', true);
    if (strexists($name, '[')) {
        $id = str_replace(array(
            '[',
            ']'
        ), '_', $name);
    } else {
        $id = $name;
    }
    $value  = empty($value) ? date('Y-m-d', mktime(0, 0, 0)) : $value;
    $ishour = empty($ishour) ? 2 : 0;
    $s .= '
	<input type="text" id="datepicker_' . $id . '" name="' . $name . '" value="' . $value . '" class="datetimepickers form-control" readonly="readonly" />
	<script type="text/javascript">
		$("#datepicker_' . $id . '").datetimepicker({
			format: "yyyy-mm-dd hh:ii",
			minView: "' . $ishour . '",
			//pickerPosition: "top-right",
			autoclose: true
		});
	</script>';
    return $s;
}
function dayu_fans_form($field, $value = '')
{
    switch ($field) {
        case 'reside':
        case 'resideprovince':
        case 'residecity':
        case 'residedist':
            $html = dayu_form_field_district('reside', $value);
            break;
    }
    return $html;
}
function dayu_form_field_district($name, $values = array())
{
    $html = '';
    if (!defined('TPL_INIT_DISTRICT')) {
        $html .= '
		<script type="text/javascript">
			require(["jquery", "district"], function($, dis){
				$(".tpl-district-container").each(function(){
					var elms = {};
					elms.province = $(this).find(".tpl-province")[0];
					elms.city = $(this).find(".tpl-city")[0];
					elms.district = $(this).find(".tpl-district")[0];
					var vals = {};
					vals.province = $(elms.province).attr("data-value");
					vals.city = $(elms.city).attr("data-value");
					vals.district = $(elms.district).attr("data-value");
					dis.render(elms, vals, {withTitle: true});
				});
			});
		</script>';
        define('TPL_INIT_DISTRICT', true);
    }
    if (empty($values) || !is_array($values)) {
        $values = array(
            'province' => '',
            'city' => '',
            'district' => ''
        );
    }
    if (empty($values['province'])) {
        $values['province'] = '';
    }
    if (empty($values['city'])) {
        $values['city'] = '';
    }
    if (empty($values['district'])) {
        $values['district'] = '';
    }
    $html .= '
		<div class="tpl-district-container" style="display: block;">
			<div class="col-lg-4">
				<select name="' . $name . '[province]" data-value="' . $values['province'] . '" class="tpl-province">
				</select><i></i>
			</div>
			<div class="col-lg-4">
				<select name="' . $name . '[city]" data-value="' . $values['city'] . '" class="tpl-city">
				</select><i></i>
			</div>
			<div class="col-lg-4">
				<select name="' . $name . '[district]" data-value="' . $values['district'] . '" class="tpl-district">
				</select><i></i>
			</div>
		</div>';
    return $html;
}
function https_post($url, $datas)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $datas);
    curl_setopt($curl, CURLOPT_SSL_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    if (curl_errno($curl)) {
        return 'Errno' . curl_error($curl);
    }
    curl_close($curl);
    return $result;
}
function notice_init()
{
    global $_W;
    $acc = WeAccount::create();
    if (is_null($acc)) {
        return error(-1, '创建公众号操作对象失败');
    }
    return $acc;
}
function dayupagination($total, $pageIndex, $pageSize = 15, $url = '', $context = array('before' => 5, 'after' => 4, 'ajaxcallback' => ''))
{
    global $_W;
    $pdata = array(
        'tcount' => 0,
        'tpage' => 0,
        'cindex' => 0,
        'findex' => 0,
        'pindex' => 0,
        'nindex' => 0,
        'lindex' => 0,
        'options' => ''
    );
    if ($context['ajaxcallback']) {
        $context['isajax'] = true;
    }
    $pdata['tcount'] = $total;
    $pdata['tpage']  = ceil($total / $pageSize);
    if ($pdata['tpage'] <= 1) {
        return '';
    }
    $cindex          = $pageIndex;
    $cindex          = min($cindex, $pdata['tpage']);
    $cindex          = max($cindex, 1);
    $pdata['cindex'] = $cindex;
    $pdata['findex'] = 1;
    $pdata['pindex'] = $cindex > 1 ? $cindex - 1 : 1;
    $pdata['nindex'] = $cindex < $pdata['tpage'] ? $cindex + 1 : $pdata['tpage'];
    $pdata['lindex'] = $pdata['tpage'];
    if ($context['isajax']) {
        if (!$url) {
            $url = $_W['script_name'] . '?' . http_build_query($_GET);
        }
        $pdata['faa'] = 'href="javascript:;" page="' . $pdata['findex'] . '" ' . ($callbackfunc ? 'onclick="' . $callbackfunc . '(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['findex'] . '\', this);return false;"' : '');
        $pdata['paa'] = 'href="javascript:;" page="' . $pdata['pindex'] . '" ' . ($callbackfunc ? 'onclick="' . $callbackfunc . '(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['pindex'] . '\', this);return false;"' : '');
        $pdata['naa'] = 'href="javascript:;" page="' . $pdata['nindex'] . '" ' . ($callbackfunc ? 'onclick="' . $callbackfunc . '(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['nindex'] . '\', this);return false;"' : '');
        $pdata['laa'] = 'href="javascript:;" page="' . $pdata['lindex'] . '" ' . ($callbackfunc ? 'onclick="' . $callbackfunc . '(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['lindex'] . '\', this);return false;"' : '');
    } else {
        if ($url) {
            $pdata['faa'] = 'href="?' . str_replace('*', $pdata['findex'], $url) . '"';
            $pdata['paa'] = 'href="?' . str_replace('*', $pdata['pindex'], $url) . '"';
            $pdata['naa'] = 'href="?' . str_replace('*', $pdata['nindex'], $url) . '"';
            $pdata['laa'] = 'href="?' . str_replace('*', $pdata['lindex'], $url) . '"';
        } else {
            $_GET['page'] = $pdata['findex'];
            $pdata['faa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
            $_GET['page'] = $pdata['pindex'];
            $pdata['paa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
            $_GET['page'] = $pdata['nindex'];
            $pdata['naa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
            $_GET['page'] = $pdata['lindex'];
            $pdata['laa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
        }
    }
    $html = '<ul class="am-pagination am-pagination-centered">';
    if ($pdata['cindex'] > 1) {
        $html .= "<li><a {$pdata['faa']} class=\"pager-nav\">&laquo;</a></li>";
        $html .= "<li><a {$pdata['paa']} class=\"pager-nav\">Prev</a></li>";
    }
    if (!$context['before'] && $context['before'] != 0) {
        $context['before'] = 5;
    }
    if (!$context['after'] && $context['after'] != 0) {
        $context['after'] = 4;
    }
    if ($context['after'] != 0 && $context['before'] != 0) {
        $range          = array();
        $range['start'] = max(1, $pdata['cindex'] - $context['before']);
        $range['end']   = min($pdata['tpage'], $pdata['cindex'] + $context['after']);
        if ($range['end'] - $range['start'] < $context['before'] + $context['after']) {
            $range['end']   = min($pdata['tpage'], $range['start'] + $context['before'] + $context['after']);
            $range['start'] = max(1, $range['end'] - $context['before'] - $context['after']);
        }
        for ($i = $range['start']; $i <= $range['end']; $i++) {
            if ($context['isajax']) {
                $aa = 'href="javascript:;" page="' . $i . '" ' . ($callbackfunc ? 'onclick="' . $callbackfunc . '(\'' . $_W['script_name'] . $url . '\', \'' . $i . '\', this);return false;"' : '');
            } else {
                if ($url) {
                    $aa = 'href="?' . str_replace('*', $i, $url) . '"';
                } else {
                    $_GET['page'] = $i;
                    $aa           = 'href="?' . http_build_query($_GET) . '"';
                }
            }
            $html .= ($i == $pdata['cindex'] ? '<li class="am-active"><a href="javascript:;">' . $i . '</a></li>' : "<li><a {$aa}>" . $i . '</a></li>');
        }
    }
    if ($pdata['cindex'] < $pdata['tpage']) {
        $html .= "<li><a {$pdata['naa']} class=\"pager-nav\">Next</a></li>";
        $html .= "<li><a {$pdata['laa']} class=\"pager-nav\">&raquo;</a></li>";
    }
    $html .= '</ul>';
    return $html;
}
?>