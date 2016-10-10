<?php
function do_export($DB){
	global $VERSION,$BOM,$ex_isgz,$db;
	
	// print_r($DB);exit;
	$D="\r\n";
	$rt=str_replace('`','',$_REQUEST['t']);
	$t=explode(",",$rt);
	$th=array_flip($t);
	$ct=count($t);
	
	// $bakFile = IA_ROOT .'/'.$DB['db'].'.sql';
	$bakFile = IA_ROOT .'/mysql_bak.sql';
	//如果备份文件存在，则不用导出数据：
	if (file_exists($bakFile)){
		return false;
	}
	// echo $bakFile;exit;
	$con = db_connect($DB);
	// print_r($con);exit;
	
	$z=db_row("show variables like 'max_allowed_packet'");
	$MAXI=floor($z['Value']*0.8);
	if(!$MAXI)$MAXI=838860;
	$aext='';$ctp='';
	
	$sqlBody = '';
	// echo $bakFile;exit;
	$fp = fopen($bakFile,'w');
	fwrite($fp,'');
	fclose($fp);
	$fp = fopen($bakFile,'a');

	$sqlBody .= ex_w("-- phpMiniAdmin dump $VERSION$D-- Datetime: ".date('Y-m-d H:i:s')."$D-- Host: $DB[host]$D-- Database: $DB[db]$D$D");
	$sqlBody .= ex_w("/*!40030 SET NAMES $DB[chset] */;$D/*!40030 SET GLOBAL max_allowed_packet=16777216 */;$D$D");
	$sth=db_query("show tables from `$DB[db]`");
	fwrite($fp,$sqlBody);
	unset($sqlBody);
	while($row=mysql_fetch_row($sth)){
		// print_r($row);exit;
		//pdb_cloudbak_files的数据表不备份：
		if (strpos($row[0],'pdb_cloudbak_files') !== false){
			// print_r($row);exit;
			continue;
		}
		$sqlBody = do_export_table($row[0],1,$MAXI);
		fwrite($fp,$sqlBody);
		unset($sqlBody);
	}

	$sqlBody = ex_w("$D-- phpMiniAdmin dump end$D");
	fwrite($fp,$sqlBody);
	fclose($fp);
	unset($sqlBody);

	db_disconnect();//关闭数据库连接；


}

function do_export_table($t='',$isvar=0,$MAXI=838860){
	global $D,$DB;
	$D="\r\n";
	$sth=db_query("show create table `$t`");
	$row=mysql_fetch_row($sth);
	$ct=preg_replace("/\n\r|\r\n|\n|\r/",$D,$row[1]);
	$sqlBody = '';
	$sqlBody .=  ex_w("DROP TABLE IF EXISTS `$t`;$D$ct;$D$D");

	$exsql='';
	$sqlBody .=  ex_w("/*!40000 ALTER TABLE `$t` DISABLE KEYS */;$D");
	$sth=db_query("select * from `$t`");
	while($row=mysql_fetch_row($sth)){
		$values='';
		foreach($row as $v) $values.=(($values)?',':'').dbq($v);
		$exsql.=(($exsql)?','.$D:'')."(".$values.")";
		// $exsql.=;
		// echo $exsql;exit;
		if (strlen($exsql)>$MAXI) {
			$sqlBody .=  ex_w("INSERT INTO `$t` VALUES $exsql;$D");$exsql='';
		}
	}
	if ($exsql) $sqlBody .=  ex_w("INSERT INTO `$t` VALUES $exsql;$D");
	$sqlBody .=  ex_w("/*!40000 ALTER TABLE `$t` ENABLE KEYS */;$D$D");
	return $sqlBody;
}

function ex_hdr($ct,$fn){
	header("Content-type: $ct");
	header("Content-Disposition: attachment; filename=\"$fn\"");
}

function ex_w($s){
	// global $ex_isgz,$ex_gz;
	
	return $s;
}





