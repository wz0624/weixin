<?php
if (!defined("IN_IA")) {
    exit("Access Denied");
}
if (!class_exists("PosteraModel")) {
    class PosteraModel extends PluginModel
    {
        public function getSceneTicket($val0, $val1)
        {
            global $_W, $_GPC;
            $val4  = m("common")->getAccount();
            $val5  = "{'expire_seconds':" . $val0 . ",'action_info':{'scene':{'scene_id':" . $val1 . "}},'action_name':'QR_SCENE'}";
            $val8  = $val4->fetch_token();
            $val10 = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=" . $val8;
            $val12 = curl_init();
            curl_setopt($val12, CURLOPT_URL, $val10);
            curl_setopt($val12, CURLOPT_POST, 1);
            curl_setopt($val12, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($val12, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($val12, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($val12, CURLOPT_POSTFIELDS, $val5);
            $val21 = curl_exec($val12);
            $val23 = @json_decode($val21, true);
            if (!is_array($val23)) {
                return false;
            }
            if (!empty($val23["errcode"])) {
                return error(-1, $val23["errmsg"]);
            }
            $val28 = $val23["ticket"];
            return array(
                "barcode" => json_decode($val5, true),
                "ticket" => $val28
            );
        }
        function getSceneID()
        {
            global $_W;
            $val33 = $_W["acid"];
            $val35 = 1;
            $val36 = 2147483647;
            $val1  = rand($val35, $val36);
            if (empty($val1)) {
                $val1 = rand($val35, $val36);
            }
            while (1) {
                $val44 = pdo_fetchcolumn("select count(*) from " . tablename("qrcode") . " where qrcid=:qrcid and acid=:acid and model=0 limit 1", array(
                    ":qrcid" => $val1,
                    ":acid" => $val33
                ));
                if ($val44 <= 0) {
                    break;
                }
                $val1 = rand($val35, $val36);
                if (empty($val1)) {
                    $val1 = rand($val35, $val36);
                }
            }
            return $val1;
        }
        public function getQR($val56, $val57)
        {
            global $_W, $_GPC;
            $val33 = $_W["acid"];
            $val62 = time();
            $val63 = $val56["timeend"];
            $val0  = $val63 - $val62;
            if ($val0 > 86400 * 30 - 15) {
                $val0 = 86400 * 30 - 15;
            }
            $val69 = $val62 + $val0;
            $val72 = pdo_fetch("select * from " . tablename("ewei_shop_postera_qr") . " where openid=:openid and acid=:acid and posterid=:posterid limit 1", array(
                ":openid" => $val57["openid"],
                ":acid" => $val33,
                ":posterid" => $val56["id"]
            ));
            if (empty($val72)) {
                $val72["current_qrimg"] = '';
                $val1                   = $this->getSceneID();
                $val23                  = $this->getSceneTicket($val0, $val1);
                if (is_error($val23)) {
                    return $val23;
                }
                if (empty($val23)) {
                    return error(-1, "生成二维码失败");
                }
                $val86 = $val23["barcode"];
                $val28 = $val23["ticket"];
                $val90 = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . $val28;
                $val92 = array(
                    "uniacid" => $_W["uniacid"],
                    "acid" => $_W["acid"],
                    "qrcid" => $val1,
                    "model" => 0,
                    "name" => "EWEI_SHOP_POSTERA_QRCODE",
                    "keyword" => "EWEI_SHOP_POSTERA",
                    "expire" => $val0,
                    "createtime" => time(),
                    "status" => 1,
                    "url" => $val23["url"],
                    "ticket" => $val23["ticket"]
                );
                pdo_insert("qrcode", $val92);
                $val72 = array(
                    "acid" => $val33,
                    "openid" => $val57["openid"],
                    "sceneid" => $val1,
                    "type" => $val56["type"],
                    "ticket" => $val23["ticket"],
                    "qrimg" => $val90,
                    "posterid" => $val56["id"],
                    "expire" => $val0,
                    "url" => $val23["url"],
                    "goodsid" => $val56["goodsid"],
                    "endtime" => $val69
                );
                pdo_insert("ewei_shop_postera_qr", $val72);
                $val72["id"] = pdo_insertid();
            } else {
                $val72["current_qrimg"] = $val72["qrimg"];
                if (time() > $val72["endtime"]) {
                    $val1  = $val72["sceneid"];
                    $val23 = $this->getSceneTicket($val0, $val1);
                    if (is_error($val23)) {
                        return $val23;
                    }
                    if (empty($val23)) {
                        return error(-1, "生成二维码失败");
                    }
                    $val86 = $val23["barcode"];
                    $val28 = $val23["ticket"];
                    $val90 = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . $val28;
                    pdo_update("qrcode", array(
                        "ticket" => $val23["ticket"],
                        "url" => $val23["url"]
                    ), array(
                        "acid" => $_W["acid"],
                        "qrcid" => $val1
                    ));
                    pdo_update("ewei_shop_postera_qr", array(
                        "ticket" => $val28,
                        "qrimg" => $val90,
                        "url" => $val23["url"],
                        "endtime" => $val69
                    ), array(
                        "id" => $val72["id"]
                    ));
                    $val72["ticket"] = $val28;
                    $val72["qrimg"]  = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . $val72["ticket"];
                }
            }
            return $val72;
        }
        public function getRealData($val145)
        {
            $val145["left"]   = intval(str_replace("px", '', $val145["left"])) * 2;
            $val145["top"]    = intval(str_replace("px", '', $val145["top"])) * 2;
            $val145["width"]  = intval(str_replace("px", '', $val145["width"])) * 2;
            $val145["height"] = intval(str_replace("px", '', $val145["height"])) * 2;
            $val145["size"]   = intval(str_replace("px", '', $val145["size"])) * 2;
            $val145["src"]    = tomedia($val145["src"]);
            return $val145;
        }
        public function createImage($val159)
        {
            load()->func("communication");
            $val160 = ihttp_request($val159);
            if ($val160["code"] == 200 && !empty($val160["content"])) {
                return imagecreatefromstring($val160["content"]);
            }
            $val165 = 0;
            while ($val165 < 3) {
                $val160 = ihttp_request($val159);
                if ($val160["code"] == 200 && !empty($val160["content"])) {
                    return imagecreatefromstring($val160["content"]);
                }
                $val165++;
            }
            return "";
        }
        public function mergeImage($val173, $val145, $val159)
        {
            $val176 = $this->createImage($val159);
            $val178 = imagesx($val176);
            $val180 = imagesy($val176);
            imagecopyresized($val173, $val176, $val145["left"], $val145["top"], 0, 0, $val145["width"], $val145["height"], $val178, $val180);
            imagedestroy($val176);
            return $val173;
        }
        public function mergeText($val173, $val145, $val194)
        {
            $val195 = IA_ROOT . "/addons/ewei_shop/static/fonts/msyh.ttf";
            $val196 = $this->hex2rgb($val145["color"]);
            $val198 = imagecolorallocate($val173, $val196["red"], $val196["green"], $val196["blue"]);
            imagettftext($val173, $val145["size"], 0, $val145["left"], $val145["top"] + $val145["size"], $val198, $val195, $val194);
            return $val173;
        }
        function hex2rgb($val212)
        {
            if ($val212[0] == "#") {
                $val212 = substr($val212, 1);
            }
            if (strlen($val212) == 6) {
                list($val217, $val218, $val219) = array(
                    $val212[0] . $val212[1],
                    $val212[2] . $val212[3],
                    $val212[4] . $val212[5]
                );
            } elseif (strlen($val212) == 3) {
                list($val217, $val218, $val219) = array(
                    $val212[0] . $val212[0],
                    $val212[1] . $val212[1],
                    $val212[2] . $val212[2]
                );
            } else {
                return false;
            }
            $val217 = hexdec($val217);
            $val218 = hexdec($val218);
            $val219 = hexdec($val219);
            return array(
                "red" => $val217,
                "green" => $val218,
                "blue" => $val219
            );
        }
        public function createPoster($val56, $val57, $val72, $val248 = true)
        {
            global $_W;
            $val250 = IA_ROOT . "/addons/ewei_shop/data/postera/" . $_W["uniacid"] . "/";
            if (!is_dir($val250)) {
                load()->func("file");
                mkdirs($val250);
            }
            if (!empty($val72["goodsid"])) {
                $val255 = pdo_fetch("select id,title,thumb,commission_thumb,marketprice,productprice from " . tablename("ewei_shop_goods") . " where id=:id and uniacid=:uniacid limit 1", array(
                    ":id" => $val72["goodsid"],
                    ":uniacid" => $_W["uniacid"]
                ));
                if (empty($val255)) {
                    m("message")->sendCustomNotice($val57["openid"], "未找到商品，无法生成海报");
                    exit;
                }
            }
            $val260 = md5(json_encode(array(
                "openid" => $val57["openid"],
                "goodsid" => $val72["goodsid"],
                "bg" => $val56["bg"],
                "data" => $val56["data"],
                "version" => 1
            )));
            $val265 = $val260 . ".png";
            if (!is_file($val250 . $val265) || $val72["qrimg"] != $val72["current_qrimg"]) {
                set_time_limit(0);
                @ini_set("memory_limit", "256M");
                $val173 = imagecreatetruecolor(640, 1008);
                $val272 = $this->createImage(tomedia($val56["bg"]));
                imagecopy($val173, $val272, 0, 0, 0, 0, 640, 1008);
                imagedestroy($val272);
                $val145 = json_decode(str_replace("&quot;", "'", $val56["data"]), true);
                foreach ($val145 as $val280) {
                    $val280 = $this->getRealData($val280);
                    if ($val280["type"] == "head") {
                        $val284 = preg_replace("/\/0$/i", "/96", $val57["avatar"]);
                        $val173 = $this->mergeImage($val173, $val280, $val284);
                    } else if ($val280["type"] == "time") {
                        $val62  = date("Y-m-d H:i", $val72["endtime"]);
                        $val173 = $this->mergeText($val173, $val280, $val62);
                    } else if ($val280["type"] == "img") {
                        $val173 = $this->mergeImage($val173, $val280, $val280["src"]);
                    } else if ($val280["type"] == "qr") {
                        $val173 = $this->mergeImage($val173, $val280, tomedia($val72["qrimg"]));
                    } else if ($val280["type"] == "nickname") {
                        $val173 = $this->mergeText($val173, $val280, $val57["nickname"]);
                    } else {
                        if (!empty($val255)) {
                            if ($val280["type"] == "title") {
                                $val173 = $this->mergeText($val173, $val280, $val255["title"]);
                            } else if ($val280["type"] == "thumb") {
                                $val320 = !empty($val255["commission_thumb"]) ? tomedia($val255["commission_thumb"]) : tomedia($val255["thumb"]);
                                $val173 = $this->mergeImage($val173, $val280, $val320);
                            } else if ($val280["type"] == "marketprice") {
                                $val173 = $this->mergeText($val173, $val280, $val255["marketprice"]);
                            } else if ($val280["type"] == "productprice") {
                                $val173 = $this->mergeText($val173, $val280, $val255["productprice"]);
                            }
                        }
                    }
                }
                imagepng($val173, $val250 . $val265);
                imagedestroy($val173);
            }
            $val176 = $_W["siteroot"] . "addons/ewei_shop/data/poster/" . $_W["uniacid"] . "/" . $val265;
            if (!$val248) {
                return $val176;
            }
            if ($val72["qrimg"] != $val72["current_qrimg"] || empty($val72["mediaid"]) || empty($val72["createtime"]) || $val72["createtime"] + 3600 * 24 * 3 - 7200 < time()) {
                $val353           = $this->uploadImage($val250 . $val265);
                $val72["mediaid"] = $val353;
                pdo_update("ewei_shop_postera_qr", array(
                    "mediaid" => $val353,
                    "createtime" => time()
                ), array(
                    "id" => $val72["id"]
                ));
            }
            return array(
                "img" => $val176,
                "mediaid" => $val72["mediaid"]
            );
        }
        public function uploadImage($val176)
        {
            load()->func("communication");
            $val4   = m("common")->getAccount();
            $val364 = $val4->fetch_token();
            $val10  = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token={$val364}&type=image";
            $val12  = curl_init();
            $val145 = array(
                "media" => "@" . $val176
            );
            if (version_compare(PHP_VERSION, "5.5.0", ">")) {
                $val145 = array(
                    "media" => curl_file_create($val176)
                );
            }
            curl_setopt($val12, CURLOPT_URL, $val10);
            curl_setopt($val12, CURLOPT_POST, 1);
            curl_setopt($val12, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($val12, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($val12, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($val12, CURLOPT_POSTFIELDS, $val145);
            $val381 = @json_decode(curl_exec($val12), true);
            if (!is_array($val381)) {
                $val381 = array(
                    "media_id" => ''
                );
            }
            curl_close($val12);
            return $val381["media_id"];
        }
        public function getQRByTicket($val28 = '')
        {
            global $_W;
            if (empty($val28)) {
                return false;
            }
            $val390 = pdo_fetchall("select * from " . tablename("ewei_shop_postera_qr") . " where ticket=:ticket and acid=:acid limit 1", array(
                ":ticket" => $val28,
                ":acid" => $_W["acid"]
            ));
            $val44  = count($val390);
            if ($val44 <= 0) {
                return false;
            }
            if ($val44 == 1) {
                return $val390[0];
            }
            return false;
        }
        public function checkMember($val398 = '')
        {
            global $_W;
            $val400           = WeiXinAccount::create($_W["acid"]);
            $val402           = $val400->fansQueryInfo($val398);
            $val402["avatar"] = $val402["headimgurl"];
            load()->model("mc");
            $val406 = mc_openid2uid($val398);
            if (!empty($val406)) {
                pdo_update("mc_members", array(
                    "nickname" => $val402["nickname"],
                    "gender" => $val402["sex"],
                    "nationality" => $val402["country"],
                    "resideprovince" => $val402["province"],
                    "residecity" => $val402["city"],
                    "avatar" => $val402["headimgurl"]
                ), array(
                    "uid" => $val406
                ));
            }
            pdo_update("mc_mapping_fans", array(
                "nickname" => $val402["nickname"]
            ), array(
                "uniacid" => $_W["uniacid"],
                "openid" => $val398
            ));
            $val419 = m("member");
            $val57  = $val419->getMember($val398);
            if (empty($val57)) {
                $val423 = mc_fetch($val406, array(
                    "realname",
                    "nickname",
                    "mobile",
                    "avatar",
                    "resideprovince",
                    "residecity",
                    "residedist"
                ));
                $val57  = array(
                    "uniacid" => $_W["uniacid"],
                    "uid" => $val406,
                    "openid" => $val398,
                    "realname" => $val423["realname"],
                    "mobile" => $val423["mobile"],
                    "nickname" => !empty($val423["nickname"]) ? $val423["nickname"] : $val402["nickname"],
                    "avatar" => !empty($val423["avatar"]) ? $val423["avatar"] : $val402["avatar"],
                    "gender" => !empty($val423["gender"]) ? $val423["gender"] : $val402["sex"],
                    "province" => !empty($val423["resideprovince"]) ? $val423["resideprovince"] : $val402["province"],
                    "city" => !empty($val423["residecity"]) ? $val423["residecity"] : $val402["city"],
                    "area" => $val423["residedist"],
                    "createtime" => time(),
                    "status" => 0
                );
                pdo_insert("ewei_shop_member", $val57);
                $val57["id"]    = pdo_insertid();
                $val57["isnew"] = true;
            } else {
                $val57["nickname"] = $val402["nickname"];
                $val57["avatar"]   = $val402["headimgurl"];
                $val57["province"] = $val402["province"];
                $val57["city"]     = $val402["city"];
                pdo_update("ewei_shop_member", $val57, array(
                    "id" => $val57["id"]
                ));
                $val57["isnew"] = false;
            }
            return $val57;
        }
        function perms()
        {
            return array(
                "postera" => array(
                    "text" => $this->getName(),
                    "isplugin" => true,
                    "view" => "浏览",
                    "add" => "添加-log",
                    "edit" => "修改-log",
                    "delete" => "删除-log",
                    "log" => "扫描记录",
                    "clear" => "清除缓存-log",
                    "setdefault" => "设置默认海报-log"
                )
            );
        }
    }
}
?>