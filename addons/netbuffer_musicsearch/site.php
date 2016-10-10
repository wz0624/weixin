<?php
defined ( 'IN_IA' ) or exit ( 'Access Denied' );
class Netbuffer_musicsearchModuleSite extends WeModuleSite {
	public function doMobileIndex() {
		global $_GPC, $_W;
		$suri=$_GPC["suri"];
		include $this->template("index");
	}
}