<?php
/**
 * 家校通模块处理程序
 *
 * @author zhuhuan
 * @url http://bbs.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');
class Lianhu_schoolModuleProcessor extends WeModuleProcessor {
    public $table_pe;
    
    public function __construct(){
        $table_pe=tablename('lianhu');
        $table_pe=trim($table_pe,'`');
        $table_pe=str_ireplace('lianhu','',$table_pe); 
        $this->table_pe=$table_pe;
    }
	public function respond() {
		global $_W;
		$content = $this->message['content'];
		$need_arr=array('work_record','test_record','error_record','weak_record','jinbu_record','newmsg','teacher_telphone','xiangxi');
		if(in_array($content, $need_arr)){
			$result=$this->register();
			if(!is_array($result)){
				return $this->respText($result);
			}
			switch ($content) {
					case 'work_record':
							return $this->get_info('lianhu_work',$result['student_id'],'作业记录',$content);		
						break;
					case 'test_record':
							return $this->get_info('lianhu_test',$result['student_id'],'考试记录',$content);	
						break;	
					case 'error_record':
							return $this->get_info('error_record',$result['student_id'],'错题记录',$content);
						break;
					case 'weak_record':
							return $this->get_info('lianhu_weak',$result['student_id'],'弱项记录',$content);
						break;
					case 'jinbu_record':
							return $this->get_info('lianhu_jinbu',$result['student_id'],'进步记录',$content);											
						break;
					case 'teacher_telphone':
							return $this->teacher_tel();											
						break;
					case 'xiangxi':
							return $this->respText("<a href='".$_W['siteroot'].'app/'.$this->createMobileUrl('xiangxi')."'>点击查看</a>");											
						break;								

			}				
		}
	}
	public function teacher_tel(){
		global $_W;
		$teacher_list=pdo_fetchall("select * from ".$this->table_pe."lianhu_teacher where status=1 and uniacid={$_W['uniacid']} ");
		foreach ($teacher_list as $key => $value) {
			$text .=" 姓名：{$value['teacher_realname']}；电话：{$value['teacher_telphone']}\n";
		}
		return $this->respText($text);
	}
	public function register(){
		global $_W;
		$openid=$this->message['from'];
		$fanid=pdo_fetchcolumn("select fanid from ".tablename('mc_mapping_fans')." where openid='{$openid}' ");
		$student_result=pdo_fetch("select * from ".$this->table_pe."lianhu_student where fanid={$fanid} or fanid1={$fanid} or fanid2={$fanid} ");
		if($student_result){
			return $student_result;
		}else{
			$notice="您的账号还未绑定我校学生，请点击<a href='".$_W['siteroot'].'app/'.$this->createMobileUrl('bangding')."'>【绑定账号】</a>";
			return $notice;
		}
	}

	public function get_info($table,$sid,$text,$content){
		global $_W;
		$list=pdo_fetch("select * from ".$this->table_pe."".$table." where student_id={$sid} order by addtime desc");
		$notice="您好，此处获得到是最新的一条{$text};\n";
		if($content=='error_record'){
			$notice .="【时间：".date('Y-m-d H:i:s',$list['addtime'])."；内容：".$list['content2']."";
		}elseif($content=='weak_record'){
			$notice .="【时间：".date('Y-m-d H:i:s',$list['addtime'])."；内容：".$list['content']."";
		}else{
			$notice .="【时间：".date('Y-m-d H:i:s',$list['addtime'])."；标题：".$list['word']."；内容：".$list['content']."";
		}
		if($content=='test_record'){
			$notice .="；分数:{$list['score']}";
		}
			$notice .="；】\n";
		$notice .="<a href='".$_W['siteroot'].'app/'.$this->createMobileUrl('record',array('op'=>$content))."'>欲查询更多记录请点击此处</a>";
		return $this->respText($notice);
	}

}