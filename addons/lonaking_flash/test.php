<?php

$content = file_get_contents(dirname(__FILE__)."/FlashCommonServiceMi.php");
$key     = "/* PHP Encode by  http://012wz.com/ */";
echo $key;
$re = strpos($content,$key);
var_dump($re) ;