<?php

/**
 * 断言:该类所有的方法用法都相同,每个方法调用都可以传入一个message和code 当断言出错则抛出相应提示和错误码的异常信息
 * User: leon
 * Date: 15/9/4
 * Time: 上午1:05
 */
require_once dirname(__FILE__).'/../exception/AssertException.php';
class FlashAssert
{
    public static function not_empty($data, $message='',$code=0){
        if(empty($data) || is_null($data)){
            $message = empty($message) ? '数据为空' : $message;
            throw new AssertException($message, $code);
        }
    }

    public static function is_number($data, $message='',$code=0){
        if(is_numeric($data)){
            $message = empty($message) ? '不是数字' : $message;
            throw new AssertException($message,$code);
        }
    }

    public static function is_mobile($data, $message='',$code=0){
        if(!preg_match("/1[3458]{1}\d{9}$/",$data)){
            $message = empty($message) ? '请输入正确的手机号码' : $message;
            throw new AssertException($message,$code);
        }
    }

    public static function not_null($data, $message = '', $code=0){
        if($data == null){
            $message = '内容为空';
            throw new AssertException($message,$code);
        }
    }
}