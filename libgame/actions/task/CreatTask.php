<?php
include_once GAMELIB.'/model/TaskManager.class.php';
class CreatTask extends GameActionBase{
	protected function _exec()
	{
		$gameuid = $this->getParam("gameuid",'string');
		$npc = $this->getParam("npc",'int');
		$requestStr = $this->getParam("request",'string');
		$rewardStr = $this->getParam("reward",'string');
		
		$task_mgr = new TaskManager();
		$taskinfo = $task_mgr->getTask($gameuid);
		
		$change =array();
		//审核 任务奖励
		//task  npc;request;reward;time;isfinished
		if ($npc == MethodType::TASK_NONPC){
			if(!empty($taskinfo) && !empty($taskinfo['my_order'])){
				$this->throwException('already has my order'.$gameuid,GameStatusCode::TASK_HAS_ACCEPTED);
			}
			$checkbool = $task_mgr->checkTaskRewards($requestStr,$rewardStr);
			if(!$checkbool){
				return array('task_result'=>FALSE);
			}
			$change['my_order'] = MethodType::TASK_NONPC.";".$requestStr.";".$rewardStr.";".time();
		}else{
			if(!empty($taskinfo) && !empty($taskinfo['npc_order'])){
				$orderArr = explode(";",$taskinfo['npc_order']);
				$task_time = $orderArr[3];
				if(($task_time + GameModelConfig::TASK_EXPIRE_TIME)>time()){
					$this->throwException('already has npc_order'.$gameuid,GameStatusCode::TASK_HAS_ACCEPTED);
				}
			}
			$rewardStr = $task_mgr->getTaskRewards($requestStr,$npc);
			$change['npc_order'] = $npc.";".$requestStr.";".$rewardStr.";".time();
			//8小时 cd
			$change['npc_time'] = time();
			$change['buy_count'] = 0;
		}
		if(empty($taskinfo)){
			//创建 玩家任务 条
			$task_mgr->createUserTask($gameuid,$change);
		}else{
			$task_mgr->updateUserTask($gameuid,$change);
		}
		
		return array('task_result'=>TRUE,"new_task"=>$change);
	}
}
?>