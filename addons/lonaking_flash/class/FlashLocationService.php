<?php
require_once dirname(__FILE__) . '/../FlashCommonService.php';
class FlashLocationService extends FlashCommonService
{
    const DWD = "http://we7.mywntc.com:8080/flash-check/tool/d_w_b";
    const WTB = "http://we7.mywntc.com:8080/flash-check/tool/l_c";
    public function __construct()
    {
        $this->table_name  = "lonaking_location_cache";
        $this->columns     = "id,uniacid,w_lng,w_lat,b_lng,b_lat,d,create_time,update_time";
        $this->plugin_name = "lonaking_flash";
    }
    public function w2bLocation($w_lng, $w_lat, $openid = null)
    {
        global $_W;
        if ($openid == null)
            $openid = $_W['openid'];
        $location = $this->selectOne(" and openid='{$openid}' and w_lng={$w_lng} and w_lat={$w_lat}");
        if (!is_null($location) && !empty($location)) {
            return array(
                'lng' => $location['b_lng'],
                'lat' => $location['b_lat']
            );
        } else {
            pdo_delete($this->table_name, array(
                'openid' => $openid
            ));
            $d      = array(
                'n' => $w_lng,
                'a' => $w_lat
            );
            $res    = $this->httpGet(FlashLocationService::WTB, $d);
            $result = $this->jsonString2Array($res);
            if ($result['code'] != 200) {
                throw new Exception($result['msg'], $result['code']);
            } else {
                $d = $result['data'];
                $a = array(
                    'uniacid' => $_W['uniacid'],
                    'openid' => $openid,
                    'w_lng' => $w_lng,
                    'w_lat' => $w_lat,
                    'b_lng' => $location['lng'],
                    'b_lat' => $location['lat'],
                    'd' => -1,
                    'create_time' => time(),
                    'update_time' => time()
                );
                $this->insertData($a);
                return $result['data'];
            }
        }
    }
    public function w2bDistance($w_lng, $w_lat, $b_lng, $b_lat, $openid = null)
    {
        global $_W;
        if (empty($w_lng) || empty($w_lat) || empty($b_lat) || empty($b_lng)) {
            throw new Exception("系统无法识别您的地理位置", 500);
        }
        if ($openid == null)
            $openid = $_W['openid'];
        $location = $this->selectOne(" and openid='{$openid}' and w_lng='{$w_lng}' and w_lat='{$w_lat}' and b_lng='{$b_lng}' and b_lat='{$b_lat}'");
        if (!is_null($location) && !empty($location)) {
            return $location['d'];
        } else {
            pdo_delete($this->table_name, array(
                'openid' => $openid
            ));
            $d      = array(
                'w_n' => $w_lng,
                'w_a' => $w_lat,
                'b_n' => $b_lng,
                'b_a' => $b_lat
            );
            $res    = $this->httpGet(FlashLocationService::DWD, $d);
            $result = $this->jsonString2Array($res);
            if ($result['code'] != 200) {
                throw new Exception($result['msg'], $result['code']);
            } else {
                $d = $result['data'];
                $a = array(
                    'uniacid' => $_W['uniacid'],
                    'openid' => $openid,
                    'w_lng' => $w_lng,
                    'w_lat' => $w_lat,
                    'b_lng' => $b_lng,
                    'b_lat' => $b_lat,
                    'd' => $d,
                    'create_time' => time(),
                    'update_time' => time()
                );
                $this->insertData($a);
                return doubleval($d);
            }
        }
    }
}
