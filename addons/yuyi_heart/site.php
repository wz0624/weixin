<?php
defined('IN_IA') or exit('Access Denied');
class Yuyi_heartModuleSite extends WeModuleSite
{
    public function doWebAdd()
    {
        global $_W, $_GPC;
        if (checksubmit('submit')) {
            $data['uniacid']  = $_W['uniacid'];
            $data['question'] = $_GPC['question'];
            if (empty($data['question'])) {
                message('问题不能为空', $this->createWebUrl('add', array()), 'info');
            } else {
                $res = pdo_insert('yuyi_heart_add', $data);
                if ($res) {
                    message('添加问题成功', $this->createWebUrl('add', array()), 'success');
                } else {
                    message('添加问题失败', $this->createWebUrl('add', array()), 'error');
                }
            }
        }
        include $this->template('add');
    }
    public function doWebAll()
    {
        global $_W, $_GPC;
        checklogin();
        $index     = isset($_GPC['page']) ? $_GPC['page'] : 1;
        $pageIndex = $index;
        $pageSize  = 9;
        $total     = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('yuyi_heart_add') . " WHERE `uniacid`=:uniacid", array(
            ':uniacid' => $_W['uniacid']
        ));
        $page      = pagination($total, $pageIndex, $pageSize);
        $contion   = 'LIMIT ' . ($pageIndex - 1) * $pageSize . ',' . $pageSize;
        $data      = pdo_fetchall("SELECT * FROM " . tablename('yuyi_heart_add') . " WHERE `uniacid`=:uniacid ORDER BY `id` DESC " . $contion, array(
            ':uniacid' => $_W['uniacid']
        ));
        include $this->template('all');
    }
    public function doWebDel()
    {
        global $_GPC, $_W;
        $data['id']      = $_GPC['id'];
        $data['uniacid'] = $_W['uniacid'];
        $res             = pdo_delete("yuyi_heart_add", array(
            'id' => $data['id'],
            'uniacid' => $data['uniacid']
        ));
        if ($res) {
            message('删除问题成功', $this->createWebUrl('all'), 'success');
        } else {
            message('删除问题失败', $this->createWebUrl('all'), 'error');
        }
    }
    public function doWebChange()
    {
        global $_GPC, $_W;
        $data = pdo_fetch("SELECT * FROM " . tablename('yuyi_heart_add') . " WHERE `id`=:id AND `uniacid`=:uniacid ", array(
            ':id' => $_GPC['id'],
            ':uniacid' => $_W['uniacid']
        ));
        if (checksubmit()) {
            if (empty($_GPC['question'])) {
                message('问题不能为空', $this->createWebUrl('change', array(
                    'id' => $_GPC['id']
                )), 'info');
            } else {
                $res = pdo_update('yuyi_heart_add', array(
                    'question' => $_GPC['question']
                ), array(
                    'id' => $_GPC['id'],
                    'uniacid' => $_W['uniacid']
                ));
                if ($res) {
                    message('更新问题成功', $this->createWebUrl('all', array(
                        'id' => $_GPC['id']
                    )), 'success');
                } else {
                    message('更新问题失败', $this->createWebUrl('change', array(
                        'id' => $_GPC['id']
                    )), 'error');
                }
            }
        }
        include $this->template('xiugai');
    }
    public function doWebContant()
    {
        global $_GPC, $_W;
        checklogin();
        $index     = isset($_GPC['page']) ? $_GPC['page'] : 1;
        $pageIndex = $index;
        $pageSize  = 9;
        $total     = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('yuyi_heart_content') . " WHERE `uniacid`=:uniacid", array(
            ':uniacid' => $_W['uniacid']
        ));
        $page      = pagination($total, $pageIndex, $pageSize);
        $contion   = 'LIMIT ' . ($pageIndex - 1) * $pageSize . ',' . $pageSize;
        $data      = pdo_fetchall("SELECT * FROM " . tablename('yuyi_heart_add') . " WHERE `uniacid`=:uniacid ORDER BY `id` DESC " . $contion, array(
            ':uniacid' => $_W['uniacid']
        ));
        include $this->template('content');
    }
    public function doWebCheck()
    {
        checklogin();
        global $_GPC, $_W;
        $index      = isset($_GPC['page']) ? $_GPC['page'] : 1;
        $pageIndex  = $index;
        $pageSize   = 9;
        $total      = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('yuyi_heart_content') . " WHERE `uniacid`=:uniacid AND `id`=:id", array(
            ':uniacid' => $_W['uniacid'],
            ':id' => $_GPC['id']
        ));
        $page       = pagination($total, $pageIndex, $pageSize);
        $contion    = 'LIMIT ' . ($pageIndex - 1) * $pageSize . ',' . $pageSize;
        $data['id'] = $_GPC['id'];
        $res        = pdo_fetchall("SELECT * FROM " . tablename('yuyi_heart_content') . ' WHERE `uniacid`=:uniacid AND `id`=:id ORDER BY `id` DESC ' . $contion, array(
            ':uniacid' => $_W['uniacid'],
            ':id' => $data['id']
        ));
        include $this->template('check');
    }
    public function doWebChangecomment()
    {
        global $_GPC, $_W;
        $data = pdo_fetch("SELECT * FROM " . tablename('yuyi_heart_content') . " WHERE `id_c`=:id_c ", array(
            ':id_c' => $_GPC['id_c']
        ));
        if (checksubmit()) {
            $data['id_c']    = $_GPC['id_c'];
            $data['content'] = $_GPC['content'];
            $res             = pdo_update('yuyi_heart_content', array(
                'content' => $data['content']
            ), array(
                'id_c' => $data['id_c']
            ));
            if ($res) {
                message("评论修改成功", $this->createWebUrl('check', array(
                    'id' => $_GPC['id'],
                    'uniacid' => $_W['uniacid']
                )), 'success');
            } else {
                message('评论修改失败', $this->createWebUrl('check', array(
                    'id' => $_GPC['id'],
                    'uniacid' => $_W['uniacid']
                )), 'error');
            }
        }
        include $this->template('xiugaiC');
    }
    public function doWebDelcomment()
    {
        global $_GPC, $_W;
        $res = pdo_delete('yuyi_heart_content', array(
            'id_c' => $_GPC['id_c']
        ));
        if ($res) {
            message("评论删除成功", $this->createWebUrl('check', array(
                'id' => $_GPC['id'],
                'uniacid' => $_W['uniacid']
            )), 'success');
        } else {
            message('评论删除失败', $this->createWebUrl('check', array(
                'id' => $_GPC['id'],
                'uniacid' => $_W['uniacid']
            )), 'error');
        }
    }
    public function doMobileIn()
    {
        global $_W, $_GPC;
        checkauth();
        $index     = isset($_GPC['page']) ? $_GPC['page'] : 1;
        $pageIndex = $index;
        $pageSize  = 9;
        $total     = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('yuyi_heart_add') . " WHERE `uniacid`=:uniacid", array(
            ':uniacid' => $_W['uniacid']
        ));
        $page      = pagination($total, $pageIndex, $pageSize);
        $contion   = 'LIMIT ' . ($pageIndex - 1) * $pageSize . ',' . $pageSize;
        $data      = pdo_fetchall("SELECT * FROM " . tablename('yuyi_heart_add') . " WHERE `uniacid`=:uniacid ORDER BY `id` DESC " . $contion, array(
            ':uniacid' => $_W['uniacid']
        ));
        include $this->template('all');
    }
    public function doMobileAnswer()
    {
        global $_W, $_GPC;
        if (checksubmit()) {
            $da['content'] = $_GPC['textarea'];
            $da['id']      = $_GPC['id'];
            $da['uniacid'] = $_GPC['uniacid'];
            if (empty($da['content'])) {
                message('答案不能为空', $this->createMobileUrl('Answer', array(
                    'id' => $da['id'],
                    'uniacid' => $da['uniacid']
                )), 'info');
            } else {
                $res = pdo_insert('yuyi_heart_content', $da);
                if ($res) {
                    message('提交答案成功', $this->createMobileUrl('In', array(
                        'uniacid' => $da['uniacid']
                    )), 'success');
                } else {
                    message('提交答案失败', $this->createMobileUrl('Answer', array(
                        'id' => $da['id'],
                        'uniacid' => $da['uniacid']
                    )), 'error');
                }
            }
        }
        $data = pdo_fetch("SELECT * FROM " . tablename('yuyi_heart_add') . " WHERE `id`=:id AND `uniacid`=:uniacid", array(
            ':id' => $_GPC['id'],
            ':uniacid' => $_W['uniacid']
        ));
        $res  = pdo_fetchall("SELECT * FROM " . tablename('yuyi_heart_content') . ' WHERE `uniacid`=:uniacid AND `id`=:id ORDER BY `id` DESC ', array(
            ':uniacid' => $_W['uniacid'],
            ':id' => $_GPC['id']
        ));
        include $this->template('answer');
    }
    public function doMobileUser()
    {
        global $_GPC, $_W;
        if (checksubmit()) {
            $data['uniacid']  = $_W['uniacid'];
            $data['question'] = $_GPC['textarea'];
            if (empty($data['question'])) {
                message('问题不能为空', $this->createMobileUrl('user'), 'info');
            } else {
                $res = pdo_insert('yuyi_heart_add', $data);
                if ($res) {
                    message('提交问题成功', $this->createMobileUrl('in'), 'success');
                } else {
                    message('提交问题失败', $this->createMobileUrl('user'), 'error');
                }
            }
        }
        include $this->template('content');
    }
}