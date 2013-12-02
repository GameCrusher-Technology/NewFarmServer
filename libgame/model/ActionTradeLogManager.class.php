<?php
class ActionTradeLogManager extends ManagerBase {
	protected function getTableName(){
		return "action_trade_log";
	}
	public function get($gameuid,$id){
		return $this->getFromDb($gameuid,array('id'=>$id));
	}
	public function insert($change){
		$this->insertDB($change);
	}
}
?>