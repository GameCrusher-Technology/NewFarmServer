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
	public function getMessage($gameuid,$data_id){
		$key=array('gameuid'=>$gameuid,'data_id'=>$data_id);
		return $this->getFromDb($gameuid,$key);
	}
	public function updateMessage($gameuid,$data_id,$change){
		$key=array('gameuid'=>$gameuid,'data_id'=>$data_id);
		return $this->updateDB($gameuid,$change,$key);
	}
	public function addMessage($gameuid,$merge)
	{
		$invite_mes = array();
		$f_messages = $this->getMessages($gameuid);
		if(empty($merge['data_id'])){
			$max_id =0;
			foreach ($f_messages as $mesInfo){
				$max_id = max($max_id,$mesInfo['data_id']);
				if($mesInfo['type'] == $merge['type']){
					array_push($invite_mes,$mesInfo);
				}
			}
			$max_id ++ ;
			$merge['data_id'] = $max_id;
		}
		
//		if (count($invite_mes) >= 20){
//			usort($invite_mes,$this->timesort);
//			$del = array_shift($invite_mes);
//			$this->delMessage($gameuid,$del['data_id']);
//		}
		$merge['gameuid'] = $gameuid;
		$mesinfo = $this->getFromDb($gameuid,array('gameuid'=>$gameuid,'data_id'=>$merge['data_id']));
		if (!empty($mesinfo)){
			$this->updateMessage($gameuid,$merge['data_id'],$merge);
		}else {
			$this->insertDB($merge,TCRequest::CACHE_KEY_LIST);
		}
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