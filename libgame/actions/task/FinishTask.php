<?php
include_once GAMELIB.'/model/TaskManager.class.php';
include_once GAMELIB.'/model/UserGameItemManager.class.php';
class FinishTask extends GameActionBase{
	protected function _exec()
	{
		//完成 npc 任务
		$gameuid = $this->getParam("gameuid",'string');
		
		$task_mgr = new TaskManager();
		$taskinfo = $task_mgr->getTask($gameuid);
		if (empty($taskinfo) || empty($taskinfo['npc_order'])){
			$this->throwException("task not exist ".$gameuid,GameStatusCode::TASK_NOT_EXIST);
		}
		$npc_order = $taskinfo['npc_order'];
		$orderArr = explode(";",$npc_order);
		$npc = $orderArr[0];
		$orderRequestStr = $orderArr[1];
		$orderRewardStr  = $orderArr[2];
		$task_time = $orderArr[3];
		//判断 任务 是否过期
		if(($task_time + GameModelConfig::TASK_EXPIRE_TIME)<time()){
			$this->throwException("task expired  ".$gameuid,GameStatusCode::TASK_HAS_EXPIRED);
		}
		
		$item_Mgr = new UserGameItemManager($gameuid);
		//判断任务条件 达到
		$orderRequestArr = explode("|",$orderRequestStr);
		foreach ($orderRequestArr as $requestItemStr){
			$requestItem = explode(":",$requestItemStr);
			$id = $requestItem[0];
			$count = $requestItem[1];
			$item_Mgr->subItem($id,$count);
		}
		$item_Mgr ->checkReduceItemCount();
		
		//给与奖励
		$changeUser = array();
		$orderRewardArr = explode("|",$orderRewardStr);
		foreach ($orderRewardArr as $rewardItemStr){
			$rewardItem = explode(":",$rewardItemStr);
			$id = $rewardItem[0];
			$count = $rewardItem[1];
			if($id == MethodType::TASK_REWARD_COIN){
				$changeUser['coin'] = $count;
			}elseif ($id == MethodType::TASK_REWARD_EXP){
				$changeUser['exp'] = $count;
			}else {
				$changeUser['love'] = $count;
			}
		}
		$this->user_account_mgr->updateUserStatus($gameuid,$changeUser);
		
		$item_Mgr->commitToDB();
		
		$merge['npc_order'] = NULL;
		$task_mgr->updateUserTask($gameuid,$merge);
		
		return TRUE;
	}
}
?>