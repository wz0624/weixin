<?php


defined('IN_IA') or exit('Access Denied');
class Cyl_phoneModuleSite extends WeModuleSite
{
    private $tb_category = 'cyl_phone_category';
    private $tb_business = 'cyl_phone_business';
    private $tb_message = 'cyl_phone_message';
    private $url = '/addons/cyl_phone/template/mobile/';
    private function getStatus($status)
    {
        $status = intval($status);
        if ($status == 1) {
            return '审核通过';
        } else {
            return '待审核';
        }
    }
    private function getAllCategory($num = 60)
    {
        global $_W;
        $sql        = 'SELECT * FROM ' . tablename($this->tb_category) . ' WHERE uniacid=:uniacid ORDER BY `orderno` asc, id asc LIMIT ' . $num;
        $params     = array(
            ':uniacid' => $_W['uniacid']
        );
        $categories = pdo_fetchall($sql, $params, 'id');
        return $categories;
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
        $pindex         = max(1, intval($_GPC['page']));
        $psize          = 30;
        $total          = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->tb_business) . ' WHERE ' . $condition, $params);
        $business       = pdo_fetchall('SELECT * FROM ' . tablename($this->tb_business) . ' WHERE ' . $condition . ' ORDER BY id DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);
        $pager          = pagination($total, $pindex, $psize);
        $categories     = $this->getAllCategory();
        $categorieshome = $this->getAllCategory(4);
        $settings       = $this->module['config'];
        $_share         = array(
            'desc' => $settings['share_desc'],
            'title' => $settings['share_title'],
            'imgUrl' => tomedia($settings['thumb'])
        );
        load()->func('tpl');
        $title = "便民电话";
        include $this->template('index');
    }
    public function doMobileContent()
    {
        global $_W, $_GPC;
        load()->func('tpl');
        $id = intval($_GPC['id']);
        load()->model('mc');
        $settings    = $this->module['config'];
        $business    = pdo_fetch('SELECT * FROM ' . tablename($this->tb_business) . ' WHERE uniacid = :uniacid AND id = :id', array(
            ':uniacid' => $_W['uniacid'],
            ':id' => $id
        ));
        $messagelist = pdo_fetchall('SELECT * FROM ' . tablename($this->tb_message) . ' WHERE uniacid = :uniacid AND contentid = :contentid LIMIT 2', array(
            ':uniacid' => $_W['uniacid'],
            ':contentid' => $id
        ));
        $openid      = $_W['fans']['openid'];
        $nickname    = $_W['fans']['nickname'];
        $categories  = $this->getAllCategory();
        if (checksubmit()) {
            if (empty($_W['fans']['nickname'])) {
                mc_oauth_userinfo();
            }
            $openid            = $_W['fans']['openid'];
            $nickname          = $_W['fans']['nickname'];
            $data              = $_GPC['business'];
            $data['contentid'] = $id;
            $data['uniacid']   = $_W['uniacid'];
            $data['openid']    = $openid;
            $data['time']      = TIMESTAMP;
            $ret               = pdo_insert($this->tb_message, $data);
            if (!empty($settings['templateid'])) {
                $kdata = array(
                    'first' => array(
                        'value' => '有人给您的店铺留言了',
                        'color' => '#ff510'
                    ),
                    'keyword1' => array(
                        'value' => $data['nickname'],
                        'color' => '#ff510'
                    ),
                    'keyword2' => array(
                        'value' => $data['content'],
                        'color' => '#ff510'
                    ),
                    'remark' => array(
                        'value' => '请进入店铺进行查看',
                        'color' => '#ff510'
                    )
                );
                $url   = $_W['siteroot'] . 'app' . ltrim(murl('entry', array(
                    'do' => 'content',
                    'm' => 'cyl_phone',
                    'id' => $id
                )), '.');
                $acc   = WeAccount::create();
                $acc->sendTplNotice($business['openid'], $settings['templateid'], $kdata, $url, $topcolor = '#FF683F');
            }
            if (!empty($ret)) {
                message('留言成功', $this->createMobileUrl('content', array(
                    'id' => $id
                )), 'success');
            } else {
                message('留言失败');
            }
        }
        $business['click'] = intval($business['click']) + 1;
        pdo_update($this->tb_business, array(
            'click' => $business['click']
        ), array(
            'uniacid' => $_W['uniacid'],
            'id' => $id
        ));
        $_share = array(
            'desc' => $business['desc'],
            'title' => $business['title'],
            'imgUrl' => tomedia($business['logo'])
        );
        $title  = "便民服务";
        include $this->template('content');
    }
    public function doMobileMessage()
    {
        global $_W, $_GPC;
        load()->func('tpl');
        $id          = intval($_GPC['id']);
        $pindex      = max(1, intval($_GPC['page']));
        $psize       = 20;
        $total       = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->tb_message) . ' WHERE uniacid = :uniacid AND contentid = :contentid', array(
            ':uniacid' => $_W['uniacid'],
            ':contentid' => $id
        ));
        $messagelist = pdo_fetchall('SELECT * FROM ' . tablename($this->tb_message) . ' WHERE uniacid = :uniacid AND contentid = :contentid ORDER BY id DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(
            ':uniacid' => $_W['uniacid'],
            ':contentid' => $id
        ));
        $pager       = pagination($total, $pindex, $psize);
        $title       = "便民电话-留言列表";
        include $this->template('message');
    }
    public function doMobileMybusiness()
    {
        global $_W, $_GPC;
        load()->model('mc');
        $settings = $this->module['config'];
        $ops      = array(
            'display',
            'edit',
            'delete'
        );
        $op       = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
        if (empty($_W['fans']['nickname'])) {
            mc_oauth_userinfo();
        }
        $openid = $_W['fans']['openid'];
        if ($op == 'display') {
            $condition          = ' uniacid = :uniacid AND status = 1 AND openid=:openid';
            $params[':uniacid'] = $_W['uniacid'];
            $params[':openid']  = $openid;
            if (!empty($_GPC['keyword'])) {
                $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
            }
            $cid = $_GPC['cid'];
            if ($cid != 0) {
                $strWhere = " AND categoryid={$cid}";
            }
            $pindex     = max(1, intval($_GPC['page']));
            $psize      = 20;
            $total      = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->tb_business) . ' WHERE ' . $condition, $params);
            $business   = pdo_fetchall('SELECT * FROM ' . tablename($this->tb_business) . ' WHERE ' . $condition . $strWhere . ' ORDER BY id DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);
            $pager      = pagination($total, $pindex, $psize);
            $categories = $this->getAllCategory();
            include $this->template('display');
        }
        if ($op == 'edit') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $sql      = 'SELECT * FROM ' . tablename($this->tb_business) . ' WHERE id=:id AND uniacid=:uniacid LIMIT 1';
                $params   = array(
                    ':id' => $id,
                    ':uniacid' => $_W['uniacid']
                );
                $business = pdo_fetch($sql, $params);
                $referer  = referer();
                if (empty($business)) {
                    message('未找到指定的商家.', $this->createMobileUrl('display'));
                }
            }
            $categories = $this->getAllCategory();
            $referer    = referer();
            if (checksubmit()) {
                $data = $_GPC['business'];
                if (empty($business)) {
                    $data['uniacid'] = $_W['uniacid'];
                    $data['time']    = TIMESTAMP;
                    $data['openid']  = $openid;
                    if ($settings['status'] == 1) {
                        $data['status'] = 1;
                    } else {
                        $data['status'] = 2;
                    }
                    $ret = pdo_insert($this->tb_business, $data);
                    if (!empty($ret)) {
                        $id = pdo_insertid();
                    }
                } else {
                    $ret = pdo_update($this->tb_business, $data, array(
                        'id' => $id
                    ));
                }
                if (!empty($settings['kfid']) && !empty($settings['templateid'])) {
                    $kdata = array(
                        'first' => array(
                            'value' => '便民电话入驻申请通知',
                            'color' => '#ff510'
                        ),
                        'keyword1' => array(
                            'value' => $data['title'],
                            'color' => '#ff510'
                        ),
                        'keyword2' => array(
                            'value' => $data['desc'],
                            'color' => '#ff510'
                        ),
                        'remark' => array(
                            'value' => '请进入后台进行查看',
                            'color' => '#ff510'
                        )
                    );
                    $acc   = WeAccount::create();
                    $acc->sendTplNotice($settings['kfid'], $settings['templateid'], $kdata, $topcolor = '#FF683F');
                }
                if (!empty($ret)) {
                    message('商家信息保存成功', $this->createMobileUrl('Mybusiness', array(
                        'op' => 'display',
                        'id' => $id
                    )), 'success');
                } else {
                    message('商家信息保存失败');
                }
            }
            load()->func('tpl');
            include $this->template('add');
        }
    }
    public function array_multi2single($array)
    {
        static $result_array = array();
        foreach ($array as $value) {
            if (is_array($value)) {
                $this->array_multi2single($value);
            } else
                $result_array[] = $value;
        }
        return $result_array;
    }
    public function doWebPush()
    {
        global $_W, $_GPC;
        load()->func('tpl');
        $settings = $this->module['config'];
        $sql      = 'SELECT * FROM ' . tablename('cyl_phone_push') . ' WHERE uniacid=:uniacid';
        $params   = array(
            ':uniacid' => $_W['uniacid']
        );
        $pushlist = pdo_fetch($sql, $params);
        $business = pdo_fetchall('SELECT * FROM ' . tablename($this->tb_business) . ' WHERE uniacid=:uniacid GROUP BY openid', $params);
        if (checksubmit()) {
            $push            = $_GPC['data'];
            $push['uniacid'] = $_W['uniacid'];
            if (!empty($pushlist)) {
                pdo_update('cyl_phone_push', $push);
            } else {
                pdo_insert('cyl_phone_push', $push);
                $push = pdo_insertid();
            }
            if (!empty($settings['templateid'])) {
                $kdata = array(
                    'first' => array(
                        'value' => $push['first'],
                        'color' => '#ff510'
                    ),
                    'keyword1' => array(
                        'value' => $push['keyword1'],
                        'color' => '#ff510'
                    ),
                    'keyword2' => array(
                        'value' => $push['keyword2'],
                        'color' => '#ff510'
                    ),
                    'remark' => array(
                        'value' => $push['remark'],
                        'color' => '#ff510'
                    )
                );
                $url   = $push['link'];
                $acc   = WeAccount::create();
                if ($push['push'] == 1) {
                    $acc->sendTplNotice($push['kfid'], $settings['templateid'], $kdata, $url, $topcolor = '#FF683F');
                } else {
                    foreach ($business as $key => $value) {
                        $array = $value['openid'];
                        $acc->sendTplNotice($array, $settings['templateid'], $kdata, $url, $topcolor = '#FF683F');
                    }
                }
            }
            message('发送成功', $this->createWebUrl('push'), 'success');
        }
        include $this->template('push');
    }
    public function doWebBusiness()
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
            $sql        = 'SELECT COUNT(*) FROM ' . tablename($this->tb_business) . $where;
            $total      = pdo_fetchcolumn($sql, $params);
            $pager      = pagination($total, $pageindex, $pagesize);
            $sql        = 'SELECT * FROM ' . tablename($this->tb_business) . " {$where} ORDER BY id asc LIMIT " . (($pageindex - 1) * $pagesize) . ',' . $pagesize;
            $business   = pdo_fetchall($sql, $params, 'id');
            $categories = $this->getAllCategory();
            load()->func('tpl');
            include $this->template('business');
        }
        if ($op == 'edit') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $sql      = 'SELECT * FROM ' . tablename($this->tb_business) . ' WHERE id=:id AND uniacid=:uniacid LIMIT 1';
                $params   = array(
                    ':id' => $id,
                    ':uniacid' => $_W['uniacid']
                );
                $business = pdo_fetch($sql, $params);
                if (empty($business)) {
                    message('未找到指定的商家.', $this->createWebUrl('business'));
                }
            }
            $categories = $this->getAllCategory();
            if (checksubmit()) {
                $data = $_GPC['business'];
                if (empty($business)) {
                    $data['uniacid'] = $_W['uniacid'];
                    $data['time']    = TIMESTAMP;
                    $ret             = pdo_insert($this->tb_business, $data);
                    if (!empty($ret)) {
                        $id = pdo_insertid();
                    }
                } else {
                    $ret = pdo_update($this->tb_business, $data, array(
                        'id' => $id
                    ));
                }
                if (!empty($ret)) {
                    message('商家信息保存成功', $this->createWebUrl('business', array(
                        'op' => 'edit',
                        'id' => $id
                    )), 'success');
                } else {
                    message('商家信息保存失败');
                }
            }
            load()->func('tpl');
            include $this->template('business_edit');
        }
        if ($op == 'delete') {
            $id = intval($_GPC['id']);
            if (empty($id)) {
                message('未找到指定商家分类');
            }
            $result = pdo_delete($this->tb_business, array(
                'id' => $id,
                'uniacid' => $_W['uniacid']
            ));
            if (intval($result) == 1) {
                message('删除商家成功.', $this->createWebUrl('business'), 'success');
            } else {
                message('删除商家失败.');
            }
        }
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
                    empty($cat['name']) && message('有分类名称未添加,无法保存.');
                    $cat['orderno'] = intval($cat['orderno']);
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
                $sql        = 'SELECT * FROM ' . tablename('cyl_phone_category') . ' WHERE id=:id AND uniacid=:uniacid LIMIT 1';
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
                $category['orderno'] = intval($cat['orderno']);
                if (!empty($id)) {
                    pdo_update('cyl_phone_category', $category, array(
                        'id' => $id
                    ));
                } else {
                    pdo_insert('cyl_phone_category', $category);
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
}