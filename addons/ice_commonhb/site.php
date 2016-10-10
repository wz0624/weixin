<?php
defined('IN_IA') or exit('Access Denied');
class Ice_commonhbModuleSite extends WeModuleSite
{
    public function doMobileIndex()
    {
        require 'inc/mobile/index.inc.php';
    }
    public function doWebManage()
    {
        require 'inc/web/manage.inc.php';
    }
    public function doMobileCode()
    {
        require 'inc/mobile/code.inc.php';
    }
    public function doMobileShow()
    {
        require 'inc/mobile/show.inc.php';
    }
    public function doMobileMyhb()
    {
        require 'inc/mobile/myhb.inc.php';
    }
    public function doMobileHbrule()
    {
        global $_W, $_GPC;
        $openid = $_W['openid'];
        if (empty($openid))
            exit();
        load()->func('tpl');
        $modulelist = uni_modules(false);
        $name       = 'ice_commonhb';
        $module     = $modulelist[$name];
        if (empty($module)) {
            message('抱歉，你操作的模块不能被访问！');
        }
        define('CRUMBS_NAV', 1);
        $ptr_title    = '参数设置';
        $module_types = module_types();
        define('ACTIVE_FRAME_URL', url('home/welcome/ext', array(
            'm' => $name
        )));
        $settings = $module['config'];
        if (substr($settings['logoImg'], 0, 5) != "http:") {
            $settings['logoImg'] = "../attachment/" . $settings['logoImg'];
        }
        $settings['hbrule'] = htmlspecialchars_decode($settings['hbrule']);
        include $this->template("guize");
    }
    public function doMobileGetMore()
    {
        global $_W, $_GPC;
        $openid            = $_W['openid'];
        $pindex            = max(1, intval($_GPC['pageno']));
        $psize             = 3;
        $param             = array();
        $param[':uniacid'] = $_W['uniacid'];
        $myhbs             = pdo_fetchall("select code,type,id from " . tablename("ice_yzmhb_code") . " where uniacid = :uniacid and openid = :openid and yzmhbid = 0 LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(
            ":uniacid" => $_W['uniacid'],
            ":openid" => $openid
        ));
        $html              = "";
        foreach ($myhbs as $k => $v) {
            $params = array(
                ":uniacid" => $_W['uniacid'],
                ":codeid" => $v['id'],
                ":openid" => $openid
            );
            if ($v['type'] == 1) {
                $url                 = $_W['siteroot'] . "app/index.php?i=" . $_W['uniacid'] . "&c=entry&do=show&m=ice_commonhb&codeid=" . $v['id'];
                $status              = '2';
                $time                = pdo_fetchcolumn("select time from " . tablename("ice_yzmhb_sendlist") . " where uniacid = :uniacid and codeid = :codeid and openid = :openid", $params);
                $myhbs[$k]['url']    = $url;
                $myhbs[$k]['status'] = $status;
                $myhbs[$k]['typemc'] = "普通红包";
                $myhbs[$k]['time']   = date("Y-m-d H:i:s", $time);
            } else if ($v['type'] == 2) {
                $url                 = $_W['siteroot'] . "app/index.php?i=" . $_W['uniacid'] . "&c=entry&do=show&m=ice_grouphb&codeid=" . $v['id'];
                $status              = '2';
                $time                = pdo_fetchcolumn("select time from " . tablename("ice_yzmhb_sendlist") . " where uniacid = :uniacid and codeid = :codeid and openid = :openid", $params);
                $myhbs[$k]['url']    = $url;
                $myhbs[$k]['status'] = $status;
                $myhbs[$k]['typemc'] = "裂变红包";
                $myhbs[$k]['time']   = date("Y-m-d H:i:s", $time);
            } else if ($v['type'] == 3) {
                $url                      = $_W['siteroot'] . "app/index.php?i=" . $_W['uniacid'] . "&c=entry&do=share&m=ice_guesshb&codeid=" . $v['id'];
                $res1                     = pdo_fetch("select status,gettime,guess_count from " . tablename("ice_guesshb") . " where uniacid = :uniacid and codeid = :codeid and openid = :openid ", $params);
                $status                   = $res1['status'];
                $time                     = $res1['gettime'];
                $myhbs[$k]['url']         = $url;
                $myhbs[$k]['status']      = $status;
                $myhbs[$k]['typemc']      = "小伙伴猜红包";
                $myhbs[$k]['time']        = date("Y-m-d H:i:s", $time);
                $myhbs[$k]['guess_count'] = $res1['guess_count'];
            } else if ($v['type'] == 4) {
                $url                 = $_W['siteroot'] . "app/index.php?i=" . $_W['uniacid'] . "&c=entry&do=share&m=ice_robhb&codeid=" . $v['id'];
                $res1                = pdo_fetch("select status,gettime from " . tablename("ice_robhb") . " where uniacid = :uniacid and codeid = :codeid and openid = :openid ", $params);
                $status              = $res1['status'];
                $time                = $res1['gettime'];
                $myhbs[$k]['status'] = $status;
                $myhbs[$k]['url']    = $url;
                $myhbs[$k]['typemc'] = "小伙伴抢红包";
                $myhbs[$k]['time']   = date("Y-m-d H:i:s", $time);
            }
            $html .= "<li>";
            $html .= '<div class="order_hd">';
            $html .= $myhbs[$k]['time'];
            $html .= "</div>";
            $html .= " <div class='order_bd' onclick = \"window.location.href='" . $myhbs[$k]['url'] . "'\">";
            $html .= '<div class="order_glist">';
            $html .= '<div class="order_goods" data-url="">';
            $html .= '<div style="position: absolute;right: 20px;top:10px;width:90px;height: 90px;z-index: 999;">';
            if ($myhbs[$k]['status'] == 1) {
                $html .= '<img  alt="" width="130" height="130" src="../addons/ice_robhb/img/success.png"/>';
            } else {
                $html .= '<img  alt="" width="130" height="130" src="../addons/ice_robhb/img/success22.png"/>';
            }
            $html .= '</div>';
            $html .= '<div class="order_goods_img">';
            $html .= '  <img alt="" src="../addons/ice_robhb/img/09.jpg">';
            $html .= '  </div>';
            $html .= '  <div class="order_goods_info">';
            $html .= '   <div class="order_goods_name">验证码：' . $myhbs[$k]['code'] . '</div>';
            $html .= '     <div class="order_goods_attr">';
            $html .= '         <div class="order_goods_attr_item" style="padding: 5px;">';
            $html .= '        <div class="tuan_g_core" >';
            $html .= '                <div class="tuan_g_price">';
            if ($myhbs[$k]['type'] == 3) {
                $html .= '          <span>已有' . $myhbs[$k]['guess_count'] . '人参与</span>';
            }
            $html .= '                                              <span>';
            $html .= '                                     </span>';
            $html .= '                                           </div>';
            $html .= '                                       <div class="tuan_g_btn"></div>';
            $html .= '                                      </div>';
            $html .= '                               </div>';
            $html .= '                              </div>';
            $html .= '                          </div>';
            $html .= '                    </div>';
            $html .= '                        <div class="order_opt">';
            $html .= '                             <span class="order_status">';
            $html .= '                                                                                    红包类型：' . $myhbs[$k]['typemc'];
            $html .= '                          </span>';
            $html .= '                        <div class="order_btn" ms-visible="order.total_status==3" style="margin: 5px;">';
            $html .= '                            <a  class="state_btn_2" href="javascript:void(0)">查看详情</a>';
            $html .= '                        </div>';
            $html .= '                         </div>';
            $html .= '              </div>';
            $html .= '            </div>';
            $html .= '             </li>';
        }
        echo $html;
    }
    public function doWebSendCommonHB()
    {
        require 'inc/web/sendCommonHB.inc.php';
    }
    public function doWebSetting()
    {
        require 'inc/web/setting.inc.php';
    }
    public function doWebCodeset()
    {
        require 'inc/web/codeset.inc.php';
    }
    public function doWebPrize()
    {
        require 'inc/web/prize.inc.php';
    }
    public function doWebExport()
    {
        global $_W, $_GPC;
        $acid = intval($_W['account']['uniacid']);
        require_once IA_ROOT . '/framework/library/phpexcel/PHPExcel.php';
        $result    = array();
        $piciid    = $_GPC['piciid'];
        $condition = "";
        $condition .= " and piciid = " . $piciid;
        $condition .= " and yzmhbid = 0 and type = '1' ";
        $list = pdo_fetchall("SELECT  code, status  FROM " . tablename('ice_yzmhb_code') . " where  uniacid = '{$_W['uniacid']}'  $condition ORDER BY time desc  ");
        foreach ($list as $k => $v) {
            $status = $v['status'];
            if ($status == '1') {
                $list[$k]['status1'] = "未使用";
            } else if ($status == '2') {
                $list[$k]['status1'] = "已使用";
            }
        }
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator('http://www.icetime.cn')->setLastModifiedBy('http://www.icetime.cn')->setTitle('Office 2007 XLSX Document')->setSubject('Office 2007 XLSX Document')->setDescription('Document for Office 2007 XLSX, generated using PHP classes.')->setKeywords('office 2007 openxml php')->setCategory('Result file');
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle('验证码');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', '批次');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', '验证码');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', '状况');
        $i = 2;
        foreach ($list as $k => $v) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $piciid);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $v['code']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $v['status1']);
            $i++;
        }
        $filename = '验证码数据' . '_' . date('Y-m-d');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
    public function doWebImport()
    {
        global $_W, $_GPC;
        load()->func('logging');
        $piciid = $_GPC['piciid'];
        if (!empty($_GPC['foo'])) {
            try {
                include_once("reader.php");
                $tmp = $_FILES['file']['tmp_name'];
                if (empty($tmp)) {
                    echo '请选择要导入的Excel文件！';
                    exit;
                }
                $file_name = IA_ROOT . "/addons/ice_commonhb/xls/code.xls";
                $uniacid   = $_W['uniacid'];
                if (copy($tmp, $file_name)) {
                    $xls = new Spreadsheet_Excel_Reader();
                    $xls->setOutputEncoding('utf-8');
                    $xls->read($file_name);
                    $data_values = "";
                    $count       = $xls->sheets[0]['numRows'];
                    for ($i = 1; $i <= $count; $i++) {
                        $code = $xls->sheets[0]['cells'][$i][1];
                        $time = time();
                        $data_values .= "('$uniacid','$code',0,'$piciid','1','$time','1'),";
                    }
                    $data_values = substr($data_values, 0, -1);
                    $query       = pdo_query("insert into `ims_ice_yzmhb_code`(uniacid,code,yzmhbid,piciid,type,time,status) values $data_values", array());
                    if ($query) {
                        pdo_query("update " . tablename("ice_yzmhb_codenum") . " set count = count + $count where id = :id and uniacid =:uniacid", array(
                            ":id" => $piciid,
                            ":uniacid" => $uniacid
                        ));
                        $url = $this->createWebUrl('codeset');
                        echo "<script>alert('导入成功！')</script>";
                        echo "<script>window.location.href= '$url'</script>";
                    } else {
                        $url = $this->createWebUrl('Import', array());
                        echo "<script>alert('导入失败！')</script>";
                        echo "<script>window.location.href= '$url'</script>";
                    }
                } else {
                    echo '复制失败！';
                    exit;
                }
            }
            catch (Exception $e) {
                logging_run($e, '', 'upload_tiku');
            }
        } else {
            include $this->template('import');
        }
    }
    public function doWebSend()
    {
        global $_W, $_GPC;
        load()->func('tpl');
        $sid        = $_GPC['sid'];
        $acid       = intval($_W['account']['uniacid']);
        $modulelist = uni_modules(false);
        $name       = 'ice_commonhb';
        $module     = $modulelist[$name];
        if (empty($module)) {
            message('抱歉，你操作的模块不能被访问！');
        }
        define('CRUMBS_NAV', 1);
        $ptr_title    = '参数设置';
        $module_types = module_types();
        define('ACTIVE_FRAME_URL', url('home/welcome/ext', array(
            'm' => $name
        )));
        $settings = $module['config'];
        $res      = pdo_fetch("select money,openid from " . tablename("ice_yzmhb_sendlist") . " where id = :id", array(
            ":id" => $sid
        ));
        $money    = $res['money'];
        $openid   = $res['openid'];
        $acc      = WeAccount::create($acid);
        if (empty($acc)) {
            $name = $_W['account']['name'];
            $acid = pdo_fetchcolumn("select acid from " . tablename("account_wechats") . " where uniacid = :uniacid and name = :name", array(
                ":uniacid" => $acid,
                ":name" => $name
            ));
            $acc  = WeAccount::create($acid);
        }
        $fan       = $acc->fansQueryInfo($openid, true);
        $issubsend = $settings['issubsend'];
        if ($issubsend == 1 && $fan['subscribe'] != '1') {
            echo "error,该用户未关注！";
            exit();
        }
        $res1 = $this->sendRedpack($openid, $settings, $money);
        if ($res1['type'] == 'ok') {
            pdo_update("ice_yzmhb_sendlist", array(
                "status" => '1'
            ), array(
                "id" => $sid
            ));
        }
        echo $res1['type'] . "," . $res1['content'];
    }
    function sendRedpack($openid, $settings, $money)
    {
        global $_W, $_GPC;
        $result = array();
        load()->func('logging');
        define('ROOT_PATH', dirname(preg_replace('@\(.*\(.*$@', '', __FILE__)));
        define('DS', DIRECTORY_SEPARATOR);
        define('SIGNTYPE', 'sha1');
        define('PARTNERKEY', $settings['partner']);
        define('APPID', $settings['appid']);
        define('apiclient_cert', $settings['apiclient_cert']);
        define('apiclient_key', $settings['apiclient_key']);
        define('rootca', $settings['rootca']);
        $mch_billno = $settings['mchid'] . date('YmdHis') . rand(1000, 9999);
        include_once(IA_ROOT . '/addons/ice_commonhb/pay/WxHongBaoHelper.php');
        $commonUtil      = new CommonUtil();
        $wxHongBaoHelper = new WxHongBaoHelper();
        $wxHongBaoHelper->setParameter("nonce_str", $commonUtil->create_noncestr());
        $wxHongBaoHelper->setParameter("mch_billno", $mch_billno);
        $wxHongBaoHelper->setParameter("mch_id", $settings['mchid']);
        $wxHongBaoHelper->setParameter("wxappid", $settings['appid']);
        $wxHongBaoHelper->setParameter("nick_name", $settings['nick_name']);
        $wxHongBaoHelper->setParameter("send_name", $settings['send_name']);
        $wxHongBaoHelper->setParameter("re_openid", $openid);
        $wxHongBaoHelper->setParameter("total_amount", $money);
        $wxHongBaoHelper->setParameter("min_value", $money);
        $wxHongBaoHelper->setParameter("max_value", $money);
        $wxHongBaoHelper->setParameter("total_num", 1);
        $wxHongBaoHelper->setParameter("wishing", $settings['wishing']);
        $wxHongBaoHelper->setParameter("client_ip", '127.0.0.1');
        $wxHongBaoHelper->setParameter("act_name", $settings['act_name']);
        $wxHongBaoHelper->setParameter("remark", $settings['remark']);
        $wxHongBaoHelper->setParameter("logo_imgurl", "https://www.baidu.com/img/bdlogo.png");
        $postXml     = $wxHongBaoHelper->create_hongbao_xml();
        $url         = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        $responseXml = $wxHongBaoHelper->curl_post_ssl($url, $postXml);
        $responseObj = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $return_code = $responseObj->return_code;
        $result_code = $responseObj->result_code;
        if ($return_code == 'SUCCESS') {
            if ($result_code == 'SUCCESS') {
                $result['type']    = "ok";
                $result['content'] = "content";
                return $result;
            } else {
                if ($responseObj->err_code == 'NOTENOUGH') {
                    $result['content'] = "后台繁忙，请稍后再试！";
                    $result['type']    = 'error';
                    return $result;
                } else if ($responseObj->err_code == 'TIME_LIMITED') {
                    $result['content'] = "现在非红包发放时间，请在北京时间0:00-8:00之外的时间前来领取";
                    $result['type']    = 'error';
                    return $result;
                } else if ($responseObj->err_code == 'SYSTEMERROR') {
                    $result['content'] = "系统繁忙，请稍后再试！";
                    $result['type']    = 'error';
                    return $result;
                } else if ($responseObj->err_code == 'DAY_OVER_LIMITED') {
                    $result['content'] = "今日红包已达上限，请明日再试！";
                    $result['type']    = 'error';
                    return $result;
                } else if ($responseObj->err_code == 'SECOND_OVER_LIMITED') {
                    $result['content'] = "每分钟红包已达上限，请稍后再试！";
                    $result['type']    = 'error';
                    return $result;
                }
                $result['content'] = "红包发放失败！" . $responseObj->return_msg . "！请稍后再试！";
                $result['type']    = 'error';
                return $result;
            }
        }
        if ($return_code == 'FAIL') {
            $result['content'] = $responseObj->return_msg;
            $result['type']    = 'error';
            return $result;
        }
    }
    public function doWebShowcode()
    {
        global $_W, $_GPC;
        $result    = array();
        $piciid    = $_GPC['piciid'];
        $condition = " ";
        $condition .= " and c.piciid = " . $piciid;
        $condition .= " and c.yzmhbid = 0 ";
        $type   = pdo_fetchcolumn("select type from " . tablename("ice_yzmhb_codenum") . " where id = :id", array(
            ":id" => $piciid
        ));
        $pindex = max(1, intval($_GPC['page']));
        $psize  = 20;
        if ($type == 1 || $type == 2) {
            $list  = pdo_fetchall("select code,status from " . tablename("ice_yzmhb_code") . " where uniacid = :uniacid and piciid = :piciid LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(
                ":uniacid" => $_W['uniacid'],
                ':piciid' => $piciid
            ));
            $total = pdo_fetchcolumn("select count(*) from " . tablename("ice_yzmhb_code") . " where uniacid = :uniacid and piciid = :piciid ", array(
                ":uniacid" => $_W['uniacid'],
                ':piciid' => $piciid
            ));
            foreach ($list as $k => $v) {
                $status = $v['status'];
                if ($status == '1') {
                    $list[$k]['status1'] = "未使用";
                } else if ($status == '2') {
                    $list[$k]['status1'] = "已使用";
                }
            }
        } elseif ($type == 3) {
            $list  = pdo_fetchall("SELECT c.code as code,b.status as status  FROM " . tablename('ice_yzmhb_code') . " c left join " . tablename("ice_guesshb") . " b on c.id = b.codeid  WHERE c.uniacid = '{$_W['uniacid']}'  $condition ORDER BY c.time desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
            $total = pdo_fetchcolumn("SELECT count(*)  FROM " . tablename('ice_yzmhb_code') . " c left join " . tablename("ice_guesshb") . " b on c.id = b.codeid  WHERE c.uniacid = '{$_W['uniacid']}'  $condition ");
            foreach ($list as $k => $v) {
                $status = $v['status'];
                if (empty($status)) {
                    $list[$k]['status1'] = "未使用";
                } else if ($status == '1') {
                    $list[$k]['status1'] = "正在猜测中";
                } else if ($status == '2') {
                    $list[$k]['status1'] = "已使用";
                }
            }
        } elseif ($type == 4) {
            $list  = pdo_fetchall("SELECT c.code as code,b.status as status  FROM " . tablename('ice_yzmhb_code') . " c left join " . tablename("ice_robhb") . " b on c.id = b.codeid  WHERE c.uniacid = '{$_W['uniacid']}'  $condition ORDER BY c.time desc  LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
            $total = pdo_fetchcolumn("SELECT count(*)  FROM " . tablename('ice_yzmhb_code') . " c left join " . tablename("ice_robhb") . " b on c.id = b.codeid  WHERE c.uniacid = '{$_W['uniacid']}'  $condition ");
            foreach ($list as $k => $v) {
                $status = $v['status'];
                if (empty($status)) {
                    $list[$k]['status1'] = "未使用";
                } else if ($status == '1') {
                    $list[$k]['status1'] = "正在抢夺中";
                } else if ($status == '2') {
                    $list[$k]['status1'] = "已使用";
                }
            }
        }
        $pager = pagination($total, $pindex, $psize);
        include $this->template("showcode");
    }
}