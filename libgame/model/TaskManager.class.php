<?php
/**
 * 该类主要是处理任务逻辑
 */
class TaskManager extends ManagerBase {
	
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 创建用户任务
	 *
	 */
	public function createUserTask($gameuid,$new_task) {
		$new_task['gameuid'] = $gameuid;
		$this->insertDB($new_task);
		return $gameuid;
	}
	//更新 任务
	public function updateUserTask($gameuid,$merge) {
		$merge['gameuid'] = $gameuid;
		$this->updateDB($gameuid,$merge,array('gameuid'=>$gameuid));
	}
	//获取 任务信息
	public function getTask($gameuid)
	{
		return $this->getFromDb($gameuid,array('gameuid'=>$gameuid));
	}
	
	//$requestStr   id:count|id:count
	//$rewardStr    id:count|id:count
	public function checkTaskRewards($requestStr,$rewardStr){
		//审核 玩家任务奖励 是否规范
		//coin exp
		$xmlRewards = $this->getXmlRewards($requestStr);
		
		$rewardArr = explode("|",$rewardStr);
		if(empty($rewardArr)){
			$this->throwException('reward  error.'.$rewardArr,GameStatusCode::PARAMETER_ERROR);
		}
		foreach ($rewardArr as $itemReward){
			$itemArr = explode(":",$itemReward);
			$id = $itemArr[0];
			$count = abs($itemArr[1]);
			if ($id == MethodType::TASK_REWARD_COIN){
				if ($count > $xmlRewards['coin'] *GameModelConfig::TASK_MAX_COIN || $count < $xmlRewards['coin'] *GameModelConfig::TASK_MIN_COIN){
					return FALSE;
				}
			}else{
				if ($count > $xmlRewards['exp'] *GameModelConfig::TASK_MAX_COIN || $count < $xmlRewards['exp'] *GameModelConfig::TASK_MIN_COIN){
					return FALSE;
				}
			}
		}
		return TRUE;
	}
	public function getTaskRewards($requestStr,$npc){
		//npc 任务奖励 核算
		$xmlRewards = $this->getXmlRewards($requestStr);
		$rand = rand(GameModelConfig::TASK_NPC_RANDMIN,GameModelConfig::TASK_NPC_RANDMAX);
		
		if($npc == MethodType::TASK_MALENPC){
			return MethodType::TASK_REWARD_COIN.":".($xmlRewards['coin']*$rand)."|".MethodType::TASK_REWARD_EXP.":".($xmlRewards['exp']*$rand);
//			return array('coin'=>$xmlRewards['coin']*$rand,'exp'=>$xmlRewards['exp']*$rand);
		}else {
			return MethodType::TASK_REWARD_COIN.":".($xmlRewards['coin']*$rand)."|".MethodType::TASK_REWARD_LOVE.":".floor($xmlRewards['exp']*$rand/2);
//			return array('coin'=>$xmlRewards['coin']*$rand,'love'=>floor($xmlRewards['exp']*$rand/2));
		}
	}
	
	private function getXmlRewards($requestStr){
		$total_coin = 0;
		$total_exp = 0;
		$requestArr = explode("|",$requestStr);
		if(empty($requestArr)){
			$this->throwException('request  error.'.$requestStr,GameStatusCode::PARAMETER_ERROR);
		}
		foreach ($requestArr as $itemRequest){
			$itemArr = explode(":",$itemRequest);
			if(empty($itemArr)){
				$this->throwException('request item  error.'.$itemRequest,GameStatusCode::PARAMETER_ERROR);
			}
			$id = $itemArr[0];
			$count = abs($itemArr[1]);
			$itemspec = get_xml_def($id, XmlDbType::XMLDB_ITEM);
	     	$total_coin += ($itemspec["coinPrice"]*$count);
	     	$total_exp += ($itemspec["exp"]*$count);
		}
		return array("coin"=>$total_coin,"exp"=>$total_exp);
	}
	
	
	protected function getTableName(){
		return "user_task";
	}
}
?>