<?php
header ("Content-type: image/png");
//$name = $_REQUEST['A'] ;


$A = $_GET['path'];
$B = $_GET['img'];

$x = $_GET['qrx'];
$y = $_GET['qry'];
$rh = 100;


$im1 = imagecreatefromstring(file_get_contents($A));
$im2 = imagecreatefromstring(file_get_contents($B));


imagecopymerge($im1, $im2, $x, $y, 0, 0, imagesx($im2), imagesy($im2), $rh);


imagejpeg($im1); 
?>