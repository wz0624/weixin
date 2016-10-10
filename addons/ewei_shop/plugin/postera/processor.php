<?php
if (!defined("IN_IA")) {
    print("Access Denied");
}
require IA_ROOT . "/addons/ewei_shop/defines.php";
require EWEI_SHOP_INC . "plugin/plugin_processor.php";
class PosteraProcessor extends PluginProcessor
{
    public function __construct()
    {
        parent::__construct("postera");
    }
    public function respond($zym_var_25 = null)
    {
        global $_W;
        $zym_var_23         = $zym_var_25->message;
        $zym_var_24         = strtolower($zym_var_23["msgtype"]);
        $zym_var_22         = strtolower($zym_var_23["event"]);
        $zym_var_25->member = $this->model->checkMember($zym_var_23["from"]);
        if ($zym_var_24 == "text" || $zym_var_22 == "click") {
            return $this->responseText($zym_var_25);
        } else if ($zym_var_24 == "event") {
            if ($zym_var_22 == "scan") {
                return $this->responseScan($zym_var_25);
            } else if ($zym_var_22 == "subscribe") {
                return $this->responseSubscribe($zym_var_25);
            }
        }
    }
    private function responseText($zym_var_25)
    {
        global $_W;
        $zym_var_21 = 4;
        load()->func("communication");
        $zym_var_19 = $_W["siteroot"] . "app/index.php?i=" . $_W['uniacid'] . "&c=entry&m=ewei_shop&do=plugin&p=postera&method=build&timestamp=" . time();
        $zym_var_20 = ihttp_request($zym_var_19, array(
            "openid" => $zym_var_25->message["from"],
            "content" => urlencode($zym_var_25->message["content"])
        ), array(), $zym_var_21);
        return $this->responseEmpty();
    }
    private function responseEmpty()
    {
        ob_clean();
        ob_start();
        echo '';
        ob_flush();
        ob_end_flush();
        print(0);
    }
    private function responseDefault($zym_var_25)
    {
        global $_W;
        return $zym_var_25->respText("感谢您的关注!");
    }
    private function responseScan($zym_var_25)
    {
        global $_W;
        $zym_var_26 = $zym_var_25->message["from"];
        $zym_var_29 = $zym_var_25->message["eventkey"];
        $zym_var_31 = $zym_var_25->message["ticket"];
        if (empty($zym_var_31)) {
            return $this->responseDefault($zym_var_25);
        }
        $zym_var_30 = $this->model->getQRByTicket($zym_var_31);
        if (empty($zym_var_30)) {
            return $this->responseDefault($zym_var_25);
        }
        $zym_var_27 = pdo_fetch("select * from " . tablename("ewei_shop_postera") . " where id=:id and uniacid=:uniacid limit 1", array(
            ":id" => $zym_var_30["posterid"],
            ":uniacid" => $_W['uniacid']
        ));
        if (empty($zym_var_27)) {
            return $this->responseDefault($zym_var_25);
        }
        $zym_var_28 = m("member")->getMember($zym_var_30["openid"]);
        $this->commission($zym_var_27, $zym_var_25->member, $zym_var_28);
        $zym_var_19 = trim($zym_var_27["respurl"]);
        if (empty($zym_var_19)) {
            if ($zym_var_28["isagent"] == 1 && $zym_var_28["status"] == 1) {
                $zym_var_19 = $_W["siteroot"] . "app/index.php?i={$_W['uniacid']}&c=entry&m=ewei_shop&do=plugin&p=commission&method=myshop&mid=" . $zym_var_28["id"];
            } else {
                $zym_var_19 = $_W["siteroot"] . "app/index.php?i={$_W['uniacid']}&c=entry&m=ewei_shop&do=shop&mid=" . $zym_var_28["id"];
            }
        }
        if ($zym_var_27["resptype"] == '0') {
            if (!empty($zym_var_27["resptitle"])) {
                $zym_var_32 = array(
                    array(
                        "title" => $zym_var_27["resptitle"],
                        "description" => $zym_var_27["respdesc"],
                        "picurl" => tomedia($zym_var_27["respthumb"]),
                        "url" => $zym_var_19
                    )
                );
                return $zym_var_25->respNews($zym_var_32);
            }
        }
        if ($zym_var_27["resptype"] == "1") {
            if (!empty($zym_var_27["resptext"])) {
                return $zym_var_25->respText($zym_var_27["resptext"]);
            }
        }
        return $this->responseEmpty();
    }
    private function responseSubscribe($zym_var_25)
    {
        global $_W;
        $zym_var_26 = $zym_var_25->message["from"];
        $zym_var_18 = explode("_", $zym_var_25->message["eventkey"]);
        $zym_var_29 = isset($zym_var_18[1]) ? $zym_var_18[1] : '';
        $zym_var_31 = $zym_var_25->message["ticket"];
        $zym_var_17 = $zym_var_25->member;
        if (empty($zym_var_31)) {
            return $this->responseDefault($zym_var_25);
        }
        $zym_var_30 = $this->model->getQRByTicket($zym_var_31);
        if (empty($zym_var_30)) {
            return $this->responseDefault($zym_var_25);
        }
        $zym_var_27 = pdo_fetch("select * from " . tablename("ewei_shop_postera") . " where id=:id and uniacid=:uniacid limit 1", array(
            ":id" => $zym_var_30["posterid"],
            ":uniacid" => $_W['uniacid']
        ));
        if (empty($zym_var_27)) {
            return $this->responseDefault($zym_var_25);
        }
        $zym_var_28 = m("member")->getMember($zym_var_30["openid"]);
        $zym_var_6  = pdo_fetch("select * from " . tablename("ewei_shop_postera_log") . " where openid=:openid and posterid=:posterid and uniacid=:uniacid limit 1", array(
            ":openid" => $zym_var_26,
            ":posterid" => $zym_var_27["id"],
            ":uniacid" => $_W['uniacid']
        ));
        if (empty($zym_var_6) && $zym_var_26 != $zym_var_30["openid"]) {
            $zym_var_6 = array(
                "uniacid" => $_W['uniacid'],
                "posterid" => $zym_var_27["id"],
                "openid" => $zym_var_26,
                "from_openid" => $zym_var_30["openid"],
                "subcredit" => $zym_var_27["subcredit"],
                "submoney" => $zym_var_27["submoney"],
                "reccredit" => $zym_var_27["reccredit"],
                "recmoney" => $zym_var_27["recmoney"],
                "createtime" => time()
            );
            pdo_insert("ewei_shop_postera_log", $zym_var_6);
            $zym_var_6["id"] = pdo_insertid();
            $zym_var_7       = $zym_var_27["subpaycontent"];
            if (empty($zym_var_7)) {
                $zym_var_7 = "您通过 [nickname] 的推广二维码扫码关注的奖励";
            }
            $zym_var_7 = str_replace("[nickname]", $zym_var_28["nickname"], $zym_var_7);
            $zym_var_5 = $zym_var_27["recpaycontent"];
            if (empty($zym_var_5)) {
                $zym_var_5 = "推荐 [nickname] 扫码关注的奖励";
            }
            $zym_var_5 = str_replace("[nickname]", $zym_var_17["nickname"], $zym_var_7);
            if ($zym_var_27["subcredit"] > 0) {
                m("member")->setCredit($zym_var_26, "credit1", $zym_var_27["subcredit"], array(
                    0,
                    "扫码关注积分+" . $zym_var_27["subcredit"]
                ));
            }
            if ($zym_var_27["submoney"] > 0) {
                $zym_var_4 = $zym_var_27["submoney"];
                if ($zym_var_27["paytype"] == 1) {
                    $zym_var_4 *= 100;
                }
                m("finance")->pay($zym_var_26, $zym_var_27["paytype"], $zym_var_4, '', $zym_var_7);
            }
            if ($zym_var_27["reccredit"] > 0) {
                m("member")->setCredit($zym_var_30["openid"], "credit1", $zym_var_27["reccredit"], array(
                    0,
                    "推荐扫码关注积分+" . $zym_var_27["reccredit"]
                ));
            }
            if ($zym_var_27["recmoney"] > 0) {
                $zym_var_4 = $zym_var_27["recmoney"];
                if ($zym_var_27["paytype"] == 1) {
                    $zym_var_4 *= 100;
                }
                m("finance")->pay($zym_var_30["openid"], $zym_var_27["paytype"], $zym_var_4, '', $zym_var_5);
            }
            $zym_var_1 = false;
            $zym_var_2 = false;
            $zym_var_3 = p("coupon");
            if ($zym_var_3) {
                if (!empty($zym_var_27["reccouponid"]) && $zym_var_27["reccouponnum"] > 0) {
                    $zym_var_8 = $zym_var_3->getCoupon($zym_var_27["reccouponid"]);
                    if (!empty($zym_var_8)) {
                        $zym_var_1 = true;
                    }
                }
                if (!empty($zym_var_27["subcouponid"]) && $zym_var_27["subcouponnum"] > 0) {
                    $zym_var_9 = $zym_var_3->getCoupon($zym_var_27["subcouponid"]);
                    if (!empty($zym_var_9)) {
                        $zym_var_2 = true;
                    }
                }
            }
            if (!empty($zym_var_27["subtext"])) {
                $zym_var_15 = $zym_var_27["subtext"];
                $zym_var_15 = str_replace("[nickname]", $zym_var_17["nickname"], $zym_var_15);
                $zym_var_15 = str_replace("[credit]", $zym_var_27["reccredit"], $zym_var_15);
                $zym_var_15 = str_replace("[money]", $zym_var_27["recmoney"], $zym_var_15);
                if ($zym_var_8) {
                    $zym_var_15 = str_replace("[couponname]", $zym_var_8["couponname"], $zym_var_15);
                    $zym_var_15 = str_replace("[couponnum]", $zym_var_27["reccouponnum"], $zym_var_15);
                }
                if (!empty($zym_var_27["templateid"])) {
                    m("message")->sendTplNotice($zym_var_30["openid"], $zym_var_27["templateid"], array(
                        "first" => array(
                            "value" => "推荐关注奖励到账通知",
                            "color" => "#4a5077"
                        ),
                        "keyword1" => array(
                            "value" => "推荐奖励",
                            "color" => "#4a5077"
                        ),
                        "keyword2" => array(
                            "value" => $zym_var_15,
                            "color" => "#4a5077"
                        ),
                        "remark" => array(
                            "value" => "
谢谢您对我们的支持！",
                            "color" => "#4a5077"
                        )
                    ), '');
                } else {
                    m("message")->sendCustomNotice($zym_var_30["openid"], $zym_var_15);
                }
            }
            if (!empty($zym_var_27["entrytext"])) {
                $zym_var_16 = $zym_var_27["entrytext"];
                $zym_var_16 = str_replace("[nickname]", $zym_var_28["nickname"], $zym_var_16);
                $zym_var_16 = str_replace("[credit]", $zym_var_27["subcredit"], $zym_var_16);
                $zym_var_16 = str_replace("[money]", $zym_var_27["submoney"], $zym_var_16);
                if ($zym_var_9) {
                    $zym_var_16 = str_replace("[couponname]", $zym_var_9["couponname"], $zym_var_16);
                    $zym_var_16 = str_replace("[couponnum]", $zym_var_27["subcouponnum"], $zym_var_16);
                }
                if (!empty($zym_var_27["templateid"])) {
                    m("message")->sendTplNotice($zym_var_26, $zym_var_27["templateid"], array(
                        "first" => array(
                            "value" => "关注奖励到账通知",
                            "color" => "#4a5077"
                        ),
                        "keyword1" => array(
                            "value" => "关注奖励",
                            "color" => "#4a5077"
                        ),
                        "keyword2" => array(
                            "value" => $zym_var_16,
                            "color" => "#4a5077"
                        ),
                        "remark" => array(
                            "value" => "
谢谢您对我们的支持！",
                            "color" => "#4a5077"
                        )
                    ), '');
                } else {
                    m("message")->sendCustomNotice($zym_var_26, $zym_var_16);
                }
            }
            $zym_var_14 = array();
            if ($zym_var_1) {
                $zym_var_14["reccouponid"]  = $zym_var_27["reccouponid"];
                $zym_var_14["reccouponnum"] = $zym_var_27["reccouponnum"];
                $zym_var_3->poster($zym_var_28, $zym_var_27["reccouponid"], $zym_var_27["reccouponnum"]);
            }
            if ($zym_var_2) {
                $zym_var_14["subcouponid"]  = $zym_var_27["subcouponid"];
                $zym_var_14["subcouponnum"] = $zym_var_27["subcouponnum"];
                $zym_var_3->poster($zym_var_17, $zym_var_27["subcouponid"], $zym_var_27["subcouponnum"]);
            }
            if (!empty($zym_var_14)) {
                pdo_update("ewei_shop_postera_log", $zym_var_14, array(
                    "id" => $zym_var_6["id"]
                ));
            }
        }
        $this->commission($zym_var_27, $zym_var_17, $zym_var_28);
        $zym_var_19 = trim($zym_var_27["respurl"]);
        if (empty($zym_var_19)) {
            if ($zym_var_28["isagent"] == 1 && $zym_var_28["status"] == 1) {
                $zym_var_19 = $_W["siteroot"] . "app/index.php?i={$_W['uniacid']}&c=entry&m=ewei_shop&do=plugin&p=commission&method=myshop&mid=" . $zym_var_28["id"];
            } else {
                $zym_var_19 = $_W["siteroot"] . "app/index.php?i={$_W['uniacid']}&c=entry&m=ewei_shop&do=shop&mid=" . $zym_var_28["id"];
            }
        }
        if ($zym_var_27["resptype"] == '0') {
            if (!empty($zym_var_27["resptitle"])) {
                $zym_var_32 = array(
                    array(
                        "title" => $zym_var_27["resptitle"],
                        "description" => $zym_var_27["respdesc"],
                        "picurl" => tomedia($zym_var_27["respthumb"]),
                        "url" => $zym_var_19
                    )
                );
                return $zym_var_25->respNews($zym_var_32);
            }
        }
        if ($zym_var_27["resptype"] == "1") {
            if (!empty($zym_var_27["resptext"])) {
                return $zym_var_25->respText($zym_var_27["resptext"]);
            }
        }
        return $this->responseEmpty();
    }
    private function commission($zym_var_27, $zym_var_17, $zym_var_28)
    {
        $zym_var_13 = time();
        $zym_var_10 = p("commission");
        if ($zym_var_10) {
            $zym_var_11 = $zym_var_10->getSet();
            if (!empty($zym_var_11)) {
                if ($zym_var_17["isagent"] != 1) {
                    if ($zym_var_28["isagent"] == 1 && $zym_var_28["status"] == 1) {
                        if (!empty($zym_var_27["bedown"])) {
                            if (empty($zym_var_17["agentid"])) {
                                if (empty($zym_var_17["fixagentid"])) {
                                    pdo_update("ewei_shop_member", array(
                                        "agentid" => $zym_var_28["id"],
                                        "childtime" => $zym_var_13
                                    ), array(
                                        "id" => $zym_var_17["id"]
                                    ));
                                    $zym_var_17["agentid"] = $zym_var_28["id"];
                                    $zym_var_10->sendMessage($zym_var_28["openid"], array(
                                        "nickname" => $zym_var_17["nickname"],
                                        "childtime" => $zym_var_13
                                    ), TM_COMMISSION_AGENT_NEW);
                                    $zym_var_10->upgradeLevelByAgent($zym_var_28["id"]);
                                }
                            }
                            if (!empty($zym_var_27["beagent"])) {
                                $zym_var_12 = intval($zym_var_11["become_check"]);
                                pdo_update("ewei_shop_member", array(
                                    "isagent" => 1,
                                    "status" => $zym_var_12,
                                    "agenttime" => $zym_var_13
                                ), array(
                                    "id" => $zym_var_17["id"]
                                ));
                                if ($zym_var_12 == 1) {
                                    $zym_var_10->sendMessage($zym_var_17["openid"], array(
                                        "nickname" => $zym_var_17["nickname"],
                                        "agenttime" => $zym_var_13
                                    ), TM_COMMISSION_BECOME);
                                    $zym_var_10->upgradeLevelByAgent($zym_var_28["id"]);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
?>