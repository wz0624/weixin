<?php
if (!defined("IN_IA")) {
    exit("Access Denied");
}
class Ewei_DShop_Order
{
    function getDispatchPrice($val0, $val1, $val2 = -1)
    {
        if (empty($val1)) {
            return 0;
        }
        $val4 = 0;
        if ($val2 == -1) {
            $val2 = $val1["calculatetype"];
        }
        if ($val2 == 1) {
            if ($val0 <= $val1["firstnum"]) {
                $val4 = floatval($val1["firstnumprice"]);
            } else {
                $val4  = floatval($val1["firstnumprice"]);
                $val15 = $val0 - floatval($val1["firstnum"]);
                $val18 = floatval($val1["secondnum"]) <= 0 ? 1 : floatval($val1["secondnum"]);
                $val21 = 0;
                if ($val15 % $val18 == 0) {
                    $val21 = ($val15 / $val18) * floatval($val1["secondnumprice"]);
                } else {
                    $val21 = ((int) ($val15 / $val18) + 1) * floatval($val1["secondnumprice"]);
                }
                $val4 += $val21;
            }
        } else {
            if ($val0 <= $val1["firstweight"]) {
                $val4 = floatval($val1["firstprice"]);
            } else {
                $val4  = floatval($val1["firstprice"]);
                $val15 = $val0 - floatval($val1["firstweight"]);
                $val18 = floatval($val1["secondweight"]) <= 0 ? 1 : floatval($val1["secondweight"]);
                $val21 = 0;
                if ($val15 % $val18 == 0) {
                    $val21 = ($val15 / $val18) * floatval($val1["secondprice"]);
                } else {
                    $val21 = ((int) ($val15 / $val18) + 1) * floatval($val1["secondprice"]);
                }
                $val4 += $val21;
            }
        }
        return $val4;
    }
    function getCityDispatchPrice($val60, $val61, $val0, $val1)
    {
        if (is_array($val60) && count($val60) > 0) {
            foreach ($val60 as $val67) {
                $val68 = explode(";", $val67["citys"]);
                if (in_array($val61, $val68) && !empty($val68)) {
                    return $this->getDispatchPrice($val0, $val67, $val1["calculatetype"]);
                }
            }
        }
        return $this->getDispatchPrice($val0, $val1);
    }
    public function payResult($val78)
    {
        global $_W;
        $val80 = intval($val78["fee"]);
        $val82 = array(
            "status" => $val78["result"] == "success" ? 1 : 0
        );
        $val84 = $val78["tid"];
        $val86 = pdo_fetch("select id,ordersn, price,openid,dispatchtype,addressid,carrier,status,isverify,deductcredit2,virtual,isvirtual,couponid from " . tablename("ewei_shop_order") . " where  ordersn=:ordersn and uniacid=:uniacid limit 1", array(
            ":uniacid" => $_W["uniacid"],
            ":ordersn" => $val84
        ));
        $val89 = $val86["id"];
        if ($val78["from"] == "return") {
            $val92 = false;
            if (empty($val86["dispatchtype"])) {
                $val92 = pdo_fetch("select realname,mobile,address from " . tablename("ewei_shop_member_address") . " where id=:id limit 1", array(
                    ":id" => $val86["addressid"]
                ));
            }
            $val96 = false;
            if ($val86["dispatchtype"] == 1 || $val86["isvirtual"] == 1) {
                $val96 = unserialize($val86["carrier"]);
            }
            if ($val78["type"] == "cash") {
                return array(
                    "result" => "success",
                    "order" => $val86,
                    "address" => $val92,
                    "carrier" => $val96
                );
            } else {
                if ($val86["status"] == 0) {
                    $val106 = p("virtual");
                    if (!empty($val86["virtual"]) && $val106) {
                        $val106->pay($val86);
                    } else {
                        pdo_update("ewei_shop_order", array(
                            "status" => 1,
                            "paytime" => time()
                        ), array(
                            "id" => $val89
                        ));
                        $this->setStocksAndCredits($val89, 1);
                        if (p("coupon") && !empty($val86["couponid"])) {
                            p("coupon")->backConsumeCoupon($val86["id"]);
                        }
                        m("notice")->sendOrderMessage($val89);
                        if (p("commission")) {
                            p("commission")->checkOrderPay($val86["id"]);
                        }
                    }
                }
                return array(
                    "result" => "success",
                    "order" => $val86,
                    "address" => $val92,
                    "carrier" => $val96,
                    "virtual" => $val86["virtual"]
                );
            }
        }
    }
    function setDeductCredit2($val86)
    {
        global $_W;
        $val122 = m("common")->getSysset("shop");
        if ($val86['deductcredit2'] > 0) {
            m("member")->setCredit($val86["openid"], "credit2", $val86['deductcredit2'], array(
                '0',
                $val122["name"] . "购物返还抵扣余额 余额: {$val86['deductcredit2']} 订单号: {$val86['ordersn']}"
            ));
        }
    }
    function setStocksAndCredits($val89 = '', $val130 = 0)
    {
        global $_W;
        $val86  = pdo_fetch("select id,ordersn,price,openid,dispatchtype,addressid,carrier,status from " . tablename("ewei_shop_order") . " where id=:id limit 1", array(
            ":id" => $val89
        ));
        $val134 = pdo_fetchall("select og.goodsid,og.total,g.totalcnf,og.realprice, g.credit,og.optionid,g.total as goodstotal,og.optionid,g.sales,g.salesreal from " . tablename("ewei_shop_order_goods") . " og " . " left join " . tablename("ewei_shop_goods") . " g on g.id=og.goodsid " . " where og.orderid=:orderid and og.uniacid=:uniacid ", array(
            ":uniacid" => $_W["uniacid"],
            ":orderid" => $val89
        ));
        $val137 = 0;
        foreach ($val134 as $val139) {
            $val140 = 0;
            if ($val130 == 0) {
                if ($val139["totalcnf"] == 0) {
                    $val140 = -1;
                }
            } else if ($val130 == 1) {
                if ($val139["totalcnf"] == 1) {
                    $val140 = -1;
                }
            } else if ($val130 == 2) {
                if ($val86["status"] >= 1) {
                    if ($val139["totalcnf"] == 1) {
                        $val140 = 1;
                    }
                } else {
                    if ($val139["totalcnf"] == 0) {
                        $val140 = 1;
                    }
                }
            }
            if (!empty($val140)) {
                if (!empty($val139["optionid"])) {
                    $val155 = m("goods")->getOption($val139["goodsid"], $val139["optionid"]);
                    if (!empty($val155) && $val155["stock"] != -1) {
                        $val160 = -1;
                        if ($val140 == 1) {
                            $val160 = $val155["stock"] + $val139["total"];
                        } else if ($val140 == -1) {
                            $val160 = $val155["stock"] - $val139["total"];
                            $val160 <= 0 && $val160 = 0;
                        }
                        if ($val160 != -1) {
                            pdo_update("ewei_shop_goods_option", array(
                                "stock" => $val160
                            ), array(
                                "uniacid" => $_W["uniacid"],
                                "goodsid" => $val139["goodsid"],
                                "id" => $val139["optionid"]
                            ));
                        }
                    }
                }
                if (!empty($val139["goodstotal"]) && $val139["goodstotal"] != -1) {
                    $val178 = -1;
                    if ($val140 == 1) {
                        $val178 = $val139["goodstotal"] + $val139["total"];
                    } else if ($val140 == -1) {
                        $val178 = $val139["goodstotal"] - $val139["total"];
                        $val178 <= 0 && $val178 = 0;
                    }
                    if ($val178 != -1) {
                        pdo_update("ewei_shop_goods", array(
                            "total" => $val178
                        ), array(
                            "uniacid" => $_W["uniacid"],
                            "id" => $val139["goodsid"]
                        ));
                    }
                }
            }
            $val193 = trim($val139["credit"]);
            if (!empty($val193)) {
                if (strexists($val193, "%")) {
                    $val137 += intval(floatval(str_replace("%", '', $val193)) / 100 * $val139["realprice"]);
                } else {
                    $val137 += intval($val139["credit"]) * $val139["total"];
                }
            }
            if ($val130 == 0) {
                pdo_update("ewei_shop_goods", array(
                    "sales" => $val139["sales"] + $val139["total"]
                ), array(
                    "uniacid" => $_W["uniacid"],
                    "id" => $val139["goodsid"]
                ));
            } elseif ($val130 == 1) {
                if ($val86["status"] >= 1) {
                    $val210 = pdo_fetchcolumn("select ifnull(sum(total),0) from " . tablename("ewei_shop_order_goods") . " og " . " left join " . tablename("ewei_shop_order") . " o on o.id = og.orderid " . " where og.goodsid=:goodsid and o.status>=1 and o.uniacid=:uniacid limit 1", array(
                        ":goodsid" => $val139["goodsid"],
                        ":uniacid" => $_W["uniacid"]
                    ));
                    pdo_update("ewei_shop_goods", array(
                        "salesreal" => $val210
                    ), array(
                        "id" => $val139["goodsid"]
                    ));
                }
            }
        }
        if ($val137 > 0) {
            $val216 = m("common")->getSysset("shop");
            if ($val130 == 1) {
                m("member")->setCredit($val86["openid"], "credit1", $val137, array(
                    0,
                    $val216["name"] . "购物积分 订单号: " . $val86['ordersn']
                ));
            } elseif ($val130 == 2) {
                if ($val86["status"] >= 1) {
                    m("member")->setCredit($val86["openid"], "credit1", -$val137, array(
                        0,
                        $val216["name"] . "购物取消订单扣除积分 订单号: " . $val86['ordersn']
                    ));
                }
            }
        }
    }
    function getDefaultDispatch()
    {
        global $_W;
        $val229 = "select * from " . tablename("ewei_shop_dispatch") . " where isdefault=1 and uniacid=:uniacid and enabled=1 Limit 1";
        $val78  = array(
            ":uniacid" => $_W["uniacid"]
        );
        $val82  = pdo_fetch($val229, $val78);
        return $val82;
    }
    function getNewDispatch()
    {
        global $_W;
        $val229 = "select * from " . tablename("ewei_shop_dispatch") . " where uniacid=:uniacid and enabled=1 order by id desc Limit 1";
        $val78  = array(
            ":uniacid" => $_W["uniacid"]
        );
        $val82  = pdo_fetch($val229, $val78);
        return $val82;
    }
    function getOneDispatch($val244)
    {
        global $_W;
        $val229 = "select * from " . tablename("ewei_shop_dispatch") . " where id=:id and uniacid=:uniacid and enabled=1 Limit 1";
        $val78  = array(
            ":id" => $val244,
            ":uniacid" => $_W["uniacid"]
        );
        $val82  = pdo_fetch($val229, $val78);
        return $val82;
    }
    function getTotals()
    {
        global $_W;
        $val255             = array(
            ":uniacid" => $_W["uniacid"]
        );
        $val257             = "";
        $val258["all"]      = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename("ewei_shop_order") . " o {$val257}" . " WHERE o.uniacid = :uniacid and o.deleted=0", $val255);
        $val258["status_1"] = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename("ewei_shop_order") . " o {$val257}" . " WHERE o.uniacid = :uniacid and o.status=-1 and o.refundtime=0", $val255);
        $val258["status0"]  = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename("ewei_shop_order") . " o {$val257}" . " WHERE o.uniacid = :uniacid  and o.status=0 and o.paytype<>3", $val255);
        $val258["status1"]  = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename("ewei_shop_order") . " o {$val257}" . " WHERE o.uniacid = :uniacid  and ( o.status=1 or ( o.status=0 and o.paytype=3) )", $val255);
        $val258["status2"]  = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename("ewei_shop_order") . " o {$val257}" . " WHERE o.uniacid = :uniacid  and o.status=2", $val255);
        $val258["status3"]  = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename("ewei_shop_order") . " o {$val257}" . " WHERE o.uniacid = :uniacid  and o.status=3", $val255);
        $val258["status4"]  = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename("ewei_shop_order") . " o {$val257}" . " WHERE o.uniacid = :uniacid  and o.refundstate>0 and o.refundid<>0", $val255);
        $val258["status5"]  = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename("ewei_shop_order") . " o {$val257}" . " WHERE o.uniacid = :uniacid  and o.refundtime<>0", $val255);
        return $val258;
    }
}
?>