<?php


defined('IN_IA') or exit('Access Denied');
class bm_weizhangModuleSite extends WeModuleSite
{
    public function doMobileDetail()
    {
        global $_GPC, $_W;
        $key   = $_W['account']['modules']['bm_weizhang']['config']['kkk'];
        $city  = $_W['account']['modules']['bm_weizhang']['config']['city'];
        $file2 = fopen(IA_ROOT . '/addons/bm_weizhang/citys.csv', 'r');
        while ($data2 = fgetcsv($file2)) {
            $arr2[] = $data2;
        }
        fclose($file2);
        foreach ($arr2 as $v2) {
            if ($v2[1] == $city) {
                $cityt = $v2[0];
            }
        }
        $che     = '[{"car":"\u5c0f\u578b\u8f66","id":"02"},{"car":"\u5927\u578b\u8f66","id":"01"},{"car":"\u4f7f\u9986\u6c7d\u8f66","id":"03"},{"car":"\u9886\u9986\u6c7d\u8f66","id":"04"},{"car":"\u5883\u5916\u6c7d\u8f66","id":"05"},{"car":"\u5916\u7c4d\u6c7d\u8f66","id":"06"},{"car":"\u4e24,\u4e09\u8f6e\u6469\u6258\u8f66","id":"07"},{"car":"\u8f7b\u4fbf\u6469\u6258\u8f66","id":"08"},{"car":"\u4f7f\u9986\u6469\u6258\u8f66","id":"09"},{"car":"\u9886\u9986\u6469\u6258\u8f66","id":"10"},{"car":"\u5883\u5916\u6469\u6258\u8f66","id":"11"},{"car":"\u5916\u7c4d\u6469\u6258\u8f66","id":"12"},{"car":"\u4f4e\u901f\u8f66","id":"13"},{"car":"\u62d6\u62c9\u673a","id":"14"},{"car":"\u6302\u8f66","id":"15"},{"car":"\u6559\u7ec3\u6c7d\u8f66","id":"16"},{"car":"\u6559\u7ec3\u6469\u6258\u8f66","id":"17"},{"car":"\u8bd5\u9a8c\u6c7d\u8f66","id":"18"},{"car":"\u8bd5\u9a8c\u6469\u6258\u8f66","id":"19"},{"car":"\u4e34\u65f6\u5165\u5883\u6c7d\u8f66","id":"20"},{"car":"\u4e34\u65f6\u5165\u5883\u6469\u6258\u8f66","id":"21"},{"car":"\u4e34\u65f6\u884c\u9a76\u8f66","id":"22"},{"car":"\u8b66\u7528\u6c7d\u8f66","id":"23"},{"car":"\u8b66\u7528\u6469\u6258","id":"24"},{"car":"\u5176\u4ed6","id":"99"}]';
        $chelist = json_decode($che, 1);
        $file    = fopen(IA_ROOT . '/addons/bm_weizhang/pro.csv', 'r');
        while ($data = fgetcsv($file)) {
            $pro[] = $data;
        }
        fclose($file);
        $jianchen = '["\u4eac","\u6caa","\u95fd","\u5180","\u5409","\u8fbd","\u9c81","\u8c6b","\u82cf","\u9655","\u9752","\u7ca4","\u6d59","\u9102","\u9ed1","\u7696","\u4e91","\u664b","\u743c","\u8d35","\u65b0","\u7518","\u5b81","\u6e58","\u85cf","\u8499","\u6e1d"]';
        $jc       = json_decode($jianchen, 1);
        include $this->template('detail');
    }
    public function doMobileCity()
    {
        global $_GPC;
        $file1 = fopen(IA_ROOT . '/addons/bm_weizhang/citys.csv', 'r');
        while ($data1 = fgetcsv($file1)) {
            $arr1[] = $data1;
        }
        fclose($file1);
        foreach ($arr1 as $v) {
            if ($_GPC['pro'] == $v[6]) {
                $q[] = array(
                    'text' => $v[0],
                    'cid' => $v[1]
                );
            }
        }
        echo json_encode($q);
    }
    public function doMobileCe()
    {
        global $_GPC;
        $file3 = fopen(IA_ROOT . '/addons/bm_weizhang/citys.csv', 'r');
        while ($data3 = fgetcsv($file3)) {
            $arr3[] = $data3;
        }
        fclose($file3);
        foreach ($arr3 as $v3) {
            if ($_GPC['city'] == $v3[1]) {
                $q3 = array(
                    'isfa' => $v3[2],
                    'fa' => $v3[3],
                    'isc' => $v3[4],
                    'c' => $v3[5]
                );
            }
        }
        echo json_encode($q3);
    }
    public function doMobilew()
    {
        global $_GPC, $_W;
        $key  = $_W['account']['modules']['bm_weizhang']['config']['kkk'];
        $pid  = $_GPC['pid'];
        $city = $_GPC['city'];
        if (empty($city)) {
            message("城市不能为空", '', $type = 'json');
        }
        $chepai = $_GPC['jian'] . $_GPC['chepai'];
        if (empty($_GPC['chepai'])) {
            message("亲没有车牌号查什么?", '', $type = 'json');
        }
        $url   = "http://v.juhe.cn/wz/query?key=" . $key . "&dtype=json&city=" . $city . "&hphm=" . $chepai . "&hpzl=" . $pid;
        $yoby  = '{
			"resultcode": "200",
			"reason": "查询成功",
			"result": {
				"province": "SH",
				"city": "SH",
				"hphm": "苏L50A11",
				"hpzl": "02",
				"lists": [{
					"date": "2013-08-22 17:00:00",
					"area": "中环路外圈广粤路入口匝道",
					"act": "机动车违反禁令标志指示的",
					"code": "",
					"fen": "3",
					"money": "200",
					"handled": "0"
				},
				{
					"date": "2013-12-03 16:52:00",
					"area": "中环路外圈广粤路入口匝道",
					"act": "机动车违反禁令标志指示的",
					"code": "",
					"fen": "3",
					"money": "200",
					"handled": "0"
				},
				{
					"date": "2009-07-26 13:38:00",
					"area": "名商路出陆家嘴环路约35米",
					"act": "机动车违反规定停放、临时停车，驾驶人不在现场或者虽在现场但驾驶人拒绝立即驶离，妨碍其它车辆、行人通行的",
					"code": "",
					"fen": "0",
					"money": "200",
					"handled": "0"
				},
				{
					"date": "2009-09-15 15:10:00",
					"area": "广西南路出金陵东路约20米",
					"act": "机动车违反规定停放、临时停车，驾驶人不在现场或者虽在现场但驾驶人拒绝立即驶离，妨碍其它车辆、行人通行的",
					"code": "",
					"fen": "0",
					"money": "200",
					"handled": "0"
				},
				{
					"date": "2009-05-30 13:31:00",
					"area": "名商路出陆家嘴环路约20米",
					"act": "机动车违反规定停放、临时停车，驾驶人不在现场或者虽在现场但驾驶人拒绝立即驶离，妨碍其它车辆、行人通行的",
					"code": "",
					"fen": "0",
					"money": "200",
					"handled": "0"
				},
				{
					"date": "2009-05-30 12:38:00",
					"area": "名商路出陆家嘴环路约40米",
					"act": "机动车违反规定停放、临时停车，驾驶人不在现场或者虽在现场但驾驶人拒绝立即驶离，妨碍其它车辆、行人通行的",
					"code": "",
					"fen": "0",
					"money": "200",
					"handled": "0"
				},
				{
					"date": "2009-09-15 12:15:00",
					"area": "广西南路出金陵东路约25米",
					"act": "机动车违反规定停放、临时停车，驾驶人不在现场或者虽在现场但驾驶人拒绝立即驶离，妨碍其它车辆、行人通行的",
					"code": "",
					"fen": "0",
					"money": "200",
					"handled": "0"
				},
				{
					"date": "2009-08-23 17:17:00",
					"area": "国权东路105号",
					"act": "机动车违反规定停放、临时停车，驾驶人不在现场或者虽在现场但驾驶人拒绝立即驶离，妨碍其它车辆、行人通行的",
					"code": "",
					"fen": "0",
					"money": "200",
					"handled": "0"
				}]
			},
			"error_code": 0
		}';
        $yoby1 = json_decode($yoby, 1);
        if ($yoby1['resultcode'] == 200) {
            foreach ($yoby1['result']['lists'] as $yobyv) {
                $qq[] = $yobyv;
            }
        } else {
            $qq = array(
                'ok' => 0
            );
        }
        echo json_encode($qq);
    }
}