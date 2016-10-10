<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
class Ewei_DShop_Message{
    public function sendTplNotice($weizan_0, $weizan_1, $weizan_2, $weizan_3 = '', $weizan_4 = null){
        if (!$weizan_4){
            $weizan_4 = m('common') -> getAccount();
        }
        if (!$weizan_4){
            return;
        }
        return $weizan_4 -> sendTplNotice($weizan_0, $weizan_1, $weizan_2, $weizan_3);
    }
    public function sendCustomNotice($weizan_5, $weizan_6, $weizan_3 = '', $weizan_4 = null){{
            if (!$weizan_4){
                $weizan_4 = m('common') -> getAccount();
            }
            if (!$weizan_4){
                return;
            }
            $weizan_7 = "";
            if(is_array($weizan_6)){
                foreach ($weizan_6 as $weizan_8 => $weizan_9){
                    if (!empty($weizan_9['title'])){
                        $weizan_7 .= $weizan_9['title'] . ':' . $weizan_9['value'] . '
';
                    }else{
                        $weizan_7 .= $weizan_9['value'] . '
';
                        if ($weizan_8 == 0){
                            $weizan_7 .= '
';
                        }
                    }
                }
            }else{
                $weizan_7 = $weizan_6;
            }
            if (!empty($weizan_3)){
                $weizan_7 .= "<a href='{$weizan_3}'>点击查看详情</a>";
            }
            return $weizan_4 -> sendCustomNotice(array('touser' => $weizan_5, 'msgtype' => 'text', 'text' => array('content' => urlencode($weizan_7))));
        }
    }
    public function sendImage($weizan_5, $weizan_10){
        $weizan_4 = m('common') -> getAccount();
        return $weizan_4 -> sendCustomNotice(array('touser' => $weizan_5, 'msgtype' => 'image', 'image' => array('media_id' => $weizan_10)));
    }
    public function sendNews($weizan_5, $weizan_11, $weizan_4 = null){
        if(!$weizan_4){
            $weizan_4 = m('common') -> getAccount();
        }
        return $weizan_4 -> sendCustomNotice(array('touser' => $weizan_5, 'msgtype' => 'news', 'news' => array('articles' => $weizan_11)));
    }
}
