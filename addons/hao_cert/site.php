<?php
defined('IN_IA') or exit('Access Denied');
class Hao_certModuleSite extends WeModuleSite
{
    public function doMobileIndex()
    {
        global $_W, $_GPC;
        include $this->template('index');
    }
    public function doMobileGetCertType()
    {
        global $_W, $_GPC;
        load()->func('tpl');
        if ($_W['ispost']) {
            $type = $_GPC['certType'];
            switch ($type) {
                case '1':
                    $typename = '丐帮弟子证';
                    include $this->template('gaibang');
                    break;
                case '2':
                    $typename = '通缉令';
                    include $this->template('tongyong');
                    break;
                case '3':
                    $typename = '帅哥证';
                    include $this->template('tongyong');
                    break;
                case '4':
                    $typename = '首富证';
                    include $this->template('tongyong');
                    break;
                case '5':
                    $typename = '痴呆证';
                    include $this->template('tongyong');
                    break;
                case '6':
                    $typename = '潜水证';
                    include $this->template('tongyong');
                    break;
                case '7':
                    $typename = '家里蹲';
                    include $this->template('jialidun');
                    break;
                case '8':
                    $typename = '好市民';
                    include $this->template('tongyong');
                    break;
                case '9':
                    $typename = 'MBA';
                    include $this->template('tongyong');
                    break;
                case '10':
                    $typename = '良家少妇';
                    include $this->template('tongyong');
                    break;
                case '11':
                    $typename = '吹牛逼';
                    include $this->template('tongyong');
                    break;
                case '12':
                    $typename = '处女证';
                    include $this->template('tongyong');
                    break;
                case '13':
                    $typename = '处男证';
                    include $this->template('tongyong');
                    break;
            }
        }
    }
    public function doMobileGaibang()
    {
        global $_W, $_GPC;
        $name  = $_GPC['name'];
        $sex   = $_GPC['sex'];
        $age   = $_GPC['age'];
        $rank  = $_GPC['rank'];
        $image = $_GPC['image'];
        $info  = getimagesize(tomedia($image));
        if ($info[2] != '2') {
            message('检测到你的图片原始格式不是JPG格式,请重新上传', $this->createMobileUrl('index'), error);
        }
        include $this->template('gaibangTemplate');
    }
    public function doMobileTuPianGaibang()
    {
        global $_GPC;
        $name  = $_GPC['name'];
        $sex   = $_GPC['sex'];
        $age   = $_GPC['age'];
        $rank  = $_GPC['rank'];
        $image = tomedia($_GPC['image']);
        $this->picgaibang($name, $sex, $age, $rank, $image);
    }
    public function picgaibang($name, $sex, $age, $rank, $image)
    {
        header('Content-Type: image/jpeg');
        $size                = 14;
        $background_pic_path = MODULE_ROOT . '/template/images/wlgaibang.jpg';
        $im                  = imagecreatefromjpeg($background_pic_path);
        $font                = MODULE_ROOT . '/template/fonts/6.ttf';
        $color               = imagecolorallocate($im, 65, 65, 65);
        imagettftext($im, $size, 0, 415, 50, $color, $font, $name);
        imagettftext($im, $size, 0, 320, 160, $color, $font, $name);
        imagettftext($im, $size, 0, 415, 80, $color, $font, $sex);
        imagettftext($im, $size, 0, 415, 110, $color, $font, $age);
        imagettftext($im, $size, 0, 350, 310, $color, $font, $rank);
        imagettftext($im, $size, 0, 350, 280, $color, $font, date('Y.m.d'));
        $filename = $image;
        $percent  = 0.2;
        header('Content-type: image/jpeg');
        list($width, $height) = getimagesize($filename);
        $newwidth  = 110;
        $newheight = 110;
        $source    = imagecreatefromjpeg($filename);
        imagecopyresampled($im, $source, 265, 18, 0, 0, $newwidth, $newheight, $width, $height);
        imagejpeg($im);
        imagedestroy($im);
    }
    public function doMobileTongYong()
    {
        global $_W, $_GPC;
        $name     = $_GPC['name'];
        $sex      = $_GPC['sex'];
        $age      = $_GPC['age'];
        $rank     = $_GPC['rank'];
        $image    = $_GPC['image'];
        $typename = $_GPC['typename'];
        $info     = getimagesize(tomedia($image));
        if ($info[2] != '2') {
            message('检测到你的图片原始格式不是JPG格式,请重新上传', $this->createMobileUrl('index'), error);
        }
        include $this->template('tongyongTemplate');
    }
    public function doMobileTuPianTongYong()
    {
        global $_GPC;
        $name     = $_GPC['name'];
        $sex      = $_GPC['sex'];
        $age      = $_GPC['age'];
        $rank     = $_GPC['rank'];
        $typename = $_GPC['typename'];
        $image    = tomedia($_GPC['image']);
        $this->pictongyong($name, $sex, $age, $rank, $image, $typename);
    }
    public function pictongyong($name, $sex, $age, $rank, $image, $typename)
    {
        header('Content-Type: image/jpeg');
        $size = 14;
        switch ($typename) {
            case '通缉令':
                $typename = 'tongjiling.jpg';
                break;
            case '帅哥证':
                $typename = 'shuaige.jpg';
                break;
            case '首富证':
                $typename = 'shoufu.jpg';
                break;
            case '痴呆证':
                $typename = 'shaonian.jpg';
                break;
            case '潜水证':
                $typename = 'qianshui.jpg';
                break;
            case '好市民':
                $typename = 'haoshimin.jpg';
                break;
            case 'MBA':
                $typename = 'hafouMBA.jpg';
                break;
            case '良家少妇':
                $typename = 'funv.jpg';
                break;
            case '吹牛逼':
                $typename = 'cuiniuboshi.jpg';
                break;
            case '处女证':
                $typename = 'chunv.jpg';
                break;
            case '处男证':
                $typename = 'chunan.jpg';
                break;
        }
        $background_pic_path = MODULE_ROOT . '/template/images/' . $typename;
        $im                  = imagecreatefromjpeg($background_pic_path);
        $font                = MODULE_ROOT . '/template/fonts/6.ttf';
        $color               = imagecolorallocate($im, 65, 65, 65);
        imagettftext($im, $size, 0, 320, 60, $color, $font, $name);
        imagettftext($im, $size, 0, 350, 150, $color, $font, $name);
        imagettftext($im, $size, 0, 320, 80, $color, $font, $sex);
        imagettftext($im, $size, 0, 320, 100, $color, $font, $age);
        imagettftext($im, 12, 0, 410, 290, $color, $font, $rank);
        imagettftext($im, 12, 0, 355, 275, $color, $font, date('Y.m.d'));
        $filename = $image;
        $percent  = 0.2;
        header('Content-type: image/jpeg');
        list($width, $height) = getimagesize($filename);
        $newwidth  = 110;
        $newheight = 110;
        $source    = imagecreatefromjpeg($filename);
        imagecopyresampled($im, $source, 365, 18, 0, 0, $newwidth, $newheight, $width, $height);
        imagejpeg($im);
        imagedestroy($im);
    }
    public function doMobileJiaLiDun()
    {
        global $_W, $_GPC;
        $name  = $_GPC['name'];
        $sex   = $_GPC['sex'];
        $rank  = $_GPC['rank'];
        $image = $_GPC['image'];
        $info  = getimagesize(tomedia($image));
        if ($info[2] != '2') {
            message('检测到你的图片原始格式不是JPG格式,请重新上传', $this->createMobileUrl('index'), error);
        }
        include $this->template('jialidunTemplate');
    }
    public function doMobileTuPianJiaLiDun()
    {
        global $_GPC;
        $name  = $_GPC['name'];
        $sex   = $_GPC['sex'];
        $rank  = $_GPC['rank'];
        $image = tomedia($_GPC['image']);
        $this->picjialidun($name, $sex, $rank, $image);
    }
    public function picjialidun($name, $sex, $rank, $image)
    {
        header('Content-Type: image/jpeg');
        $size                = 14;
        $background_pic_path = MODULE_ROOT . '/template/images/jialidun.jpg';
        $im                  = imagecreatefromjpeg($background_pic_path);
        $font                = MODULE_ROOT . '/template/fonts/6.ttf';
        $color               = imagecolorallocate($im, 65, 65, 65);
        imagettftext($im, $size, 0, 260, 60, $color, $font, $name);
        imagettftext($im, $size, 0, 385, 60, $color, $font, $sex);
        imagettftext($im, $size, 0, 120, 275, $color, $font, $rank);
        imagettftext($im, $size, 0, 310, 275, $color, $font, date('Y.m.d'));
        $filename = $image;
        $percent  = 0.2;
        header('Content-type: image/jpeg');
        list($width, $height) = getimagesize($filename);
        $newwidth  = 110;
        $newheight = 110;
        $source    = imagecreatefromjpeg($filename);
        imagecopyresampled($im, $source, 50, 110, 0, 0, $newwidth, $newheight, $width, $height);
        imagejpeg($im);
        imagedestroy($im);
    }
}