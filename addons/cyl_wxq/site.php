<?php


defined('IN_IA') or exit('Access Denied');
class Cyl_wxqModuleSite extends WeModuleSite
{
    private $tb_category = 'cyl_wxq_category';
    private $tb_area = 'cyl_wxq_area';
    private $tb_wxq = 'cyl_wxq_wxq';
    private $url = '/addons/cyl_wxq/template/mobile/';
    private function getStatus($status)
    {
        $status = intval($status);
        if ($status == 1) {
            return '审核通过';
        } else {
            return '待审核';
        }
    }
    public function doMobileIndex()
    {
        global $_W, $_GPC;
        $condition          = ' uniacid = :uniacid AND status = 1';
        $params[':uniacid'] = $_W['uniacid'];
        if (!empty($_GPC['keyword'])) {
            $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
        }
        $cid = $_GPC['cid'];
        if ($cid != 0) {
            $condition .= " AND categoryid={$cid}";
        }
        $aid = $_GPC['aid'];
        if ($aid != 0) {
            $condition .= " AND address={$aid}";
        }
        $pindex     = max(1, intval($_GPC['page']));
        $psize      = 30;
        $total      = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->tb_wxq) . ' WHERE ' . $condition, $params);
        $list       = pdo_fetchall('SELECT * FROM ' . tablename($this->tb_wxq) . ' WHERE ' . $condition . ' ORDER BY id DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);
        $pager      = pagination($total, $pindex, $psize);
        $categories = $this->getAllCategory();
        $areas      = $this->getAllArea();
        $settings   = $this->module['config'];
        $_share     = array(
            'desc' => $settings['share_desc'],
            'title' => $settings['share_title'],
            'imgUrl' => tomedia($settings['thumb'])
        );
        load()->func('tpl');
        $title = "微信群收录";
        include $this->template('index');
    }
    public function doMobileContent()
    {
        global $_W, $_GPC;
        load()->func('tpl');
        $id = intval($_GPC['id']);
        load()->model('mc');
        $settings         = $this->module['config'];
        $content          = pdo_fetch('SELECT * FROM ' . tablename($this->tb_wxq) . ' WHERE uniacid = :uniacid AND id = :id', array(
            ':uniacid' => $_W['uniacid'],
            ':id' => $id
        ));
        $openid           = $_W['fans']['openid'];
        $nickname         = $_W['fans']['nickname'];
        $categories       = $this->getAllCategory();
        $areas            = $this->getAllArea();
        $content['click'] = intval($content['click']) + 1;
        pdo_update($this->tb_wxq, array(
            'click' => $business['click']
        ), array(
            'uniacid' => $_W['uniacid'],
            'id' => $id
        ));
        $_share = array(
            'desc' => $content['desc'],
            'title' => $content['title'],
            'imgUrl' => tomedia($content['thumb'])
        );
        $title  = "微信群收录";
        include $this->template('content');
    }
    private function getAllCategory()
    {
        global $_W;
        $sql        = 'SELECT * FROM ' . tablename($this->tb_category) . ' WHERE uniacid=:uniacid ORDER BY `sort` desc, id desc ';
        $params     = array(
            ':uniacid' => $_W['uniacid']
        );
        $categories = pdo_fetchall($sql, $params, 'id');
        return $categories;
    }
    private function getAllArea()
    {
        global $_W;
        $sql    = 'SELECT * FROM ' . tablename($this->tb_area) . ' WHERE uniacid=:uniacid ORDER BY `sort` desc, id desc ';
        $params = array(
            ':uniacid' => $_W['uniacid']
        );
        $areas  = pdo_fetchall($sql, $params, 'id');
        return $areas;
    }
    public function doWebCategory()
    {
        global $_W, $_GPC;
        $ops = array(
            'display',
            'create',
            'delete'
        );
        $op  = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
        if ($op == 'display') {
            if (checksubmit()) {
                $cats = $_GPC['categories'];
                if (empty($cats)) {
                    message('尚未添加任何分类.');
                }
                foreach ($cats as $k => $cat) {
                    empty($cat['title']) && message('有分类名称未添加,无法保存.');
                    $cat['sort'] = intval($cat['sort']);
                }
                foreach ($cats as $k => $cat) {
                    pdo_update($this->tb_category, $cat, array(
                        'id' => $k
                    ));
                }
                message('保存成功.', '', 'success');
            }
            $categories = $this->getAllCategory();
            load()->func('tpl');
            include $this->template('category');
        }
        if ($op == 'create') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $sql        = 'SELECT * FROM ' . tablename($this->tb_category) . ' WHERE id=:id AND uniacid=:uniacid LIMIT 1';
                $params     = array(
                    ':id' => $id,
                    ':uniacid' => $_W['uniacid']
                );
                $categories = pdo_fetch($sql, $params);
                if (empty($categories)) {
                    message('未找到指定的分类.', $this->createWebUrl('categories'));
                }
            }
            if (checksubmit()) {
                $category            = $_GPC['category'];
                $category['uniacid'] = $_W['uniacid'];
                $category['sort']    = intval($cat['sort']);
                if (!empty($id)) {
                    pdo_update($this->tb_category, $category, array(
                        'id' => $id
                    ));
                } else {
                    pdo_insert($this->tb_category, $category);
                    $id = pdo_insertid();
                }
                message('更新分类成功！', $this->createWebUrl('category', array(
                    'op' => 'display'
                )), 'success');
            }
            include $this->template('category');
        }
        if ($op == 'delete') {
            $id = intval($_GPC['id']);
            if (empty($id)) {
                message('未找到指定商家分类');
            }
            $result = pdo_delete($this->tb_category, array(
                'id' => $id,
                'uniacid' => $_W['uniacid']
            ));
            if (intval($result) == 1) {
                message('删除商家分类成功.', $this->createWebUrl('category'), 'success');
            } else {
                message('删除商家分类失败.');
            }
        }
    }
    public function doWebArea()
    {
        global $_W, $_GPC;
        $ops = array(
            'display',
            'create',
            'delete'
        );
        $op  = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
        if ($op == 'display') {
            if (checksubmit()) {
                $cats = $_GPC['area'];
                if (empty($cats)) {
                    message('尚未添加任何分类.');
                }
                foreach ($cats as $k => $cat) {
                    empty($cat['title']) && message('有区域名称未添加,无法保存.');
                    $cat['sort'] = intval($cat['sort']);
                }
                foreach ($cats as $k => $cat) {
                    pdo_update($this->tb_area, $cat, array(
                        'id' => $k
                    ));
                }
                message('保存成功.', '', 'success');
            }
            $areas = $this->getAllArea();
            load()->func('tpl');
            include $this->template('area');
        }
        if ($op == 'create') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $sql    = 'SELECT * FROM ' . tablename($this->tb_area) . ' WHERE id=:id AND uniacid=:uniacid LIMIT 1';
                $params = array(
                    ':id' => $id,
                    ':uniacid' => $_W['uniacid']
                );
                $areas  = pdo_fetch($sql, $params);
                if (empty($categories)) {
                    message('未找到指定的区域.', $this->createWebUrl('areas'));
                }
            }
            if (checksubmit()) {
                $area            = $_GPC['area'];
                $area['uniacid'] = $_W['uniacid'];
                $area['sort']    = intval($cat['sort']);
                if (!empty($id)) {
                    pdo_update($this->tb_area, $area, array(
                        'id' => $id
                    ));
                } else {
                    pdo_insert($this->tb_area, $area);
                    $id = pdo_insertid();
                }
                message('更新分类成功！', $this->createWebUrl('area', array(
                    'op' => 'display'
                )), 'success');
            }
            include $this->template('area');
        }
        if ($op == 'delete') {
            $id = intval($_GPC['id']);
            if (empty($id)) {
                message('未找到指定商家分类');
            }
            $result = pdo_delete($this->tb_area, array(
                'id' => $id,
                'uniacid' => $_W['uniacid']
            ));
            if (intval($result) == 1) {
                message('删除分类成功.', $this->createWebUrl('area'), 'success');
            } else {
                message('删除分类失败.');
            }
        }
    }
    public function doWebQun()
    {
        global $_W, $_GPC;
        $ops = array(
            'display',
            'edit',
            'delete'
        );
        $op  = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
        if ($op == 'display') {
            $pageindex = max(intval($_GPC['page']), 1);
            $pagesize  = 20;
            $where     = ' WHERE uniacid=:uniacid';
            $params    = array(
                ':uniacid' => $_W['uniacid']
            );
            if (!empty($_GPC['keyword'])) {
                $where .= ' AND ( (`title` like :keyword) )';
                $params[':keyword'] = "%{$_GPC['keyword']}%";
            }
            if (!empty($_GPC['status'])) {
                $where .= ' AND (status = :status)';
                $params[':status'] = intval($_GPC['status']);
            }
            if (!empty($_GPC['categoryid'])) {
                $where .= ' AND (categoryid = :categoryid)';
                $params[':categoryid'] = intval($_GPC['categoryid']);
            }
            $sql        = 'SELECT COUNT(*) FROM ' . tablename($this->tb_wxq) . $where;
            $total      = pdo_fetchcolumn($sql, $params);
            $pager      = pagination($total, $pageindex, $pagesize);
            $sql        = 'SELECT * FROM ' . tablename($this->tb_wxq) . " {$where} ORDER BY time desc LIMIT " . (($pageindex - 1) * $pagesize) . ',' . $pagesize;
            $list       = pdo_fetchall($sql, $params, 'id');
            $categories = $this->getAllCategory();
            $areas      = $this->getAllArea();
            load()->func('tpl');
            include $this->template('qun');
        }
        if ($op == 'edit') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $sql    = 'SELECT * FROM ' . tablename($this->tb_wxq) . ' WHERE id=:id AND uniacid=:uniacid LIMIT 1';
                $params = array(
                    ':id' => $id,
                    ':uniacid' => $_W['uniacid']
                );
                $list   = pdo_fetch($sql, $params);
                if (empty($list)) {
                    message('未找到指定的群.', $this->createWebUrl('wxq'));
                }
            }
            $categories = $this->getAllCategory();
            $areas      = $this->getAllArea();
            if (checksubmit()) {
                $data            = $_GPC['data'];
                $data['uniacid'] = $_W['uniacid'];
                $data['time']    = TIMESTAMP;
                if (empty($id)) {
                    pdo_insert($this->tb_wxq, $data);
                    $id = pdo_insertid();
                } else {
                    pdo_update($this->tb_wxq, $data, array(
                        'id' => $id
                    ));
                }
                message('信息保存成功', $this->createWebUrl('qun', array(
                    'op' => 'edit',
                    'id' => $id
                )), 'success');
            }
            load()->func('tpl');
            include $this->template('qun_edit');
        }
        if ($op == 'delete') {
            $id = intval($_GPC['id']);
            if (empty($id)) {
                message('未找到指定分类');
            }
            $result = pdo_delete($this->tb_wxq, array(
                'id' => $id,
                'uniacid' => $_W['uniacid']
            ));
            if (intval($result) == 1) {
                message('删除成功.', $this->createWebUrl('wxq'), 'success');
            } else {
                message('删除失败.');
            }
        }
    }
}