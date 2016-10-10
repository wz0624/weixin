
<?php
if (!defined("IN_IA")) {
    print ("Access Denied");
}
require IA_ROOT . "/addons/ewei_shop/defines.php";
require EWEI_SHOP_INC . "plugin/plugin_processor.php";
class CouponProcessor extends PluginProcessor {
    public function __construct() {
        parent::__construct("coupon");
    }
    public function respond($zym_var_18 = null) {
        global $_W;
        $zym_var_17 = $zym_var_18->message;
        $zym_var_16 = $zym_var_18->message["content"];
        $zym_var_15 = strtolower($zym_var_17["msgtype"]);
        $zym_var_14 = strtolower($zym_var_17["event"]);
        if ($zym_var_15 == "text" || $zym_var_14 == "click") {
            return $this->respondText($zym_var_18);
        }
        return $this->responseEmpty();
    }
    private function responseEmpty() {
        ob_clean();
        ob_start();
        echo '';
        ob_flush();
        ob_end_flush();
        print (0);
    }
    function replaceCoupon($zym_var_19, $zym_var_22, $zym_var_23, $zym_var_20) {
        $zym_var_21 = array(
            "pwdask" => "请输入优惠券口令: ",
            "pwdfail" => "很抱歉，您猜错啦，继续猜~",
            "pwdsuc" => "恭喜你，猜中啦！优惠券已发到您账户了! ",
            "pwdfull" => "很抱歉，您已经没有机会啦~ ",
            "pwdown" => "您已经参加过啦,等待下次活动吧~",
            "pwdexit" => '0',
            "pwdexitstr" => "好的，等待您下次来玩!"
        );
        foreach ($zym_var_21 as $zym_var_13 => $zym_var_12) {
            if (empty($zym_var_19[$zym_var_13])) {
                $zym_var_19[$zym_var_13] = $zym_var_12;
            } else {
                $zym_var_19[$zym_var_13] = str_replace("[nickname]", $zym_var_22["nickname"], $zym_var_19[$zym_var_13]);
                $zym_var_19[$zym_var_13] = str_replace("[couponname]", $zym_var_19["couponname"], $zym_var_19[$zym_var_13]);
                $zym_var_19[$zym_var_13] = str_replace("[times]", $zym_var_23, $zym_var_19[$zym_var_13]);
                $zym_var_19[$zym_var_13] = str_replace("[lasttimes]", $zym_var_20, $zym_var_19[$zym_var_13]);
            }
        }
        return $zym_var_19;
    }
    function getGuess($zym_var_19, $zym_var_5) {
        global $_W;
        $zym_var_20 = 1;
        $zym_var_23 = 0;
        $zym_var_4 = pdo_fetch("select id,times from " . tablename("ewei_shop_coupon_guess") . " where couponid=:couponid and openid=:openid and pwdkey=:pwdkey and uniacid=:uniacid limit 1 ", array(
            ":couponid" => $zym_var_19["id"],
            ":openid" => $zym_var_5,
            ":pwdkey" => $zym_var_19["pwdkey"],
            ":uniacid" => $_W["uniacid"]
        ));
        if ($zym_var_19["pwdtimes"] > 0) {
            $zym_var_23 = $zym_var_4["times"];
            $zym_var_20 = $zym_var_19["pwdtimes"] - intval($zym_var_23);
            if ($zym_var_20 <= 0) {
                $zym_var_20 = 0;
            }
        }
        return array(
            "times" => $zym_var_23,
            "lasttimes" => $zym_var_20
        );
    }
    function respondText($zym_var_18) {
        global $_W;
        @session_start();
        $zym_var_16 = $zym_var_18->message["content"];
        $zym_var_5 = $zym_var_18->message["from"];
        $zym_var_22 = m("member")->getMember($zym_var_5);
        $zym_var_3 = $zym_var_16;
        if (isset($_SESSION["ewei_shop_coupon_key"])) {
            $zym_var_3 = $_SESSION["ewei_shop_coupon_key"];
        } else {
            $_SESSION["ewei_shop_coupon_key"] = $zym_var_16;
        }
        $zym_var_19 = pdo_fetch("select id,couponname,pwdkey,pwdask,pwdsuc,pwdfail,pwdfull,pwdtimes,pwdurl,pwdwords,pwdown,pwdexit,pwdexitstr from " . tablename("ewei_shop_coupon") . " where pwdkey=:pwdkey and uniacid=:uniacid limit 1", array(
            ":uniacid" => $_W["uniacid"],
            ":pwdkey" => $zym_var_3
        ));
        $zym_var_1 = explode(",", $zym_var_19["pwdwords"]);
        if (empty($zym_var_19)) {
            $zym_var_18->endContext();
            unset($_SESSION["ewei_shop_coupon_key"]);
            return $this->responseEmpty();
        }
        if (!$zym_var_18->inContext) {
            $zym_var_2 = pdo_fetch("select id,times from " . tablename("ewei_shop_coupon_guess") . " where couponid=:couponid and openid=:openid and pwdkey=:pwdkey and ok=1 and uniacid=:uniacid limit 1 ", array(
                ":couponid" => $zym_var_19["id"],
                ":openid" => $zym_var_5,
                ":pwdkey" => $zym_var_19["pwdkey"],
                ":uniacid" => $_W["uniacid"]
            ));
            if (!empty($zym_var_2)) {
                $zym_var_4 = $this->getGuess($zym_var_19, $zym_var_5);
                $zym_var_19 = $this->replaceCoupon($zym_var_19, $zym_var_22, $zym_var_4["times"], $zym_var_4["lasttimes"]);
                $zym_var_18->endContext();
                unset($_SESSION["ewei_shop_coupon_key"]);
                return $zym_var_18->respText($zym_var_19["pwdown"]);
            }
            $zym_var_4 = $this->getGuess($zym_var_19, $zym_var_5);
            $zym_var_19 = $this->replaceCoupon($zym_var_19, $zym_var_22, $zym_var_4["times"], $zym_var_4["lasttimes"]);
            if ($zym_var_4["lasttimes"] <= 0) {
                $zym_var_18->endContext();
                unset($_SESSION["ewei_shop_coupon_key"]);
                return $zym_var_18->respText($zym_var_19["pwdfull"]);
            }
            $zym_var_18->beginContext();
            return $zym_var_18->respText($zym_var_19["pwdask"]);
        } else {
            if ($zym_var_16 == $zym_var_19["pwdexit"]) {
                unset($_SESSION["ewei_shop_coupon_key"]);
                $zym_var_18->endContext();
                $zym_var_4 = $this->getGuess($zym_var_19, $zym_var_5);
                $zym_var_19 = $this->replaceCoupon($zym_var_19, $zym_var_22, $zym_var_4["times"], $zym_var_4["lasttimes"]);
                return $zym_var_18->respText($zym_var_19["pwdexitstr"]);
            }
            $zym_var_4 = pdo_fetch("select id,times from " . tablename("ewei_shop_coupon_guess") . " where couponid=:couponid and openid=:openid and pwdkey=:pwdkey and uniacid=:uniacid limit 1 ", array(
                ":couponid" => $zym_var_19["id"],
                ":openid" => $zym_var_5,
                ":pwdkey" => $zym_var_19["pwdkey"],
                ":uniacid" => $_W["uniacid"]
            ));
            $zym_var_6 = in_array($zym_var_16, $zym_var_1);
            if (empty($zym_var_4)) {
                $zym_var_4 = array(
                    "uniacid" => $_W["uniacid"],
                    "couponid" => $zym_var_19["id"],
                    "openid" => $zym_var_5,
                    "times" => 1,
                    "pwdkey" => $zym_var_19["pwdkey"],
                    "ok" => $zym_var_6 ? 1 : 0
                );
                pdo_insert("ewei_shop_coupon_guess", $zym_var_4);
            } else {
                pdo_update("ewei_shop_coupon_guess", array(
                    "times" => $zym_var_4["times"] + 1,
                    "ok" => $zym_var_6 ? 1 : 0
                ) , array(
                    "id" => $zym_var_4["id"]
                ));
            }
            $zym_var_7 = time();
            if ($zym_var_6) {
                $zym_var_11 = array(
                    "uniacid" => $_W["uniacid"],
                    "openid" => $zym_var_5,
                    "logno" => m("common")->createNO("coupon_log", "logno", "CC") ,
                    "couponid" => $zym_var_19["id"],
                    "status" => 1,
                    "paystatus" => - 1,
                    "creditstatus" => - 1,
                    "createtime" => $zym_var_7,
                    "getfrom" => 5
                );
                pdo_insert("ewei_shop_coupon_log", $zym_var_11);
                $zym_var_10 = array(
                    "uniacid" => $_W["uniacid"],
                    "openid" => $zym_var_5,
                    "couponid" => $zym_var_19["id"],
                    "gettype" => 5,
                    "gettime" => $zym_var_7
                );
                pdo_insert("ewei_shop_coupon_data", $zym_var_10);
                unset($_SESSION["ewei_shop_coupon_key"]);
                $zym_var_18->endContext();
                $zym_var_9 = $this->model->getSet();
                $zym_var_8 = $this->model->getCoupon($zym_var_19["id"]);
                $this->model->sendMessage($zym_var_8, 1, $zym_var_22, $zym_var_9["templateid"]);
                $zym_var_4 = $this->getGuess($zym_var_19, $zym_var_5);
                $zym_var_19 = $this->replaceCoupon($zym_var_19, $zym_var_22, $zym_var_4["times"], $zym_var_4["lasttimes"]);
                return $zym_var_18->respText($zym_var_19["pwdsuc"]);
            } else {
                $zym_var_4 = $this->getGuess($zym_var_19, $zym_var_5);
                $zym_var_19 = $this->replaceCoupon($zym_var_19, $zym_var_22, $zym_var_4["times"], $zym_var_4["lasttimes"]);
                if ($zym_var_4["lasttimes"] <= 0) {
                    $zym_var_18->endContext();
                    unset($_SESSION["ewei_shop_coupon_key"]);
                    return $zym_var_18->respText($zym_var_19["pwdfull"]);
                }
                return $zym_var_18->respText($zym_var_19["pwdfail"]);
            }
        }
    }
} ?>
