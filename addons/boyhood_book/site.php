<?php
/**
 * 微情书模块微站定义
 *
 * @author junsion
 * @url http://www.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');
define('RES','../addons/boyhood_book/template/mobile/');
class boyhood_bookModuleSite extends WeModuleSite {

	public function doWebManage() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_W,$_GPC;
		$op = $_GPC['op'];
		$rid = $_GPC['rid'];
		if (!$op){
			$list = pdo_fetchall('select m.*,r.name from '.tablename($this->modulename.'_rule')." m left join "
					.tablename('rule')." r on r.id=m.rid "." where m.weid='{$_W['weid']}' order by rid desc");
// 			//预约列表
			foreach ($list as $key => $value) {
				$list[$key]['book'] = pdo_fetchcolumn('select count(id) from '.tablename($this->modulename."_record")." where rid='{$value['rid']}'");
			}
			include $this->template('manage');
			exit;
		}elseif ($op == 'post'){
			$id = $_GPC['id'];
			if (checksubmit('submit')){
				$data = array(
					'weid'=>$_W['uniacid'],
					'title'=>$_GPC['title'],
					'cate'=>$_GPC['cate'],
					'thumb'=>$_GPC['thumb'],
					'oprice'=>floatval($_GPC['oprice']),
					'nprice'=>floatval($_GPC['nprice']),
					'content'=>$_GPC['content'],
					'displayorder'=>$_GPC['displayorder'],
					'cate'=>$_GPC['cate'],
				);
				if (empty($id)){
					if (pdo_insert($this->modulename."_list",$data) === false){
						message('保存失败！');
					}else message('保存成功！',$this->createWebUrl('manage',array('op'=>'display')));
				}else{
					if (pdo_update($this->modulename."_list",$data,array('id'=>$id)) === false){
						message('保存失败！');
					}else message('保存成功！',$this->createWebUrl('manage',array('op'=>'display')));
				}
			}
			$cates = pdo_fetchall('select * from '.tablename($this->modulename."_cate")." where weid='{$_W['uniacid']}'");
			$item = pdo_fetch('select * from '.tablename($this->modulename."_list")." where id='{$id}'");
		}elseif ($op == 'display'){
			$cid = $_GPC['cate'];
			$cates = pdo_fetchall('select * from '.tablename($this->modulename."_cate")." where weid='{$_W['uniacid']}'");
			$condition = '';
			if (!empty($cid)){
				$condition .= " and cate={$cid}";
			}
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$list = pdo_fetchall('select *,(select count(*) from '.tablename($this->modulename."_record").' where lid=l.id) as count from '.tablename($this->modulename.'_list')." l where weid='{$_W['uniacid']}' {$condition} order by displayorder desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
			$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this->modulename.'_list') . " where weid='{$_W['uniacid']}' {$condition}");
			$pager = pagination($total, $pindex, $psize);
		}elseif ($op == 'record'){
			$rid = $_GPC['rid'];
			$lid = $_GPC['lid'];
			$name = $_GPC['name'];
			$title = $_GPC['title'];
			$date = $_GPC['date'];
			$mobile = $_GPC['mobile'];
			$condition = '';
			if (!empty($rid)) $condition = " and rid={$rid}";
			if (!empty($lid)) $condition = " and lid={$lid}";
			if (!empty($name)) $condition = " and realname like '%{$name}%'";
			if (!empty($title)) $condition = " and title like '%{$title}'";
			if (!empty($mobile)) $condition = " and mobile like '%{$mobile}'";
			if ($_GPC['export'] == 1){//导出
				$list = pdo_fetchall('select r.*,l.title from '.tablename($this->modulename.'_record')." r join ".tablename($this->modulename.'_list')." l on l.id=r.lid where r.weid='{$_W['uniacid']}' {$condition} order by r.status,r.createtime desc ");
				$filename = '预约记录'. date('YmdHis') . '.csv';
				$exceler = new Jason_Excel_Export();
				$exceler->charset('UTF-8');
				// 生成excel格式 这里根据后缀名不同而生成不同的格式。jason_excel.csv
				$exceler->setFileName($filename);
				// 设置excel标题行
				$excel_title = array('业务项目', '预约人','手机号码', '到访时间', '预约时间', '状态');
				$exceler->setTitle($excel_title);
				// 设置excel内容
				$excel_data = array();
				foreach ($list as $key => $value) {
					$excel_data[] = array($value["title"], $value['realname'], $value['mobile'], date('Y-m-d H:i',$value['visitetime']), date('Y-m-d H:i',$value['createtime']),$value['status']==1?'已处理':'未处理');
				}
				$excel_data[] = array('','','','','','');
				$excel_data[] = array('', '总人数:', count($list), '', '','', '');
				$exceler->setContent($excel_data);
				// 生成excel
				$exceler->export();
				exit;
			}
			if (!empty($date)) $condition = " and visitetime>='".strtotime($date['start'])."' and visitetime<='".strtotime($date['end'])."'";
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$list = pdo_fetchall('select r.*,l.title from '.tablename($this->modulename.'_record')." r join ".tablename($this->modulename.'_list')." l on l.id=r.lid where r.weid='{$_W['uniacid']}' {$condition} order by r.createtime desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
			$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this->modulename.'_record') . " where weid='{$_W['uniacid']}' {$condition}");
			$pager = pagination($total, $pindex, $psize);
		}elseif ($op == 'status'){
			if (pdo_update($this->modulename."_record",array('status'=>1),array('id'=>$_GPC['id'])) === false){
				message('处理失败！');
			}else message('处理成功！',$this->createWebUrl('manage',array('op'=>'record')));
		}elseif ($op == 'delete'){
			$id = $_GPC['id'];
			$record = pdo_fetch("SELECT * FROM " . tablename($this->modulename.'_record') . " WHERE 'lid'={$id} and 'weid'={$_W['uniacid']}");
			if (!empty($record)){
				if (pdo_delete($this->modulename.'_record',array('lid'=>$id , 'weid'=>$_W['uniacid']))== false) message('删除失败！' , referer() , 'error');
				else {
					pdo_delete($this->modulename.'_list',array('id'=>$id , 'weid'=>$_W['uniacid']));
					message('删除成功！' , referer() , 'success');
				}
			}else {
				pdo_delete($this->modulename.'_list',array('id'=>$id , 'weid'=>$_W['uniacid']));
				message('删除成功！' , referer() , 'success');
			}
			
		}
		include $this->template('list');
	}
	
	public function doWebEditCate(){
		global $_W,$_GPC;
		$weid = $_W['uniacid'];
		$type = $_GPC['type'];
		$new_cate = $_GPC['new_cate'];
		$cid = $_GPC['cid'];
		$cate = $new_cate;
		if($type == 1)
			$cate = $_GPC['old_cate'];
		$cates = pdo_fetch('select * from '.tablename($this->modulename.'_cate').' where id=:id',array('id'=>$cid));
		if(!empty($cates) && $type != 0){
			if($type == 1){
				if(pdo_update($this->modulename.'_cate',array('title'=>$new_cate),array('id'=>$cates['id'])) == false)
					echo 0;
				else{
					echo 1;
				}
			}else if($type == 2){//删除分类
				if(pdo_delete($this->modulename.'_cate',array('id'=>$cates['id'])) == false)
					echo 0;
				else{//先删除分类 后删除二维码
				//	pdo_delete($this->modulename.'_songs',array('weid'=>$weid,'cate'=>$cates['id']));
					echo 1;
				}
			}
			else echo -1;
		}
		else{
			if($type == 0){
				$cates = pdo_fetch('select * from '.tablename($this->modulename.'_cate').' where title=:title',array('title'=>$cate));
				if(!empty($cates)){
					die('-1');
				}
				if(pdo_insert($this->modulename.'_cate',array('weid'=>$weid,'title'=>$new_cate)) == false)
					echo pdo_insertid();
				else echo 1;
			}
		}
	}
	
	public function doMobileIndex(){
		global $_W,$_GPC;
		$rid = $_GPC['rid'];
		$rule = pdo_fetch('select * from '.tablename($this->modulename."_rule")." where rid='{$rid}'");
		$list = pdo_fetchall('select * from '.tablename($this->modulename."_list")." where cate='{$rule['cate']}'");
		include $this->template('index');
	}
	
	public function doMobileRecord(){
		global $_W,$_GPC;
		$rid = $_GPC['rid'];
		$lid = $_GPC['lid'];
		$mobile = $_GPC['mobile'];
		if(!preg_match("/^1[34578]\d{9}$/", $mobile)) message('请填写正确的手机号码');
		$realname = $_GPC['realname'];
		$visitetime = $_GPC['visitetime'];
		$from_user = $_W['openid'];
		$record = pdo_fetch('select * from '.tablename($this->modulename."_record")." where rid='{$rid}' and lid='{$lid}' and mobile='{$mobile}'");
		$title = pdo_fetchcolumn('select title from ' . tablename($this->modulename."_list") . "where id={$lid}");
		$type = pdo_fetchcolumn('select c.title from ' . tablename($this->modulename."_list") . " l left join " . tablename($this->modulename."_cate") . " c on c.id=l.cate where l.id={$lid}");
		$cfg = $this->module['config'];
		$from_admin = explode(',', $cfg['MSGID']['adminmsg']);
		if (!empty($record)){
			message('该手机号码已经预约了哦！');
		}
		if (pdo_insert($this->modulename."_record",array('weid'=>$_W['uniacid'],'rid'=>$rid,'lid'=>$lid,'realname'=>$realname,'mobile'=>$mobile,'visitetime'=>strtotime($visitetime),'createtime'=>time(),'openid'=>$from_user)) === false){
			message('预约失败，请联系管理员！');
		}else {
			message('预约成功！',$this->createMobileUrl('index',array('rid'=>$rid)));
		}
	}
	function sendTemplateMsg($openid,$msgid,$data,$url = '',$topcolor = '#FF0000'){
		global $_W;
		if (stripos($url,'http://') === false){
			$url = $_W['siteroot'].str_replace('./','app/',$url);
		}
		load()->classs('weixin.account');
		$acid = pdo_fetchcolumn('select acid from '.tablename('account')." where uniacid={$_W['uniacid']}");
		$accObj = WeixinAccount::create($acid);
		$accObj->sendTplNotice($openid,$msgid,$data,$url,$topcolor);
	}
	
	public function doMobileImg(){
		global $_W,$_GPC;
		$lid = $_GPC['lid'];
		$list = pdo_fetch('select * from '.tablename($this->modulename."_list")." where id='{$lid}'");
		die("
			<title>{$list['title']}</title>
			<img src='".toimage($list['thumb'])."'>
				");
	}

}


/**
 * 导出Excel
 *
 * @package:     Jason
 * @subpackage:  Excel
 * @version:     1.0
 */
