<?php
function get_timelineauction($pubtime)
{
    $time    = time();
    $seconds = $time - $pubtime;
    $days    = idate('z', $time) - idate('z', $pubtime);
    if ($days == 0) {
        if ($seconds < 3600) {
            if ($seconds < 60) {
                if (3 > $seconds) {
                    return '刚刚';
                } else {
                    return $seconds . '秒前';
                }
            }
            return intval($seconds / 60) . '分钟前';
        }
        return idate('H', $time) - idate('H', $pubtime) . '小时前';
    }
    if ($days == 1) {
        return '昨天 ' . date('H:i', $pubtime);
    }
    if ($days == 2) {
        return '前天 ' . date('H:i', $pubtime);
    }
    if ($days <= 7 && $days > 0) {
        return $days . '天前';
    }
    return date('Y-m-d', $pubtime);
}