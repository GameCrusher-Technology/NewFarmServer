<?php
class UserActionCountManager extends ManagerBase {
	protected $gameuid=0;
	public function __construct($gameuid){
		parent::__construct();
		$gameuid=intval($gameuid);
		if (empty($gameuid)||$gameuid<1){
			$this->throwException("gameuid[$gameuid] error",GameStatusCode::USER_NOT_EXISTS);
		}
		$this->gameuid=$gameuid;
	}
	public static function updateActionCount($gameuid,$action_id,$count=1){
		$count=abs(intval($count));
		if($count<1) return false;
		$action_count_mgr=new self($gameuid);
		$old_entry=$action_count_mgr->getEntry($action_id);
		if (empty($old_entry)){
			$action_count_mgr->insert($action_id,$count);
		}else {
			$new_count=$old_entry['count']+$count;
			$action_count_mgr->update($action_id,array('count'=>$new_count));
		}
		return true;
	}
	public function getCacheEntry($action_id){
		$key = $this->getTableName().'_'.$action_id.'_'.$this->gameuid;
		return $this->getFromCache($key,$this->gameuid);
	}
	public function getEntry($action_id){
		return $this->getFromDb($this->gameuid,array('gameuid'=>$this->gameuid,'action_id'=>$action_id));
	}
	public function update($action_id,$data){
		$this->updateDB($this->gameuid,$data,array('gameuid'=>$this->gameuid,'action_id'=>$action_id));
	}
	public function insert($action_id,$count){
		$data=array('gameuid'=>$this->gameuid,"action_id"=>$action_id,"count"=>$count);
		$this->insertDB($data,TCRequest::CACHE_KEY_LIST);
	}
	public function getEntryList(){
		$list=$this->getFromDb($this->gameuid,array('gameuid'=>$this->gameuid),TCRequest::CACHE_KEY_LIST);
		return $list;
	}
	protected function getTableName(){
		return "user_action_count";
	}
}
?>