class Jason_Excel_Export
{
	/**
	 * Excel 标题
	 *
	 * @type: Array
	 */
	private $_titles = array();

	/**
	 * Excel 标题数目
	 *
	 * @type: int
	*/
	private $_titles_count = 0;

	/**
	 * Excel 内容
	 *
	 * @type:  Array
	 */
	private $_contents = array();

	/**
	 * Excel 内容数据
	 *
	 * @type:  Array
	*/
	private $_contents_count = 0;

	/**
	 * Excel 文件名
	 *
	 * @type: string
	 */
	private $_fileName = '';
	private $_split = "\t";

	private $_charset = '';

	/**
	 * 默认文件名
	 *
	 * @const :
	 */
	const DEFAULT_FILE_NAME = 'jason_excel.xls';


	/**
	 * 构造函数..
	 *
	 * @param    string  param
	 * @return   mixed   return
	 */
	function __construct($fileName = null)
	{
		if ($fileName !== null) {
			$this->_fileName = $fileName;
		} else {
			$this->setFileName();
		}
	}

	/**
	 * 设置生成文件名
	 *
	 * @param    string  param
	 * @return   mixed   Jason_Excel_Export
	 */
	public function setFileName($fileName = self::DEFAULT_FILE_NAME)
	{
		$this->_fileName = $fileName;
		$this->setSplite();
		return $this;
	}

