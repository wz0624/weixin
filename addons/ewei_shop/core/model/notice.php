<?php


if (!defined('IN_IA')) {
    exit('Access Denied');
}
class Ewei_DShop_Notice
{
    public function sendOrderMessage($zym_var_33 = '0', $zym_var_30 = false)
    {
        global $_W;
        if (empty($zym_var_33)) {
            return;
        }
        $zym_var_31 = pdo_fetch("select * from " . tablename("ewei_shop_order") . " where id=:id limit 1", array(
            ":id" => $zym_var_33
        ));
        if (empty($zym_var_31)) {
            return;
        }
        $zym_var_32 = $_W["siteroot"] . "app/index.php?i=" . $_W["uniacid"] . "&c=entry&m=ewei_shop&do=order&p=detail&id=" . $zym_var_33;
        if (strexists($zym_var_32, "/addons/ewei_shop/")) {
            $zym_var_32 = str_replace("/addons/ewei_shop/", "/", $zym_var_32);
        }
        if (strexists($zym_var_32, "/core/mobile/order/")) {
            $zym_var_32 = str_replace("/core/mobile/order/", "/", $zym_var_32);
        }
        $zym_var_29 = $zym_var_31["openid"];
        $zym_var_28 = pdo_fetchall("select g.id,g.title,og.realprice,og.total,og.price,og.optionname as optiontitle,g.noticeopenid,g.noticetype from " . tablename("ewei_shop_order_goods") . " og " . " left join " . tablename("ewei_shop_goods") . " g on g.id=og.goodsid " . " where og.uniacid=:uniacid and og.orderid=:orderid ", array(
            ":uniacid" => $_W["uniacid"],
            ":orderid" => $zym_var_33
        ));
        $zym_var_24 = '';
        foreach ($zym_var_28 as $zym_var_25) {
            $zym_var_24 .= "" . $zym_var_25["title"] . "( ";
            if (!empty($zym_var_25["optiontitle"])) {
                $zym_var_24 .= " 规格: " . $zym_var_25["optiontitle"];
            }
            $zym_var_24 .= " 单价: " . ($zym_var_25["realprice"] / $zym_var_25["total"]) . " 数量: " . $zym_var_25["total"] . " 总价: " . $zym_var_25["realprice"] . "); ";
        }
        $zym_var_26 = " 订单总价: " . $zym_var_31["price"] . "(包含运费:" . $zym_var_31["dispatchprice"] . ")";
        $zym_var_27 = m("member")->getMember($zym_var_29);
        $zym_var_34 = unserialize($zym_var_27["noticeset"]);
        if (!is_array($zym_var_34)) {
            $zym_var_34 = array();
        }
        $zym_var_39 = m("common")->getSysset();
        $zym_var_41 = $zym_var_39["shop"];
        $zym_var_42 = $zym_var_39["notice"];
        if ($zym_var_30) {
            $zym_var_43 = array(
                '0' => "退款",
                "1" => "退货退款",
                "2" => "换货"
            );
            if (!empty($zym_var_31["refundid"])) {
                $zym_var_40 = pdo_fetch("select * from " . tablename("ewei_shop_order_refund") . " where id=:id limit 1", array(
                    ":id" => $zym_var_31["refundid"]
                ));
                if (empty($zym_var_40)) {
                    return;
                }
                if (empty($zym_var_40["status"])) {
                    $zym_var_35 = array(
                        "first" => array(
                            "value" => "您的" . $zym_var_43[$zym_var_40["rtype"]] . "申请已经提交！",
                            "color" => "#4a5077"
                        ),
                        "orderProductPrice" => array(
                            "title" => "退款金额",
                            "value" => $zym_var_40["rtype"] == 3 ? "-" : ("¥" . $zym_var_40["applyprice"] . "元"),
                            "color" => "#4a5077"
                        ),
                        "orderProductName" => array(
                            "title" => "商品详情",
                            "value" => $zym_var_24 . $zym_var_26,
                            "color" => "#4a5077"
                        ),
                        "orderName" => array(
                            "title" => "订单编号",
                            "value" => $zym_var_31["ordersn"],
                            "color" => "#4a5077"
                        ),
                        "remark" => array(
                            "value" => "
等待商家确认" . $zym_var_43[$zym_var_40["rtype"]] . "信息！",
                            "color" => "#4a5077"
                        )
                    );
                    if (!empty($zym_var_42["refund"]) && empty($zym_var_34["refund"])) {
                        m("message")->sendTplNotice($zym_var_29, $zym_var_42["refund"], $zym_var_35, $zym_var_32);
                    } else if (empty($zym_var_34["refund"])) {
                        m("message")->sendCustomNotice($zym_var_29, $zym_var_35, $zym_var_32);
                    }
                } else if ($zym_var_40["status"] == 3) {
                    $zym_var_36 = iunserializer($zym_var_40["refundaddress"]);
                    $zym_var_37 = "退货地址: " . $zym_var_36["province"] . " " . $zym_var_36["city"] . " " . $zym_var_36["area"] . " " . $zym_var_36["address"] . " 收件人: " . $zym_var_36["name"] . " (" . $zym_var_36["mobile"] . ")(" . $zym_var_36["tel"] . ") ";
                    $zym_var_35 = array(
                        "first" => array(
                            "value" => "您的" . $zym_var_43[$zym_var_40["rtype"]] . "申请已经通过！",
                            "color" => "#4a5077"
                        ),
                        "orderProductPrice" => array(
                            "title" => "退款金额",
                            "value" => $zym_var_40["rtype"] == 3 ? "-" : ("¥" . $zym_var_40["applyprice"] . "元"),
                            "color" => "#4a5077"
                        ),
                        "orderProductName" => array(
                            "title" => "商品详情",
                            "value" => $zym_var_24 . $zym_var_26,
                            "color" => "#4a5077"
                        ),
                        "orderName" => array(
                            "title" => "订单编号",
                            "value" => $zym_var_31["ordersn"],
                            "color" => "#4a5077"
                        ),
                        "remark" => array(
                            "value" => "
请您根据商家提供的退货地址将商品寄回！" . $zym_var_37 . "",
                            "color" => "#4a5077"
                        )
                    );
                    if (!empty($zym_var_42["refund"]) && empty($zym_var_34["refund"])) {
                        m("message")->sendTplNotice($zym_var_29, $zym_var_42["refund"], $zym_var_35, $zym_var_32);
                    } else if (empty($zym_var_34["refund"])) {
                        m("message")->sendCustomNotice($zym_var_29, $zym_var_35, $zym_var_32);
                    }
                } else if ($zym_var_40["status"] == 5) {
                    if (!empty($zym_var_31["address"])) {
                        $zym_var_38 = iunserializer($zym_var_31["address_send"]);
                        if (!is_array($zym_var_38)) {
                            $zym_var_38 = iunserializer($zym_var_31["address"]);
                            if (!is_array($zym_var_38)) {
                                $zym_var_38 = pdo_fetch("select id,realname,mobile,address,province,city,area from " . tablename("ewei_shop_member_address") . " where id=:id and uniacid=:uniacid limit 1", array(
                                    ":id" => $zym_var_31["addressid"],
                                    ":uniacid" => $_W["uniacid"]
                                ));
                            }
                        }
                    }
                    if (empty($zym_var_38)) {
                        return;
                    }
                    $zym_var_35 = array(
                        "first" => array(
                            "value" => "您的换货物品已经发货！",
                            "color" => "#4a5077"
                        ),
                        "keyword1" => array(
                            "title" => "订单内容",
                            "value" => "【" . $zym_var_31["ordersn"] . "】" . $zym_var_24,
                            "color" => "#4a5077"
                        ),
                        "keyword2" => array(
                            "title" => "物流服务",
                            "value" => $zym_var_40["rexpresscom"],
                            "color" => "#4a5077"
                        ),
                        "keyword3" => array(
                            "title" => "快递单号",
                            "value" => $zym_var_40["rexpresssn"],
                            "color" => "#4a5077"
                        ),
                        "keyword4" => array(
                            "title" => "收货信息",
                            "value" => "地址: " . $zym_var_38["province"] . " " . $zym_var_38["city"] . " " . $zym_var_38["area"] . " " . $zym_var_38["address"] . "收件人: " . $zym_var_38["realname"] . " (" . $zym_var_38["mobile"] . ") ",
                            "color" => "#4a5077"
                        ),
                        "remark" => array(
                            "value" => "
我们正加速送到您的手上，请您耐心等候。",
                            "color" => "#4a5077"
                        )
                    );
                    if (!empty($zym_var_42["send"]) && empty($zym_var_34["send"])) {
                        m("message")->sendTplNotice($zym_var_29, $zym_var_42["send"], $zym_var_35, $zym_var_32);
                    } else if (empty($zym_var_34["send"])) {
                        m("message")->sendCustomNotice($zym_var_29, $zym_var_35, $zym_var_32);
                    }
                } else if ($zym_var_40["status"] == 1) {
                    if ($zym_var_40["rtype"] == 2) {
                        $zym_var_35 = array(
                            "first" => array(
                                "value" => "您的订单已经完成换货！",
                                "color" => "#4a5077"
                            ),
                            "orderProductPrice" => array(
                                "title" => "退款金额",
                                "value" => "-",
                                "color" => "#4a5077"
                            ),
                            "orderProductName" => array(
                                "title" => "商品详情",
                                "value" => $zym_var_24 . $zym_var_26,
                                "color" => "#4a5077"
                            ),
                            "orderName" => array(
                                "title" => "订单编号",
                                "value" => $zym_var_31["ordersn"],
                                "color" => "#4a5077"
                            ),
                            "remark" => array(
                                "value" => "
 换货成功！
【" . $zym_var_41["name"] . "】期待您再次购物！",
                                "color" => "#4a5077"
                            )
                        );
                    } else {
                        $zym_var_44 = '';
                        if (empty($zym_var_40["refundtype"])) {
                            $zym_var_44 = ", 已经退回您的余额账户，请留意查收！";
                        } else if ($zym_var_40["refundtype"] == 1) {
                            $zym_var_44 = ", 已经退回您的对应支付渠道（如银行卡，微信钱包等, 具体到账时间请您查看微信支付通知)，请留意查收！";
                        } else {
                            $zym_var_44 = ", 请联系客服进行退款事项！";
                        }
                        $zym_var_35 = array(
                            "first" => array(
                                "value" => "您的订单已经完成退款！",
                                "color" => "#4a5077"
                            ),
                            "orderProductPrice" => array(
                                "title" => "退款金额",
                                "value" => "¥" . $zym_var_40["price"] . "元",
                                "color" => "#4a5077"
                            ),
                            "orderProductName" => array(
                                "title" => "商品详情",
                                "value" => $zym_var_24 . $zym_var_26,
                                "color" => "#4a5077"
                            ),
                            "orderName" => array(
                                "title" => "订单编号",
                                "value" => $zym_var_31["ordersn"],
                                "color" => "#4a5077"
                            ),
                            "remark" => array(
                                "value" => "
 退款金额 ¥" . $zym_var_40["price"] . "{$zym_var_44}\r\n 【" . $zym_var_41["name"] . "】期待您再次购物！",
                                "color" => "#4a5077"
                            )
                        );
                    }
                    if (!empty($zym_var_42["refund1"]) && empty($zym_var_34["refund1"])) {
                        m("message")->sendTplNotice($zym_var_29, $zym_var_42["refund1"], $zym_var_35, $zym_var_32);
                    } else if (empty($zym_var_34["refund1"])) {
                        m("message")->sendCustomNotice($zym_var_29, $zym_var_35, $zym_var_32);
                    }
                } elseif ($zym_var_40["status"] == -1) {
                    $zym_var_23 = "
驳回原因: " . $zym_var_40["reply"];
                    if (!empty($zym_var_41["phone"])) {
                        $zym_var_23 .= "
客服电话:  " . $zym_var_41["phone"];
                    }
                    $zym_var_35 = array(
                        "first" => array(
                            "value" => "您的" . $zym_var_43[$zym_var_40["rtype"]] . "申请被商家驳回，可与商家协商沟通！",
                            "color" => "#4a5077"
                        ),
                        "orderProductPrice" => array(
                            "title" => "退款金额",
                            "value" => "¥" . $zym_var_40["price"] . "元",
                            "color" => "#4a5077"
                        ),
                        "orderProductName" => array(
                            "title" => "商品详情",
                            "value" => $zym_var_24 . $zym_var_26,
                            "color" => "#4a5077"
                        ),
                        "orderName" => array(
                            "title" => "订单编号",
                            "value" => $zym_var_31["ordersn"],
                            "color" => "#4a5077"
                        ),
                        "remark" => array(
                            "value" => $zym_var_23,
                            "color" => "#4a5077"
                        )
                    );
                    if (!empty($zym_var_42["refund2"]) && empty($zym_var_34["refund2"])) {
                        m("message")->sendTplNotice($zym_var_29, $zym_var_42["refund2"], $zym_var_35, $zym_var_32);
                    } else if (empty($zym_var_34["refund2"])) {
                        m("message")->sendCustomNotice($zym_var_29, $zym_var_35, $zym_var_32);
                    }
                }
                return;
            }
        }
        $zym_var_22 = '';
        if (!empty($zym_var_31["address"])) {
            $zym_var_38 = iunserializer($zym_var_31["address_send"]);
            if (!is_array($zym_var_38)) {
                $zym_var_38 = iunserializer($zym_var_31["address"]);
                if (!is_array($zym_var_38)) {
                    $zym_var_38 = pdo_fetch("select id,realname,mobile,address,province,city,area from " . tablename("ewei_shop_member_address") . " where id=:id and uniacid=:uniacid limit 1", array(
                        ":id" => $zym_var_31["addressid"],
                        ":uniacid" => $_W["uniacid"]
                    ));
                }
            }
            if (!empty($zym_var_38)) {
                $zym_var_22 = "收件人: " . $zym_var_38["realname"] . "
联系电话: " . $zym_var_38["mobile"] . "
收货地址: " . $zym_var_38["province"] . $zym_var_38["city"] . $zym_var_38["area"] . " " . $zym_var_38["address"];
            }
        } else {
            $zym_var_7 = iunserializer($zym_var_31["carrier"]);
            if (is_array($zym_var_7)) {
                $zym_var_22 = "联系人: " . $zym_var_7["carrier_realname"] . "
联系电话: " . $zym_var_7["carrier_mobile"];
            }
        }
        if ($zym_var_31["status"] == -1) {
            if (empty($zym_var_31["dispatchtype"])) {
                $zym_var_8 = array(
                    "title" => "收货信息",
                    "value" => "收货地址: " . $zym_var_38["province"] . " " . $zym_var_38["city"] . " " . $zym_var_38["area"] . " " . $zym_var_38["address"] . " 收件人: " . $zym_var_38["realname"] . " 联系电话: " . $zym_var_38["mobile"],
                    "color" => "#4a5077"
                );
            } else {
                $zym_var_8 = array(
                    "title" => "收货信息",
                    "value" => "自提地点: " . $zym_var_7["address"] . " 联系人: " . $zym_var_7["realname"] . " 联系电话: " . $zym_var_7["mobile"],
                    "color" => "#4a5077"
                );
            }
            $zym_var_35 = array(
                "first" => array(
                    "value" => "您的订单已取消!",
                    "color" => "#4a5077"
                ),
                "orderProductPrice" => array(
                    "title" => "订单金额",
                    "value" => "¥" . $zym_var_31["price"] . "元(含运费" . $zym_var_31["dispatchprice"] . "元)",
                    "color" => "#4a5077"
                ),
                "orderProductName" => array(
                    "title" => "商品详情",
                    "value" => $zym_var_24,
                    "color" => "#4a5077"
                ),
                "orderAddress" => $zym_var_8,
                "orderName" => array(
                    "title" => "订单编号",
                    "value" => $zym_var_31["ordersn"],
                    "color" => "#4a5077"
                ),
                "remark" => array(
                    "value" => "
【" . $zym_var_41["name"] . "】欢迎您的再次购物！",
                    "color" => "#4a5077"
                )
            );
            if (!empty($zym_var_42["cancel"]) && empty($zym_var_34["cancel"])) {
                m("message")->sendTplNotice($zym_var_29, $zym_var_42["cancel"], $zym_var_35, $zym_var_32);
            } else if (empty($zym_var_34["cancel"])) {
                m("message")->sendCustomNotice($zym_var_29, $zym_var_35, $zym_var_32);
            }
        } else if ($zym_var_31["status"] == 0) {
            $zym_var_9 = explode(",", $zym_var_42["newtype"]);
            if (empty($zym_var_42["newtype"]) || (is_array($zym_var_9) && in_array(0, $zym_var_9))) {
                $zym_var_23 = "
订单下单成功,请到后台查看!";
                if (!empty($zym_var_22)) {
                    $zym_var_23 .= "
下单者信息:
" . $zym_var_22;
                }
                $zym_var_35 = array(
                    "first" => array(
                        "value" => "订单下单通知!",
                        "color" => "#4a5077"
                    ),
                    "keyword1" => array(
                        "title" => "时间",
                        "value" => date("Y-m-d H:i:s", $zym_var_31["createtime"]),
                        "color" => "#4a5077"
                    ),
                    "keyword2" => array(
                        "title" => "商品名称",
                        "value" => $zym_var_24 . $zym_var_26,
                        "color" => "#4a5077"
                    ),
                    "keyword3" => array(
                        "title" => "订单号",
                        "value" => $zym_var_31["ordersn"],
                        "color" => "#4a5077"
                    ),
                    "remark" => array(
                        "value" => $zym_var_23,
                        "color" => "#4a5077"
                    )
                );
                $zym_var_10 = m("common")->getAccount();
                if (!empty($zym_var_42["openid"])) {
                    $zym_var_6 = explode(",", $zym_var_42["openid"]);
                    foreach ($zym_var_6 as $zym_var_5) {
                        if (empty($zym_var_5)) {
                            continue;
                        }
                        if (!empty($zym_var_42["new"])) {
                            m("message")->sendTplNotice($zym_var_5, $zym_var_42["new"], $zym_var_35, '', $zym_var_10);
                        } else {
                            m("message")->sendCustomNotice($zym_var_5, $zym_var_35, '', $zym_var_10);
                        }
                    }
                }
            }
            $zym_var_23 = "
商品已经下单，请及时备货，谢谢!";
            if (!empty($zym_var_22)) {
                $zym_var_23 .= "
下单者信息:
" . $zym_var_22;
            }
            foreach ($zym_var_28 as $zym_var_25) {
                if (!empty($zym_var_25["noticeopenid"])) {
                    $zym_var_1 = explode(",", $zym_var_25["noticetype"]);
                    if (empty($zym_var_25["noticetype"]) || (is_array($zym_var_1) && in_array(0, $zym_var_1))) {
                        $zym_var_2 = $zym_var_25["title"] . "( ";
                        if (!empty($zym_var_25["optiontitle"])) {
                            $zym_var_2 .= " 规格: " . $zym_var_25["optiontitle"];
                        }
                        $zym_var_2 .= " 单价: " . ($zym_var_25["realprice"] / $zym_var_25["total"]) . " 数量: " . $zym_var_25["total"] . " 总价: " . $zym_var_25["realprice"] . "); ";
                        $zym_var_35 = array(
                            "first" => array(
                                "value" => "商品下单通知!",
                                "color" => "#4a5077"
                            ),
                            "keyword1" => array(
                                "title" => "时间",
                                "value" => date("Y-m-d H:i:s", $zym_var_31["createtime"]),
                                "color" => "#4a5077"
                            ),
                            "keyword2" => array(
                                "title" => "商品名称",
                                "value" => $zym_var_2,
                                "color" => "#4a5077"
                            ),
                            "keyword3" => array(
                                "title" => "订单号",
                                "value" => $zym_var_31["ordersn"],
                                "color" => "#4a5077"
                            ),
                            "remark" => array(
                                "value" => $zym_var_23,
                                "color" => "#4a5077"
                            )
                        );
                        if (!empty($zym_var_42["new"])) {
                            m("message")->sendTplNotice($zym_var_25["noticeopenid"], $zym_var_42["new"], $zym_var_35, '', $zym_var_10);
                        } else {
                            m("message")->sendCustomNotice($zym_var_25["noticeopenid"], $zym_var_35, '', $zym_var_10);
                        }
                    }
                }
            }
            if (!empty($zym_var_31["addressid"])) {
                $zym_var_23 = "
您的订单我们已经收到，支付后我们将尽快配送~~";
            } else if (!empty($zym_var_31["isverify"])) {
                $zym_var_23 = "
您的订单我们已经收到，支付后您就可以到店使用了~~";
            } else if (!empty($zym_var_31["virtual"])) {
                $zym_var_23 = "
您的订单我们已经收到，支付后系统将会自动发货~~";
            } else {
                $zym_var_23 = "
您的订单我们已经收到，支付后您就可以到自提点提货物了~~";
            }
            $zym_var_35 = array(
                "first" => array(
                    "value" => "您的订单已提交成功！",
                    "color" => "#4a5077"
                ),
                "keyword1" => array(
                    "title" => "店铺",
                    "value" => $zym_var_41["name"],
                    "color" => "#4a5077"
                ),
                "keyword2" => array(
                    "title" => "下单时间",
                    "value" => date("Y-m-d H:i:s", $zym_var_31["createtime"]),
                    "color" => "#4a5077"
                ),
                "keyword3" => array(
                    "title" => "商品",
                    "value" => $zym_var_24,
                    "color" => "#4a5077"
                ),
                "keyword4" => array(
                    "title" => "金额",
                    "value" => "¥" . $zym_var_31["price"] . "元(含运费" . $zym_var_31["dispatchprice"] . "元)",
                    "color" => "#4a5077"
                ),
                "remark" => array(
                    "value" => $zym_var_23,
                    "color" => "#4a5077"
                )
            );
            if (!empty($zym_var_42["submit"]) && empty($zym_var_34["submit"])) {
                m("message")->sendTplNotice($zym_var_29, $zym_var_42["submit"], $zym_var_35, $zym_var_32);
            } else if (empty($zym_var_34["submit"])) {
                m("message")->sendCustomNotice($zym_var_29, $zym_var_35, $zym_var_32);
            }
        } else if ($zym_var_31["status"] == 1) {
            $zym_var_9 = explode(",", $zym_var_42["newtype"]);
            if ($zym_var_42["newtype"] == 1 || (is_array($zym_var_9) && in_array(1, $zym_var_9))) {
                $zym_var_23 = "
订单已经下单支付，请及时备货，谢谢!";
                if (!empty($zym_var_22)) {
                    $zym_var_23 .= "
购买者信息:
" . $zym_var_22;
                }
                $zym_var_35 = array(
                    "first" => array(
                        "value" => "订单下单支付通知!",
                        "color" => "#4a5077"
                    ),
                    "keyword1" => array(
                        "title" => "时间",
                        "value" => date("Y-m-d H:i:s", $zym_var_31["createtime"]),
                        "color" => "#4a5077"
                    ),
                    "keyword2" => array(
                        "title" => "商品名称",
                        "value" => $zym_var_24 . $zym_var_26,
                        "color" => "#4a5077"
                    ),
                    "keyword3" => array(
                        "title" => "订单号",
                        "value" => $zym_var_31["ordersn"],
                        "color" => "#4a5077"
                    ),
                    "remark" => array(
                        "value" => $zym_var_23,
                        "color" => "#4a5077"
                    )
                );
                $zym_var_10 = m("common")->getAccount();
                if (!empty($zym_var_42["openid"])) {
                    $zym_var_6 = explode(",", $zym_var_42["openid"]);
                    foreach ($zym_var_6 as $zym_var_5) {
                        if (empty($zym_var_5)) {
                            continue;
                        }
                        if (!empty($zym_var_42["new"])) {
                            m("message")->sendTplNotice($zym_var_5, $zym_var_42["new"], $zym_var_35, '', $zym_var_10);
                        } else {
                            m("message")->sendCustomNotice($zym_var_5, $zym_var_35, '', $zym_var_10);
                        }
                    }
                }
            }
            $zym_var_23 = "
商品已经下单支付，请及时备货，谢谢!";
            if (!empty($zym_var_22)) {
                $zym_var_23 .= "
购买者信息:
" . $zym_var_22;
            }
            foreach ($zym_var_28 as $zym_var_25) {
                $zym_var_1 = explode(",", $zym_var_25["noticetype"]);
                if ($zym_var_25["noticetype"] == "1" || (is_array($zym_var_1) && in_array(1, $zym_var_1))) {
                    $zym_var_2 = $zym_var_25["title"] . "( ";
                    if (!empty($zym_var_25["optiontitle"])) {
                        $zym_var_2 .= " 规格: " . $zym_var_25["optiontitle"];
                    }
                    $zym_var_2 .= " 单价: " . ($zym_var_25["price"] / $zym_var_25["total"]) . " 数量: " . $zym_var_25["total"] . " 总价: " . $zym_var_25["price"] . "); ";
                    $zym_var_35 = array(
                        "first" => array(
                            "value" => "商品下单支付通知!",
                            "color" => "#4a5077"
                        ),
                        "keyword1" => array(
                            "title" => "时间",
                            "value" => date("Y-m-d H:i:s", $zym_var_31["createtime"]),
                            "color" => "#4a5077"
                        ),
                        "keyword2" => array(
                            "title" => "商品名称",
                            "value" => $zym_var_2,
                            "color" => "#4a5077"
                        ),
                        "keyword3" => array(
                            "title" => "订单号",
                            "value" => $zym_var_31["ordersn"],
                            "color" => "#4a5077"
                        ),
                        "remark" => array(
                            "value" => $zym_var_23,
                            "color" => "#4a5077"
                        )
                    );
                    if (!empty($zym_var_42["new"])) {
                        m("message")->sendTplNotice($zym_var_25["noticeopenid"], $zym_var_42["new"], $zym_var_35, '', $zym_var_10);
                    } else {
                        m("message")->sendCustomNotice($zym_var_25["noticeopenid"], $zym_var_35, '', $zym_var_10);
                    }
                }
            }
            $zym_var_23 = "
【" . $zym_var_41["name"] . "】欢迎您的再次购物！";
            if ($zym_var_31["isverify"]) {
                $zym_var_23 = "
点击订单详情查看可消费门店, 【" . $zym_var_41["name"] . "】欢迎您的再次购物！";
            }
            $zym_var_35 = array(
                "first" => array(
                    "value" => "您已支付成功订单！",
                    "color" => "#4a5077"
                ),
                "keyword1" => array(
                    "title" => "订单",
                    "value" => $zym_var_31["ordersn"],
                    "color" => "#4a5077"
                ),
                "keyword2" => array(
                    "title" => "支付状态",
                    "value" => "支付成功",
                    "color" => "#4a5077"
                ),
                "keyword3" => array(
                    "title" => "支付日期",
                    "value" => date("Y-m-d H:i:s", $zym_var_31["paytime"]),
                    "color" => "#4a5077"
                ),
                "keyword4" => array(
                    "title" => "商户",
                    "value" => $zym_var_41["name"],
                    "color" => "#4a5077"
                ),
                "keyword5" => array(
                    "title" => "金额",
                    "value" => "¥" . $zym_var_31["price"] . "元(含运费" . $zym_var_31["dispatchprice"] . "元)",
                    "color" => "#4a5077"
                ),
                "remark" => array(
                    "value" => $zym_var_23,
                    "color" => "#4a5077"
                )
            );
            $zym_var_3  = $zym_var_32;
            if (strexists($zym_var_3, "/addons/ewei_shop/")) {
                $zym_var_3 = str_replace("/addons/ewei_shop/", "/", $zym_var_3);
            }
            if (strexists($zym_var_3, "/core/mobile/order/")) {
                $zym_var_3 = str_replace("/core/mobile/order/", "/", $zym_var_3);
            }
            if (!empty($zym_var_42["pay"]) && empty($zym_var_34["pay"])) {
                m("message")->sendTplNotice($zym_var_29, $zym_var_42["pay"], $zym_var_35, $zym_var_3);
            } else if (empty($zym_var_34["pay"])) {
                m("message")->sendCustomNotice($zym_var_29, $zym_var_35, $zym_var_3);
            }
            if ($zym_var_31["dispatchtype"] == 1 && empty($zym_var_31["isverify"])) {
                $zym_var_7 = iunserializer($zym_var_31["carrier"]);
                if (!is_array($zym_var_7)) {
                    return;
                }
                $zym_var_35 = array(
                    "first" => array(
                        "value" => "自提订单提交成功!",
                        "color" => "#4a5077"
                    ),
                    "keyword1" => array(
                        "title" => "自提码",
                        "value" => $zym_var_31["ordersn"],
                        "color" => "#4a5077"
                    ),
                    "keyword2" => array(
                        "title" => "商品详情",
                        "value" => $zym_var_24 . $zym_var_26,
                        "color" => "#4a5077"
                    ),
                    "keyword3" => array(
                        "title" => "提货地址",
                        "value" => $zym_var_7["address"],
                        "color" => "#4a5077"
                    ),
                    "keyword4" => array(
                        "title" => "提货时间",
                        "value" => $zym_var_7["content"],
                        "color" => "#4a5077"
                    ),
                    "remark" => array(
                        "value" => "
请您到选择的自提点进行取货, 自提联系人: " . $zym_var_7["realname"] . " 联系电话: " . $zym_var_7["mobile"],
                        "color" => "#4a5077"
                    )
                );
                if (!empty($zym_var_42["carrier"]) && empty($zym_var_34["carrier"])) {
                    m("message")->sendTplNotice($zym_var_29, $zym_var_42["carrier"], $zym_var_35, $zym_var_32);
                } else if (empty($zym_var_34["carrier"])) {
                    m("message")->sendCustomNotice($zym_var_29, $zym_var_35, $zym_var_32);
                }
            }
        } else if ($zym_var_31["status"] == 2) {
            if (empty($zym_var_31["dispatchtype"])) {
                if (empty($zym_var_38)) {
                    return;
                }
                $zym_var_35 = array(
                    "first" => array(
                        "value" => "您的宝贝已经发货！",
                        "color" => "#4a5077"
                    ),
                    "keyword1" => array(
                        "title" => "订单内容",
                        "value" => "【" . $zym_var_31["ordersn"] . "】" . $zym_var_24 . $zym_var_26,
                        "color" => "#4a5077"
                    ),
                    "keyword2" => array(
                        "title" => "物流服务",
                        "value" => $zym_var_31["expresscom"],
                        "color" => "#4a5077"
                    ),
                    "keyword3" => array(
                        "title" => "快递单号",
                        "value" => $zym_var_31["expresssn"],
                        "color" => "#4a5077"
                    ),
                    "keyword4" => array(
                        "title" => "收货信息",
                        "value" => "地址: " . $zym_var_38["province"] . " " . $zym_var_38["city"] . " " . $zym_var_38["area"] . " " . $zym_var_38["address"] . "收件人: " . $zym_var_38["realname"] . " (" . $zym_var_38["mobile"] . ") ",
                        "color" => "#4a5077"
                    ),
                    "remark" => array(
                        "value" => "
我们正加速送到您的手上，请您耐心等候。",
                        "color" => "#4a5077"
                    )
                );
                if (!empty($zym_var_42["send"]) && empty($zym_var_34["send"])) {
                    m("message")->sendTplNotice($zym_var_29, $zym_var_42["send"], $zym_var_35, $zym_var_32);
                } else if (empty($zym_var_34["send"])) {
                    m("message")->sendCustomNotice($zym_var_29, $zym_var_35, $zym_var_32);
                }
            }
        } else if ($zym_var_31["status"] == 3) {
            $zym_var_4 = p("virtual");
            if ($zym_var_4 && !empty($zym_var_31["virtual"])) {
                $zym_var_11 = $zym_var_4->getSet();
                $zym_var_12 = "
" . $zym_var_22 . "
" . $zym_var_31["virtual_str"];
                $zym_var_35 = array(
                    "first" => array(
                        "value" => "您购物的物品已自动发货!",
                        "color" => "#4a5077"
                    ),
                    "keyword1" => array(
                        "title" => "订单金额",
                        "value" => "¥" . $zym_var_31["price"] . "元",
                        "color" => "#4a5077"
                    ),
                    "keyword2" => array(
                        "title" => "商品详情",
                        "value" => $zym_var_24,
                        "color" => "#4a5077"
                    ),
                    "keyword3" => array(
                        "title" => "收货信息",
                        "value" => $zym_var_12,
                        "color" => "#4a5077"
                    ),
                    "remark" => array(
                        "title" => '',
                        "value" => "
【" . $zym_var_41["name"] . "】感谢您的支持与厚爱，欢迎您的再次购物！",
                        "color" => "#4a5077"
                    )
                );
                if (!empty($zym_var_11["tm"]["send"]) && empty($zym_var_34["finish"])) {
                    m("message")->sendTplNotice($zym_var_29, $zym_var_11["tm"]["send"], $zym_var_35, $zym_var_32);
                } else if (empty($zym_var_34["finish"])) {
                    m("message")->sendCustomNotice($zym_var_29, $zym_var_35, $zym_var_32);
                }
                $zym_var_19 = "买家购买的商品已经自动发货!";
                $zym_var_23 = "
发货信息:" . $zym_var_12;
                $zym_var_9  = explode(",", $zym_var_42["newtype"]);
                if ($zym_var_42["newtype"] == 2 || (is_array($zym_var_9) && in_array(2, $zym_var_9))) {
                    $zym_var_35 = array(
                        "first" => array(
                            "value" => $zym_var_19,
                            "color" => "#4a5077"
                        ),
                        "keyword1" => array(
                            "title" => "订单号",
                            "value" => $zym_var_31["ordersn"],
                            "color" => "#4a5077"
                        ),
                        "keyword2" => array(
                            "title" => "商品名称",
                            "value" => $zym_var_24 . $zym_var_26,
                            "color" => "#4a5077"
                        ),
                        "keyword3" => array(
                            "title" => "下单时间",
                            "value" => date("Y-m-d H:i:s", $zym_var_31["createtime"]),
                            "color" => "#4a5077"
                        ),
                        "keyword4" => array(
                            "title" => "发货时间",
                            "value" => date("Y-m-d H:i:s", $zym_var_31["sendtime"]),
                            "color" => "#4a5077"
                        ),
                        "keyword5" => array(
                            "title" => "确认收货时间",
                            "value" => date("Y-m-d H:i:s", $zym_var_31["finishtime"]),
                            "color" => "#4a5077"
                        ),
                        "remark" => array(
                            "title" => '',
                            "value" => $zym_var_23,
                            "color" => "#4a5077"
                        )
                    );
                    $zym_var_10 = m("common")->getAccount();
                    if (!empty($zym_var_42["openid"])) {
                        $zym_var_6 = explode(",", $zym_var_42["openid"]);
                        foreach ($zym_var_6 as $zym_var_5) {
                            if (empty($zym_var_5)) {
                                continue;
                            }
                            if (!empty($zym_var_42["finish"])) {
                                m("message")->sendTplNotice($zym_var_5, $zym_var_42["finish"], $zym_var_35, '', $zym_var_10);
                            } else {
                                m("message")->sendCustomNotice($zym_var_5, $zym_var_35, '', $zym_var_10);
                            }
                        }
                    }
                }
                foreach ($zym_var_28 as $zym_var_25) {
                    $zym_var_1 = explode(",", $zym_var_25["noticetype"]);
                    if ($zym_var_25["noticetype"] == "2" || (is_array($zym_var_1) && in_array(2, $zym_var_1))) {
                        $zym_var_2 = $zym_var_25["title"] . "( ";
                        if (!empty($zym_var_25["optiontitle"])) {
                            $zym_var_2 .= " 规格: " . $zym_var_25["optiontitle"];
                        }
                        $zym_var_2 .= " 单价: " . ($zym_var_25["price"] / $zym_var_25["total"]) . " 数量: " . $zym_var_25["total"] . " 总价: " . $zym_var_25["price"] . "); ";
                        $zym_var_35 = array(
                            "first" => array(
                                "value" => $zym_var_19,
                                "color" => "#4a5077"
                            ),
                            "keyword1" => array(
                                "title" => "订单号",
                                "value" => $zym_var_31["ordersn"],
                                "color" => "#4a5077"
                            ),
                            "keyword2" => array(
                                "title" => "商品名称",
                                "value" => $zym_var_2,
                                "color" => "#4a5077"
                            ),
                            "keyword3" => array(
                                "title" => "下单时间",
                                "value" => date("Y-m-d H:i:s", $zym_var_31["createtime"]),
                                "color" => "#4a5077"
                            ),
                            "keyword4" => array(
                                "title" => "发货时间",
                                "value" => date("Y-m-d H:i:s", $zym_var_31["sendtime"]),
                                "color" => "#4a5077"
                            ),
                            "keyword5" => array(
                                "title" => "确认收货时间",
                                "value" => date("Y-m-d H:i:s", $zym_var_31["finishtime"]),
                                "color" => "#4a5077"
                            ),
                            "remark" => array(
                                "title" => '',
                                "value" => $zym_var_23,
                                "color" => "#4a5077"
                            )
                        );
                        if (!empty($zym_var_42["finish"])) {
                            m("message")->sendTplNotice($zym_var_25["noticeopenid"], $zym_var_42["finish"], $zym_var_35, '', $zym_var_10);
                        } else {
                            m("message")->sendCustomNotice($zym_var_25["noticeopenid"], $zym_var_35, '', $zym_var_10);
                        }
                    }
                }
            } else {
                $zym_var_35 = array(
                    "first" => array(
                        "value" => "亲, 您购买的宝贝已经确认收货!",
                        "color" => "#4a5077"
                    ),
                    "keyword1" => array(
                        "title" => "订单号",
                        "value" => $zym_var_31["ordersn"],
                        "color" => "#4a5077"
                    ),
                    "keyword2" => array(
                        "title" => "商品名称",
                        "value" => $zym_var_24 . $zym_var_26,
                        "color" => "#4a5077"
                    ),
                    "keyword3" => array(
                        "title" => "下单时间",
                        "value" => date("Y-m-d H:i:s", $zym_var_31["createtime"]),
                        "color" => "#4a5077"
                    ),
                    "keyword4" => array(
                        "title" => "发货时间",
                        "value" => date("Y-m-d H:i:s", $zym_var_31["sendtime"]),
                        "color" => "#4a5077"
                    ),
                    "keyword5" => array(
                        "title" => "确认收货时间",
                        "value" => date("Y-m-d H:i:s", $zym_var_31["finishtime"]),
                        "color" => "#4a5077"
                    ),
                    "remark" => array(
                        "title" => '',
                        "value" => "【" . $zym_var_41["name"] . "】感谢您的支持与厚爱，欢迎您的再次购物！",
                        "color" => "#4a5077"
                    )
                );
                if (!empty($zym_var_42["finish"]) && empty($zym_var_34["finish"])) {
                    m("message")->sendTplNotice($zym_var_29, $zym_var_42["finish"], $zym_var_35, $zym_var_32);
                } else if (empty($zym_var_34["finish"])) {
                    m("message")->sendCustomNotice($zym_var_29, $zym_var_35, $zym_var_32);
                }
                $zym_var_19 = "买家购买的商品已经确认收货!";
                if ($zym_var_31["isverify"] == 1) {
                    $zym_var_19 = "买家购买的商品已经确认核销!";
                }
                $zym_var_23 = "";
                if (!empty($zym_var_22)) {
                    $zym_var_23 = "购买者信息:" . $zym_var_22;
                }
                $zym_var_9 = explode(",", $zym_var_42["newtype"]);
                if ($zym_var_42["newtype"] == 2 || (is_array($zym_var_9) && in_array(2, $zym_var_9))) {
                    $zym_var_35 = array(
                        "first" => array(
                            "value" => $zym_var_19,
                            "color" => "#4a5077"
                        ),
                        "keyword1" => array(
                            "title" => "订单号",
                            "value" => $zym_var_31["ordersn"],
                            "color" => "#4a5077"
                        ),
                        "keyword2" => array(
                            "title" => "商品名称",
                            "value" => $zym_var_24 . $zym_var_26,
                            "color" => "#4a5077"
                        ),
                        "keyword3" => array(
                            "title" => "下单时间",
                            "value" => date("Y-m-d H:i:s", $zym_var_31["createtime"]),
                            "color" => "#4a5077"
                        ),
                        "keyword4" => array(
                            "title" => "发货时间",
                            "value" => date("Y-m-d H:i:s", $zym_var_31["sendtime"]),
                            "color" => "#4a5077"
                        ),
                        "keyword5" => array(
                            "title" => "确认收货时间",
                            "value" => date("Y-m-d H:i:s", $zym_var_31["finishtime"]),
                            "color" => "#4a5077"
                        ),
                        "remark" => array(
                            "title" => '',
                            "value" => $zym_var_23,
                            "color" => "#4a5077"
                        )
                    );
                    $zym_var_10 = m("common")->getAccount();
                    if (!empty($zym_var_42["openid"])) {
                        $zym_var_6 = explode(",", $zym_var_42["openid"]);
                        foreach ($zym_var_6 as $zym_var_5) {
                            if (empty($zym_var_5)) {
                                continue;
                            }
                            if (!empty($zym_var_42["finish"])) {
                                m("message")->sendTplNotice($zym_var_5, $zym_var_42["finish"], $zym_var_35, '', $zym_var_10);
                            } else {
                                m("message")->sendCustomNotice($zym_var_5, $zym_var_35, '', $zym_var_10);
                            }
                        }
                    }
                }
                foreach ($zym_var_28 as $zym_var_25) {
                    $zym_var_1 = explode(",", $zym_var_25["noticetype"]);
                    if ($zym_var_25["noticetype"] == "2" || (is_array($zym_var_1) && in_array(2, $zym_var_1))) {
                        $zym_var_2 = $zym_var_25["title"] . "( ";
                        if (!empty($zym_var_25["optiontitle"])) {
                            $zym_var_2 .= " 规格: " . $zym_var_25["optiontitle"];
                        }
                        $zym_var_2 .= " 单价: " . ($zym_var_25["price"] / $zym_var_25["total"]) . " 数量: " . $zym_var_25["total"] . " 总价: " . $zym_var_25["price"] . "); ";
                        $zym_var_35 = array(
                            "first" => array(
                                "value" => $zym_var_19,
                                "color" => "#4a5077"
                            ),
                            "keyword1" => array(
                                "title" => "订单号",
                                "value" => $zym_var_31["ordersn"],
                                "color" => "#4a5077"
                            ),
                            "keyword2" => array(
                                "title" => "商品名称",
                                "value" => $zym_var_2,
                                "color" => "#4a5077"
                            ),
                            "keyword3" => array(
                                "title" => "下单时间",
                                "value" => date("Y-m-d H:i:s", $zym_var_31["createtime"]),
                                "color" => "#4a5077"
                            ),
                            "keyword4" => array(
                                "title" => "发货时间",
                                "value" => date("Y-m-d H:i:s", $zym_var_31["sendtime"]),
                                "color" => "#4a5077"
                            ),
                            "keyword5" => array(
                                "title" => "确认收货时间",
                                "value" => date("Y-m-d H:i:s", $zym_var_31["finishtime"]),
                                "color" => "#4a5077"
                            ),
                            "remark" => array(
                                "title" => '',
                                "value" => $zym_var_23,
                                "color" => "#4a5077"
                            )
                        );
                        if (!empty($zym_var_42["finish"])) {
                            m("message")->sendTplNotice($zym_var_25["noticeopenid"], $zym_var_42["finish"], $zym_var_35, '', $zym_var_10);
                        } else {
                            m("message")->sendCustomNotice($zym_var_25["noticeopenid"], $zym_var_35, '', $zym_var_10);
                        }
                    }
                }
            }
        }
    }
    public function zymfunc_1($zym_var_29 = '', $zym_var_20 = null, $zym_var_21 = null)
    {
        global $_W, $_GPC;
        $zym_var_27 = m("member")->getMember($zym_var_29);
        $zym_var_34 = unserialize($zym_var_27["noticeset"]);
        if (!is_array($zym_var_34)) {
            $zym_var_34 = array();
        }
        $zym_var_41 = m("common")->getSysset("shop");
        $zym_var_42 = m("common")->getSysset("notice");
        $zym_var_32 = $_W["siteroot"] . "app/index.php?i=" . $_W["uniacid"] . "&c=entry&m=ewei_shop&do=member";
        if (strexists($zym_var_32, "/addons/ewei_shop/")) {
            $zym_var_32 = str_replace("/addons/ewei_shop/", "/", $zym_var_32);
        }
        if (strexists($zym_var_32, "/core/mobile/order/")) {
            $zym_var_32 = str_replace("/core/mobile/order/", "/", $zym_var_32);
        }
        if (!$zym_var_21) {
            $zym_var_21 = m("member")->getLevel($zym_var_29);
        }
        $zym_var_18 = empty($zym_var_41["levelname"]) ? "普通会员" : $zym_var_41["levelname"];
        $zym_var_35 = array(
            "first" => array(
                "value" => "亲爱的" . $zym_var_27["nickname"] . ", 恭喜您成功升级！",
                "color" => "#4a5077"
            ),
            "keyword1" => array(
                "title" => "任务名称",
                "value" => "会员升级",
                "color" => "#4a5077"
            ),
            "keyword2" => array(
                "title" => "通知类型",
                "value" => "您会员等级从 " . $zym_var_18 . " 升级为 " . $zym_var_21["levelname"] . ", 特此通知!",
                "color" => "#4a5077"
            ),
            "remark" => array(
                "value" => "
您即可享有" . $zym_var_21["levelname"] . "的专属优惠及服务！",
                "color" => "#4a5077"
            )
        );
        if (!empty($zym_var_42["upgrade"]) && empty($zym_var_34["upgrade"])) {
            m("message")->sendTplNotice($zym_var_29, $zym_var_42["upgrade"], $zym_var_35, $zym_var_32);
        } else if (empty($zym_var_34["upgrade"])) {
            m("message")->sendCustomNotice($zym_var_29, $zym_var_35, $zym_var_32);
        }
    }
    public function sendMemberLogMessage($zym_var_17 = '')
    {
        global $_W, $_GPC;
        $zym_var_13 = pdo_fetch("select * from " . tablename("ewei_shop_member_log") . " where id=:id and uniacid=:uniacid limit 1", array(
            ":id" => $zym_var_17,
            ":uniacid" => $_W["uniacid"]
        ));
        $zym_var_27 = m("member")->getMember($zym_var_13["openid"]);
        $zym_var_41 = m("common")->getSysset("shop");
        $zym_var_34 = unserialize($zym_var_27["noticeset"]);
        if (!is_array($zym_var_34)) {
            $zym_var_34 = array();
        }
        $zym_var_10 = m("common")->getAccount();
        if (!$zym_var_10) {
            return;
        }
        $zym_var_42 = m("common")->getSysset("notice");
        if ($zym_var_13["type"] == 0) {
            if ($zym_var_13["status"] == 1) {
                $zym_var_14 = "后台充值";
                if ($zym_var_13["rechargetype"] == "wechat") {
                    $zym_var_14 = "微信支付";
                } else if ($zym_var_13 == "alipay") {
                    $zym_var_14["rechargetype"] = "支付宝";
                }
                $zym_var_15 = "¥" . $zym_var_13["money"] . "元";
                if ($zym_var_13["gives"] > 0) {
                    $zym_var_16 = $zym_var_13["money"] + $zym_var_13["gives"];
                    $zym_var_15 .= "，系统赠送" . $zym_var_13["gives"] . "元，合计:" . $zym_var_16 . "元";
                }
                $zym_var_35 = array(
                    "first" => array(
                        "value" => "恭喜您充值成功!",
                        "color" => "#4a5077"
                    ),
                    "money" => array(
                        "title" => "充值金额",
                        "value" => $zym_var_15,
                        "color" => "#4a5077"
                    ),
                    "product" => array(
                        "title" => "充值方式",
                        "value" => $zym_var_14,
                        "color" => "#4a5077"
                    ),
                    "remark" => array(
                        "value" => "
谢谢您对我们的支持！",
                        "color" => "#4a5077"
                    )
                );
                $zym_var_32 = $_W["siteroot"] . "app/index.php?i=" . $_W["uniacid"] . "&c=entry&m=ewei_shop&do=member";
                if (strexists($zym_var_32, "/addons/ewei_shop/")) {
                    $zym_var_32 = str_replace("/addons/ewei_shop/", "/", $zym_var_32);
                }
                if (strexists($zym_var_32, "/core/mobile/order/")) {
                    $zym_var_32 = str_replace("/core/mobile/order/", "/", $zym_var_32);
                }
                if (!empty($zym_var_42["recharge_ok"]) && empty($zym_var_34["recharge_ok"])) {
                    m("message")->sendTplNotice($zym_var_13["openid"], $zym_var_42["recharge_ok"], $zym_var_35, $zym_var_32);
                } else if (empty($zym_var_34["recharge_ok"])) {
                    m("message")->sendCustomNotice($zym_var_13["openid"], $zym_var_35, $zym_var_32);
                }
            } else if ($zym_var_13["status"] == 3) {
                $zym_var_35 = array(
                    "first" => array(
                        "value" => "充值退款成功!",
                        "color" => "#4a5077"
                    ),
                    "reason" => array(
                        "title" => "退款原因",
                        "value" => "【" . $zym_var_41["name"] . "】充值退款",
                        "color" => "#4a5077"
                    ),
                    "refund" => array(
                        "title" => "退款金额",
                        "value" => "¥" . $zym_var_13["money"] . "元",
                        "color" => "#4a5077"
                    ),
                    "remark" => array(
                        "value" => "
退款成功，请注意查收! 谢谢您对我们的支持！",
                        "color" => "#4a5077"
                    )
                );
                $zym_var_32 = $_W["siteroot"] . "app/index.php?i=" . $_W["uniacid"] . "&c=entry&m=ewei_shop&do=member";
                if (strexists($zym_var_32, "/addons/ewei_shop/")) {
                    $zym_var_32 = str_replace("/addons/ewei_shop/", "/", $zym_var_32);
                }
                if (strexists($zym_var_32, "/core/mobile/order/")) {
                    $zym_var_32 = str_replace("/core/mobile/order/", "/", $zym_var_32);
                }
                if (!empty($zym_var_42["recharge_fund"]) && empty($zym_var_34["recharge_fund"])) {
                    m("message")->sendTplNotice($zym_var_13["openid"], $zym_var_42["recharge_fund"], $zym_var_35, $zym_var_32);
                } else if (empty($zym_var_34["recharge_fund"])) {
                    m("message")->sendCustomNotice($zym_var_13["openid"], $zym_var_35, $zym_var_32);
                }
            }
        } else if ($zym_var_13["type"] == 1 && $zym_var_13["status"] == 0) {
            $zym_var_35 = array(
                "first" => array(
                    "value" => "提现申请已经成功提交!",
                    "color" => "#4a5077"
                ),
                "money" => array(
                    "title" => "提现金额",
                    "value" => "¥" . $zym_var_13["money"] . "元",
                    "color" => "#4a5077"
                ),
                "timet" => array(
                    "title" => "提现时间",
                    "value" => date("Y-m-d H:i:s", $zym_var_13["createtime"]),
                    "color" => "#4a5077"
                ),
                "remark" => array(
                    "value" => "
请等待我们的审核并打款！",
                    "color" => "#4a5077"
                )
            );
            $zym_var_32 = $_W["siteroot"] . "app/index.php?i=" . $_W["uniacid"] . "&c=entry&m=ewei_shop&do=member&p=log&type=1";
            if (strexists($zym_var_32, "/addons/ewei_shop/")) {
                $zym_var_32 = str_replace("/addons/ewei_shop/", "/", $zym_var_32);
            }
            if (!empty($zym_var_42["withdraw"]) && empty($zym_var_34["withdraw"])) {
                m("message")->sendTplNotice($zym_var_13["openid"], $zym_var_42["withdraw"], $zym_var_35, $zym_var_32);
            } else if (empty($zym_var_34["withdraw"])) {
                m("message")->sendCustomNotice($zym_var_13["openid"], $zym_var_35, $zym_var_32);
            }
        } else if ($zym_var_13["type"] == 1 && $zym_var_13["status"] == 1) {
            $zym_var_35 = array(
                "first" => array(
                    "value" => "恭喜您成功提现!",
                    "color" => "#4a5077"
                ),
                "money" => array(
                    "title" => "提现金额",
                    "value" => "¥" . $zym_var_13["money"] . "元",
                    "color" => "#4a5077"
                ),
                "timet" => array(
                    "title" => "提现时间",
                    "value" => date("Y-m-d H:i:s", $zym_var_13["createtime"]),
                    "color" => "#4a5077"
                ),
                "remark" => array(
                    "value" => "
感谢您的支持！",
                    "color" => "#4a5077"
                )
            );
            $zym_var_32 = $_W["siteroot"] . "app/index.php?i=" . $_W["uniacid"] . "&c=entry&m=ewei_shop&do=member&p=log&type=1";
            if (!empty($zym_var_42["withdraw_ok"]) && empty($zym_var_34["withdraw_ok"])) {
                m("message")->sendTplNotice($zym_var_13["openid"], $zym_var_42["withdraw_ok"], $zym_var_35, $zym_var_32);
            } else if (empty($zym_var_34["withdraw_ok"])) {
                m("message")->sendCustomNotice($zym_var_13["openid"], $zym_var_35, $zym_var_32);
            }
        } else if ($zym_var_13["type"] == 1 && $zym_var_13["status"] == -1) {
            $zym_var_35 = array(
                "first" => array(
                    "value" => "抱歉，提现申请审核失败!",
                    "color" => "#4a5077"
                ),
                "money" => array(
                    "title" => "提现金额",
                    "value" => "¥" . $zym_var_13["money"] . "元",
                    "color" => "#4a5077"
                ),
                "timet" => array(
                    "title" => "提现时间",
                    "value" => date("Y-m-d H:i:s", $zym_var_13["createtime"]),
                    "color" => "#4a5077"
                ),
                "remark" => array(
                    "value" => "
有疑问请联系客服，谢谢您的支持！",
                    "color" => "#4a5077"
                )
            );
            $zym_var_32 = $_W["siteroot"] . "app/index.php?i=" . $_W["uniacid"] . "&c=entry&m=ewei_shop&do=member&p=log&type=1";
            if (strexists($zym_var_32, "/addons/ewei_shop/")) {
                $zym_var_32 = str_replace("/addons/ewei_shop/", "/", $zym_var_32);
            }
            if (strexists($zym_var_32, "/core/mobile/order/")) {
                $zym_var_32 = str_replace("/core/mobile/order/", "/", $zym_var_32);
            }
            if (!empty($zym_var_42["withdraw_fail"]) && empty($zym_var_34["withdraw_fail"])) {
                m("message")->sendTplNotice($zym_var_13["openid"], $zym_var_42["withdraw_fail"], $zym_var_35, $zym_var_32);
            } else if (empty($zym_var_34["withdraw_fail"])) {
                m("message")->sendCustomNotice($zym_var_13["openid"], $zym_var_35, $zym_var_32);
            }
        }
    }
}
?>