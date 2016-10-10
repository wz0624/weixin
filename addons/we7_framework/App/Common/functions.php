<?php
/**
 * 微赞模块框架
 *
 * @author Jialin
 * @url http://www.012wz.com/thread-13093-1-1.html
 * @承接web网站定制化开发，微赞模块开发
 * @qq 77035993
 * @php开发学习，技术交流群70886552
 */

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