	private function _getType()
	{
		return substr($this->_fileName, strrpos($this->_fileName, '.') + 1);
	}

	public function setSplite($split = null)
	{
		if ($split === null) {
			switch ($this->_getType()) {
				case 'xls':
					$this->_split = "\t";
					break;
				case 'csv':
					$this->_split = ",";
					break;
			}
		} else
			$this->_split = $split;
	}

	/**
	 * 设置Excel标题
	 *
	 * @param    string  param
	 * @return   mixed   Jason_Excel_Export
	 */
	public function setTitle(&$title = array())
	{
		$this->_titles = $title;
		$this->_titles_count = count($title);
		return $this;
	}

	/**
	 * 设置Excel内容
	 *
	 * @param    string  param
	 * @return   mixed   Jason_Excel_Export
	 */
	public function setContent(&$content = array())
	{
		$this->_contents = $content;
		$this->_contents_count = count($content);
		return $this;
	}

	/**
	 * 向excel中添加一行内容
	 */
	public function addRow($row = array())
	{
		$this->_contents[] = $row;
		$this->_contents_count++;
		return $this;
	}

	/**
	 * 向excel中添加多行内容
	 */
	public function addRows($rows = array())
	{
		$this->_contents = array_merge($this->_contents, $rows);
		$this->_contents_count += count($rows);
		return $this;
	}


