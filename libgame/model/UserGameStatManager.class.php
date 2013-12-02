<?php
/*
`gameuid` 
`uid` 
`total_charge_amount` 玩家总支付数额
`total_charge_count` 玩家总支付次数
`last_charge_amount` 上次支付数额
`last_charge_time` 上次支付时间
`is_new`  是不是没有给过奖励的支付
`total_active_days` 总活跃天数
`last_login_time` 最近登录时间
`resource`记录用户来源
`int_a` 
`int_b` 
`int_c` 
`int_d` 
`int_e`
`update_time` 
`create_time` 
*/
class UserGameStatManager extends ManagerBase {
	protected function getTableName(){
		return "user_game_stat";
	}
	public function update($gameuid,$modify){
		$this->updateDB($gameuid,$modify,array("gameuid"=>$gameuid),false);
	}
	public function updateLoadTime($gameuid,$loadTime){
		$this->updateDB($gameuid,array("gameuid"=>$gameuid),array("last_login_time"=>$loadTime),true);
	}
	public function get($gameuid){
		$result=$this->getFromDb($gameuid,array("gameuid"=>$gameuid));
		return $result;
	}
	public function insert($gameuid,$data){
		$data['gameuid']=$gameuid;
		$this->insertDB($data);
	}
}
?>