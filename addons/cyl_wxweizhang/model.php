<?php
function get_caiji($url)
{
    $contents = file_get_contents($url);
    preg_match_all('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', $contents, $match);
    $pic      = $match['0'];
    $img      = $match['2'];
    foreach ($pic as $key => $value) {
        $url    = $value;
        $imgarr = getimagesize($img[$key]);
        if ($imgarr['0'] > 60) {
            $fileurl = "<img src='http://img01.store.sogou.com/net/a/04/link?appid=100520031&w=600&url=$img[$key]' width='100%'/>";
        } else {
            $fileurl = "<img src='http://img01.store.sogou.com/net/a/04/link?appid=100520031&url=$img[$key]' width=$imgarr[0] />";
        }
        $contents = str_replace("$url", $fileurl, $contents);
    }
    $title    = explode('var msg_title = "', $contents);
    $title    = explode('";', $title['1']);
    $desc     = explode('var msg_desc = "', $contents);
    $desc     = explode('";', $desc['1']);
    $thumb    = explode('var msg_cdn_url = "', $contents);
    $thumb    = explode('";', $thumb['1']);
    $contents = explode('js_content', $contents);
    $contents = $contents[1];
    $contents = explode('<script type="text/javascript">', $contents);
    $contents = $contents[0];
    $contents = '<div id="js_content' . $contents;
    $data     = array(
        'title' => $title['0'],
        'contents' => $contents,
        'desc' => $desc['0'],
        'thumb' => $thumb['0']
    );
    return $data;
}
function getImgs($content, $order = 'ALL')
{
    $pattern = "/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i";
    preg_match_all($pattern, $content, $match);
    if (isset($match[2]) && !empty($match[2])) {
        if ($order === 'ALL') {
            return $match[2];
        }
        if (is_numeric($order) && isset($match[2][$order])) {
            return $match[2][$order];
        }
    }
    return '';
}
function relatively_date($date)
{
    if (!preg_match('/^\d+$/', $date))
        $date = strtotime(trim($date));
    $sec = time() - $date;
    switch (true) {
        case $sec < 3600:
            return round($sec / 60) . ' 分钟前';
        case $sec < 86400:
            return round($sec / 3600) . ' 小时前';
        case $sec < (86400 * 7):
            return round($sec / 86400) . ' 天前';
        case $sec < (86400 * 7 * 4):
            return round($sec / (86400 * 7)) . ' 周前';
        default:
            return longDate($date);
    }
}
function getSimilar($title, $arr_title)
{
    $arr_len = count($arr_title);
    for ($i = 0; $i <= ($arr_len - 1); $i++) {
        $arr_similar[$i] = similar_text($arr_title[$i], $title);
    }
    arsort($arr_similar);
    reset($arr_similar);
    $index = 0;
    foreach ($arr_similar as $old_index => $similar) {
        $new_title_array[$index] = $arr_title[$old_index];
        $index++;
    }
    return $new_title_array;
}
