<?php

/**
 * Created by PhpStorm.
 * User: leon
 * Date: 15/10/17
 * Time: 下午2:29
 */
class FlashHelper
{
    /**
     *
     */
    public static function fetchModelArrayIds($arr,$columns_name = 'id'){
        $result = array();
        foreach($arr as $a){
            $result[] = $a[$columns_name];
        }
        return $result;
    }
    public static function fetchColumnArray($arr,$columns_name ,$kill_null = false, $kill_repeat = false){
        $result = array();
        foreach($arr as $a){
            if($kill_null){
                if(!empty($a[$columns_name])){
                    $result[] = $a[$columns_name];
                }
            }else{
                $result[] = $a[$columns_name];
            }
        }
        if($kill_repeat){
            return array_unique($result);
        }else{
            return $result;
        }
    }

    public static function array2Map($arr, $key = 'id'){
        $result[] = array();
        foreach($arr as $data){
            $result[$data[$key]] = $data;
        }
        return $result;
    }
}