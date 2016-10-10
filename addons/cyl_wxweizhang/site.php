<?php
defined('IN_IA') or exit('Access Denied');
include('model.php');
class Cyl_wxweizhangModuleSite extends WeModuleSite
{
    private $tb_category = 'cyl_wxwenzhang_category';
    private $tb_article = 'cyl_wxwenzhang_article';
    private $tb_styles = 'cyl_wxwenzhang_styles';
    private $tb_styles_vars = 'cyl_wxwenzhang_styles_vars';
    private $tb_templates = 'cyl_wxwenzhang_templates';
    private $tb_message = 'cyl_wxwenzhang_message';
    private $tb_shang = 'cyl_wxwenzhang_shang';
    private $tb_article_share = 'cyl_wxwenzhang_article_share';
    private $tb_article_gg = 'cyl_wxwenzhang_article_gg';
    private function getAllCategory()
    {
        global $_W;
        $sql        = 'SELECT * FROM ' . tablename($this->tb_category) . ' WHERE uniacid=:uniacid ORDER BY `displayorder` desc, id desc ';
        $params     = array(
            ':uniacid' => $_W['uniacid']
        );
        $categories = pdo_fetchall($sql, $params, 'id');
        return $categories;
    }
    private function getAllArticle()
    {
        global $_W;
        $sql     = 'SELECT * FROM ' . tablename($this->tb_article) . ' WHERE uniacid=:uniacid ORDER BY id desc ';
        $params  = array(
            ':uniacid' => $_W['uniacid']
        );
        $article = pdo_fetchall($sql, $params, 'id');
        return $article;
    }
    public function typeid()
    {
        $ch     = curl_init();
        $url    = 'http://apis.baidu.com/showapi_open_bus/weixin/weixin_article_type';
        $header = array(
            'apikey: 9605e74753cc33db2fe49910953ae54e'
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $res  = curl_exec($ch);
        $data = json_decode($res);
        $news = array();
        foreach ($data->showapi_res_body->typeList as $item) {
            $news[] = array(
                'id' => $item->id,
                'name' => $item->name
            );
        }
        return $news;
    }
    public function sendhongbaoto($arr)
    {
        global $_W, $_GPC;
        $settings                 = $this->module['config'];
        $data['mch_appid']        = $settings['appid'];
        $data['mchid']            = $settings['mchid'];
        $data['nonce_str']        = $this->createNoncestr();
        $data['partner_trade_no'] = random(10) . date('Ymd') . random(3);
        $data['openid']           = $arr['openid'];
        $data['check_name']       = "NO_CHECK";
        $data['amount']           = $arr['fee'];
        $data['spbill_create_ip'] = $settings['ip'];
        $data['desc']             = $arr['body'];
        if (!$data['openid']) {
            $rearr['return_msg'] = '缺少用户openid';
            return $rearr;
        }
        $data['sign'] = $this->getSign($data);
        $xml          = $this->arrayToXml($data);
        $url          = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
        $re           = $this->wxHttpsRequestPem($xml, $url);
        $rearr        = $this->xmlToArray($re);
        return $rearr;
    }
    function trimString($value)
    {
        $ret = null;
        if (null != $value) {
            $ret = $value;
            if (strlen($ret) == 0) {
                $ret = null;
            }
        }
        return $ret;
    }
    public function createNoncestr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str   = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    function formatBizQueryParaMap($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if ($urlencode) {
                $v = urlencode($v);
            }
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar;
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }
    public function getSign($Obj)
    {
        foreach ($Obj as $k => $v) {
            $Parameters[$k] = $v;
        }
        ksort($Parameters);
        $String  = $this->formatBizQueryParaMap($Parameters, false);
        $String  = $String . "&key=" . $this->module['config']['password'];
        $String  = md5($String);
        $result_ = strtoupper($String);
        return $result_;
    }
    public function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
        $xml .= "</xml>";
        return $xml;
    }
    public function xmlToArray($xml)
    {
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }
    public function wxHttpsRequestPem($vars, $url, $second = 30, $aHeader = array())
    {
        global $_W;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, MODULE_ROOT . '/cert/apiclient_cert.pem' . '.' . $_W['uniacid']);
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLKEY, MODULE_ROOT . '/cert/apiclient_key.pem' . '.' . $_W['uniacid']);
        curl_setopt($ch, CURLOPT_CAINFO, 'PEM');
        curl_setopt($ch, CURLOPT_CAINFO, MODULE_ROOT . '/cert/rootca.pem' . '.' . $_W['uniacid']);
        if (count($aHeader) >= 1) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        }
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
        $data = curl_exec($ch);
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            echo "call faild, errorCode:$error\n";
            curl_close($ch);
            return false;
        }
    }
    public function doMobileIndex()
    {
        global $_W, $_GPC;
        load()->model('mc');
        $id          = $_GPC['id'];
        $category    = $this->getAllCategory();
        $settings    = $this->module['config'];
        $page        = isset($_GPC['page']) ? $_GPC['page'] : 1;
        $pageindex   = 30;
        $pageindexgg = 1;
        if (!empty($_GPC['keyword'])) {
            $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
        }
        if (!empty($_GPC['pcate'])) {
            $pcate = intval($_GPC['pcate']);
            $condition .= " AND pcate = $pcate";
        }
        if (!empty($_GPC['ccate'])) {
            $ccate = $_GPC['ccate'];
            $condition .= " AND ccate = $ccate";
        }
        $total     = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->tb_article) . " WHERE uniacid = {$_W['uniacid']} $condition");
        $totalpage = ceil($total / $pageindex);
        if ($settings['weizhuan'] == 1) {
            $pageindexgg = 20;
            $listgg      = pdo_fetchall("SELECT id,uid,title,thumb,pic,createtime,click,pcate,description,credit FROM " . tablename($this->tb_article) . " WHERE uniacid = '{$_W['uniacid']}' AND zongjia > 0 AND status = 1 $condition ORDER BY jiage DESC LIMIT " . ($page - 1) * $pageindexgg . ',' . $pageindexgg);
        } else {
            $list   = pdo_fetchall("SELECT id,uid,title,thumb,pic,createtime,click,pcate,description,credit,source FROM " . tablename($this->tb_article) . " WHERE uniacid = '{$_W['uniacid']}' AND zongjia = 0 AND status = 1  $condition ORDER BY displayorder DESC , id DESC LIMIT " . ($page - 1) * $pageindex . ',' . $pageindex);
            $listgg = pdo_fetchall("SELECT id,uid,title,thumb,pic,createtime,click,pcate,description,credit,source FROM " . tablename($this->tb_article) . " WHERE uniacid = '{$_W['uniacid']}' AND zongjia > 0 AND status = 1 $condition ORDER BY jiage DESC LIMIT " . ($page - 1) * $pageindexgg . ',' . $pageindexgg);
        }
        $_share = array(
            'desc' => $settings['description'],
            'title' => $settings['title'],
            'imgUrl' => tomedia($settings['thumb'])
        );
        include $this->template('index');
    }
    public function doMobileCategory()
    {
        global $_W, $_GPC;
        $pcate    = $_GPC['pcate'];
        $category = $this->getAllCategory();
        $settings = $this->module['config'];
        include $this->template('category');
    }
    public function doMobileRenwu()
    {
        global $_W, $_GPC;
        load()->model('mc');
        $id       = $_GPC['id'];
        $category = $this->getAllCategory();
        $settings = $this->module['config'];
        $listgg   = pdo_fetchall("SELECT id,uid,title,thumb,pic,createtime,click,pcate,description,credit FROM " . tablename($this->tb_article) . " WHERE uniacid = {$_W['uniacid']} AND zongjia > 0 $condition ORDER BY jiage DESC , id asc");
        $_share   = array(
            'desc' => $settings['description'],
            'title' => $settings['title'],
            'imgUrl' => tomedia($settings['thumb'])
        );
        include $this->template('renwu');
    }
    public function doMobileFaxian()
    {
        global $_W, $_GPC;
        $category = $this->getAllCategory();
        include $this->template('faxian');
    }
    public function doMobileList()
    {
        global $_W, $_GPC;
        load()->model('mc');
        $id          = $_GPC['id'];
        $category    = $this->getAllCategory();
        $settings    = $this->module['config'];
        $page        = isset($_GPC['page']) ? $_GPC['page'] : 1;
        $pageindex   = 15;
        $pageindexgg = 1;
        if (!empty($_GPC['keyword'])) {
            $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
        }
        if (!empty($_GPC['pcate'])) {
            $pcate = $_GPC['pcate'];
            $condition .= " AND pcate = $pcate";
        }
        if (!empty($_GPC['ccate'])) {
            $ccate = $_GPC['ccate'];
            $condition .= " AND ccate = $ccate";
        }
        $total     = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->tb_article) . " WHERE uniacid = {$_W['uniacid']} $condition");
        $totalpage = ceil($total / $pageindex);
        if ($settings['weizhuan'] == 1) {
            $pageindexgg = 20;
            $listgg      = pdo_fetchall("SELECT id,uid,title,thumb,pic,createtime,click,pcate,description,credit FROM " . tablename($this->tb_article) . " WHERE uniacid = '{$_W['uniacid']}' AND zongjia > 0 AND status = 1 $condition ORDER BY jiage DESC LIMIT " . ($page - 1) * $pageindexgg . ',' . $pageindexgg);
        } else {
            $list   = pdo_fetchall("SELECT id,uid,title,thumb,pic,createtime,click,pcate,description,credit FROM " . tablename($this->tb_article) . " WHERE uniacid = '{$_W['uniacid']}' AND zongjia = 0 AND status = 1  $condition ORDER BY displayorder DESC , id DESC LIMIT " . ($page - 1) * $pageindex . ',' . $pageindex);
            $listgg = pdo_fetchall("SELECT id,uid,title,thumb,pic,createtime,click,pcate,description,credit FROM " . tablename($this->tb_article) . " WHERE uniacid = '{$_W['uniacid']}' AND zongjia > 0 AND status = 1 $condition ORDER BY jiage DESC LIMIT " . ($page - 1) * $pageindexgg . ',' . $pageindexgg);
        }
        $_share = array(
            'desc' => $settings['description'],
            'title' => $settings['title'],
            'imgUrl' => tomedia($settings['thumb'])
        );
        include $this->template('list');
    }
    public function doMobileDetail()
    {
        global $_W, $_GPC;
        $page     = $_GPC['page'];
        $pcate    = $_GPC['pcate'];
        $typeid   = $this->typeId();
        $op       = $_GPC['op'];
        $settings = $this->module['config'];
        $category = $this->getAllCategory();
        if (!empty($_GPC['keyword'])) {
            $keyword = $_GPC['keyword'];
        }
        $id       = intval($_GPC['id']);
        $contents = pdo_fetch("SELECT * FROM " . tablename($this->tb_article) . " WHERE id = :id", array(
            ':id' => $id
        ));
        if ($contents['zongjia']) {
            if (empty($_W['fans']['nickname'])) {
                mc_oauth_userinfo();
            }
        }
        $title             = $contents['title'];
        $message           = pdo_fetchall('SELECT * FROM ' . tablename($this->tb_message) . ' WHERE uniacid = :uniacid AND article_id = :article_id AND status = 1', array(
            ':uniacid' => $_W['uniacid'],
            ':article_id' => $id
        ));
        $contents['click'] = intval($contents['click']) + 1;
        pdo_update($this->tb_article, array(
            'click' => $contents['click']
        ), array(
            'uniacid' => $_W['uniacid'],
            'id' => $id
        ));
        $_share      = array(
            'desc' => $contents['description'],
            'title' => $contents['title'],
            'imgUrl' => $contents['thumb']
        );
        $user        = pdo_getall($this->tb_article, array(
            'uniacid' => $_W['uniacid']
        ), array(
            'title',
            'thumb'
        ));
        $shang       = pdo_getall($this->tb_shang, array(
            'uniacid' => $_W['uniacid'],
            'article_id' => $id,
            'status' => 1
        ));
        $shang_total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this->tb_shang) . ' WHERE uniacid = :uniacid AND article_id = :article_id AND status=1', array(
            ':uniacid' => $_W['uniacid'],
            ':article_id' => $id
        ));
        $list        = pdo_fetchall("SELECT id,title,thumb,pic,createtime,click,pcate,description FROM " . tablename($this->tb_article) . " WHERE uniacid = '{$_W['uniacid']}' AND pcate = '{$pcate}' ORDER BY rand() LIMIT 5");
        $gg          = pdo_fetch("SELECT id,uid,jiage,thumb,link,zongjia,title FROM " . tablename($this->tb_article_gg) . " WHERE uniacid = :uniacid AND status = 1 Order By Rand() LIMIT 1", array(
            ':uniacid' => $_W['uniacid']
        ));
        if ($gg['zongjia'] <= 0) {
            pdo_update($this->tb_article_gg, array(
                'status' => 2
            ), array(
                'id' => $gg['id']
            ));
        }
        $user_total = pdo_fetch("SELECT id,sharenum,time FROM " . tablename('cyl_wxwenzhang_article_share') . " WHERE openid = :openid AND action = :action AND article_id = :id order by id desc", array(
            ':openid' => $_W['fans']['openid'],
            ':action' => 'share',
            ':id' => $id
        ));
        $user_click = pdo_fetch("SELECT id,sharenum,time,action,formuid FROM " . tablename('cyl_wxwenzhang_article_share') . " WHERE formuid = :formuid AND action = :action AND article_id = :id order by id desc", array(
            ':formuid' => CLIENT_IP,
            ':action' => 'click',
            ':id' => $id
        ));
        $credit     = iunserializer($contents['credit']);
        if ($credit['status'] && $credit['limit'] <= 1) {
            pdo_update($this->tb_article, array(
                'status' => 2
            ), array(
                'id' => $id
            ));
        }
        if ($op == 'detail') {
            include $this->template('detail');
        }
        if ($op == 'shang') {
            include $this->template('shang');
        }
        if ($op == 'liuyan') {
            if (checksubmit()) {
                if (empty($_W['fans']['nickname'])) {
                    mc_oauth_userinfo();
                }
                $data = array(
                    'uniacid' => $_W['uniacid'],
                    'article_id' => $id,
                    'openid' => $_W['fans']['openid'],
                    'nickname' => $_W['fans']['nickname'],
                    'content' => $_GPC['content'],
                    'avatar' => $_W['fans']['tag']['avatar'],
                    'time' => TIMESTAMP
                );
                if ($settings['status'] == 1) {
                    $data['status'] = 0;
                } else {
                    $data['status'] = 1;
                }
                $ret            = pdo_insert($this->tb_message, $data);
                $contents['ly'] = intval($contents['ly']) + 1;
                pdo_update($this->tb_article, array(
                    'ly' => $contents['ly']
                ), array(
                    'uniacid' => $_W['uniacid'],
                    'id' => $id
                ));
                if (!empty($ret)) {
                    message('留言成功', $this->createMobileUrl('detail', array(
                        'id' => $id,
                        'op' => 'detail'
                    )), 'success');
                } else {
                    message('留言失败');
                }
            }
            include $this->template('liuyan');
        }
    }
    public function doMobileHandsel()
    {
        global $_W, $_GPC;
        load()->model('mc');
        $settings = $this->module['config'];
        if ($_W['ispost']) {
            $id  = intval($_GPC['id']);
            $uid = $_GPC['uid'];
            load()->classs('weixin.account');
            load()->func('communication');
            $acc        = WeAccount::create($acid);
            $fxjifen    = pdo_fetchcolumn("SELECT MAX(amount) FROM " . tablename('cyl_wxwenzhang_article_share') . " WHERE uniacid = {$_W['uniacid']} AND uid={$uid}");
            $article    = pdo_fetch('SELECT id, credit,title,jifen FROM ' . tablename($this->tb_article) . ' WHERE uniacid = :uniacid AND id = :id ', array(
                ':uniacid' => $_W['uniacid'],
                ':id' => $id
            ));
            $user_total = pdo_fetch("SELECT id,sharenum,time,article_id FROM " . tablename('cyl_wxwenzhang_article_share') . " WHERE openid = :openid AND article_id = :article_id order by id desc", array(
                ':openid' => $_W['fans']['openid'],
                ':article_id' => $article['id']
            ));
            $credit     = iunserializer($article['credit']) ? iunserializer($article['credit']) : array();
            if ($_GPC['action'] == 'img') {
                $user_img = pdo_fetch("SELECT id,sharenum,time,action,formuid FROM " . tablename('cyl_wxwenzhang_article_share') . " WHERE formuid = :formuid AND action = :action AND article_id = :id order by id desc", array(
                    ':formuid' => CLIENT_IP,
                    ':action' => 'img',
                    ':id' => $_GPC['ggid']
                ));
                $gg       = pdo_fetch("SELECT * FROM " . tablename($this->tb_article_gg) . " WHERE uniacid = :uniacid AND id = :id", array(
                    ':uniacid' => $_W['uniacid'],
                    ':id' => $_GPC['ggid']
                ));
                if ($user_img['sharenum'] >= 1 && date('Y-m-d', $user_img['time']) == date('Y-m-d', time())) {
                } else {
                    $creditval = array(
                        '1' => '图片广告点击扣除'
                    );
                    $data      = array(
                        'uniacid' => $_W['uniacid'],
                        'openid' => $_W['fans']['openid'],
                        'uid' => $_W['fans']['uid'],
                        'article_id' => $_GPC['ggid'],
                        'nickname' => $_W['fans']['nickname'],
                        'title' => $article['title'],
                        'member_uid' => $gg['uid'],
                        'formuid' => CLIENT_IP,
                        'action' => 'img',
                        'credit_value' => $gg['jiage'],
                        'sharenum' => 1,
                        'time' => TIMESTAMP
                    );
                    pdo_insert($this->tb_article_share, $data);
                    pdo_update($this->tb_article_gg, array(
                        'zongjia' => $gg['zongjia'] - $data['credit_value']
                    ), array(
                        'id' => $gg['id']
                    ));
                    $zongjia   = $gg['zongjia'] - $data['credit_value'];
                    $creditval = array(
                        '1' => '广告点击扣除金额'
                    );
                    mc_credit_update($gg['uid'], 'credit2', -$data['credit_value'], $creditval);
                    $kdata = array(
                        'first' => array(
                            'value' => '您的图片广告被点击',
                            'color' => '#ff510'
                        ),
                        'keyword1' => array(
                            'value' => $_W['uniaccount']['name'],
                            'color' => '#ff510'
                        ),
                        'keyword2' => array(
                            'value' => '您的余额被扣除' . $data['credit_value'] . '元，当前余额：' . $zongjia . '元',
                            'color' => '#ff510'
                        ),
                        'remark' => array(
                            'value' => '点击查看',
                            'color' => '#ff510'
                        )
                    );
                    $url   = $_W['siteroot'] . 'app' . ltrim(url('mc/bond/credits', array(
                        'credittype' => $behavior['currency']
                    )), '.');
                    $acc->sendTplNotice($gg['openid'], $settings['templateid'], $kdata, $url, $topcolor = '#FF683F');
                    exit;
                }
            }
            if (!empty($article) && $credit['status'] == 1) {
                if ($_GPC['action'] == 'share') {
                    if ($user_total['sharenum'] >= 1 && date('Y-m-d', $user_total['time']) == date('Y-m-d', time())) {
                    } else {
                        $touid          = $_W['fans']['uid'];
                        $formuid        = -1;
                        $credit_value   = $credit['share'];
                        $creditval      = array(
                            '1' => '分享文章赠送奖励'
                        );
                        $data           = array(
                            'uniacid' => $_W['uniacid'],
                            'openid' => $_W['fans']['openid'],
                            'uid' => $touid,
                            'article_id' => $id,
                            'nickname' => $_W['fans']['nickname'],
                            'title' => $article['title'],
                            'member_uid' => $uid,
                            'action' => 'share',
                            'credit_value' => $credit['share'],
                            'sharenum' => 1,
                            'time' => TIMESTAMP
                        );
                        $data['amount'] = $fxjifen + $credit['share'];
                        $openid         = mc_fansinfo($touid, $_W['acid'], $_W['uniacid']);
                        $cdata          = array(
                            'first' => array(
                                'value' => '分享文章成功',
                                'color' => '#ff510'
                            ),
                            'keyword1' => array(
                                'value' => $_W['uniaccount']['name'],
                                'color' => '#ff510'
                            ),
                            'keyword2' => array(
                                'value' => '您本次分享获得' . $credit_value . '奖励',
                                'color' => '#ff510'
                            ),
                            'remark' => array(
                                'value' => '请进入会员中心点击查看余额',
                                'color' => '#ff510'
                            )
                        );
                        $url            = $_W['siteroot'] . 'app' . ltrim(murl('entry', array(
                            'do' => 'member',
                            'm' => 'cyl_wxweizhang',
                            'uid' => $uid
                        )), '.');
                        $acc->sendTplNotice($openid['openid'], $settings['templateid'], $cdata, $url, $topcolor = '#FF683F');
                    }
                } elseif ($_GPC['action'] == 'click') {
                    if ($user_click['sharenum'] >= 1 && date('Y-m-d', $user_click['time']) == date('Y-m-d', time())) {
                    } else {
                        $touid          = intval($_GPC['u']);
                        $formuid        = CLIENT_IP;
                        $credit_value   = $credit['click'];
                        $creditval      = array(
                            '1' => '分享的文章被阅读赠送奖励'
                        );
                        $data           = array(
                            'uniacid' => $_W['uniacid'],
                            'openid' => $_W['fans']['openid'],
                            'uid' => $touid,
                            'article_id' => $id,
                            'nickname' => $_W['fans']['nickname'],
                            'title' => $article['title'],
                            'member_uid' => $uid,
                            'formuid' => $formuid,
                            'action' => 'click',
                            'credit_value' => $credit['click'],
                            'sharenum' => 1,
                            'time' => TIMESTAMP
                        );
                        $data['amount'] = $fxjifen + $credit_value;
                        $openid         = mc_fansinfo($touid, $_W['acid'], $_W['uniacid']);
                        $cdata          = array(
                            'first' => array(
                                'value' => '分享的文章被阅读',
                                'color' => '#ff510'
                            ),
                            'keyword1' => array(
                                'value' => $_W['uniaccount']['name'],
                                'color' => '#ff510'
                            ),
                            'keyword2' => array(
                                'value' => '您本次获得' . $credit['click'] . '奖励',
                                'color' => '#ff510'
                            ),
                            'remark' => array(
                                'value' => '请进入会员中心点击查看余额',
                                'color' => '#ff510'
                            )
                        );
                        $url            = $_W['siteroot'] . 'app' . ltrim(murl('entry', array(
                            'do' => 'member',
                            'm' => 'cyl_wxweizhang',
                            'uid' => $uid
                        )), '.');
                        $acc->sendTplNotice($openid['openid'], $settings['templateid'], $cdata, $url, $topcolor = '#FF683F');
                    }
                }
                if (!empty($id)) {
                    $item           = pdo_fetch("SELECT * FROM " . tablename($this->tb_article) . " WHERE id = :id", array(
                        ':id' => $id
                    ));
                    $item['credit'] = iunserializer($item['credit']) ? iunserializer($item['credit']) : array();
                    if (!empty($item['credit']['limit'])) {
                        $credit_num = pdo_fetchcolumn('SELECT SUM(credit_value) FROM ' . tablename('cyl_wxwenzhang_article_share') . ' WHERE uniacid = :uniacid AND article_id = :article_id', array(
                            ':uniacid' => $_W['uniacid'],
                            ':article_id' => $id
                        ));
                        if (is_null($credit_num))
                            $credit_num = 0;
                        $credit_yu = (($item['credit']['limit'] - $credit_num) < 0) ? 0 : $item['credit']['limit'] - $credit_num;
                    }
                } else {
                    $item['credit'] = array();
                }
                if ($credit_yu <= 0.1) {
                    $openid = mc_fansinfo($uid, $_W['acid'], $_W['uniacid']);
                    $kdata  = array(
                        'first' => array(
                            'value' => '您的文章' . $article['title'] . '余额不足，请修改余额',
                            'color' => '#ff510'
                        ),
                        'keyword1' => array(
                            'value' => $_W['uniaccount']['name'],
                            'color' => '#ff510'
                        ),
                        'keyword2' => array(
                            'value' => '请进入文章页重新设置赠送余额上限',
                            'color' => '#ff510'
                        ),
                        'remark' => array(
                            'value' => '点击查看',
                            'color' => '#ff510'
                        )
                    );
                    $url    = $_W['siteroot'] . 'app' . ltrim(murl('entry', array(
                        'do' => 'member',
                        'm' => 'cyl_wxweizhang',
                        'uid' => $uid
                    )), '.');
                    $acc->sendTplNotice($openid['openid'], $settings['templateid'], $kdata, $url, $topcolor = '#FF683F');
                } else {
                    pdo_insert($this->tb_article_share, $data);
                    if ($article['jifen'] == 1) {
                        mc_credit_update($touid, 'credit2', $credit_value, $creditval);
                    } else {
                        mc_credit_update($touid, 'credit1', $credit_value, $creditval);
                    }
                    if ($_GPC['action'] == 'click') {
                        $creditval = array(
                            '1' => '分享的文章阅读,扣除余额'
                        );
                    } else {
                        $creditval = array(
                            '1' => '文章被分享,扣除余额'
                        );
                    }
                    mc_credit_update($data['member_uid'], 'credit2', -$credit_value, $creditval);
                    $openid    = mc_fansinfo($data['member_uid'], $_W['acid'], $_W['uniacid']);
                    $credit_yu = $credit_yu - $credit_value;
                    if ($_GPC['action'] == 'click') {
                        $value = '您的文章' . $data['title'] . '被人阅读了';
                    } else {
                        $value = '您的文章' . $data['title'] . '被人分享了';
                    }
                    $kdata = array(
                        'first' => array(
                            'value' => $value,
                            'color' => '#ff510'
                        ),
                        'keyword1' => array(
                            'value' => $_W['uniaccount']['name'],
                            'color' => '#ff510'
                        ),
                        'keyword2' => array(
                            'value' => '您的余额被扣除' . $credit_value . '元，当前余额：' . $credit_yu . '元',
                            'color' => '#ff510'
                        ),
                        'remark' => array(
                            'value' => '点击查看',
                            'color' => '#ff510'
                        )
                    );
                    $url   = $_W['siteroot'] . 'app' . ltrim(murl('entry', array(
                        'do' => 'member',
                        'm' => 'cyl_wxweizhang',
                        'uid' => $uid
                    )), '.');
                    $acc->sendTplNotice($openid['openid'], $settings['templateid'], $kdata, $url, $topcolor = '#FF683F');
                }
                if (is_error($status)) {
                    exit(json_encode($status));
                } else {
                    exit('success');
                }
            } else {
                exit(json_encode(array(
                    -1,
                    '文章没有设置赠送积分'
                )));
            }
        }
    }
    public function doMobilePay()
    {
        global $_W, $_GPC;
        load()->model('account');
        $id  = $_GPC['id'];
        $fee = $_GPC['fee'];
        $uid = $_GPC['uid'];
        if (empty($_W['fans']['openid'])) {
            mc_oauth_userinfo();
        }
        if (checksubmit()) {
            $fee = $_GPC['fee'];
            $id  = $_GPC['id'];
            $uid = $_GPC['uid'];
        }
        if ($fee <= 0) {
            message('支付错误, 金额小于0');
        }
        $contents = pdo_fetch("SELECT * FROM " . tablename($this->tb_article) . " WHERE id = :id", array(
            ':id' => $id
        ));
        $title    = $contents['title'];
        $params   = array(
            'module' => 'cyl_wxweizhang',
            'tid' => date('YmdHi') . random(8, 1),
            'ordersn' => date(YmdHis) . $id . $_W['member']['uid'],
            'title' => $title . "赏金",
            'fee' => $fee,
            'user' => $_W['member']['uid']
        );
        $data     = array(
            'uniacid' => $_W['uniacid'],
            'article_id' => $id,
            'tid' => $params['tid'],
            'uid' => $_W['member']['uid'],
            'memberuid' => $uid,
            'openid' => $_W['fans']['openid'],
            'nickname' => $_W['fans']['nickname'],
            'avatar' => $_W['fans']['tag']['avatar'],
            'status' => 0,
            'time' => TIMESTAMP
        );
        pdo_insert($this->tb_shang, $data);
        $this->pay($params);
    }
    public function payResult($params)
    {
        global $_W, $_GPC;
        load()->model('mc');
        load()->func('tpl');
        $order    = pdo_fetch("SELECT * FROM " . tablename($this->tb_shang) . " WHERE tid = :tid", array(
            ':tid' => $params['tid']
        ));
        $article  = $this->getAllArticle();
        $data     = array(
            'fee' => $params['fee'],
            'status' => 1
        );
        $settings = $this->module['config'];
        if ($param['result'] == 'success' && ($param['from'] == 'notify' || $param['from'] == 'return')) {
        }
        if (empty($params['result']) || $params['result'] != 'success') {
        }
        if ($params['from'] == 'return') {
            if ($params['result'] == 'success') {
                pdo_update($this->tb_shang, $data, array(
                    'tid' => $order['tid']
                ));
                mc_credit_update($order['memberuid'], 'credit2', $params['fee'], array(
                    '1' => '文章打赏'
                ));
                load()->classs('weixin.account');
                load()->func('communication');
                $acc    = WeAccount::create($acid);
                $openid = mc_fansinfo($order['memberuid'], $_W['acid'], $_W['uniacid']);
                mc_credit_update($order['uid'], 'credit2', -$params['fee'], array(
                    '1' => '文章打赏'
                ));
                $kdata = array(
                    'first' => array(
                        'value' => '有人给您赞赏了',
                        'color' => '#ff510'
                    ),
                    'keyword1' => array(
                        'value' => $_W['uniaccount']['name'],
                        'color' => '#ff510'
                    ),
                    'keyword2' => array(
                        'value' => '有人打赏,获得奖励，进入会员中心查看',
                        'color' => '#ff510'
                    ),
                    'remark' => array(
                        'value' => '点击查看',
                        'color' => '#ff510'
                    )
                );
                $url   = $_W['siteroot'] . 'app' . ltrim(murl('entry', array(
                    'do' => 'detail',
                    'm' => 'cyl_wxweizhang',
                    'id' => $order['article_id'],
                    'op' => 'detail'
                )), '.');
                $acc->sendTplNotice($openid['openid'], $settings['templateid'], $kdata, $url, $topcolor = '#FF683F');
                message('感谢您的赏金！', $this->createMobileUrl('detail', array(
                    'id' => $order['article_id'],
                    'op' => 'detail'
                )), 'success');
            } else {
                message('支付失败！', 'error');
            }
        }
    }
    protected function pay($params = array())
    {
        global $_W;
        $setting = uni_setting($_W['uniacid']);
        if (!is_array($setting['payment'])) {
            message('没有有效的支付方式, 请联系网站管理员.');
        }
        $pay                       = $setting['payment'];
        $credtis                   = mc_credit_fetch($_W['fans']['uid']);
        $pay['delivery']['switch'] = false;
        include $this->template('common/paycenter');
    }
    public function doMobileMember()
    {
        global $_W, $_GPC;
        load()->model('mc');
        $credit     = mc_credit_fetch($_W['fans']['uid']);
        $settings   = $this->module['config'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $useragent  = addslashes($_SERVER['HTTP_USER_AGENT']);
        if (strpos($useragent, 'MicroMessenger') === false && strpos($useragent, 'Windows Phone') === false) {
            echo "请在微信打开";
        } else {
            if ($settings['weizhuan'] == 1) {
                $settings['articletougao'] = 0;
            }
            if (empty($_W['fans']['nickname'])) {
                $userinfo = mc_oauth_userinfo();
            }
            include $this->template('member');
        }
    }
    public function doMobileMembergg()
    {
        global $_W, $_GPC;
        load()->func('tpl');
        load()->model('mc');
        load()->classs('weixin.account');
        load()->func('communication');
        $credit   = mc_credit_fetch($_W['fans']['uid']);
        $acc      = WeAccount::create($acid);
        $settings = $this->module['config'];
        $category = pdo_fetchall("SELECT id,parentid,name FROM " . tablename($this->tb_category) . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder ASC, id ASC ", array(), 'id');
        $ops      = array(
            'tw',
            'tp',
            'delete',
            'post'
        );
        $op       = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
        $article  = $this->getAllArticle();
        if ($op == 'tw') {
            $list = pdo_fetchall("SELECT sourcelink FROM " . tablename($this->tb_article) . " WHERE uniacid = {$_W['uniacid']}");
            foreach ($list as $key => $val) {
                foreach ($val as $value) {
                    $new_arr[] = $value;
                }
            }
            if (checksubmit('submit')) {
                $url   = $_GPC['wxurl'];
                $pcate = $_GPC['pcate'];
                if (empty($pcate)) {
                    message('请选择分类');
                }
                if (!in_array($url, $new_arr)) {
                    $config = get_caiji($url);
                }
                $data             = array(
                    'uniacid' => $_W['uniacid'],
                    'title' => $config['title'],
                    'thumb' => 'http://img01.store.sogou.com/net/a/04/link?appid=100520031&w=600&url=' . $config['thumb'],
                    'description' => $config['desc'],
                    'source' => $_W['uniaccount']['name'],
                    'pcate' => intval($pcate),
                    'author' => $_W['fans']['nickname'],
                    'uid' => $_W['fans']['uid'],
                    'ccate' => intval($_GPC['category']['childid']),
                    'content' => htmlspecialchars_decode($config['contents']),
                    'createtime' => TIMESTAMP,
                    'sourcelink' => $url,
                    'click' => intval($_GPC['click'])
                );
                $data['pic']      = iserializer(getImgs($config['contents']));
                $credit['status'] = 1;
                $credit['limit']  = $_GPC['credit']['limit'] ? $_GPC['credit']['limit'] : message('请设置余额上限');
                $credit['share']  = $_GPC['credit']['share'] ? $_GPC['credit']['share'] : message('请设置分享时赠余额多少');
                $credit['click']  = $_GPC['credit']['click'] ? $_GPC['credit']['click'] : message('请设置阅读时赠送余额多少');
                $data['credit']   = iserializer($credit);
                if ($credit['share'] < 0.01) {
                    message('不能低于0.01元', '', 'error');
                }
                if ($credit['click'] < 0.01) {
                    message('不能低于0.01元', '', 'error');
                }
                if ($credit['limit'] > $credit['credit2']) {
                    message('您当前余额不足，请进行充值！', url('entry', array(
                        'm' => 'recharge',
                        'do' => 'pay'
                    )), 'error');
                }
                if ($settings['articlestatus'] == 1) {
                    $data['status'] = 2;
                }
                $data['zongjia'] = $credit['limit'];
                $data['jiage']   = $credit['share'];
                if (!in_array($url, $new_arr)) {
                    pdo_insert($this->tb_article, $data);
                }
                $kdata = array(
                    'first' => array(
                        'value' => '有粉丝提交了图文广告',
                        'color' => '#ff510'
                    ),
                    'keyword1' => array(
                        'value' => $_W['uniaccount']['name'],
                        'color' => '#ff510'
                    ),
                    'keyword2' => array(
                        'value' => $data['description'],
                        'color' => '#ff510'
                    ),
                    'remark' => array(
                        'value' => '请进入后台审核',
                        'color' => '#ff510'
                    )
                );
                $acc->sendTplNotice($settings['kfid'], $settings['templateid'], $kdata, $topcolor = '#FF683F');
                message('文章添加成功！', $this->createMobileUrl('member'), 'success');
            }
        }
        if ($op == 'tp') {
            if (checksubmit('submit')) {
                $url  = $_GPC['link'];
                $data = array(
                    'uniacid' => $_W['uniacid'],
                    'title' => $_GPC['title'],
                    'thumb' => $_GPC['thumb'],
                    'uid' => $_W['fans']['uid'],
                    'nickname' => $_W['fans']['nickname'],
                    'openid' => $_W['fans']['openid'],
                    'time' => TIMESTAMP,
                    'zongjia' => $_GPC['zongjia'],
                    'jiage' => $_GPC['jiage'],
                    'link' => $_GPC['link']
                );
                if ($data['zongjia'] >= $credit['credit2']) {
                    message('您当前余额不足，请进行充值！', url('entry', array(
                        'm' => 'recharge',
                        'do' => 'pay'
                    )), 'error');
                }
                if ($data['jiage'] < 0.01) {
                    message('不能低于0.01元', '', 'error');
                }
                if ($settings['articlestatus'] == 1) {
                    $data['status'] = 2;
                } else {
                    $data['status'] = 1;
                }
                pdo_insert($this->tb_article_gg, $data);
                $kdata = array(
                    'first' => array(
                        'value' => '有粉丝提交了图片广告',
                        'color' => '#ff510'
                    ),
                    'keyword1' => array(
                        'value' => $_W['uniaccount']['name'],
                        'color' => '#ff510'
                    ),
                    'keyword2' => array(
                        'value' => '总价为' . $data['zongjia'] . '元 点击价格为' . $data['jiage'] . '元',
                        'color' => '#ff510'
                    ),
                    'remark' => array(
                        'value' => '请进入后台审核',
                        'color' => '#ff510'
                    )
                );
                $acc->sendTplNotice($settings['kfid'], $settings['templateid'], $kdata, $topcolor = '#FF683F');
                message('添加成功！', $this->createMobileUrl('member'), 'success');
            }
        }
        include $this->template('membergg');
    }
    public function doMobileTixian()
    {
        global $_W, $_GPC;
        $settings = $this->module['config'];
        load()->model('mc');
        $uid      = $_GPC['uid'];
        $settings = $this->module['config'];
        $fromUser = $_W['fans']['openid'];
        $credit   = pdo_fetchcolumn("SELECT max(amount) FROM " . tablename('cyl_wxwenzhang_article_share') . " WHERE uniacid = {$_W['uniacid']} AND uid={$_W['fans']['uid']}");
        load()->classs('weixin.account');
        load()->func('communication');
        $acc  = WeAccount::create($acid);
        $user = pdo_get('cyl_wxwenzhang_tixian', array(
            'uid' => $_W['fans']['uid']
        ), array(
            'title',
            'wxh'
        ));
        if (checksubmit('submit')) {
            $data = array(
                'uniacid' => $_W['uniacid'],
                'title' => $_GPC['title'],
                'wxh' => $_GPC['wxh'],
                'nickname' => $_W['fans']['nickname'],
                'openid' => $_W['fans']['openid'],
                'amount' => $_GPC['amount'],
                'uid' => $_W['fans']['uid'],
                'createtime' => TIMESTAMP
            );
            if ($credit < $settings['tixian']) {
                message('您的余额不足' . $settings['tixian'] . '元，无法提现', '', 'error');
            }
            if ($credit < $data['amount']) {
                message('您的余额不足无法提现', '', 'error');
            }
            if ($data['amount'] < $settings['tixian']) {
                message('提现金额不足' . $settings['tixian'] . '元，无法提现', '', 'error');
            }
            if ($settings['tixianstatus'] == 1) {
                mc_credit_update($_W['fans']['uid'], 'credit2', -$data['amount'], array(
                    '1' => "提现扣除"
                ));
                pdo_insert('cyl_wxwenzhang_tixian', $data);
                $num = $credit - $data['amount'];
                pdo_update('cyl_wxwenzhang_article_share', array(
                    'amount' => $num
                ), array(
                    'uid' => $_W['fans']['uid']
                ));
                message('提现申请成功，请等待管理员审核', $this->createMobileUrl('member'), 'success');
            } else {
                $amount        = $data['amount'] * 100;
                $arr['openid'] = $_W['openid'];
                $arr['hbname'] = '余额提现';
                $arr['body']   = "余额提现";
                $arr['fee']    = $amount;
                $res           = $this->sendhongbaoto($arr);
                mc_credit_update($_W['fans']['uid'], 'credit2', -$data['amount'], array(
                    '1' => "提现扣除"
                ));
                if ($res['result_code'] == 'SUCCESS') {
                    $kdata = array(
                        'first' => array(
                            'value' => '提现成功',
                            'color' => '#ff510'
                        ),
                        'keyword1' => array(
                            'value' => $_W['uniaccount']['name'],
                            'color' => '#ff510'
                        ),
                        'keyword2' => array(
                            'value' => '提现金额为' . $data['amount'],
                            'color' => '#ff510'
                        ),
                        'remark' => array(
                            'value' => '请进入微信领取查看',
                            'color' => '#ff510'
                        )
                    );
                    $url   = $_W['siteroot'] . 'app' . ltrim(murl('entry', array(
                        'do' => 'member',
                        'm' => 'cyl_wxweizhang',
                        'uid' => $uid
                    )), '.');
                    $acc->sendTplNotice($data['openid'], $settings['templateid'], $kdata, $url, $topcolor = '#FF683F');
                    pdo_insert('cyl_wxwenzhang_tixian', $data);
                    $num = $credit - $data['amount'];
                    pdo_update('cyl_wxwenzhang_article_share', array(
                        'amount' => $num
                    ), array(
                        'uid' => $_W['fans']['uid']
                    ));
                    message('提现成功请查看微信零钱', $this->createMobileUrl('member'), 'success');
                } else {
                    $msg = $res['return_msg'];
                    message('提现失败请联系管理员处理', $this->createMobileUrl('tixian'), 'error');
                }
            }
        }
        include $this->template('tixian');
    }
    public function doMobileMemberfabu()
    {
        global $_W, $_GPC;
        $settings = $this->module['config'];
        $category = pdo_fetchall("SELECT id,parentid,name FROM " . tablename($this->tb_category) . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder ASC, id ASC ", array(), 'id');
        $list     = pdo_fetchall("SELECT sourcelink FROM " . tablename($this->tb_article) . " WHERE uniacid = {$_W['uniacid']}");
        foreach ($list as $key => $val) {
            foreach ($val as $value) {
                $new_arr[] = $value;
            }
        }
        if (checksubmit('submit')) {
            $url   = $_GPC['wxurl'];
            $pcate = $_GPC['pcate'];
            if (empty($pcate)) {
                message('请选择分类');
            }
            if (!in_array($url, $new_arr)) {
                $config = get_caiji($url);
            }
            $data        = array(
                'uniacid' => $_W['uniacid'],
                'title' => $config['title'],
                'thumb' => 'http://img01.store.sogou.com/net/a/04/link?appid=100520031&w=600&url=' . $config['thumb'],
                'description' => $config['desc'],
                'source' => $_W['uniaccount']['name'],
                'pcate' => intval($pcate),
                'author' => $_W['fans']['nickname'],
                'uid' => $_W['fans']['uid'],
                'ccate' => intval($_GPC['category']['childid']),
                'content' => htmlspecialchars_decode($config['contents']),
                'createtime' => TIMESTAMP,
                'sourcelink' => $url,
                'click' => intval($_GPC['click'])
            );
            $data['pic'] = iserializer(getImgs($config['contents']));
            if ($settings['articlestatus'] == 1) {
                $data['status'] = 2;
            }
            if (!in_array($url, $new_arr)) {
                pdo_insert($this->tb_article, $data);
            }
            message('文章添加成功！', $this->createMobileUrl('member'), 'success');
        }
        include $this->template('fabu');
    }
    public function doMobileMemberfabumanage()
    {
        global $_W, $_GPC;
        $uid     = $_W['member']['uid'];
        $article = pdo_fetchall('SELECT id,thumb,title,ly,createtime FROM ' . tablename($this->tb_article) . ' WHERE uniacid = :uniacid AND uid = :uid', array(
            ':uniacid' => $_W['uniacid'],
            ':uid' => $uid
        ));
        include $this->template('fabumanage');
    }
    public function doWebShang()
    {
        global $_W, $_GPC;
        $pindex          = max(1, intval($_GPC['page']));
        $psize           = 30;
        $article         = $this->getAllArticle();
        $params          = array(
            ':uniacid' => $_W['uniacid']
        );
        $shang_total     = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this->tb_shang) . ' WHERE uniacid = :uniacid  AND status=1', array(
            ':uniacid' => $_W['uniacid']
        ));
        $shang_total_fee = pdo_fetchcolumn("SELECT SUM(fee) FROM " . tablename($this->tb_shang) . ' WHERE uniacid = :uniacid  AND status=1', array(
            ':uniacid' => $_W['uniacid']
        ));
        $pager           = pagination($shang_total, $pindex, $psize);
        $shang           = pdo_fetchall("SELECT * FROM " . tablename($this->tb_shang) . " WHERE uniacid = '{$_W['uniacid']}' AND status=1 ORDER BY fee DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
        include $this->template('shang');
    }
    public function doMobileGg()
    {
        global $_W, $_GPC;
        load()->func('tpl');
        $ops = array(
            'display',
            'post',
            'delete'
        );
        $op  = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
        if ($op == 'display') {
            $article = $this->getAllArticle();
            $params  = array(
                ':uniacid' => $_W['uniacid']
            );
            $shang   = pdo_fetchall("SELECT * FROM " . tablename($this->tb_article_gg) . " WHERE uniacid = '{$_W['uniacid']}' AND status=1 AND uid={$_W['fans']['uid']} ORDER BY id DESC ", $params);
            include $this->template('gg');
        } elseif ($op == 'post') {
            $id = intval($_GPC['id']);
            load()->model('mc');
            if (!empty($id)) {
                $item = pdo_fetch("SELECT * FROM " . tablename($this->tb_article_gg) . " WHERE id = :id", array(
                    ':id' => $id
                ));
                if (empty($item)) {
                    message('抱歉，数据不存在或是已经删除！！', '', 'error');
                }
            } else {
                $item = array(
                    'time' => TIMESTAMP
                );
            }
            if (checksubmit('submit')) {
                $url    = $_GPC['link'];
                $data   = array(
                    'uniacid' => $_W['uniacid'],
                    'title' => $_GPC['title'],
                    'thumb' => $_GPC['thumb'],
                    'uid' => $_W['fans']['uid'],
                    'nickname' => $_W['fans']['nickname'],
                    'openid' => $_W['fans']['openid'],
                    'time' => TIMESTAMP,
                    'zongjia' => $_GPC['zongjia'],
                    'jiage' => $_GPC['jiage'],
                    'link' => $_GPC['link']
                );
                $credit = mc_credit_fetch($_W['fans']['uid']);
                if ($credit['credit2'] < $data['zongjia']) {
                    message('您的余额不足，请充值', '', 'error');
                }
                if (empty($id)) {
                    pdo_insert($this->tb_article_gg, $data);
                } else {
                    unset($data['time']);
                    pdo_update($this->tb_article_gg, $data, array(
                        'id' => $id
                    ));
                }
                message('添加成功，请等待审核！', $this->createMobileUrl('gg', array(
                    'op' => 'display'
                )), 'success');
            }
            include $this->template('gg');
        } elseif ($op == 'delete') {
            $id       = intval($_GPC['id']);
            $contents = pdo_fetch("SELECT * FROM " . tablename($this->tb_article_gg) . " WHERE id = :id", array(
                ':id' => $id
            ));
            pdo_delete($this->tb_article_gg, array(
                'id' => $id
            ));
            message('删除成功！', $this->createWebUrl('gg'), 'success');
        }
    }
    public function doWebGg()
    {
        global $_W, $_GPC;
        load()->func('tpl');
        $ops = array(
            'display',
            'post',
            'delete'
        );
        $op  = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
        if ($op == 'display') {
            $pindex      = max(1, intval($_GPC['page']));
            $psize       = 30;
            $article     = $this->getAllArticle();
            $params      = array(
                ':uniacid' => $_W['uniacid']
            );
            $shang_total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this->tb_article_gg) . ' WHERE uniacid = :uniacid  AND status=1', array(
                ':uniacid' => $_W['uniacid']
            ));
            $pager       = pagination($shang_total, $pindex, $psize);
            $shang       = pdo_fetchall("SELECT * FROM " . tablename($this->tb_article_gg) . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
            include $this->template('gg');
        } elseif ($op == 'post') {
            $id = intval($_GPC['id']);
            load()->model('mc');
            if (!empty($id)) {
                $item = pdo_fetch("SELECT * FROM " . tablename($this->tb_article_gg) . " WHERE id = :id", array(
                    ':id' => $id
                ));
                if (empty($item)) {
                    message('抱歉，数据不存在或是已经删除！！', '', 'error');
                }
            } else {
                $item = array(
                    'time' => TIMESTAMP
                );
            }
            if (checksubmit('submit')) {
                $uid      = mc_openid2uid($_GPC['openid']);
                $nickname = mc_fansinfo($uid, $_W['acid'], $_W['uniacid']);
                $credit   = mc_credit_fetch($uid);
                $data     = array(
                    'uniacid' => intval($_W['uniacid']),
                    'uid' => $uid,
                    'title' => $_GPC['title'],
                    'openid' => $_GPC['openid'] ? $_GPC['openid'] : message('请设置粉丝编号'),
                    'thumb' => $_GPC['thumb'],
                    'nickname' => $nickname['nickname'],
                    'link' => $_GPC['link'],
                    'zongjia' => $_GPC['zongjia'],
                    'jiage' => $_GPC['jiage'],
                    'status' => $_GPC['status'],
                    'time' => TIMESTAMP
                );
                if ($credit['credit2'] < $data['zongjia']) {
                    message('粉丝的余额不足，请在后台充值', '', 'error');
                }
                if (empty($id)) {
                    pdo_insert($this->tb_article_gg, $data);
                } else {
                    unset($data['time']);
                    pdo_update($this->tb_article_gg, $data, array(
                        'id' => $id
                    ));
                }
                message('数据更新成功！', $this->createWebUrl('gg', array(
                    'op' => 'display'
                )), 'success');
            }
            include $this->template('gg');
        } elseif ($op == 'delete') {
            $id       = intval($_GPC['id']);
            $contents = pdo_fetch("SELECT * FROM " . tablename($this->tb_article_gg) . " WHERE id = :id", array(
                ':id' => $id
            ));
            pdo_delete($this->tb_article_gg, array(
                'id' => $id
            ));
            message('删除成功！', $this->createWebUrl('gg'), 'success');
        }
    }
    public function doWebMessage()
    {
        global $_W, $_GPC;
        $ops     = array(
            'display',
            'post',
            'delete'
        );
        $op      = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
        $article = $this->getAllArticle();
        if ($op == 'display') {
            $id     = $_GPC['id'];
            $pindex = max(1, intval($_GPC['page']));
            $psize  = 20;
            if (!empty($id)) {
                $condition .= " AND article_id = $id";
            }
            $sql     = 'SELECT * FROM ' . tablename($this->tb_message) . " WHERE uniacid=:uniacid $condition ORDER BY id desc ";
            $params  = array(
                ':uniacid' => $_W['uniacid']
            );
            $total   = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->tb_message) . " WHERE uniacid = '{$_W['uniacid']}' $condition");
            $pager   = pagination($total, $pindex, $psize);
            $message = pdo_fetchall($sql, $params, 'id');
            include $this->template('liuyan');
        } elseif ($op == 'delete') {
            $id         = intval($_GPC['id']);
            $article_id = intval($_GPC['article_id']);
            $contents   = pdo_fetch("SELECT * FROM " . tablename($this->tb_article) . " WHERE id = :id", array(
                ':id' => $article_id
            ));
            pdo_delete($this->tb_message, array(
                'id' => $id
            ), 'OR');
            $contents['ly'] = intval($contents['ly']) - 1;
            pdo_update($this->tb_article, array(
                'ly' => $contents['ly']
            ), array(
                'uniacid' => $_W['uniacid'],
                'id' => $article_id
            ));
            message('删除成功！', $this->createWebUrl('message', array(
                'id' => $article_id
            )), 'success');
        } elseif ($op == 'post') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $item = pdo_fetch("SELECT * FROM " . tablename($this->tb_message) . " WHERE id = :id", array(
                    ':id' => $id
                ));
                if (empty($item)) {
                    message('抱歉，数据不存在或是已经删除！！', '', 'error');
                }
            } else {
                $item = array(
                    'time' => TIMESTAMP
                );
            }
            if (checksubmit('submit')) {
                $data = array(
                    'uniacid' => intval($_W['uniacid']),
                    'nickname' => trim($_GPC['nickname']),
                    'content' => $_GPC['content'],
                    'status' => $_GPC['status'],
                    'huifu' => $_GPC['huifu'],
                    'time' => TIMESTAMP
                );
                if (empty($id)) {
                    pdo_insert($this->tb_message, $data);
                } else {
                    unset($data['time']);
                    pdo_update($this->tb_message, $data, array(
                        'id' => $id
                    ));
                }
                message('数据更新成功！', $this->createWebUrl('message', array(
                    'op' => 'display'
                )), 'success');
            }
            include $this->template('liuyan');
        }
    }
    public function doWebCategory()
    {
        global $_W, $_GPC;
        $typeid       = $this->typeId();
        $ops          = array(
            'display',
            'post',
            'delete',
            'fetch',
            'check'
        );
        $op           = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
        $setting      = uni_setting($_W['uniacid'], 'default_site');
        $default_site = intval($setting['default_site']);
        if ($op == 'display') {
            if (!empty($_GPC['displayorder'])) {
                foreach ($_GPC['displayorder'] as $id => $displayorder) {
                    $update = array(
                        'displayorder' => $displayorder
                    );
                    pdo_update($this->tb_category, $update, array(
                        'id' => $id
                    ));
                }
                message('分类排序更新成功！', 'refresh', 'success');
            }
            $children = array();
            $category = pdo_fetchall("SELECT * FROM " . tablename($this->tb_category) . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY parentid, displayorder DESC, id");
            foreach ($category as $index => $row) {
                if (!empty($row['parentid'])) {
                    $children[$row['parentid']][] = $row;
                    unset($category[$index]);
                }
            }
            include $this->template('category');
        } elseif ($op == 'post') {
            $parentid = intval($_GPC['parentid']);
            $id       = intval($_GPC['id']);
            $setting  = uni_setting($_W['uniacid'], array(
                'default_site'
            ));
            if ($site_styleid) {
                $site_template = pdo_fetch("SELECT a.*,b.name,b.sections FROM " . tablename($this->tb_styles) . ' AS a LEFT JOIN ' . tablename($this->tb_templates) . ' AS b ON a.templateid = b.id WHERE a.uniacid = :uniacid AND a.id = :id', array(
                    ':uniacid' => $_W['uniacid'],
                    ':id' => $site_styleid
                ));
            }
            $styles = pdo_fetchall("SELECT a.*, b.name AS tname, b.title FROM " . tablename($this->tb_styles) . ' AS a LEFT JOIN ' . tablename($this->tb_templates) . ' AS b ON a.templateid = b.id WHERE a.uniacid = :uniacid', array(
                ':uniacid' => $_W['uniacid']
            ), 'id');
            if (!empty($id)) {
                $category = pdo_fetch("SELECT * FROM " . tablename($this->tb_category) . " WHERE id = '$id' AND uniacid = {$_W['uniacid']}");
                if (empty($category)) {
                    message('分类不存在或已删除', '', 'error');
                }
                if (!empty($category['css'])) {
                    $category['css'] = iunserializer($category['css']);
                } else {
                    $category['css'] = array();
                }
            } else {
                $category = array(
                    'displayorder' => 0,
                    'css' => array()
                );
            }
            if (!empty($parentid)) {
                $parent = pdo_fetch("SELECT id, name FROM " . tablename($this->tb_category) . " WHERE id = '$parentid'");
                if (empty($parent)) {
                    message('抱歉，上级分类不存在或是已经被删除！', $this->createWebUrl('category'), 'error');
                }
            }
            $category['style']          = $styles[$category['styleid']];
            $category['style']['tname'] = empty($category['style']['tname']) ? 'default' : $category['style']['tname'];
            if (checksubmit('submit')) {
                if (empty($_GPC['cname'])) {
                    message('抱歉，请输入分类名称！');
                }
                $data             = array(
                    'uniacid' => $_W['uniacid'],
                    'name' => $_GPC['cname'],
                    'displayorder' => intval($_GPC['displayorder']),
                    'parentid' => intval($parentid),
                    'description' => $_GPC['description'],
                    'styleid' => intval($_GPC['styleid']),
                    'linkurl' => $_GPC['linkurl'],
                    'ishomepage' => intval($_GPC['ishomepage'])
                );
                $data['icontype'] = intval($_GPC['icontype']);
                if ($data['icontype'] == 1) {
                    $data['icon'] = '';
                    $data['css']  = serialize(array(
                        'icon' => array(
                            'font-size' => $_GPC['icon']['size'],
                            'color' => $_GPC['icon']['color'],
                            'width' => $_GPC['icon']['size'],
                            'icon' => empty($_GPC['icon']['icon']) ? 'fa fa-external-link' : $_GPC['icon']['icon']
                        )
                    ));
                } else {
                    $data['css']  = '';
                    $data['icon'] = $_GPC['iconfile'];
                }
                if (!empty($id)) {
                    unset($data['parentid']);
                    pdo_update($this->tb_category, $data, array(
                        'id' => $id
                    ));
                } else {
                    pdo_insert($this->tb_category, $data);
                    $id = pdo_insertid();
                }
                message('更新分类成功！', $this->createWebUrl('category'), 'success');
            }
            include $this->template('category');
        } elseif ($op == 'fetch') {
            $category = pdo_fetchall("SELECT id, name FROM " . tablename($this->tb_category) . " WHERE parentid = '" . intval($_GPC['parentid']) . "' ORDER BY id ASC, displayorder ASC, id ASC ");
            message($category, '', 'ajax');
        } elseif ($op == 'delete') {
            load()->func('file');
            $id       = intval($_GPC['id']);
            $category = pdo_fetch("SELECT id, parentid, nid FROM " . tablename($this->tb_category) . " WHERE id = '$id'");
            if (empty($category)) {
                message('抱歉，分类不存在或是已经被删除！', $this->createWebUrl('category'), 'error');
            }
            pdo_delete($this->tb_category, array(
                'id' => $id,
                'parentid' => $id
            ), 'OR');
            message('分类删除成功！', $this->createWebUrl('category'), 'success');
        } elseif ($op == 'check') {
            $styleid = intval($_GPC['styleid']);
            if ($styleid > 0) {
                $styles = pdo_fetch("SELECT a.*,b.name,b.sections FROM " . tablename('site_styles') . ' AS a LEFT JOIN ' . tablename('site_templates') . ' AS b ON a.templateid = b.id WHERE a.uniacid = :uniacid AND a.id = :id', array(
                    ':uniacid' => $_W['uniacid'],
                    ':id' => $styleid
                ));
                if (empty($styles) || $styles['sections'] != 0) {
                    exit('error');
                } else {
                    exit('success');
                }
            }
            exit('error');
        }
    }
    public function message($error, $url = '', $errno = -1)
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
    public function doWebArticle()
    {
        global $_W, $_GPC;
        load()->model('mc');
        $ops       = array(
            'display',
            'post',
            'delete',
            'caiji',
            'handsel',
            'deleteall',
            'checkall'
        );
        $op        = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
        $settings  = $this->module['config'];
        $openiduid = mc_openid2uid($settings['kfid']);
        $category  = pdo_fetchall("SELECT id,parentid,name FROM " . tablename($this->tb_category) . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder ASC, id ASC ", array(), 'id');
        $parent    = array();
        $children  = array();
        if (!empty($category)) {
            $children = '';
            foreach ($category as $cid => $cate) {
                if (!empty($cate['parentid'])) {
                    $children[$cate['parentid']][] = $cate;
                } else {
                    $parent[$cate['id']] = $cate;
                }
            }
        }
        if ($op == 'display') {
            $pindex    = max(1, intval($_GPC['page']));
            $psize     = 20;
            $condition = '';
            $params    = array();
            if (!empty($_GPC['status'])) {
                $status = $_GPC['status'];
                $condition .= " AND status = $status";
            }
            if (!empty($_GPC['keyword'])) {
                $condition .= " AND title LIKE :keyword";
                $params[':keyword'] = "%{$_GPC['keyword']}%";
            }
            if (!empty($_GPC['pcate'])) {
                $pcate = $_GPC['pcate'];
                $condition .= " AND pcate = $pcate";
            }
            if (!empty($_GPC['ccate'])) {
                $ccate = $_GPC['ccate'];
                $condition .= " AND ccate = $ccate";
            }
            if (!empty($_GPC['category']['childid'])) {
                $cid = intval($_GPC['category']['childid']);
                $condition .= " AND ccate = '{$cid}'";
            } elseif (!empty($_GPC['category']['parentid'])) {
                $cid = intval($_GPC['category']['parentid']);
                $condition .= " AND pcate = '{$cid}'";
            }
            $list  = pdo_fetchall("SELECT * FROM " . tablename($this->tb_article) . " WHERE uniacid = '{$_W['uniacid']}' $condition ORDER BY displayorder DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->tb_article) . " WHERE uniacid = '{$_W['uniacid']}' $condition");
            $pager = pagination($total, $pindex, $psize);
            include $this->template('article');
        } elseif ($op == 'post') {
            load()->func('file');
            $id       = intval($_GPC['id']);
            $template = uni_templates();
            $pcate    = $_GPC['pcate'];
            $ccate    = $_GPC['ccate'];
            if (!empty($id)) {
                $item         = pdo_fetch("SELECT * FROM " . tablename($this->tb_article) . " WHERE id = :id", array(
                    ':id' => $id
                ));
                $item['type'] = explode(',', $item['type']);
                $pcate        = $item['pcate'];
                $ccate        = $item['ccate'];
                if (empty($item)) {
                    message('抱歉，文章不存在或是已经删除！', '', 'error');
                }
                $item['credit'] = iunserializer($item['credit']) ? iunserializer($item['credit']) : array();
                if (!empty($item['credit']['limit'])) {
                    $credit_num = pdo_fetchcolumn('SELECT SUM(credit_value) FROM ' . tablename('cyl_wxwenzhang_article_share') . ' WHERE uniacid = :uniacid AND article_id = :article_id', array(
                        ':uniacid' => $_W['uniacid'],
                        ':article_id' => $id
                    ));
                    if (is_null($credit_num))
                        $credit_num = 0;
                    $credit_yu = (($item['credit']['limit'] - $credit_num) < 0) ? 0 : $item['credit']['limit'] - $credit_num;
                }
            } else {
                $item['credit'] = array();
            }
            if (checksubmit('submit')) {
                if (empty($_GPC['title'])) {
                    message('标题不能为空，请输入标题！');
                }
                $data = array(
                    'uniacid' => $_W['uniacid'],
                    'iscommend' => intval($_GPC['option']['commend']),
                    'ishot' => intval($_GPC['option']['hot']),
                    'pcate' => intval($_GPC['category']['parentid']),
                    'ccate' => intval($_GPC['category']['childid']),
                    'uid' => $openiduid,
                    'template' => $_GPC['template'],
                    'title' => $_GPC['title'],
                    'status' => $_GPC['status'],
                    'description' => $_GPC['description'],
                    'content' => htmlspecialchars_decode($_GPC['content'], ENT_QUOTES),
                    'incontent' => intval($_GPC['incontent']),
                    'source' => $_GPC['source'],
                    'sharelink' => $_GPC['sharelink'],
                    'articlegg' => $_GPC['articlegg'],
                    'articlelink' => $_GPC['articlelink'],
                    'articledsfgg' => htmlspecialchars_decode($_GPC['articledsfgg']),
                    'author' => $_GPC['author'],
                    'displayorder' => intval($_GPC['displayorder']),
                    'linkurl' => $_GPC['linkurl'],
                    'pic' => iserializer($_GPC['pic']),
                    'createtime' => TIMESTAMP,
                    'click' => intval($_GPC['click'])
                );
                if (!empty($_GPC['thumb'])) {
                    $data['thumb'] = $_GPC['thumb'];
                } elseif (!empty($_GPC['autolitpic'])) {
                    $match = array();
                    preg_match('/attachment\\/(.*?)(\\.gif|\\.jpg|\\.png|\\.bmp)/', $_GPC['content'], $match);
                    if (!empty($match[1])) {
                        $data['thumb'] = $match[1] . $match[2];
                    }
                } else {
                    $data['thumb'] = '';
                }
                $keyword = str_replace('，', ',', trim($_GPC['keyword']));
                $keyword = explode(',', $keyword);
                if (!empty($keyword)) {
                    $rule['uniacid'] = $_W['uniacid'];
                    $rule['name']    = '文章：' . $_GPC['title'] . ' 触发规则';
                    $rule['module']  = 'news';
                    $rule['status']  = 1;
                    $keywords        = array();
                    foreach ($keyword as $key) {
                        $key = trim($key);
                        if (empty($key))
                            continue;
                        $keywords[] = array(
                            'uniacid' => $_W['uniacid'],
                            'module' => 'news',
                            'content' => $key,
                            'status' => 1,
                            'type' => 1,
                            'displayorder' => 1
                        );
                    }
                    $reply['title']       = $_GPC['title'];
                    $reply['description'] = $_GPC['description'];
                    $reply['thumb']       = $_GPC['thumb'];
                    $reply['url']         = murl('site/site/detail', array(
                        'id' => $id
                    ));
                }
                if (!empty($_GPC['credit']['status'])) {
                    $credit['status'] = intval($_GPC['credit']['status']);
                    $credit['limit']  = $_GPC['credit']['limit'] ? $_GPC['credit']['limit'] : message('请设置余额上限');
                    $credit['share']  = $_GPC['credit']['share'] ? $_GPC['credit']['share'] : message('请设置分享时赠余额多少');
                    $credit['click']  = $_GPC['credit']['click'] ? $_GPC['credit']['click'] : message('请设置阅读时赠送余额多少');
                    $data['credit']   = iserializer($credit);
                } else {
                    $data['credit'] = iserializer(array(
                        'status' => 0,
                        'limit' => 0,
                        'share' => 0,
                        'click' => 0
                    ));
                }
                $data['zongjia'] = $credit['limit'];
                $data['jiage']   = $credit['share'];
                $data['jifen']   = $_GPC['jifen'];
                if (empty($id)) {
                    pdo_insert($this->tb_article, $data);
                    $id = pdo_insertid();
                } else {
                    unset($data['createtime']);
                    pdo_update($this->tb_article, $data, array(
                        'id' => $id
                    ));
                }
                message('文章更新成功！', $this->createWebUrl('article'), 'success');
            } else {
                include $this->template('article');
            }
        } elseif ($op == 'delete') {
            load()->func('file');
            $id  = intval($_GPC['id']);
            $row = pdo_fetch("SELECT id,rid,kid,thumb FROM " . tablename($this->tb_article) . " WHERE id = :id", array(
                ':id' => $id
            ));
            if (empty($row)) {
                message('抱歉，文章不存在或是已经被删除！');
            }
            if (!empty($row['thumb'])) {
                file_delete($row['thumb']);
            }
            pdo_delete($this->tb_article, array(
                'id' => $id
            ));
            message('删除成功！', referer(), 'success');
        } elseif ($op == 'deleteall') {
            $rowcount    = 0;
            $notrowcount = 0;
            foreach ($_GPC['idArr'] as $k => $id) {
                $id = intval($id);
                if (!empty($id)) {
                    $row = pdo_fetch("SELECT * FROM " . tablename($this->tb_article) . " WHERE id = :id", array(
                        ':id' => $id
                    ));
                    if (empty($row)) {
                        $notrowcount++;
                        continue;
                    }
                    pdo_delete($this->tb_article, array(
                        'id' => $id,
                        'uniacid' => $_W['uniacid']
                    ));
                    $rowcount++;
                }
            }
            $this->message("操作成功！共删除{$rowcount}条数据,{$notrowcount}条数据不能删除!", '', 0);
        } elseif ($op == 'checkall') {
            load()->model('mc');
            $rowcount    = 0;
            $notrowcount = 0;
            $settings    = $this->module['config'];
            foreach ($_GPC['idArr'] as $k => $id) {
                $id = intval($id);
                if (!empty($id)) {
                    $row = pdo_fetch("SELECT status,uid,credit FROM " . tablename($this->tb_article) . " WHERE id = :id", array(
                        ':id' => $id
                    ));
                    if (empty($row)) {
                        $notrowcount++;
                        continue;
                    }
                    $credit = mc_credit_fetch($row['uid']);
                    pdo_update($this->tb_article, array(
                        'status' => 1
                    ), array(
                        "id" => $id,
                        "uniacid" => $_W['uniacid']
                    ));
                    $rowcredit = iunserializer($row['credit']);
                    if ($rowcredit['status'] == 0) {
                        $result = mc_fansinfo($row['uid'], $_W['acid'], $_W['uniacid']);
                        load()->classs('weixin.account');
                        load()->func('communication');
                        $acc    = WeAccount::create($acid);
                        $credit = $credit['credit2'] + $settings['credit2'];
                        mc_credit_update($row['uid'], 'credit2', 10, array(
                            '1' => '文章通过审核增加余额'
                        ));
                        $kdata = array(
                            'first' => array(
                                'value' => '您的文章已审核通过',
                                'color' => '#ff510'
                            ),
                            'keyword1' => array(
                                'value' => $_W['uniaccount']['name'],
                                'color' => '#ff510'
                            ),
                            'keyword2' => array(
                                'value' => '系统赠送您' . $settings['credit2'] . '元',
                                'color' => '#ff510'
                            ),
                            'remark' => array(
                                'value' => '请进入会员中心点击查看',
                                'color' => '#ff510'
                            )
                        );
                        $url   = $_W['siteroot'] . 'app' . ltrim(murl('entry', array(
                            'do' => 'memberfabumanage',
                            'm' => 'cyl_wxweizhang',
                            'uid' => $uid
                        )), '.');
                        $acc->sendTplNotice($result['openid'], $settings['templateid'], $kdata, $url, $topcolor = '#FF683F');
                    }
                    $rowcount++;
                }
            }
            $this->message("操作成功！共审核{$rowcount}条数据,{$notrowcount}条数据不能删除!!", '', 0);
        } elseif ($op == 'caiji') {
            $typeid = $this->typeid();
            $list   = pdo_fetchall("SELECT sourcelink FROM " . tablename($this->tb_article) . " WHERE uniacid = {$_W['uniacid']}");
            foreach ($list as $key => $val) {
                foreach ($val as $value) {
                    $new_arr[] = $value;
                }
            }
            if (checksubmit('submit')) {
                $pcate = $_GPC['category']['parentid'];
                if (empty($_GPC['category']['parentid'])) {
                    message('主分类不能为空，或者您还未创建分类');
                }
                if (!empty($_GPC['wxurl'])) {
                    $url = explode("\r\n", $_GPC['wxurl']);
                }
                if (empty($url)) {
                    $id = intval($_GPC['typeid']);
                    if ($id == 100) {
                        message('请选择采集分类');
                    }
                    if ($id == 20) {
                        $id = 0;
                    }
                    $start = $_GPC['start'];
                    if (empty($_GPC['start'])) {
                        message('请输入采集的页数');
                    }
                    $gjc     = $_GPC['gjc'];
                    $ch      = curl_init();
                    $urllist = "http://apis.baidu.com/showapi_open_bus/weixin/weixin_article_list?typeId=$id&page=$start&key=$gjc";
                    $header  = array(
                        'apikey: 9605e74753cc33db2fe49910953ae54e'
                    );
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_URL, $urllist);
                    $res     = curl_exec($ch);
                    $data    = json_decode($res);
                    $urlnews = array();
                    foreach ($data->showapi_res_body->pagebean->contentlist as $item) {
                        $urlnews[] = array(
                            'link' => $item->url
                        );
                    }
                    foreach ($urlnews as $key => $value) {
                        if (!in_array($value['link'], $new_arr)) {
                            $config = get_caiji($value['link']);
                        }
                        $data        = array(
                            'uniacid' => $_W['uniacid'],
                            'title' => $config['title'],
                            'thumb' => 'http://img01.store.sogou.com/net/a/04/link?appid=100520031&w=600&url=' . $config['thumb'],
                            'description' => $config['desc'],
                            'uid' => $openiduid,
                            'source' => $_W['uniaccount']['name'],
                            'pcate' => intval($_GPC['category']['parentid']),
                            'ccate' => intval($_GPC['category']['childid']),
                            'content' => htmlspecialchars_decode($config['contents']),
                            'createtime' => TIMESTAMP,
                            'sourcelink' => $value['link'],
                            'zongjia' => 0,
                            'click' => random(4, true)
                        );
                        $data['pic'] = iserializer(getImgs($config['contents']));
                        if (!in_array($data['sourcelink'], $new_arr)) {
                            pdo_insert($this->tb_article, $data);
                        }
                    }
                } else {
                    foreach ($url as $key => $value) {
                        if (!in_array($value, $new_arr)) {
                            $config = get_caiji($value);
                        }
                        $data        = array(
                            'uniacid' => $_W['uniacid'],
                            'title' => $config['title'],
                            'uid' => $openiduid,
                            'thumb' => 'http://img01.store.sogou.com/net/a/04/link?appid=100520031&w=600&url=' . $config['thumb'],
                            'description' => $config['desc'],
                            'source' => $_W['uniaccount']['name'],
                            'pcate' => intval($_GPC['category']['parentid']),
                            'ccate' => intval($_GPC['category']['childid']),
                            'content' => htmlspecialchars_decode($config['contents']),
                            'createtime' => TIMESTAMP,
                            'sourcelink' => $value,
                            'zongjia' => 0,
                            'click' => intval($_GPC['click'])
                        );
                        $data['pic'] = iserializer(getImgs($config['contents']));
                        if (!in_array($value, $new_arr)) {
                            pdo_insert($this->tb_article, $data);
                        }
                    }
                }
                message('文章采集成功！', $this->createWebUrl('article', array(
                    'id' => $id
                )), 'success');
            }
            include $this->template('article');
        }
    }
    public function doWebTixian()
    {
        global $_W, $_GPC;
        $settings    = $this->module['config'];
        $totalamount = pdo_fetchcolumn(" SELECT SUM(amount) FROM " . tablename('cyl_wxwenzhang_tixian') . " WHERE uniacid ={$_W['uniacid']} ");
        $pindex      = max(1, intval($_GPC['page']));
        $psize       = 10;
        $total       = pdo_fetchcolumn(" SELECT COUNT(*) FROM " . tablename('cyl_wxwenzhang_tixian') . " WHERE uniacid ={$_W['uniacid']} ");
        $list        = pdo_fetchall("select * from" . tablename('cyl_wxwenzhang_tixian') . "where uniacid ={$_W['uniacid']} ORDER BY id DESC " . "LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        $pager       = pagination($total, $pindex, $psize);
        include $this->template('tixian');
    }
    public function doWebTixianShenhe()
    {
        global $_W, $_GPC;
        load()->model('mc');
        $settings = $this->module['config'];
        load()->classs('weixin.account');
        load()->func('communication');
        $acc           = WeAccount::create($acid);
        $user          = pdo_get('cyl_wxwenzhang_tixian', array(
            'id' => $_GPC['id']
        ), array(
            'uid',
            'title',
            'wxh',
            'openid',
            'amount',
            'nickname'
        ));
        $amount        = $user['amount'] * 100;
        $arr['openid'] = $user['openid'];
        $arr['hbname'] = '余额提现';
        $arr['body']   = "余额提现";
        $arr['fee']    = $amount;
        $res           = $this->sendhongbaoto($arr);
        if ($res['result_code'] == 'SUCCESS') {
            $kdata = array(
                'first' => array(
                    'value' => '提现审核成功',
                    'color' => '#ff510'
                ),
                'keyword1' => array(
                    'value' => $_W['uniaccount']['name'],
                    'color' => '#ff510'
                ),
                'keyword2' => array(
                    'value' => '提现金额为' . $user['amount'],
                    'color' => '#ff510'
                ),
                'remark' => array(
                    'value' => '请进入微信领取查看',
                    'color' => '#ff510'
                )
            );
            $url   = $_W['siteroot'] . 'app' . ltrim(murl('entry', array(
                'do' => 'member',
                'm' => 'cyl_wxweizhang',
                'uid' => $uid
            )), '.');
            $acc->sendTplNotice($user['openid'], $settings['templateid'], $kdata, $url, $topcolor = '#FF683F');
            $user_data = array(
                'status' => 1
            );
            pdo_update('cyl_wxwenzhang_tixian', $user_data, array(
                'id' => $_GPC['id']
            ));
            message('审核成功！', $this->createWebUrl('tixian'), 'success');
        } else {
            $msg = $res['return_msg'];
            message("$msg", $this->createWebUrl('tixian'), 'error');
        }
    }
}