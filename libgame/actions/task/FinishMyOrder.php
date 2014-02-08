<?php
include_once GAMELIB.'/model/TaskManager.class.php';
include_once GAMELIB.'/model/UserGameItemManager.class.php';
include_once GAMELIB.'/model/UserMessageManager.class.php';
class FinishMyOrder extends GameActionBase{
	protected function _exec()
	{
		//完成 个人 任务
		$gameuid = $this->getParam("gameuid",'string');
		$f_gameuid = $this->getParam("f_gameuid",'string');
		$isNpc = $this->getParam("isNpc",'int');
		$mes_id = $this->getParam("mes_id",'int');
		
		$task_mgr = new TaskManager();
		$taskinfo = $task_mgr->getTask($f_gameuid);
		if (empty($taskinfo) || empty($taskinfo['my_order'])){
			$this->throwException("task not exist ".$gameuid,GameStatusCode::TASK_NOT_EXIST);
		}
		$my_order = $taskinfo['my_order'];
		$orderArr = explode(";",$my_order);
		$npc = $orderArr[0];
		$orderRequestStr = $orderArr[1];
		$orderRewardStr  = $orderArr[2];
		$task_time = $orderArr[3];
		
		
		//给创建任务者 奖励
		$item_Mgr = new UserGameItemManager($f_gameuid);
		//判断任务条件 达到
		$orderRequestArr = explode("|",$orderRequestStr);
		foreach ($orderRequestArr as $requestItemStr){
			$requestItem = explode(":",$requestItemStr);
			$id = $requestItem[0];
			$count = $requestItem[1];
			$item_Mgr->addItem($id,$count);
		}
		$item_Mgr->commitToDB();
		
		$mes_info = array();
		if ($isNpc == 1){
			//true
			$mes_info['f_gameuid']="1";
			
		}else{
			$mes_info['f_gameuid']=$gameuid;
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
		}
		
		
		$merge['my_order'] = NULL;
		$task_mgr->updateUserTask($f_gameuid,$merge);
		
		$mes_mgr = new UserMessageManager();
		$mes_info['gameuid'] = $f_gameuid;
		$mes_info['type'] =  MethodType::MESSTYPE_ORDER;
		$mes_info['updatetime'] = time();
		$mes_info['data_id'] = $mes_id;
		$mes_mgr->addMessage($f_gameuid,$mes_info);
		
		return TRUE;
	}
}
?>