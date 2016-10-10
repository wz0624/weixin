<?php
/**
 * hello world模块微站定义
 *
 * @author 穿越的一只小猪
 * sir_vip@126.com
 * @url http://bbs.wdlcms.com/
 */
defined('IN_IA') or exit('Access Denied');

class Jiakao_systemModuleSite extends WeModuleSite {
	// 此处为科目一模拟考试的题库的图片的云存储地址，也可以配置为自己站点
	public $img_path = 'http://osingertest.qiniudn.com/';// 七牛云存储图片地址
	// public $img_path = 'addons/jiakao_system/pic/';
	// 首页
	public function doMobileIndex() {
		global $_W;
		include $this->template('index');
	}
	// 顺序答题
	public function doMobileSequent()
	{
		global $_W;
		include $this->template('fn_sequent');
	}
	// 模拟考试 随机100题目
	public function doMobileImitate() {
		global $_W;
		//这个操作被定义用来呈现 功能封面
		//生成100个题目的id
		$id_arr = array();
		for($i=0;$i<100;$i++)
		{
			$id= rand(1,898);
			if($id_arr[$id])
			{
			continue;
				$i--;
			}
			else
			{
				$id_arr[] = $id;
			}
		}
		
		$t_id_arr = json_encode($id_arr);
		include $this->template('fn_imitate');
	}
	// 随机10题
	public function doMobileRandomten() {
		global $_W;
		//这个操作被定义用来呈现 功能封面
		$id_arr = array();
		for($i=0;$i<10;$i++)
		{
			$id= rand(1,898);
			if($id_arr[$id])
			{
				continue;
				$i--;
			}
			else 
			{
				$id_arr[] = $id;
			}
		}
		
		$t_id_arr = json_encode($id_arr);
		include $this->template('fn_random_ten');
	}
	// 考试的易错题
	public function doMobileMyct() {
		global $_W;
		//这个操作被定义用来呈现 规则列表
		include $this->template('fn_my_ct');
	}
	// 我的易错题
	public function doMobileMywrong()
	{
		global $_W;
		//这个操作被定义用来呈现 规则列表
		include $this->template('fn_my_wrong');
	}

	/*
	根据题号得到题目信息
	*/
	protected function getinfo($id)
	{
		//$r = pdo_fetchall("SELECT * FROM ".tablename('jiakao_tiku')." WHERE t_id = :t_id", array(':t_id' => '$id'));
		$r = pdo_fetchall("select * from ".tablename('jiakao_tiku')." where t_id='$id' ");
		return $r[0];
	}
	/*
	用于获取顺序答题的内容信息的处理ajax返回
	*/
	public function doMobileSequentAjax()
	{

		global $_W;
		$t_id = $_POST["t_id"];
		$t_id<0 && $t_id =0;
		$t_id>898 && $t_id =0;

		$info = $this->getinfo($t_id);

		if($info === false)
		{
			//获取题目信息失败
		}
		else 
		{
			$item['id'] = $info['id'];
			$item['t_id'] = $info['t_id'];//试题编号
			$item['sort'] = $info['sort'];//试题类别
			$item['type'] = $info['type'];//试题类型
			$item['title'] = $info['title'];//题目
			$item['pic'] = $info['pic'];//图片名称
			if($item['pic'])
			{
				// $item['pic_url'] = $_W['siteroot'].$this->img_path.$info['pic'].".png";//本地图库地址
				$item['pic_url'] = $this->img_path.$info['pic'].".png";//七牛图片地址
			}
			else 
			{
				$item['pic_url'] = '';
			}
			
			$item['s_a'] = $info['s_a'];
			$item['s_b'] = $info['s_b'];
			$item['s_c'] = $info['s_c'];
			$item['s_d'] = $info['s_d'];
			if($item['type'] == "选择题")
			{
				$item['type_desc'] = "0";
			}
			elseif($item['type'] == "判断题")
			{
				$item['type_desc'] = "1";
			}
			$item['answer'] = $info['answer'];//答案
			$item['belong'] = $info['belong'];//试题大类
			$item['percent'] = $info['percent'];
		}
		
		echo json_encode($item);
	}
}