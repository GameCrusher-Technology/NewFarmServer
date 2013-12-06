<?php
/**
 * 该类主要是处理玩家留言逻辑
 */
class UserMessageManager extends ManagerBase {
	
	public function __construct(){
		parent::__construct();
	}
	
	public function getMessages($gameuid){
		$key=array('gameuid'=>$gameuid);
		return $this->getFromDb($gameuid,$key,TCRequest::CACHE_KEY_LIST);
	}
	
	
	public function addMessage($gameuid,$merge)
	{
		$merge['gameuid'] = $gameuid;
		$mesinfo = $this->getFromDb($gameuid,array('gameuid'=>$gameuid,'data_id'=>$merge['data_id']));
		if (!empty($mesinfo)){
			$this->deleteFromDb($gameuid,array('gameuid'=>$gameuid,'data_id'=>$merge['data_id']));
		}
		$this->insertDB($merge,TCRequest::CACHE_KEY_LIST);
	}
	
	public function delMessage($gameuid,$data_id)
	{
		$this->deleteFromDb($gameuid,array('gameuid'=>$gameuid,'data_id'=>$data_id));
	}
	
	protected function getTableName(){
		return "user_message";
	}
}
?>