	/**
	 * 数据编码转换
	 */
	public function toCode($type = 'GB2312', $from = 'auto')
	{
		foreach ($this->_titles as $k => $title) {
			$this->_titles[$k] = mb_convert_encoding($title, $type, $from);
		}

		foreach ($this->_contents as $i => $contents) {
			$this->_contents[$i] = $this->_toCodeArr($contents);
		}

		return $this;
	}

	private function _toCodeArr(&$arr = array(), $type = 'GB2312', $from = 'auto')
	{
		foreach ($arr as $k => $val) {
			$arr[$k] = mb_convert_encoding($val, $type, $from);
		}

		return $arr;
	}

	public function charset($charset = '')
	{
		if ($charset == '')
			$this->_charset = '';
		else {
			$charset = strtoupper($charset);
			switch ($charset) {
				case 'UTF-8' :
				case 'UTF8' :
					$this->_charset = ';charset=UTF-8';
					break;

				default:
					$this->_charset = ';charset=' . $charset;
			}
		}

		return $this;
	}

	/**
	 * 导出Excel
	 *
	 * @param    string  param
	 * @return   mixed   return
	 */
	public function export()
	{
		$header = '';
		$data = array();

		$header = implode($this->_split, $this->_titles);

		for ($i = 0; $i < $this->_contents_count; $i++) {
			$line_arr = array();
			foreach ($this->_contents[$i] as $value) {
				if (!isset($value) || $value == "") {
					$value = '""';
				} else {
					$value = str_replace('"', '""', $value);
					$value = '"' . $value . '"';
				}

				$line_arr[] = $value;
			}

			$data[] = implode($this->_split, $line_arr);
		}

		$data = implode("\n", $data);
		$data = str_replace("\r", "", $data);

		if ($data == "") {
			$data = "\n(0) Records Found!\n";
		}

		header("Content-type: application/vnd.ms-excel" . $this->_charset);
		header("Content-Disposition: attachment; filename=$this->_fileName");
		header("Pragma: no-cache");
		header("Expires: 0");

		echo "\xEF\xBB\xBF" . $header . "\n" . $data;
	}
}