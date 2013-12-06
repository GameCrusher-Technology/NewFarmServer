<?php
include_once GAMELIB.'/model/TaskManager.class.php';
class BuyNpcTask extends GameActionBase{
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$npc = $this->getParam("npc",'int');
		$requestStr = $this->getParam("request",'string');
		
		$task_mgr = new TaskManager();
		$taskinfo = $task_mgr->getTask($gameuid);
		
		//判断当前任务
		$npc_order = $taskinfo['npc_order'];
		if(!empty($npc_order)){
			$orderArr = explode(";",$npc_order);
			$task_time = $orderArr[3];
			
			if(($task_time + GameModelConfig::TASK_EXPIRE_TIME)>time()){
				$this->throwException("task already exist  ".$gameuid,GameStatusCode::TASK_NOT_EXIST);
			}
		}
		$rewardStr = $task_mgr->getTaskRewards($requestStr,$npc);
		$change = array();
		$cost['gem'] = -pow(2,$taskinfo['buy_count']);
		
		$change['npc_order'] = $npc.";".$requestStr.";".$rewardStr.";".time();
		$change['buy_count'] = 1+$taskinfo['buy_count'];
		
		$task_mgr->updateUserTask($gameuid,$change);
		//扣去 农币
		$this->user_account_mgr->updateUserStatus($gameuid,$cost);
		
		return array('task_result'=>TRUE,"new_task"=>$change);
	}
}
?>