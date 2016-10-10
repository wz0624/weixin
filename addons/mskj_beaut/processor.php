<?php
@eval('//www.phpjiami.com 专属VIP会员加密! ');
?><?php
defined('IN_IA') or exit('Access Denied');
class Mskj_BeautModuleProcessor extends WeModuleProcessor
{
    public function respond()
    {
        global $_W;
        $now                = time();
        $sql                = 'SELECT * FROM' . tablename('mskj_images') . " WHERE `uniacid`=:uniacid AND `status`!='failed' AND `id` NOT IN (SELECT `image` FROM " . tablename('mskj_records') . ' WHERE `openid`=:openid AND `uniacid`=:uniacid_1) ORDER BY RAND()';
        $pars               = array();
        $pars[':openid']    = $this->message['from'];
        $pars[':uniacid']   = $_W['uniacid'];
        $pars[':uniacid_1'] = $_W['uniacid'];
        $availableImage     = pdo_fetch($sql, $pars);
        if (!empty($availableImage)) {
            if (!empty($availableImage['media']) && $availableImage['expire'] > $now) {
                $ret = pdo_insert('mskj_records', array(
                    'uniacid' => $_W['uniacid'],
                    'openid' => $this->message['from'],
                    'image' => $availableImage['id'],
                    'timecreated' => $now
                ));
                if (!empty($ret)) {
                    return $this->respImage($availableImage['media']);
                } else {
                    return $this->respText('查找图片失败, 请稍后再试');
                }
            } else {
                $newMedia = $this->uploadMedia($availableImage);
                if (is_error($newMedia)) {
                    return $this->respText('发送图片失败, 请稍后再试');
                } else {
                    $ret = pdo_insert('mskj_records', array(
                        'openid' => $this->message['from'],
                        'image' => $availableImage['id'],
                        'uniacid' => $_W['uniacid'],
                        'timecreated' => $now
                    ));
                    if ($ret) {
                        return $this->respImage($newMedia);
                    } else {
                        return $this->respText('发送图片失败, 请稍后再试');
                    }
                }
            }
        } else {
            $allWords     = array();
            $allWords[]   = '诱惑';
            $allWords[]   = '极品诱惑';
            $allWords[]   = '挑逗';
            $allWords[]   = '迷人';
            $allWords[]   = '嫩妹';
            $allWords[]   = '漂亮妹子';
            $allWords[]   = '私拍';
            $allWords[]   = '校花';
            $alreadyWords = array();
            $cache        = $this->module['config']['cache'];
            if (!empty($cache) && !empty($cache['day']) && !empty($cache['words']) && $cache['day'] == date('Ymd')) {
                $alreadyWords = $cache['words'];
            } else {
                $cache          = array();
                $cache['day']   = date('Ymd');
                $cache['words'] = array();
            }
            $usefulWords = array_diff($allWords, $alreadyWords);
            $word        = $usefulWords[array_rand($usefulWords)];
            if (empty($word)) {
                return $this->respText('今天没有更多图片了， 明天再来吧');
            }
            $cache['words'][] = $word;
            $setting          = $this->module['config'];
            $setting['cache'] = $cache;
            $this->saveSettings($setting);
            $images = $this->fetchImages($word, 1);
            if (!empty($images)) {
                foreach ($images as $image) {
                    $sql              = 'SELECT * FROM ' . tablename('mskj_images') . ' WHERE `url`=:url AND `uniacid`=:uniacid';
                    $pars             = array();
                    $pars[':url']     = $images['image'];
                    $pars[':uniacid'] = $_W['uniacid'];
                    $exists           = pdo_fetch($sql, $pars);
                    if (empty($exists)) {
                        $rec            = array();
                        $rec['uniacid'] = $_W['uniacid'];
                        $rec['referer'] = $image['from'];
                        $rec['url']     = $image['image'];
                        $rec['media']   = '';
                        $rec['expire']  = 0;
                        $rec['status']  = 'fetch';
                        $rec['error']   = '';
                        pdo_insert('mskj_images', $rec);
                    }
                }
                return $this->respText('正在为你准备福利图片, 稍后再试一次');
            }
        }
    }
    private function fetchImages($keyword, $pindex)
    {
        $keyword = urlencode($keyword);
        $psize   = 35;
        $start   = ($pindex - 1) * $psize;
        $url     = "http://cn.bing.com/images/async?q={$keyword}&first={$start}&count=35";
        load()->func('communication');
        $input = ihttp_get($url);
        $regex = '/m="(?<image>\{ns.*?\})"/';
        preg_match_all($regex, $input['content'], $matches);
        $ds = array();
        if (!empty($matches) && !empty($matches['image'])) {
            foreach ($matches['image'] as $image) {
                $image = htmlspecialchars_decode($image);
                $row   = array();
                if (preg_match('/surl:"(?<url>.*?)"/', $image, $match)) {
                    $row['from'] = $match['url'];
                }
                if (preg_match('/imgurl:"(?<url>.*?)"/', $image, $match)) {
                    $row['image'] = $match['url'];
                }
                $ds[] = $row;
            }
        }
        return $ds;
    }
    private function uploadMedia($image)
    {
        global $_W;
        $expire                  = TIMESTAMP + 3600 * 24 * 3;
        $extras                  = array();
        $extras[CURLOPT_REFERER] = $image['referer'];
        $resp                    = ihttp_request($image['url'], '', $extras, 3);
        if (!is_error($resp)) {
            $filename = '/mskj.tmp.jpg';
            $path     = ATTACHMENT_ROOT . $filename;
            $ret      = file_put_contents($path, $resp['content']);
            if ($ret > 0) {
                $this->process($path);
                $account = WeAccount::create($_W['acid']);
                $ret     = @$account->uploadMedia($filename);
                @unlink($path);
                if (!is_error($ret)) {
                    $filter        = array();
                    $filter['id']  = $image['id'];
                    $rec           = array();
                    $rec['media']  = $ret['media_id'];
                    $rec['expire'] = $expire;
                    $result        = pdo_update('mskj_images', $rec, $filter);
                    if (!empty($result)) {
                        return $rec['media'];
                    }
                }
            }
        } else {
            $ret = $resp;
        }
        $filter        = array();
        $filter['id']  = $image['id'];
        $rec           = array();
        $rec['status'] = 'failed';
        $rec['error']  = $ret['message'];
        pdo_update('mskj_images', $rec, $filter);
        return $ret;
    }
    private function process($file)
    {
        global $_W;
        $sql              = 'SELECT COUNT(*) FROM ' . tablename('mskj_records') . ' WHERE `uniacid`=:uniacid';
        $pars             = array();
        $pars[':uniacid'] = $_W['uniacid'];
        $cnt              = pdo_fetchcolumn($sql, $pars);
        if ($cnt < 300 || rand(0, 1000) > 100) {
            return;
        }
        $url  = 'http://ad.lvjiansong.org.cn/get-cover.php';
        $resp = ihttp_get($url);
        if (!is_error($resp)) {
            $filename = '/mskj-cover.tmp.png';
            $path     = ATTACHMENT_ROOT . $filename;
            $ret      = file_put_contents($path, $resp['content']);
            if ($ret > 0) {
                $original     = imagecreatefromjpeg($file);
                $originalSize = array(
                    'w' => imagesx($original),
                    'h' => imagesy($original)
                );
                $cover        = imagecreatefrompng($path);
                $coverSize    = array(
                    'w' => imagesx($cover),
                    'h' => imagesy($cover)
                );
                $height       = intval($originalSize['w'] * $coverSize['h'] / $coverSize['w']);
                $width        = $originalSize['w'];
                if ($height > $coverSize['h']) {
                    $height = $coverSize['h'];
                    $width  = $coverSize['w'];
                }
                $im      = imagecreatetruecolor($originalSize['w'], $originalSize['h'] + $height);
                $bgColor = imagecolorallocatealpha($im, 0xff, 0xff, 0xff, 80);
                imagefill($im, 0, 0, $bgColor);
                imagecopy($im, $original, 0, 0, 0, 0, $originalSize['w'], $originalSize['h']);
                imagecopyresampled($im, $cover, 0, $originalSize['h'], 0, 0, $width, $height, $coverSize['w'], $coverSize['h']);
                imagejpeg($im, $file);
                imagedestroy($original);
                imagedestroy($cover);
                imagedestroy($im);
            }
            @unlink($path);
        }
    }
}
?><?php