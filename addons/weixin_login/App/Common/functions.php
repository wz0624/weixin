<?php

/**
 * 输出Json信息
 * @param Array $data 输出的数据
 */
function JSON($data)
{
    $return['Code'] = I('data.0',1,'',$data);
    if(C('IF_RETURN_INFO'))
        $return['Message'] = I('data.1','' ,'',$data);
    if(C('IF_RETURN_DATA'))
        $return['Data'] = I('data.2','',false,$data);
    die(json_encode($return,JSON_UNESCAPED_UNICODE));
}
function curl($url, $data = false,$s_option = array()){
    if(!$data){
        return file_get_contents($url);
    }
    $postdata = http_build_query( $data );
    $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );
    $context = stream_context_create($opts);
    $result = file_get_contents($url, false, $context);
    return trim($result, "\xEF\xBB\xBF");
//    $ch = curl_init();
//    $option = array(
//        CURLOPT_URL => $url,
//        CURLOPT_HEADER => 0,
//        CURLOPT_FOLLOWLOCATION => TRUE,
//        CURLOPT_TIMEOUT => 30,
//        CURLOPT_RETURNTRANSFER => TRUE,
//        CURLOPT_SSL_VERIFYPEER => 0,
//    );
//    if ( $data ) {
//        $option[CURLOPT_POST] = 1;
//        $option[CURLOPT_POSTFIELDS] = http_build_query($data);
//    }
//    foreach($s_option as $k => $v){
//        $option[$k] = $v;
//    };
//    curl_setopt_array($ch, $option);
//    $response = curl_exec($ch);
//    if (curl_errno($ch) > 0) {
//        exit("CURL ERROR:$url " . curl_error($ch));
//    }
//    curl_close($ch);
//    return trim($response, "\xEF\xBB\xBF");
}