<?php
$hshCore=new Hsh_core();
class Hsh_core {
	
	var $fileList=array(
		"/addons/hsh_tools/include/WxHelper.class.php",
		"/addons/hsh_tools/include/function.class.php",
	);
	public function __construct() {
		foreach($this->fileList as $filename){
			$this->chekAddons($filename);
			require_once IA_ROOT.$filename;
		}
	}
	private function chekAddons($file) {
		if(!file_exists(IA_ROOT.$file)){
			die("'$file' not found<br /> 请安装我们的工具集模块") ;
		}
	}

}
