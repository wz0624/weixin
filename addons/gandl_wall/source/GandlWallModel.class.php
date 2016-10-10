<?php

class GandlWallModel extends Model{


	// 自动验证定义
	protected $_validate = array(
		
		// 主题：必须，1~20
		array('topic','require','活动主题不能为空！'), 
		array('topic','1,20','活动主题长度为1~20个字',0,'length'),

		// 宣传图：必须
		array('banner','require','顶部宣传图不能为空！'), 

		// 详细说明：1~20000
		array('detail','require','详细说明不能为空！'), 
		array('detail','0,20000','详细说明长度不能超过20000个字',0,'length'),

		// 开始时间：日期时间
		array('start_time','require','开始时间不能为空！'), 

		// 结束时间：日期时间
		array('end_time','require','结束时间不能为空！'), 	

		// 每天开枪时间
		array('begin_time','require','每天开抢时间不能为空！'), 	
		// 每天结束时间时间
		array('over_time','require','每天停抢时间不能为空！'), 	

		// 冷却时间：数字
		array('cold_time','require','请设置冷却时间'), 
		array('cold_time','is_numeric','冷却时间输入错误,只能是数字',0,'function'),

		// 预热展示规则
		array('hot_rule','require','请设置预热展示规则'), 

		// 红包最小金额
		array('total_min','require','请设置撒钱最小金额'), 
		array('total_min','is_numeric','撒钱最小金额只能是数字',0,'function'),
	
		// 红包最大金额
		array('total_max','require','请设置撒钱最大金额'), 
		array('total_max','is_numeric','撒钱最大金额只能是数字',0,'function'),

		// 单个红包最低平均金额
		array('avg_min','require','请设置每份最低平均金额'), 
		array('avg_min','is_numeric','每份最低平均金额只能是数字',0,'function'),
		
		// 服务费率
		array('fee','require','请设置服务费率'), 
		array('fee','is_numeric','服务费率只能是数字',0,'function'),		
		
	);



	/**
	// 自定义自动完成
	// 时间段转换
	protected function auto_time($d){
		if(empty($d) || count($d)==0){
			return null;
		}else{
			foreach($d as $k=>$v){
				$this->__set($k.'_time',strtotime($v));
			}
		}
		return null;
	}
	**/
	
	// 创建活动
    public function add($data='',$options=array(),$replace=false) {
        if(empty($data)) {
            // 没有传递数据，获取当前数据对象的值
            if(!empty($this->data)) {
                $data           =   $this->data;
                // 重置数据
                $this->data     = array();
            }else{
                $this->error    = '没有数据';
                return false;
            }
        }
		
		// 业务补足
		$data['status']=1;
		$data['create_time']=time();
		$data['update_time']=time();

		pdo_insert($this->tableName, $data);

        return pdo_insertid();
    }

	// 保存活动
	public function save($data='',$options=array(),$replace=false) {
        if(empty($data)) {
            // 没有传递数据，获取当前数据对象的值
            if(!empty($this->data)) {
                $data           =   $this->data;
                // 重置数据
                $this->data     = array();
            }else{
                $this->error    = '没有数据';
                return false;
            }
        }
		
		// 业务处理
		unset($data['uniacid']); // 不允许修改所属
		$data['update_time']=time();

		$ret = pdo_update($this->tableName, $data, array('id'=>$data['id']));
        if($ret === false) {
			$this->error    = '数据更新失败';
			return false;
        }

        return true;
    }
}