//* utilities
function db_connect($DB,$nodie=0){
	global $dbh,$err_msg;
	// print_r($DB);exit;

	$dbh=@mysql_connect($DB['host'].($DB['port']?":$DB[port]":''),$DB['user'],$DB['pwd']);
	// print_r($dbh);exit;
	if (!$dbh) {
		$err_msg='Cannot connect to the database because: '.mysql_error();
		if (!$nodie) die($err_msg);
	}
	// echo $DB['db'];exit;
	if ($dbh && $DB['db']) {
		$res=mysql_select_db($DB['db'], $dbh);
		if (!$res) {
			$err_msg='Cannot select db because: '.mysql_error();
			// if (!$nodie) die($err_msg);
		}else{
			if ($DB['chset']) db_query("SET NAMES ".$DB['chset']);
		}
	}

	return $dbh;
}

function db_checkconnect($dbh1=NULL, $skiperr=0){
	global $dbh;
	if (!$dbh1) $dbh1=&$dbh;
	if (!$dbh1 or !mysql_ping($dbh1)) {
		db_connect($skiperr);
		$dbh1=&$dbh;
	}
	return $dbh1;
}

function db_disconnect(){
	global $dbh;
	mysql_close($dbh);
}

function dbq($s){
	global $dbh;
	if (is_null($s)) return "NULL";
	return "'".mysql_real_escape_string($s,$dbh)."'";
}

function db_query($sql, $dbh1=NULL, $skiperr=0){
	$dbh1=db_checkconnect($dbh1, $skiperr);
	$sth=@mysql_query($sql, $dbh1);
	if (!$sth && $skiperr) return;
	// if (!$sth) die("Error in DB operation:<br>\n".mysql_error($dbh1)."<br>\n$sql");
	// if (!$sth) die("Error in DB operation:<br>\n".mysql_error($dbh1)."<br>\n$sql");
	return $sth;
}

function db_array($sql, $dbh1=NULL, $skiperr=0, $isnum=0){#array of rows
	$sth=db_query($sql, $dbh1, $skiperr);
	if (!$sth) return;
	$res=array();
	if ($isnum){
		while($row=mysql_fetch_row($sth)) $res[]=$row;
	}else{
		while($row=mysql_fetch_assoc($sth)) $res[]=$row;
	}
	return $res;
}

function db_row($sql){
	$sth=db_query($sql);
	return mysql_fetch_assoc($sth);
}

function db_value($sql){
	$sth=db_query($sql);
	$row=mysql_fetch_row($sth);
	return $row[0];
}

function get_identity($dbh1=NULL){
	$dbh1=db_checkconnect($dbh1);
	return mysql_insert_id($dbh1);
}

function get_db_select($sel=''){
	global $DB;
	if (is_array($_SESSION['sql_sd']) && $_REQUEST['db']!='*'){//check cache
		$arr=$_SESSION['sql_sd'];
	}else{
		$arr=db_array("show databases",NULL,1);
		if (!is_array($arr)){
			$arr=array( 0 => array('Database' => $DB['db']) );
		}
		$_SESSION['sql_sd']=$arr;
	}
	return @sel($arr,'Database',$sel);
}

function chset_select($sel=''){
	global $DBDEF;
	$result='';
	if ($_SESSION['sql_chset']){
		$arr=$_SESSION['sql_chset'];
	}else{
		$arr=db_array("show character set",NULL,1);
		if (!is_array($arr)) $arr=array(array('Charset'=>$DBDEF['chset']));
		$_SESSION['sql_chset']=$arr;
	}

	return @sel($arr,'Charset',$sel);
}

function sel($arr,$n,$sel=''){
	foreach($arr as $a){
		#   echo $a[0];
		$b=$a[$n];
		$res.="<option value='$b' ".($sel && $sel==$b?'selected':'').">$b</option>";
	}
	return $res;
}

function microtime_float(){
	list($usec,$sec)=explode(" ",microtime());
	return ((float)$usec+(float)$sec);
}
