<?php
defined('IN_IA') or exit('Access Denied');
class Weather_searchModuleProcessor extends WeModuleProcessor
{
    public function respond()
    {
        global $_W;
        $content   = $this->message['content'];
        $frommsg   = preg_replace('/(天气|\s)/', '', $content);
        $str_araid = $this->getcontent('http://yhdjy.cn/index/soft/araid.json');
        if (!$str_araid) {
            return $this->respText('系统错误');
        } else {
            $json_araid = json_decode($str_araid);
            $row        = $json_araid->row;
            $araid      = $this->getaraid($frommsg, $row);
            if (!$araid) {
                return $this->respText('未找到该城市,请回复 城市+天气，例如：北京天气');
            } else {
                $url         = 'http://weather.51wnl.com/weatherinfo/GetMoreWeather?cityCode=' . $araid . '&weatherType=0';
                $str         = $this->getcontent($url);
                $weatherinfo = json_decode($str);
                $data        = $weatherinfo->weatherinfo;
                if (!$data) {
                    return $this->respText("未找到天气数据");
                } else {
                    $path = $_W['siteroot'] . 'addons/weather_search/template/images/';
                    $pic  = array(
                        0 => array(
                            'title' => $frommsg . " 今天天气：" . $data->weather1 . "  气温：" . $data->temp1,
                            'description' => '今天有点冷',
                            'picurl' => $path . 'banner.jpg'
                        ),
                        1 => array(
                            'title' => "▶" . $this->getday('1') . "\n天气：" . $data->weather2 . "  气温：" . $data->temp2,
                            'picurl' => $path . $data->img3 . '.png'
                        ),
                        2 => array(
                            'title' => "▶" . $this->getday('2') . "\n天气：" . $data->weather3 . "  气温：" . $data->temp3,
                            'picurl' => $path . $data->img5 . '.png'
                        ),
                        3 => array(
                            'title' => "▶" . $this->getday('3') . "\n天气：" . $data->weather4 . "  气温：" . $data->temp4,
                            'picurl' => $path . $data->img7 . '.png'
                        ),
                        4 => array(
                            'title' => "▶" . $this->getday('4') . "\n天气：" . $data->weather5 . "  气温：" . $data->temp5,
                            'picurl' => $path . $data->img9 . '.png'
                        ),
                        5 => array(
                            'title' => "▶" . $this->getday('5') . "\n天气：" . $data->weather6 . "  气温：" . $data->temp6,
                            'picurl' => $path . $data->img9 . '.png'
                        )
                    );
                    return $this->respNews($pic);
                }
            }
        }
    }
    public function getaraid($str, $arr)
    {
        foreach ($arr as $key => $value) {
            if ($value->name == $str)
                return $value->araid;
        }
        return false;
    }
    public function getcontent($url)
    {
        if (function_exists("file_get_contents")) {
            $file_contents = file_get_contents($url);
        } else {
            $ch      = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $file_contents = curl_exec($ch);
            curl_close($ch);
        }
        return $file_contents;
    }
    public function getday($num)
    {
        $addday = $num * 86400;
        $time   = time() + $addday;
        $day    = date('Y年m月d日', $time);
        return $day;
    }
}